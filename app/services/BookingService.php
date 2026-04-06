<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class BookingService
{
    public function createBookings(array $data)
    {
        $dates = $this->resolveDates($data);

        if (empty($dates)) {
            throw new \Exception('Nenhum dia útil encontrado no período.');
        }

        foreach ($dates as $date) {
            $start = $date . ' ' . $data['start_time'];
            $end = $date . ' ' . $data['end_time'];

            // Regra de Ouro: Validação de Conflito
            if ($this->hasConflict($data['resource_type'], $start, $end)) {
                $local = $data['resource_type'] === 'auditorio' ? 'Auditório' : 'Sala de Reunião';
                $formattedDate = Carbon::parse($date)->format('d/m/Y');
                throw new \Exception("Conflito: O {$local} já está reservado para o dia {$formattedDate} entre {$data['start_time']} e {$data['end_time']}.");
            }

            Booking::create([
                'resource_type'    => $data['resource_type'],
                'responsible_name' => $data['responsible_name'],
                'cpf'              => !empty($data['cpf']) ? preg_replace('/[^0-9]/', '', $data['cpf']) : null,
                'booking_date'     => $start,
                'end_date'         => $end,
                'guests_count'     => $data['guests_count'],
                'observation'      => $data['observation'] ?? null,
                'user_id'          => Auth::id(),
            ]);
        }

        return count($dates);
    }

    private function hasConflict($resource, $start, $end)
    {
        return Booking::where('resource_type', $resource)
            ->where(function ($query) use ($start, $end) {
                // Lógica de interseção de horários
                $query->where('booking_date', '<', $end)
                      ->where('end_date', '>', $start);
            })->exists();
    }

    private function resolveDates($data)
    {
        $dates = [];
        if ($data['type'] === 'single') {
            $dates[] = $data['single_date'];
        } elseif ($data['type'] === 'consecutive') {
            $period = CarbonPeriod::create($data['start_date'], $data['end_date_period']);
            foreach ($period as $date) {
                if (!$date->isWeekend()) $dates[] = $date->format('Y-m-d');
            }
        } elseif ($data['type'] === 'alternated') {
            $dates = $data['selected_dates'];
        }
        return $dates;
    }
}