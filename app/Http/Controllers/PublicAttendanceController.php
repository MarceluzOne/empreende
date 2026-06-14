<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Rules\CpfOrCnpj;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicAttendanceController extends Controller
{
    public function __construct(private AttendanceService $service) {}

    public function availability(Request $request)
    {
        $request->validate([
            'year'  => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $start = Carbon::create($request->year, $request->month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $attendances = Attendance::where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', $start)
            ->where('scheduled_at', '<=', $end)
            ->get(['id', 'scheduled_at']);

        $byDate = [];
        foreach ($attendances as $a) {
            $date = $a->scheduled_at->format('Y-m-d');
            $byDate[$date][] = [
                'start' => $a->scheduled_at->format('H:i'),
                'end'   => $a->scheduled_at->copy()->addMinutes(30)->format('H:i'),
            ];
        }

        return response()->json(['bookings_by_date' => $byDate]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_cpf'   => ['nullable', 'string', new CpfOrCnpj],
            'customer_phone' => 'nullable|string|max:20',
            'service_type'   => 'required|string',
            'description'    => 'required|string',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required',
        ]);

        $this->service->storePublic($request->all());

        return redirect()->route('home')->with('success', 'Agendamento realizado com sucesso! Aguarde a confirmação da nossa equipe.');
    }
}
