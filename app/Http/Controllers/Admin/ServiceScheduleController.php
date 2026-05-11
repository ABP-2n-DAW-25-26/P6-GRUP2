<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Service;
use App\Models\ServiceSchedule;

class ServiceScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
        $serviceId = $request->query('service_id');

        if (!$serviceId) {
            abort(404);
        }

        $service = Service::findOrFail($serviceId);

        return Inertia::render('admin/ServiceSchedules/Create', [
            'service' => $service->only('id', 'name'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'service_id'  => 'required|exists:services,id',
            'day_of_week' => 'required|integer|between:1,7',
            'start_time'  => 'required',
            'end_time'    => 'required',
        ]);

        ServiceSchedule::create($validated);

        return redirect()->route('services.show', $validated['service_id']);
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
        $schedule = ServiceSchedule::findOrFail($id);
        $serviceId = $schedule->service_id;

        $schedule->delete();

        return to_route('services.show', $serviceId)
            ->with('message', 'Horari eliminat correctament.');
    }
}
