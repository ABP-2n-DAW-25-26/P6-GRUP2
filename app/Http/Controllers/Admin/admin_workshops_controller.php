<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Models\WorkshopInscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class admin_workshops_controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workshops = Workshop::query()
            ->select([
                'id',
                'name',
                'description',
                'photo_path',
                'workshop_date',
                'start_time',
                'end_time',
                'max_attendees',
                'is_active',
            ])
            ->orderBy('workshop_date')
            ->orderBy('start_time')
            ->get();

        return Inertia::render('admin/Workshops/Index', [
            'workshops' => $workshops,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/Workshops/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'photo' => ['required', 'image', 'mimes:webp', 'max:5120'],
            'workshop_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'max_attendees' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ]);

        $photoPath = $request->file('photo')->store('photos/workshops', 'public');

        Workshop::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'photo_path' => $photoPath,
            'workshop_date' => $validated['workshop_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'max_attendees' => $validated['max_attendees'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        return to_route('workshops.index')
            ->with('success', 'Taller creat correctament.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workshop $workshop)
    {
        return Inertia::render('admin/Workshops/Edit', [
            'workshop' => $workshop,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'photo' => ['nullable', 'image', 'mimes:webp', 'max:5120'],
            'workshop_date' => ['sometimes', 'required', 'date'],
            'start_time' => ['sometimes', 'required', 'date_format:H:i'],
            'end_time' => ['sometimes', 'required', 'date_format:H:i', 'after:start_time'],
            'max_attendees' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ]);

        if ($request->hasFile('photo')) {
            if ($workshop->photo_path) {
                Storage::disk('public')->delete($workshop->photo_path);
            }

            $validated['photo_path'] = $request->file('photo')->store('photos/workshops', 'public');
        }

        unset($validated['photo']);

        $workshop->update($validated);

        return to_route('workshops.index')
            ->with('success', 'Taller actualitzat correctament.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workshop $workshop)
    {
        if ($workshop->photo_path) {
            Storage::disk('public')->delete($workshop->photo_path);
        }

        $workshop->delete();

        return to_route('workshops.index')
            ->with('success', 'Taller eliminat correctament.');
    }
    //Show inscriptions for a workshop 
     public function inscriptions(Workshop $workshop)
    {
        $inscriptions = $workshop->inscriptions()
            ->select(['id', 'workshop_id', 'name', 'email', 'phone', 'created_at'])
            ->orderByDesc('created_at')
            ->get();
 
        return Inertia::render('admin/Workshops/Inscriptions', [
            'workshop' => [
                'id' => $workshop->id,
                'name' => $workshop->name,
                'workshop_date' => $workshop->workshop_date,
                'start_time' => $workshop->start_time,
                'end_time' => $workshop->end_time,
                'max_attendees' => $workshop->max_attendees,
            ],
            'inscriptions' => $inscriptions,
        ]);
    }
     public function destroyInscription(Workshop $workshop, WorkshopInscription $inscription)
    {
        // Make sure the inscription actually belongs to the given workshop.
        if ($inscription->workshop_id !== $workshop->id) {
            abort(404);
        }
 
        $inscription->delete();
 
        return to_route('workshops.inscriptions', $workshop->id)
            ->with('success', 'Inscripció eliminada correctament.');
    }
}
