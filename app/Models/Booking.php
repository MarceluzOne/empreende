<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'responsible_name',
        'cpf',
        'booking_date',
        'guests_count',
        'observation',
        'user_id'
    ];

    protected $casts = [
        'booking_date' => 'datetime',
    ];

    public function user()
{
    return $this->belongsTo(User::class);
}
}