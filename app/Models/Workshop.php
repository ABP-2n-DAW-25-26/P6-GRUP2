<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    protected $fillable = [
        'name',
        'description',
        'photo_path',
        'workshop_date',
        'start_time',
        'end_time',
        'max_attendees',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'workshop_date' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
