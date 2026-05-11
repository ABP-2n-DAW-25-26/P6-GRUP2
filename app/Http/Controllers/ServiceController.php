<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Inertia\Inertia;
use App\Http\Resources\ServiceResource;
use App\Http\Controllers\Controller;
use App\Models\ServiceAppointment;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Rules\TurnstileRule;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationCreated;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /*
     return Inertia::render('PedirCita', [
        'services' => ServiceResource::collection(Service::all())
    ]);
    */
        return Inertia::render('PedirCita', [
            'turnstileSiteKey' => config('services.turnstile.site_key'),
            'services' => Service::whereHas('schedules')->get()->map(function ($service) {
                return [
                    'id' => $service->id,
                    'nom' => $service->name,
                    'descripció' => $service->description,
                    'durada' => $service->duration_minutes . ' min',
                    'icon' => $service->icon,
                ];
            })
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
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'customer_name' => 'required|string|max:255',

            'customer_phone' => ['required', 'regex:/^[0-9]{9}$/'],
            'customer_email' => 'required|email|max:255',
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'cf-turnstile-response' => ['required', 'string', new TurnstileRule],

        ], [
            'cf-turnstile-response.required' => 'Por favor, completa la verificación.',
        ]);
        $appointmentDateTime = \Carbon\Carbon::parse(
            $validated['appointment_date'] . ' ' . $validated['start_time']
        );

        if ($appointmentDateTime->isPast()) {
            return back()->withErrors([
                'start_time' => 'No pots reservar una hora passada.',
            ]);
        }
        $exists = ServiceAppointment::whereDate('appointment_date', $validated['appointment_date'])
            ->where('service_id', $validated['service_id'])
            ->where('start_time', $validated['start_time'])
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
            'start_time' => \Carbon\Carbon::parse($validated['start_time'])->format('H:i:s'),
            'end_time' => \Carbon\Carbon::parse($validated['start_time'])->addMinutes(Service::find($validated['service_id'])->duration_minutes)->format('H:i'),
            'status' => 'pending',

        ]);
        $mailData = [
            'name'         => $validated['customer_name'],
            'email'        => $validated['customer_email'],
            'service_name' => $service->name,
            'duration'     => $service->duration_minutes . ' min',
            'date'         => $validated['appointment_date'],
            'time'         => $validated['start_time'],
            'pharmacy'     => 'Farmàcia Soler',
            'address'      => 'Carrer Nou, 22, 17600 Figueres, Girona',
            'phone'        => '972 50 02 99',
        ];
        Mail::to($validated['customer_email'])->send(new ReservationCreated($mailData));

        /*   Inertia::flash([
            'message' => 'Reservació creada amb èxit! Rebràs una confirmació per correu electrònic aviat.',

        ]);*/
        return to_route('pedir-cita')->with('success', [
            'message' => 'Reservació creada amb èxit! Rebràs una confirmació aviat.',
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
    public function downloadPdf(Request $request)
    {
        $request->validate([
            'service' => 'required|exists:services,id',
            'date' => 'required|date',
            'time' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email',
        ]);

        $service = Service::findOrFail($request->service);

        $data = [
            'service_name' => $service->name,
            'duration' => $service->duration_minutes . ' min',
            'date' => $request->date,
            'time' => $request->time,
            'name' => $request->name,
            'email' => $request->email,
            'pharmacy' => 'Farmàcia Soler',
            'address' => 'Carrer Nou, 22, 17600 Figueres, Girona',
            'phone' => '972 50 02 99',
        ];

        $pdf = Pdf::loadView('pdf.appointment', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'cita-' . str_replace(' ', '-', $request->name) . '-' . $request->date . '.pdf';

        return $pdf->download($filename);
    }

    public function getBookedTimes(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'service_id' => 'required|exists:services,id',
        ]);

        $times = ServiceAppointment::whereDate('appointment_date', $request->date)
            ->where('service_id', $request->service_id)
            ->get()
            ->map(function ($appointment) {
                return \Carbon\Carbon::parse($appointment->start_time)->format('H:i');
            })
            ->unique()
            ->values();

        return response()->json($times);
    }
    public function getSchedule(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $service = Service::with('schedules')->findOrFail($request->service_id);

        $schedules = $service->schedules->map(function ($schedule) use ($service) {
            // Generate time slots from start_time to end_time by duration_minutes
            $slots = [];
            $current = \Carbon\Carbon::parse($schedule->start_time);
            $end = \Carbon\Carbon::parse($schedule->end_time);

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
