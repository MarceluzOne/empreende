<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    protected $casts = [
        'date'        => 'date',
        'extra_dates' => 'array',
    ];

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
        return match ($this->type) {
            'alternated'  => $this->extra_dates ?? [$this->date->format('Y-m-d')],
            'consecutive' => $this->extra_dates
                ? array_merge([$this->date->format('Y-m-d')], $this->extra_dates)
                : [$this->date->format('Y-m-d')],
            default => [$this->date->format('Y-m-d')],
        };
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

    public function endTime(): string
    {
        return Carbon::createFromTimeString($this->start_time)
            ->addMinutes($this->duration_minutes)
            ->format('H:i');
    }
}
