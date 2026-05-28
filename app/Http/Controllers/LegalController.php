<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class LegalController extends Controller
{
    public function privacyPolicy()
    {
        return Inertia::render('Legal/Privacitat');
    }

    public function cookiePolicy()
    {
        return Inertia::render('Legal/Cookies');
    }

    public function legalNotice()
    {
        return Inertia::render('Legal/AvisLegal');
    }
}
