<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class admin_pharmacies_controller extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function filter(Request $request)
    {
        $query = DB::table('pharmacies')
            ->select('id', 'name', 'latitude', 'longitude', 'created_at');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $pharmacies = $query
            ->orderBy('name')
            ->get();

        return response()->json([
            'pharmacies' => $pharmacies,
        ]);
    }
    public function index()
    {
        $pharmacies = DB::table('pharmacies')
            ->select('id', 'name', 'latitude', 'longitude', 'created_at')
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/Pharmacies/Index', [
            'pharmacies' => $pharmacies,
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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:pharmacies,name'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        DB::table('pharmacies')->insert([
            'name' => $validated['name'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return to_route('pharmacies.index')
            ->with('message', 'Farmàcia creada correctament.');
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
        DB::table('pharmacies')
            ->where('id', $id)
            ->delete();

        return to_route('pharmacies.index')
            ->with('message', 'Farmàcia eliminada correctament.');
    }
}
