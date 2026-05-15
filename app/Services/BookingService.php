<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class BookingService
{
    public function createBookings(array $data): int
    {
        $dates = $this->resolveDates($data);

        if (empty($dates)) {
            throw new \Exception('Nenhum dia útil encontrado no período.');
        }

        foreach ($dates as $date) {
            $start = $date.' '.$data['start_time'];
            $end   = $date.' '.$data['end_time'];

            if ($this->hasConflict($data['resource_type'], $start, $end)) {
                $local = $data['resource_type'] === 'auditorio' ? 'Auditório' : 'Sala de Reunião';
                $formattedDate = Carbon::parse($date)->format('d/m/Y');
                throw new \Exception("Conflito: O {$local} já está reservado para {$formattedDate} entre {$data['start_time']} e {$data['end_time']}.");
            }

            Booking::create([
                'resource_type'    => $data['resource_type'],
                'responsible_name' => $data['responsible_name'],
                'cpf'              => !empty($data['cpf']) ? preg_replace('/[^0-9]/', '', $data['cpf']) : null,
                'phone'            => !empty($data['phone']) ? preg_replace('/[^0-9]/', '', $data['phone']) : null,
                'booking_date'     => $start,
                'end_date'         => $end,
                'guests_count'     => $data['guests_count'],
                'observation'      => $data['observation'] ?? null,
                'user_id'          => Auth::id(),
            ]);
        }

        return count($dates);
    }

    public function update(Booking $booking, array $data): Booking
    {
        $start = $data['date'].' '.$data['start_time'];
        $end   = $data['date'].' '.$data['end_time'];

        if ($this->hasConflict($data['resource_type'], $start, $end, $booking->id)) {
            throw new \Exception('Este horário já está ocupado por outro agendamento.');
        }

        $booking->update([
            'responsible_name' => $data['responsible_name'],
            'resource_type'    => $data['resource_type'],
            'booking_date'     => $start,
            'end_date'         => $end,
            'guests_count'     => $data['guests_count'],
            'cpf'              => !empty($data['cpf']) ? preg_replace('/[^0-9]/', '', $data['cpf']) : null,
            'observation'      => $data['observation'] ?? null,
        ]);

        return $booking;
    }

    private function hasConflict(string $resource, string $start, string $end, ?int $excludeId = null): bool
    {
        return Booking::where('resource_type', $resource)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($start, $end) {
                $q->where('booking_date', '<', $end)
                  ->where('end_date', '>', $start);
            })->exists();
    }

    private function resolveDates(array $data): array
    {
        if ($data['type'] === 'single') {
            return [$data['single_date']];
        }

        if ($data['type'] === 'consecutive') {
            $dates = [];
            foreach (CarbonPeriod::create($data['start_date'], $data['end_date_period']) as $date) {
                if (!$date->isWeekend()) {
                    $dates[] = $date->format('Y-m-d');
                }
            }
            return $dates;
        }

        if ($data['type'] === 'alternated') {
            return $data['selected_dates'];
        }

        return [];
    }
}
