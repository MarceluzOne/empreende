<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasUuids;
    protected $fillable = [
        'responsible_name',
        'cpf',
        'booking_date',
        'end_date', 
        'guests_count',
        'observation',
        'user_id',
        'resource_type',

    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
