<?php

namespace App\Providers;

use App\Contracts\AppointmentRepositoryInterface;
use App\Contracts\ServiceRepositoryInterface;
use App\Contracts\WorkingHourRepositoryInterface;
use App\Repositories\AppointmentRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\WorkingHourRepository;
use Illuminate\Support\ServiceProvider;

// Регистрация зависимостей
class AppServiceProvider extends ServiceProvider
{
    // Регистрируем сервисы приложения
    public function register(): void
    {
        // Связываем интерфейсы с реализациями
        $this->app->bind(AppointmentRepositoryInterface::class, AppointmentRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(WorkingHourRepositoryInterface::class, WorkingHourRepository::class);
    }

    // Загрузка сервисов
    public function boot(): void
    {
        //
    }
}
