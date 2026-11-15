<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'client_name',
        'client_email',
        'review_text',
        'rating',
        'is_approved'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'rating' => 'integer'
    ];

    // Получить только одобренные отзывы
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
