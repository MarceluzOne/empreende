<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventParticipant extends Model
{
    use HasUuids;
    protected $fillable = ['event_id', 'name', 'email', 'cpf', 'whatsapp'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
