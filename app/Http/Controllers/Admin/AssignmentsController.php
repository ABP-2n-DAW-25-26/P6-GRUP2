<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\assignments;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssignmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = assignments::latest()->get();

        return Inertia::render('admin/Assignments/Index', [
            'assignments' => $assignments ?? [],
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
        $request->validate([
            'status' => ['required', 'in:pending,cancelled,completed'],
        ]);

        assignments::findOrFail($id)->update(['status' => $request->status]);

        $messages = [
            'completed' => 'Encàrrec marcat com a completat.',
            'cancelled' => 'Encàrrec cancel·lat correctament.',
            'pending' => 'Encàrrec marcat com a pendent.',
        ];

        return back()->with('success', $messages[$request->status]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
