<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceSchedule;

class ServiceSchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         $now = now();

        // Define base weekly schedule (same for all services)
        $weeklySchedule = [
            // Monday to Friday
            ['day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '13:00'],
            ['day_of_week' => 1, 'start_time' => '15:00', 'end_time' => '19:00'],

            ['day_of_week' => 2, 'start_time' => '09:00', 'end_time' => '13:00'],
            ['day_of_week' => 2, 'start_time' => '15:00', 'end_time' => '19:00'],

            ['day_of_week' => 3, 'start_time' => '09:00', 'end_time' => '13:00'],
            ['day_of_week' => 3, 'start_time' => '15:00', 'end_time' => '19:00'],

            ['day_of_week' => 4, 'start_time' => '09:00', 'end_time' => '13:00'],
            ['day_of_week' => 4, 'start_time' => '15:00', 'end_time' => '19:00'],

            ['day_of_week' => 5, 'start_time' => '09:00', 'end_time' => '13:00'],
            ['day_of_week' => 5, 'start_time' => '15:00', 'end_time' => '19:00'],

            // Saturday (morning only)
            ['day_of_week' => 6, 'start_time' => '09:00', 'end_time' => '13:00'],
        ];

        $services = Service::all();

        foreach ($services as $service) {
            foreach ($weeklySchedule as $schedule) {
                ServiceSchedule::updateOrCreate(
                    [
                        'service_id' => $service->id,
                        'day_of_week' => $schedule['day_of_week'],
                        'start_time' => $schedule['start_time'],
                    ],
                    [
                        'end_time' => $schedule['end_time'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }

    }

