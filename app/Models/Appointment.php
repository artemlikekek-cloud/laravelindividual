<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'client_name',
        'client_phone',
        'client_email',
        'car_model',
        'appointment_date',
        'appointment_time',
        'total_duration_minutes',
        'total_price',
        'status',
        'notes'
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'total_price' => 'decimal:2'
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'appointment_services');
    }
}
