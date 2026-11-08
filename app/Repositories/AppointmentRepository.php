<?php

namespace App\Repositories;

use App\Contracts\AppointmentRepositoryInterface;
use App\Models\Appointment;
use Illuminate\Support\Collection;

// Репозиторий для работы с записями
class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function getByDate(string $date, array $statuses = []): Collection
    {
        $query = Appointment::whereDate('appointment_date', $date);

        if (!empty($statuses)) {
            $query->whereIn('status', $statuses);
        }

        return $query->get();
    }

    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }

    public function attachServices(Appointment $appointment, array $serviceIds): void
    {
        $appointment->services()->attach($serviceIds);
    }

    public function getAllPaginated(int $perPage = 20)
    {
        return Appointment::with('services')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Appointment
    {
        return Appointment::findOrFail($id);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $appointment = $this->findOrFail($id);
        $appointment->status = $status;
        return $appointment->save();
    }

    public function delete(int $id): bool
    {
        return Appointment::findOrFail($id)->delete();
    }
}
