<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('admin/Mail/Index', [
            'mail' => [
                'mail_mailer' => (string) config('mail.default', 'smtp'),
                'mail_scheme' => (string) config('mail.mailers.smtp.scheme', 'smtp'),
                'mail_host' => (string) config('mail.mailers.smtp.host', ''),
                'mail_port' => (string) config('mail.mailers.smtp.port', '587'),
                'mail_username' => (string) config('mail.mailers.smtp.username', ''),
                'mail_from_address' => (string) config('mail.from.address', ''),
            ],
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
        $update = [
            'MAIL_MAILER' => (string) $request->input('mail_mailer', ''),
            'MAIL_SCHEME' => (string) $request->input('mail_scheme', ''),
            'MAIL_HOST' => (string) $request->input('mail_host', ''),
            'MAIL_PORT' => (string) $request->input('mail_port', ''),
            'MAIL_USERNAME' => (string) $request->input('mail_username', ''),
            'MAIL_FROM_ADDRESS' => '"'.(string) $request->input('mail_from_address', '').'"',
        ];

        if ($request->filled('mail_password')) {
            $update['MAIL_PASSWORD'] = (string) $request->input('mail_password');
        }

        $envPath = base_path('.env');
        $env = file_get_contents($envPath);

        foreach ($update as $key => $value) {
            $pattern = '/^'.preg_quote($key, '/').'=.*$/m';
            $line = $key.'='.$value;

            if (preg_match($pattern, $env) === 1) {
                $env = preg_replace($pattern, $line, $env);
            } else {
                $env .= PHP_EOL.$line;
            }
        }

        file_put_contents($envPath, $env);

        redirect()->route('mail.index');
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
}
