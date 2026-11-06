<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkingHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hours = [
            // Понедельник-Пятница (1-5): 9:00-18:00
            ['day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '18:00', 'is_working' => true],
            ['day_of_week' => 2, 'start_time' => '09:00', 'end_time' => '18:00', 'is_working' => true],
            ['day_of_week' => 3, 'start_time' => '09:00', 'end_time' => '18:00', 'is_working' => true],
            ['day_of_week' => 4, 'start_time' => '09:00', 'end_time' => '18:00', 'is_working' => true],
            ['day_of_week' => 5, 'start_time' => '09:00', 'end_time' => '18:00', 'is_working' => true],
            // Суббота (6): 10:00-16:00
            ['day_of_week' => 6, 'start_time' => '10:00', 'end_time' => '16:00', 'is_working' => true],
            // Воскресенье (0): выходной
            ['day_of_week' => 0, 'start_time' => null, 'end_time' => null, 'is_working' => false],
        ];

        foreach ($hours as $hour) {
            \App\Models\WorkingHour::create($hour);
        }
    }
}
