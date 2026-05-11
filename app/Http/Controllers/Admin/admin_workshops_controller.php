<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class admin_workshops_controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
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
    public function create(): Response
    {
        return Inertia::render('admin/Workshops/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'photo' => ['required', 'image', 'max:5120'],
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
            ->with('message', 'Taller creat correctament.');
    }



    /**
     * Display the specified resource.
     */
    public function show($id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}
}
