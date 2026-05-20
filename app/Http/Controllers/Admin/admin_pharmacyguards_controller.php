<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\PharmacyGuard;

class admin_pharmacyguards_controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function filter(Request $request)
    {
        $query = DB::table('pharmacy_guards')
            ->join('pharmacies', 'pharmacy_guards.pharmacy_id', '=', 'pharmacies.id')
            ->select(
                'pharmacy_guards.id',
                'pharmacy_guards.date',
                'pharmacy_guards.pharmacy_id',
                'pharmacies.name as pharmacy_name',
            );

        if ($request->filled('search')) {
            $query->where('pharmacies.name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('pharmacy_guards.date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('pharmacy_guards.date', '<=', $request->date_to);
        }

        $guards = $query
            ->orderBy('pharmacy_guards.date')
            ->get();

        return response()->json([
            'pharmacyguards' => $guards,
        ]);
    }

    public function index()
    {
        $pharmacies = DB::table('pharmacies')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $guards = DB::table('pharmacy_guards')
            ->join('pharmacies', 'pharmacy_guards.pharmacy_id', '=', 'pharmacies.id')
            ->select(
                'pharmacy_guards.id',
                'pharmacy_guards.date',
                'pharmacy_guards.pharmacy_id',
                'pharmacies.name as pharmacy_name',
            )
            ->orderBy('pharmacy_guards.date')
            ->get();

        return Inertia::render('admin/PharmacyGuards/Index', [
            'pharmacies' => $pharmacies,
            'guards' => $guards,
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
            'date' => ['required', 'date', 'unique:pharmacy_guards,date'],
            'pharmacy_id' => ['required', 'exists:pharmacies,id'],
        ]);

        PharmacyGuard::create($validated);

        return to_route('pharmacyguards.index')
            ->with('success', 'Guàrdia creada correctament.');
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
        DB::table('pharmacy_guards')
            ->where('id', $id)
            ->delete();

        return to_route('pharmacyguards.index')
            ->with('success', 'Guàrdia eliminada correctament.');
    }
}
