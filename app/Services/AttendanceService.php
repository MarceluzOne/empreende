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
            'customer_cpf'   => $this->sanitizeDocument($data['customer_cpf'] ?? null),
            'customer_cnpj'  => $this->sanitizeDocument($data['customer_cnpj'] ?? null),
            'customer_phone' => $this->sanitizePhone($data['customer_phone'] ?? null),
            'service_type'   => $data['service_type'],
            'description'    => $data['description'],
            'scheduled_at'   => $isScheduled
                                    ? Carbon::parse($data['scheduled_date'].' '.$data['scheduled_time'])
                                    : now(),
            'status'         => $isScheduled ? 'scheduled' : 'completed',
        ]);
    }

    /**
     * Cria um agendamento feito pelo cidadão no site público (sem usuário logado).
     * Sempre entra como 'scheduled'.
     */
    public function storePublic(array $data): Attendance
    {
        return Attendance::create([
            'user_id'        => null,
            'customer_name'  => $data['customer_name'],
            'customer_cpf'   => $this->sanitizeDocument($data['customer_cpf'] ?? null),
            'customer_phone' => $this->sanitizePhone($data['customer_phone'] ?? null),
            'service_type'   => $data['service_type'],
            'description'    => $data['description'],
            'scheduled_at'   => Carbon::parse($data['scheduled_date'].' '.$data['scheduled_time']),
            'status'         => 'scheduled',
        ]);
    }

    public function update(Attendance $attendance, array $data): Attendance
    {
        $isScheduled = filter_var($data['is_scheduled'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $attendance->update([
            // Quem fez a atualização cadastral passa a ser o atendente registrado.
            'user_id'        => Auth::id(),
            'customer_name'  => $data['customer_name'],
            'customer_cpf'   => $this->sanitizeDocument($data['customer_cpf'] ?? null),
            'customer_cnpj'  => $this->sanitizeDocument($data['customer_cnpj'] ?? null),
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

    private function sanitizeDocument(?string $value): ?string
    {
        return $value ? preg_replace('/[^0-9]/', '', $value) : null;
    }

    private function sanitizePhone(?string $value): ?string
    {
        return $value ? preg_replace('/[^0-9+]/', '', $value) : null;
    }
}
