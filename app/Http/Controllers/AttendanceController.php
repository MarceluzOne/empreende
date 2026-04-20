<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Services\AttendanceService;
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
        $attendances = Attendance::with('user')
            ->when($request->search, fn($q) => $q->where('customer_name', 'ilike', '%'.$request->search.'%'))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->service_type, fn($q) => $q->where('service_type', $request->service_type))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('attendances.index', compact('attendances'))->with('serviceTypes', $this->serviceList());
    }

    public function create()
    {
        return view('attendances.create', ['services' => $this->serviceList()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_cpf'   => 'nullable|string|max:14',
            'customer_phone' => 'nullable|string|max:20',
            'service_type'   => 'required|string',
            'description'    => 'required|string',
            'scheduled_date' => 'required_if:is_scheduled,true,1|nullable|date',
            'scheduled_time' => 'required_if:is_scheduled,true,1|nullable',
        ]);

        try {
            $this->service->store($request->all());

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
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'service_type'   => 'required',
            'description'    => 'required',
        ]);

        $this->service->update($attendance, $request->all());

        return redirect()->route('attendances.index')->with('success', 'Atendimento atualizado!');
    }
}
