<?php

namespace App\Actions\Emails;

use App\Models\Email;

class CreateEmailAction
{
    public function execute(array $data): Email
    {
        return Email::create([
            'email' => $data['email'],
            'active' => $data['active'] ?? 0,
        ]);
    }
}
