<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasUuids;
    protected $fillable = [
        'title',
        'date',
        'start_time',
        'duration_minutes',
        'max_capacity',
        'speaker_id',
        'booking_id',
        'type',
        'extra_dates',
        'status',
        'reopened_at',
        'image_path',
        'visibility',
        'whatsapp_group_link',
        'share_token',
    ];

    protected $casts = [
        'date'        => 'date',
        'extra_dates' => 'array',
        'reopened_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Todo evento nasce com um token de acesso (link de convite).
        static::creating(function (Event $event) {
            if (empty($event->share_token)) {
                $event->share_token = self::uniqueShareToken();
            }
        });
    }

    public static function uniqueShareToken(): string
    {
        do {
            $token = Str::random(12);
        } while (self::where('share_token', $token)->exists());

        return $token;
    }

    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    public function isPrivate(): bool
    {
        return $this->visibility === 'private';
    }

    public function getShareUrlAttribute(): string
    {
        return route('public.events.register', $this->share_token);
    }

    public function speaker(): BelongsTo
    {
        return $this->belongsTo(Speaker::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'event_bookings');
    }

    public function allDates(): array
    {
        // Armazenamento é o mesmo para todos os tipos: `date` = primeira data e
        // `extra_dates` = as demais. Então a lista completa é sempre a primeira
        // data seguida das extras (corrige "alternated", que perdia a primeira).
        $first = $this->date->format('Y-m-d');

        return empty($this->extra_dates)
            ? [$first]
            : array_merge([$first], $this->extra_dates);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function availableSpots(): int
    {
        return max(0, $this->max_capacity - $this->participants()->count());
    }

    public function isFull(): bool
    {
        return $this->availableSpots() <= 0;
    }

    /**
     * Momento em que o evento termina: última data + horário de início + duração.
     */
    public function endsAt(): ?Carbon
    {
        $dates = $this->allDates();
        if (empty($dates)) {
            return null;
        }

        // Datas em 'Y-m-d' — max() coincide com a ordem cronológica.
        return Carbon::parse(max($dates).' '.$this->start_time)
            ->addMinutes((int) $this->duration_minutes);
    }

    /**
     * Indica se o evento já terminou (última data + horário de término no passado).
     * Usado para derivar o status automaticamente, sem depender de cron/agendador.
     */
    public function hasEnded(): bool
    {
        return (bool) $this->endsAt()?->isPast();
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Reaberto: o admin devolveu o evento para "Em andamento" depois de ele já
     * ter encerrado pela data. A reabertura só vale para o encerramento que ela
     * desfez — se o evento for remarcado para uma data posterior, `reopened_at`
     * fica no passado em relação ao novo fim e a conclusão automática volta a
     * valer, sem precisar limpar a coluna.
     */
    public function isReopened(): bool
    {
        $endsAt = $this->endsAt();

        return $this->reopened_at !== null && $endsAt !== null && $this->reopened_at->gte($endsAt);
    }

    /**
     * Evento concluído: marcado manualmente como 'completed' OU encerrado
     * automaticamente pela data/horário. Cancelado e reaberto nunca contam
     * como concluído.
     *
     * É esta a condição que libera a emissão de certificados — usar sempre este
     * método no lugar de comparar `status === 'completed'`, senão os eventos
     * concluídos automaticamente ficam sem certificado.
     */
    public function isCompleted(): bool
    {
        if ($this->status === 'completed') {
            return true;
        }

        return !$this->isCancelled() && !$this->isReopened() && $this->hasEnded();
    }

    /**
     * Inscrições fechadas: evento concluído (manual ou automático) ou cancelado.
     */
    public function registrationsClosed(): bool
    {
        return $this->isCompleted() || $this->isCancelled();
    }

    public function getStatusLabelAttribute(): string
    {
        return match (true) {
            $this->isCancelled() => 'Cancelado',
            $this->isCompleted() => 'Concluído',
            default              => 'Em andamento',
        };
    }

    /**
     * Classes Tailwind do badge de status, alinhadas ao status_label.
     */
    public function getStatusColorAttribute(): string
    {
        return match (true) {
            $this->isCancelled() => 'bg-red-100 text-red-700',
            $this->isCompleted() => 'bg-green-100 text-green-700',
            default              => 'bg-blue-100 text-blue-700',
        };
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    /**
     * Rótulo amigável do período do evento, como intervalo (não lista todos os
     * dias). Ex.: "de 23 a 31 de julho de 2026".
     */
    public function datesLabel(): string
    {
        $dates = collect($this->allDates())->map(fn ($d) => Carbon::parse($d))->sort()->values();
        $first = $dates->first();
        $last  = $dates->last();

        if (!$first) {
            return '';
        }

        if ($dates->count() === 1) {
            return $first->locale('pt_BR')->isoFormat('D [de] MMMM [de] YYYY');
        }

        // Mesmo mês e ano: "de 23 a 31 de julho de 2026".
        if ($first->isSameMonth($last)) {
            return 'de '.$first->format('d').' a '.$last->format('d')
                 .' de '.$first->locale('pt_BR')->isoFormat('MMMM [de] YYYY');
        }

        // Mesmo ano, meses diferentes: "de 23 de julho a 3 de agosto de 2026".
        if ($first->year === $last->year) {
            return 'de '.$first->locale('pt_BR')->isoFormat('D [de] MMMM')
                 .' a '.$last->locale('pt_BR')->isoFormat('D [de] MMMM [de] YYYY');
        }

        // Anos diferentes: inclui o ano nas duas pontas.
        return 'de '.$first->locale('pt_BR')->isoFormat('D [de] MMMM [de] YYYY')
             .' a '.$last->locale('pt_BR')->isoFormat('D [de] MMMM [de] YYYY');
    }

    public function totalHours(): float
    {
        return round(count($this->allDates()) * $this->duration_minutes / 60, 1);
    }

    public function endTime(): string
    {
        return Carbon::createFromTimeString($this->start_time)
            ->addMinutes($this->duration_minutes)
            ->format('H:i');
    }
}
