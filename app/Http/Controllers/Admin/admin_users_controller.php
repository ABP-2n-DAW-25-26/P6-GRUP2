<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class admin_users_controller extends Controller
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();

        return Inertia::render('admin/Users/Index', [
            'users' => $users,
            'currentUser' => Auth::user(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/Users/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => $this->nameRules(),
            'email' => $this->emailRules(),
            'password' => $this->passwordRules(),
            'role' => ['required', 'string', 'in:admin,user'],
        ], $this->userMessages());

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => $validated['password'],
        ]);

        return to_route('users.index')->with('success', 'Usuari creat correctament.');
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
    public function edit(User $user): Response
    {
        return Inertia::render('admin/Users/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $rules = [
            'name' => $this->nameRules(),
            'email' => $this->emailRules($user->id),
            'role' => ['required', 'string', 'in:admin,superadmin'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', Password::default(), 'confirmed'];
        }

        $validated = $request->validate($rules, $this->userMessages());

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        return to_route('users.index')->with('success', 'Usuari actualitzat correctament.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['general' => 'No pots eliminar el teu propi compte.']);
        }

        $user->delete();

        return to_route('users.index')->with('success', 'Usuari eliminat correctament.');
    }

    private function userMessages(): array
    {
        return [
            'name.required' => 'El nom és obligatori.',
            'name.max' => 'El nom no pot superar els 255 caràcters.',
            'email.required' => 'El correu electrònic és obligatori.',
            'email.email' => 'El correu electrònic no té un format vàlid.',
            'email.max' => 'El correu no pot superar els 255 caràcters.',
            'email.unique' => 'Aquest correu electrònic ja està en ús.',
            'password.required' => 'La contrasenya és obligatòria.',
            'password.confirmed' => 'Les contrasenyes no coincideixen.',
            'password.min' => 'La contrasenya ha de tenir almenys 12 caràcters.',
            'password.mixed' => 'La contrasenya ha de contenir majúscules i minúscules.',
            'password.letters' => 'La contrasenya ha de contenir almenys una lletra.',
            'password.numbers' => 'La contrasenya ha de contenir almenys un número.',
            'password.symbols' => 'La contrasenya ha de contenir almenys un símbol.',
            'password.uncompromised' => 'Aquesta contrasenya ha aparegut en filtracions de dades. Si us plau, tria\'n una altra.',
            'role.required' => 'El rol és obligatori.',
            'role.in' => 'El rol seleccionat no és vàlid.',
        ];
    }
}
