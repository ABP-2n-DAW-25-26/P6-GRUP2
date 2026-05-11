<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Actions\Assignments\CreateAssignmentAction;
use App\Http\Requests\CreateAssignmentRequest;
use App\Mail\AssignmentCreated;
use App\Mail\AssignmentListCode;
use App\Models\Link;
use Illuminate\Support\Facades\Mail;
use App\Rules\TurnstileRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;


class AssignmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Assignments/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return Inertia::render('Assignments/Create', [
            'turnstileSiteKey' => config('services.turnstile.site_key'),
            'name' => $request->query('name'),
            'address' => $request->query('address'),
            'phone_number' => $request->query('phone_number'),
            'description' => $request->query('description'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAssignmentRequest $request, CreateAssignmentAction $createAssignment)
    {
        $validated = $request->validated();
        $assignment = $createAssignment->execute($validated);
        Mail::to($validated['address'])->send(new AssignmentCreated($validated));
        Inertia::flash([
            'message' => 'Ecarrec creat correctament',
        ]);
        return to_route('assignments.create', [
            'name' => $validated['name'] ?? null,
            'address' => $validated['address'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
            'description' => $validated['description'] ?? null,
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

    public function code(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required'],
        ]);

        $otp = (string) random_int(100000, 999999);

        Cache::put(
            'assignment_code:' . strtolower($validated['email']),
            Hash::make($otp),
            now()->addMinutes(1)
        );

        Mail::to($validated['email'])->send(new AssignmentListCode($otp));

        return back()->with('message', 'Codi enviat correctament');
    }
}