<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

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

    public function inscriptions(): HasMany
    {
        return $this->hasMany(WorkshopInscription::class);
    }

    public static function getActiveWorkshops(): LengthAwarePaginator
    {
        return self::query()
            ->select(['id', 'name', 'description', 'photo_path', 'workshop_date', 'start_time', 'end_time', 'max_attendees', 'is_active'])
            ->where('is_active', true)
            ->orderBy('workshop_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(3)
            ->through(fn(Workshop $workshop) => [
                'id' => $workshop->id,
                'name' => $workshop->name,
                'description' => $workshop->description,
                'photo_url' => $workshop->photo_path ? Storage::url($workshop->photo_path) : null,
                'workshop_date' => $workshop->workshop_date->toDateString(),
                'start_time' => substr($workshop->start_time, 0, 5),
                'end_time' => substr($workshop->end_time, 0, 5),
                'max_attendees' => $workshop->max_attendees,
                'is_active' => $workshop->is_active,
            ]);
    }
}
