<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ServiceSchedule;


class Service extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'duration_minutes',
        'icon',
    ];
    public function schedules()
{
    return $this->hasMany(ServiceSchedule::class)->orderBy('day_of_week')->orderBy('start_time');
}

}

