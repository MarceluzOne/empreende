<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Rules\CpfOrCnpj;
use App\Services\AttendanceService;
use App\Support\BusinessDay;
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
        $minDate = BusinessDay::nextBookable();
        $opening = BusinessDay::openingTime();
        $closing = BusinessDay::closingTime();

        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_cpf'   => ['required', 'string', new CpfOrCnpj],
            'customer_phone' => ['required', 'string', 'max:20',
                function ($attribute, $value, $fail) {
                    if (strlen(preg_replace('/\D/', '', (string) $value)) < 10) {
                        $fail('Informe um telefone válido com DDD.');
                    }
                },
            ],
            'service_type'   => 'required|string',
            'description'    => 'required|string',
            'scheduled_date' => ['required', 'date', 'after_or_equal:'.$minDate->format('Y-m-d'),
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isWeekend()) {
                        $fail('Não há atendimento aos finais de semana. Escolha um dia útil.');
                    }
                },
            ],
            'scheduled_time' => ['required',
                function ($attribute, $value, $fail) use ($opening, $closing) {
                    if (!preg_match('/^\d{2}:\d{2}$/', (string) $value)) {
                        $fail('Horário inválido.');
                        return;
                    }
                    if ($value < $opening) {
                        $fail('Os atendimentos começam a partir das '.$opening.'.');
                    } elseif ($value > $closing) {
                        $fail('O último horário de atendimento é às '.$closing.'.');
                    }
                },
            ],
        ], [
            'customer_name.required'        => 'Informe seu nome completo.',
            'customer_cpf.required'         => 'Informe o CPF ou CNPJ.',
            'customer_phone.required'       => 'Informe o telefone para contato.',
            'service_type.required'         => 'Selecione o serviço desejado.',
            'description.required'          => 'Descreva sua situação.',
            'scheduled_date.required'       => 'Selecione o dia do atendimento no calendário.',
            'scheduled_date.date'           => 'A data selecionada é inválida.',
            'scheduled_date.after_or_equal' => 'Só é possível agendar a partir do próximo dia útil ('.$minDate->format('d/m/Y').').',
            'scheduled_time.required'       => 'Selecione um horário disponível.',
        ]);

        $this->service->storePublic($request->all());

        return redirect()->route('home')->with('success', 'Agendamento realizado com sucesso! Aguarde a confirmação da nossa equipe.');
    }
}
