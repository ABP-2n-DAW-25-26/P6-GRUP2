<?php

namespace App\Http\Controllers;

use App\Http\Requests\DownloadAppointmentPdfRequest;
use App\Http\Requests\GetBookedTimesRequest;
use App\Http\Requests\GetScheduleRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Mail\ReservationCreated;
use App\Mail\ReservationCreatedAdmin;
use App\Models\Email;
use App\Models\Service;
use App\Models\ServiceAppointment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AppointmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Show the appointment booking form with available services
        return Inertia::render('Appointments/Create', [
            'turnstileSiteKey' => config('services.turnstile.site_key'),
            'services' => Service::whereHas('schedules')->get()->map(function ($service) {
                return [
                    'id' => $service->id,
                    'nom' => $service->name,
                    'descripció' => $service->description,
                    'durada' => $service->duration_minutes.' min',
                    'icon' => $service->icon,
                ];
            }),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request)
    {
        // Validate and store the appointment, then send confirmation email

        $validated = $request->validated();
        $appointmentDateTime = Carbon::parse(
            $validated['appointment_date'].' '.$validated['start_time']
        );

        if ($appointmentDateTime->isPast()) {
            return back()->withErrors([
                'appointment_date' => 'No pots reservar una hora passada.',
            ]);
        }

        $startTimeFormatted = Carbon::parse($validated['start_time'])->format('H:i:s');

        $exists = ServiceAppointment::whereDate('appointment_date', $validated['appointment_date'])
            ->where('service_id', $validated['service_id'])
            ->where('start_time', $startTimeFormatted)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'start_time' => 'Aquesta hora ja està reservada.',
            ]);
        }

        $service = Service::findOrFail($validated['service_id']);
        ServiceAppointment::create([
            'service_id' => $validated['service_id'],
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'],
            'appointment_date' => $validated['appointment_date'],
            'start_time' => Carbon::parse($validated['start_time'])->format('H:i:s'),
            'end_time' => Carbon::parse($validated['start_time'])->addMinutes($service->duration_minutes)->format('H:i'),
            'status' => 'pending',

        ]);
        $mailData = [
            'name' => $validated['customer_name'],
            'email' => $validated['customer_email'],
            'service_name' => $service->name,
            'duration' => $service->duration_minutes.' min',
            'date' => $validated['appointment_date'],
            'time' => $validated['start_time'],
            'pharmacy' => 'Farmàcia Soler',
            'address' => 'Carrer Nou, 22, 17600 Figueres, Girona',
            'phone' => '688 466 225',
        ];

        $activeEmails = Email::where('active', 1)->pluck('email')->toArray();

        $flashKey = 'success';
        $flashMessage = 'Reservació creada correctament! Rebràs un correu de confirmació.';

        try {
            Mail::to($validated['customer_email'])->send(new ReservationCreated($mailData));
            if (! empty($activeEmails)) {
                Mail::to($activeEmails)->send(new ReservationCreatedAdmin($mailData));
            }
        } catch (\Exception $e) {
            $flashKey = 'error';
            $flashMessage = 'Reservació creada, però no hem pogut enviar el correu.';
        }

        return to_route('appointments.create')
            ->with($flashKey, $flashMessage)
            ->with('reservation', [
                'service' => $validated['service_id'],
                'date' => $validated['appointment_date'],
                'time' => $validated['start_time'],
                'name' => $validated['customer_name'],
                'email' => $validated['customer_email'],
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function downloadPdf(DownloadAppointmentPdfRequest $request)
    {
        // Generate PDF with appointment details

        $validated = $request->validated();

        $service = Service::findOrFail($validated['service']);
        $data = [
            'service_name' => $service->name,
            'duration' => $service->duration_minutes.' min',
            'date' => $validated['date'],
            'time' => $validated['time'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'pharmacy' => 'Farmàcia Soler',
            'address' => 'Carrer Nou, 22, 17600 Figueres, Girona',
            'phone' => '688 466 225',
        ];

        $pdf = Pdf::loadView('pdf.appointment', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'cita-'.str_replace(' ', '-', $validated['name']).'-'.$validated['date'].'.pdf';

        return $pdf->download($filename);
    }

    // Get booked times for a specific date and service
    public function getBookedTimes(GetBookedTimesRequest $request)
    {
        $validated = $request->validated();

        $times = ServiceAppointment::whereDate('appointment_date', $validated['date'])
            ->where('service_id', $validated['service_id'])
            ->get()
            ->map(function ($appointment) {
                return Carbon::parse($appointment->start_time)->format('H:i');
            })
            ->unique()
            ->values();

        return response()->json($times);
    }

    // Get schedule for a specific service
    public function getSchedule(GetScheduleRequest $request)
    {
        $validated = $request->validated();

        $service = Service::with('schedules')->findOrFail($validated['service_id']);

        $schedules = $service->schedules->map(function ($schedule) use ($service) {
            // Generate time slots from start_time to end_time by duration_minutes
            $slots = [];
            $current = Carbon::parse($schedule->start_time);
            $end = Carbon::parse($schedule->end_time);

            while ($current->copy()->addMinutes($service->duration_minutes)->lte($end)) {
                $slots[] = $current->format('H:i');
                $current->addMinutes($service->duration_minutes);
            }

            return [
                'day_of_week' => $schedule->day_of_week,
                'slots' => $slots,
            ];
        });

        return response()->json($schedules);
    }
}
