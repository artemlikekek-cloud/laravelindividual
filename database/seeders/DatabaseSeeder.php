<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Создаем админа
        User::create([
            'name' => 'Администратор',
            'email' => 'admin@sto.local',
            'password' => bcrypt('admin123'),
        ]);

        // Запускаем другие сидеры
        $this->call([
            ServicesSeeder::class,
            WorkingHoursSeeder::class,
        ]);
    }
}
