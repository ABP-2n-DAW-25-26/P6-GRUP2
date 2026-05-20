<?php

namespace App\Actions\Workshops;

use App\Models\Workshop;
use App\Models\WorkshopInscription;

class CreateWorkshopInscriptionAction
{
    /**
     * Create a new inscription for a workshop.
     *
     * @param  array{name: string, email: string, phone: string}  $data
     */
    public function execute(Workshop $workshop, array $data): WorkshopInscription
    {
        return WorkshopInscription::create([
            'workshop_id' => $workshop->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);
    }
}
