<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        $services = Service::select(
            'id',
            'name',
            'description',
            'duration_minutes',
            'icon'
        )->latest()->get();

        return Inertia::render('admin/Services/Index', [
            'services' => $services,
            'currentUser' => Auth::user(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return Inertia::render('admin/Services/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        //

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s]+$/u',
            ],

            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'icon' => 'nullable|string|max:50',
        ]);

        Service::create($validated);

        Inertia::flash(['message' => 'Servei creat correctament.']);

        return redirect()
            ->route('services.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $service = Service::with('schedules')->findOrFail($id);

        return Inertia::render('admin/ServiceSchedules/Show', [
            'service' => $service,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $service = Service::findOrFail($id);

        return Inertia::render('admin/Services/Edit', [
            'service' => $service,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, string $id)
    {
        //
        $validated = $request->validated();

        $service = Service::findOrFail($id);
        $service->update($validated);

        return redirect()
            ->route('services.index')
            ->with('message', 'Servei actualitzat correctament.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $service = Service::findOrFail($id);
        $service->delete();

        Inertia::flash(['message' => 'Servei eliminat correctament.']);

        return redirect()->back();
    }
}
