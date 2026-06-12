<?php

namespace App\Services;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceService
{
    public function store(array $data): Attendance
    {
        $isScheduled = filter_var($data['is_scheduled'] ?? false, FILTER_VALIDATE_BOOLEAN);

        return Attendance::create([
            'user_id'        => Auth::id(),
            'customer_name'  => $data['customer_name'],
            'customer_cpf'   => $this->sanitizeCpf($data['customer_cpf'] ?? null),
            'customer_phone' => $this->sanitizePhone($data['customer_phone'] ?? null),
            'service_type'   => $data['service_type'],
            'description'    => $data['description'],
            'scheduled_at'   => $isScheduled
                                    ? Carbon::parse($data['scheduled_date'].' '.$data['scheduled_time'])
                                    : now(),
            'status'         => $isScheduled ? 'scheduled' : 'completed',
        ]);
    }

    public function update(Attendance $attendance, array $data): Attendance
    {
        $isScheduled = filter_var($data['is_scheduled'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $attendance->update([
            'customer_name'  => $data['customer_name'],
            'customer_cpf'   => $this->sanitizeCpf($data['customer_cpf'] ?? null),
            'customer_phone' => $this->sanitizePhone($data['customer_phone'] ?? null),
            'service_type'   => $data['service_type'],
            'description'    => $data['description'],
            'scheduled_at'   => $isScheduled
                                    ? Carbon::parse($data['scheduled_date'].' '.$data['scheduled_time'])
                                    : $attendance->scheduled_at,
            'status'         => $data['status'],
        ]);

        return $attendance;
    }

    private function sanitizeCpf(?string $value): ?string
    {
        return $value ? preg_replace('/[^0-9]/', '', $value) : null;
    }

    private function sanitizePhone(?string $value): ?string
    {
        return $value ? preg_replace('/[^0-9+]/', '', $value) : null;
    }
}
