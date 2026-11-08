<?php

namespace App\Repositories;

use App\Contracts\WorkingHourRepositoryInterface;
use App\Models\WorkingHour;

// Репозиторий для работы с рабочими часами
class WorkingHourRepository implements WorkingHourRepositoryInterface
{
    public function getByDayOfWeek(int $dayOfWeek): ?WorkingHour
    {
        return WorkingHour::where('day_of_week', $dayOfWeek)
            ->where('is_working', true)
            ->first();
    }
}
