<?php

namespace App\Contracts;

use App\Models\Appointment;
use Illuminate\Support\Collection;

// Интерфейс для работы с записями
interface AppointmentRepositoryInterface
{
    /**
     * Получить все записи на определенную дату
     */
    public function getByDate(string $date, array $statuses = []): Collection;

    /**
     * Создать новую запись
     */
    public function create(array $data): Appointment;

    /**
     * Прикрепить услуги к записи
     */
    public function attachServices(Appointment $appointment, array $serviceIds): void;

    /**
     * Получить все записи с пагинацией
     */
    public function getAllPaginated(int $perPage = 20);

    /**
     * Найти запись по ID
     */
    public function findOrFail(int $id): Appointment;

    /**
     * Обновить статус записи
     */
    public function updateStatus(int $id, string $status): bool;

    /**
     * Удалить запись
     */
    public function delete(int $id): bool;
}
