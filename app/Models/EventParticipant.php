<?php

namespace App\Models;

use App\Support\Phone;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventParticipant extends Model
{
    use HasUuids;
    protected $fillable = ['event_id', 'name', 'email', 'cpf', 'whatsapp'];

    public function getFormattedWhatsappAttribute(): ?string
    {
        return Phone::format($this->whatsapp);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
