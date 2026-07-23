<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Rules\Cnpj;
use App\Rules\Cpf;
use App\Services\AttendanceService;
use App\Services\AuditService;
use App\Support\BusinessDay;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $service) {}

    private function serviceList(): array
    {
        return [
            'Formalização MEI',
            'Emissão de DAS',
            'Declaração Anual (DASN)',
            'Parcelamento de Débitos',
            'Alteração Cadastral',
            'Baixa de Empresa',
            'Consultoria Sebrae',
            'Crédito/Banco do Nordeste',
            'Outros',
        ];
    }

    public function index(Request $request)
    {
        // "proximas" (padrão): de hoje em diante. "passadas": histórico p/ busca.
        $periodo = $request->input('periodo') === 'passadas' ? 'passadas' : 'proximas';
        $today   = Carbon::today();

        $attendances = Attendance::with('user')
            ->when($request->search, fn($q) => $q->where('customer_name', 'like', '%'.$request->search.'%'))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->service_type, fn($q) => $q->where('service_type', $request->service_type))
            ->when(
                $periodo === 'passadas',
                fn($q) => $q->whereDate('scheduled_at', '<', $today)->orderBy('scheduled_at', 'desc'),
                fn($q) => $q->where(fn($w) => $w->whereDate('scheduled_at', '>=', $today)->orWhereNull('scheduled_at'))->latest()
            )
            ->paginate(10)
            ->withQueryString();

        return view('attendances.index', compact('attendances', 'periodo'))->with('serviceTypes', $this->serviceList());
    }

    public function create()
    {
        return view('attendances.create', [
            'services'  => $this->serviceList(),
            'startHour' => (int) substr(BusinessDay::openingTime(), 0, 2),
        ]);
    }

    /**
     * Regra do horário do agendamento: quando for agendado, o horário precisa
     * estar dentro do expediente (a partir das 09h). Não afeta "Realizado Agora".
     */
    private function scheduledTimeRule(Request $request): array
    {
        $isScheduled = filter_var($request->is_scheduled, FILTER_VALIDATE_BOOLEAN);
        $opening = BusinessDay::openingTime();
        $closing = BusinessDay::closingTime();

        return ['required_if:is_scheduled,true,1', 'nullable',
            function ($attribute, $value, $fail) use ($isScheduled, $opening, $closing) {
                if (!$isScheduled || !$value) {
                    return;
                }
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
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_cpf'   => ['required', 'string', new Cpf],
            'customer_cnpj'  => ['nullable', 'string', new Cnpj],
            'customer_phone' => 'nullable|string|max:20',
            'service_type'   => 'required|string',
            'description'    => 'required|string',
            'scheduled_date' => 'required_if:is_scheduled,true,1|nullable|date',
            'scheduled_time' => $this->scheduledTimeRule($request),
        ], [
            'customer_cpf.required'      => 'Informe o CPF do cidadão.',
            'scheduled_date.required_if' => 'Selecione o dia do agendamento.',
            'scheduled_time.required_if' => 'Selecione o horário do agendamento.',
        ]);

        try {
            $attendance = $this->service->store($request->all());
            AuditService::log('created', $attendance);

            $message = filter_var($request->is_scheduled, FILTER_VALIDATE_BOOLEAN)
                ? 'Atendimento agendado com sucesso!'
                : 'Atendimento registrado com sucesso!';

            return redirect()->route('attendances.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors('Erro ao processar: '.$e->getMessage())->withInput();
        }
    }

    public function edit(Attendance $attendance)
    {
        $isScheduled = $attendance->status === 'scheduled';

        return view('attendances.edit', [
            'attendance'  => $attendance,
            'services'    => $this->serviceList(),
            'isScheduled' => $isScheduled,
            'startHour'   => (int) substr(BusinessDay::openingTime(), 0, 2),
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_cpf'   => ['required', 'string', new Cpf],
            'customer_cnpj'  => ['nullable', 'string', new Cnpj],
            'customer_phone' => 'nullable|string|max:20',
            'service_type'   => 'required',
            'description'    => 'required',
            'scheduled_time' => $this->scheduledTimeRule($request),
        ], [
            'customer_cpf.required'      => 'Informe o CPF do cidadão.',
            'scheduled_time.required_if' => 'Selecione o horário do agendamento.',
        ]);

        $this->service->update($attendance, $request->all());
        AuditService::log('updated', $attendance);

        return redirect()->route('attendances.index')->with('success', 'Atendimento atualizado!');
    }

    public function complete(Attendance $attendance)
    {
        // Quem concluiu (atualização de status) passa a ser o atendente registrado.
        $attendance->update(['status' => 'completed', 'user_id' => auth()->id()]);
        AuditService::log('updated', $attendance->fresh(), [['status' => 'completed']]);

        return back()->with('success', 'Atendimento concluído com sucesso!');
    }

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

    public function destroy(Attendance $attendance)
    {
        abort_unless(auth()->user()->roles->contains('name', 'admin'), 403);

        AuditService::log('deleted', $attendance);
        $attendance->delete();

        return redirect()->route('attendances.index')->with('success', 'Atendimento excluído com sucesso!');
    }
}
