<?php

namespace App\Models;

use App\Support\Phone;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventParticipant extends Model
{
    use HasUuids;
    protected $fillable = ['event_id', 'name', 'email', 'cpf', 'whatsapp'];

    public function getFormattedWhatsappAttribute(): ?string
    {
        return Phone::format($this->whatsapp);
    }

    /**
     * CPF parcialmente mascarado para exibição pública (ex.: ata de presença).
     * Mostra os 2 primeiros e os 3 últimos dígitos: 08x.xxx.xx4-92.
     */
    public function maskedCpf(): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $this->cpf);

        if (strlen($digits) !== 11) {
            return $this->cpf ?: null;
        }

        $masked = substr($digits, 0, 2).str_repeat('x', 6).substr($digits, 8, 3);

        return substr($masked, 0, 3).'.'.substr($masked, 3, 3).'.'.substr($masked, 6, 3).'-'.substr($masked, 9, 2);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(EventAttendance::class);
    }

    /**
     * Datas (Y-m-d) em que o participante teve presença registrada.
     */
    public function presentDates(): array
    {
        return $this->attendances
            ->map(fn ($a) => Carbon::parse($a->event_date)->format('Y-m-d'))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Presença completa: registrou presença em todas as datas do evento.
     * Requisito para emissão do certificado.
     */
    public function hasFullAttendance(): bool
    {
        $eventDates = $this->event->allDates();

        return count($eventDates) > 0
            && empty(array_diff($eventDates, $this->presentDates()));
    }
}
