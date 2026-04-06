<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceService
{
    public function store(array $data)
    {
        if (!empty($data['customer_cpf'])) {
            $data['customer_cpf'] = preg_replace('/[^0-9]/', '', $data['customer_cpf']);
        }

        return Attendance::create([
            'user_id'       => Auth::id(),
            'booking_id'    => $data['booking_id'] ?? null,
            'customer_name' => $data['customer_name'],
            'customer_cpf'  => $data['customer_cpf'],
            'service_type'  => $data['service_type'],
            'description'   => $data['description'],
            'status'        => $data['status'] ?? 'completed',
        ]);
    }
}