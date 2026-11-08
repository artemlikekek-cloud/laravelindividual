<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

// Интерфейс для работы с услугами
interface ServiceRepositoryInterface
{
    /**
     * Получить все активные услуги
     */
    public function getActive(): Collection;

    /**
     * Получить услуги по массиву ID
     */
    public function findByIds(array $ids): Collection;
}
