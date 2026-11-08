<?php

namespace App\Services;

use App\Contracts\AppointmentRepositoryInterface;
use App\Contracts\ServiceRepositoryInterface;

// Сервис для работы с записями
class AppointmentService
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository,
        private ServiceRepositoryInterface $serviceRepository,
        private TimeSlotService $timeSlotService
    ) {}

    // Создать новую запись
    public function createAppointment(array $data): array
    {
        // Получаем выбранные услуги
        $services = $this->serviceRepository->findByIds($data['services']);
        $totalDuration = $services->sum('duration_minutes');
        $totalPrice = $services->sum('price');

        // Проверяем конфликты времени
        if ($this->timeSlotService->checkConflict($data['appointment_date'], $data['appointment_time'], $totalDuration)) {
            return [
                'success' => false,
                'error' => 'К сожалению, выбранное время уже занято. Пожалуйста, выберите другое время.'
            ];
        }

        // Создаем запись
        $appointment = $this->appointmentRepository->create([
            'client_name' => $data['client_name'],
            'client_phone' => $data['client_phone'],
            'client_email' => $data['client_email'],
            'car_model' => $data['car_model'],
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
            'total_duration_minutes' => $totalDuration,
            'total_price' => $totalPrice,
            'status' => 'pending'
        ]);

        // Привязываем услуги
        $this->appointmentRepository->attachServices($appointment, $data['services']);

        return [
            'success' => true,
            'appointment' => $appointment
        ];
    }

    // Обновить статус записи
    public function updateStatus(int $id, string $status): bool
    {
        return $this->appointmentRepository->updateStatus($id, $status);
    }

    // Удалить запись
    public function deleteAppointment(int $id): bool
    {
        return $this->appointmentRepository->delete($id);
    }

    // Получить все записи с пагинацией
    public function getAllAppointments(int $perPage = 20)
    {
        return $this->appointmentRepository->getAllPaginated($perPage);
    }
}
