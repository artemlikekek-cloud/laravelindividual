<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;

class ReviewsSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            [
                'client_name' => 'Иван Петров',
                'client_email' => 'ivan.petrov@example.com',
                'review_text' => 'Отличный сервис! Быстро диагностировали проблему с двигателем и устранили её. Цены адекватные, персонал вежливый.',
                'rating' => 5,
                'is_approved' => true,
            ],
            [
                'client_name' => 'Мария Сидорова',
                'client_email' => 'maria.sidorova@example.com',
                'review_text' => 'Делала развал-схождение, всё прошло хорошо. Мастера знают своё дело. Приду ещё!',
                'rating' => 5,
                'is_approved' => true,
            ],
            [
                'client_name' => 'Алексей Иванов',
                'client_email' => 'alex.ivanov@example.com',
                'review_text' => 'Хороший автосервис, но пришлось немного подождать своей очереди. В остальном всё отлично.',
                'rating' => 4,
                'is_approved' => true,
            ],
            [
                'client_name' => 'Ольга Кузнецова',
                'client_email' => 'olga.k@example.com',
                'review_text' => 'Очень довольна обслуживанием! Сделали замену масла и тормозных колодок. Работа выполнена качественно и в срок.',
                'rating' => 5,
                'is_approved' => true,
            ],
            [
                'client_name' => 'Дмитрий Смирнов',
                'client_email' => 'dmitry.s@example.com',
                'review_text' => 'Неплохо, но можно было бы побыстрее. Качество работы хорошее.',
                'rating' => 4,
                'is_approved' => true,
            ],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
    }
}
