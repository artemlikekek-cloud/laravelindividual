<?php

namespace App\Services;

use App\Contracts\AppointmentRepositoryInterface;
use App\Contracts\WorkingHourRepositoryInterface;
use Carbon\Carbon;

// Сервис для работы с временными слотами
class TimeSlotService
{
    private const SLOT_INTERVAL_MINUTES = 30;

    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository,
        private WorkingHourRepositoryInterface $workingHourRepository
    ) {}

    // Получить доступные временные слоты для записи
    public function getAvailableSlots(string $date, int $duration): array
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $workingHour = $this->workingHourRepository->getByDayOfWeek($dayOfWeek);

        if (!$workingHour) {
            return [];
        }

        $existingAppointments = $this->appointmentRepository->getByDate(
            $date,
            ['pending', 'confirmed']
        );

        return $this->generateAvailableSlots($date, $workingHour, $duration, $existingAppointments);
    }

    // Генерация доступных слотов
    private function generateAvailableSlots($date, $workingHour, $duration, $existingAppointments): array
    {
        $slots = [];
        $start = Carbon::parse($date . ' ' . $workingHour->start_time);
        $end = Carbon::parse($date . ' ' . $workingHour->end_time);

        while ($start->copy()->addMinutes($duration)->lte($end)) {
            $slotTime = $start->format('H:i');
            
            if (!$this->hasConflict($date, $slotTime, $duration, $existingAppointments)) {
                $slots[] = $slotTime;
            }

            $start->addMinutes(self::SLOT_INTERVAL_MINUTES);
        }

        return $slots;
    }

    // Проверка конфликта времени
    private function hasConflict($date, $time, $duration, $existingAppointments): bool
    {
        $slotStart = Carbon::parse($date . ' ' . $time);
        $slotEnd = $slotStart->copy()->addMinutes($duration);

        foreach ($existingAppointments as $appointment) {
            $appointmentStart = Carbon::parse(
                $appointment->appointment_date->format('Y-m-d') . ' ' . $appointment->appointment_time
            );
            $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->total_duration_minutes);

            if ($slotStart->lt($appointmentEnd) && $slotEnd->gt($appointmentStart)) {
                return true;
            }
        }

        return false;
    }

    // Проверить конфликт для новой записи
    public function checkConflict(string $date, string $time, int $duration): bool
    {
        $existingAppointments = $this->appointmentRepository->getByDate(
            $date,
            ['pending', 'confirmed']
        );

        return $this->hasConflict($date, $time, $duration, $existingAppointments);
    }
}
