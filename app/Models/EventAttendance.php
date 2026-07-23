<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAttendance extends Model
{
    use HasUuids;

    protected $fillable = ['event_participant_id', 'event_date', 'checked_in_at'];

    protected $casts = [
        'event_date'    => 'date',
        'checked_in_at' => 'datetime',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(EventParticipant::class, 'event_participant_id');
    }
}
