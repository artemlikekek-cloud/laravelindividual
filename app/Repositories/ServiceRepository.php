<?php

namespace App\Repositories;

use App\Contracts\ServiceRepositoryInterface;
use App\Models\Service;
use Illuminate\Support\Collection;

// Репозиторий для работы с услугами
class ServiceRepository implements ServiceRepositoryInterface
{
    public function getActive(): Collection
    {
        return Service::where('is_active', true)->get();
    }

    public function findByIds(array $ids): Collection
    {
        return Service::whereIn('id', $ids)->get();
    }
}
