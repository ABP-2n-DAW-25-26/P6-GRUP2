<?php

namespace App\Http\Controllers;

use App\Actions\Assignments\CreateAssignmentAction;
use App\Http\Requests\CreateAssignmentRequest;
use App\Mail\AssignmentCreated;
use App\Mail\AssignmentCreatedAdmin;
use App\Mail\AssignmentListCode;
use App\Models\assignments as Assignment;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

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
        $activeEmails = Email::where('active', 1)->pluck('email')->toArray();

        $flashKey = 'success';
        $flashMessage = 'Encàrrec creat correctament! Rebràs un correu de confirmació.';

        try {
            Mail::to($validated['address'])->send(new AssignmentCreated($validated));
            if (! empty($activeEmails)) {
                Mail::to($activeEmails)->send(new AssignmentCreatedAdmin($validated));
            }
        } catch (\Exception $e) {
            $flashKey = 'error';
            $flashMessage = 'No hem pogut enviar el correu de confirmació.';
        }

        return to_route('assignments.create', [
            'name' => $validated['name'] ?? null,
            'address' => $validated['address'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
            'description' => $validated['description'] ?? null,
        ])->with($flashKey, $flashMessage);
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
            'email' => ['required', 'email'],
        ]);

        $otpExpiresInMinutes = 10;
        $otp = (string) random_int(100000, 999999);
        $normalizedEmail = strtolower($validated['email']);

        Cache::put(
            'assignment_code:'.$normalizedEmail,
            Hash::make($otp),
            now()->addMinutes($otpExpiresInMinutes)
        );

        $request->session()->put('assignment_code_email', $normalizedEmail);

        try {
            Mail::to($validated['email'])->send(new AssignmentListCode($otp, $otpExpiresInMinutes));
            $message = 'Rebràs el codi al correu.';
        } catch (\Exception $e) {
            $message = 'Correu no trobat.';
        }

        return back()->with('success', $message);
    }

    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $email = $request->session()->get('assignment_code_email');

        if (! $email) {
            return back()->withErrors([
                'code' => 'Primer envia el codi al teu correu',
            ]);
        }

        $cacheKey = 'assignment_code:'.$email;
        $hashedOtp = Cache::get($cacheKey);

        if (! $hashedOtp || ! Hash::check($validated['code'], $hashedOtp)) {
            return back()->withErrors([
                'code' => 'Codi invalid o caducat',
            ]);
        }

        Cache::forget($cacheKey);

        $assignments = [];
        if ($email) {
            $assignments = Assignment::where('address', $email)->get()->values();
        }

        return Inertia::render('Assignments/Index', [
            'assignments' => $assignments,
        ]);
    }
}
