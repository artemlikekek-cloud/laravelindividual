<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'Диагностика двигателя', 'duration_minutes' => 30, 'price' => 1000, 'description' => 'Компьютерная диагностика двигателя'],
            ['name' => 'Замена масла', 'duration_minutes' => 45, 'price' => 1500, 'description' => 'Замена моторного масла и масляного фильтра'],
            ['name' => 'Шиномонтаж (4 колеса)', 'duration_minutes' => 60, 'price' => 2000, 'description' => 'Снятие/установка 4 колес с балансировкой'],
            ['name' => 'Развал-схождение', 'duration_minutes' => 90, 'price' => 2500, 'description' => 'Регулировка углов установки колес'],
            ['name' => 'Замена тормозных колодок', 'duration_minutes' => 120, 'price' => 3000, 'description' => 'Замена передних или задних колодок'],
            ['name' => 'ТО-1 (базовое)', 'duration_minutes' => 90, 'price' => 5000, 'description' => 'Базовое техническое обслуживание'],
            ['name' => 'ТО-2 (расширенное)', 'duration_minutes' => 180, 'price' => 8000, 'description' => 'Полное техническое обслуживание'],
        ];

        foreach ($services as $service) {
            \App\Models\Service::create($service);
        }
    }
}
