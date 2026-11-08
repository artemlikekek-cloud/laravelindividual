<?php

namespace App\Contracts;

use App\Models\WorkingHour;

// Интерфейс для работы с рабочими часами
interface WorkingHourRepositoryInterface
{
    /**
     * Получить рабочие часы для конкретного дня недели
     */
    public function getByDayOfWeek(int $dayOfWeek): ?WorkingHour;
}
