<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Email;
use App\Actions\Emails\CreateEmailAction;
use App\Http\Requests\CreateEmailRequest;
use Illuminate\Support\Facades\DB;

class EmailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Email::orderBy('email')->orderByDesc('active');

        if ($request->filled('filter')) {
            match ($request->input('filter')) {
                'active' => $query->where('active', 1),
                'inactive' => $query->where('active', 0),
                default => null,
            };
        }

        $emails = $query->get();

        if ($request->expectsJson()) {
            return response()->json($emails);
        }

        return Inertia::render('admin/Emails/Index', [
            'emails' => $emails,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/Emails/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateEmailRequest $request, CreateEmailAction $createEmail)
    {
        $validated = $request->validated();
        $createEmail->execute($validated);

        Inertia::flash([
            'message' => 'Correu electrònic creat correctament.',
        ]);

        return redirect('/admin/emails/create');
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
    public function update(Request $request, Email $email)
    {
        $request->validate([
            'active' => ['required', 'boolean'],
        ]);

        $email->update(['active' => $request->boolean('active')]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Email $email)
    {
        $email->delete();

        return redirect()->back();
    }
}
