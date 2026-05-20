<?php

namespace App\Http\Controllers;

use App\Actions\Workshops\CreateWorkshopInscriptionAction;
use App\Http\Requests\StoreWorkshopInscriptionRequest;
use App\Mail\WorkshopInscripted;
use App\Mail\WorkshopInscriptedAdmin;
use App\Models\Email;
use App\Models\Workshop;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class public_workshops_controller extends Controller
{
    public function index()
    {
        return Inertia::render('workshops/index', [
            'workshops' => Inertia::scroll(fn () => Workshop::getActiveWorkshops()),
        ]);
    }

    public function show(Workshop $workshop)
    {
        abort_if(! $workshop->is_active, 404);
        $inscriptionsCount = $workshop->inscriptions()->count();
        $isFull = $workshop->max_attendees !== null && $inscriptionsCount >= $workshop->max_attendees;

        return Inertia::render('workshops/workshopdetails', [
            'workshop' => [
                'id' => $workshop->id,
                'name' => $workshop->name,
                'description' => $workshop->description,
                'photo_url' => $workshop->photo_path ? Storage::url($workshop->photo_path) : null,
                'workshop_date' => $workshop->workshop_date->toDateString(),
                'start_time' => substr($workshop->start_time, 0, 5),
                'end_time' => substr($workshop->end_time, 0, 5),
                'max_attendees' => $workshop->max_attendees,
                'inscriptions_count' => $inscriptionsCount,
                'is_full' => $isFull,
            ],
            'turnstileSiteKey' => config('services.turnstile.site_key'),
        ]);
    }

    public function inscribe(
        StoreWorkshopInscriptionRequest $request,
        Workshop $workshop,
        CreateWorkshopInscriptionAction $action,
    ) {
        abort_if(! $workshop->is_active, 404);

        // Re-check capacity at write time to avoid a race condition between
        // two simultaneous registrations on the very last seat.
        if ($workshop->max_attendees !== null) {
            $currentCount = $workshop->inscriptions()->count();
            if ($currentCount >= $workshop->max_attendees) {
                return back()->withErrors([
                    'phone' => 'Aquest taller ja està complet.',
                ]);
            }
        }

        $validated = $request->validated();

        $inscription = $action->execute($workshop, [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        $mailData = [
            'name' => $inscription->name,
            'email' => $inscription->email,
            'phone' => $inscription->phone,
            'workshop_name' => $workshop->name,
            'workshop_date' => $workshop->workshop_date->toDateString(),
            'start_time' => substr($workshop->start_time, 0, 5),
            'end_time' => substr($workshop->end_time, 0, 5),
            'pharmacy' => 'Farmàcia Soler',
            'address' => 'Carrer Nou, 22, 17600 Figueres, Girona',
            'phone_pharmacy' => '972 50 02 99',
        ];

        $activeEmails = Email::where('active', 1)->pluck('email')->toArray();

        try {
            Mail::to($inscription->email)->send(new WorkshopInscripted($mailData));
            if (! empty($activeEmails)) {
                Mail::to($activeEmails)->send(new WorkshopInscriptedAdmin($mailData));
            }
            $message = 'Inscripció realitzada correctament! Rebràs un correu de confirmació.';
        } catch (\Exception $e) {
            $message = 'Inscripció realitzada correctament, però no hem pogut enviar el correu.';
        }

        return back()->with('success', [
            'message' => $message,
            'name' => $inscription->name,
            'email' => $inscription->email,
            'workshop_name' => $workshop->name,
        ]);
    }
}
