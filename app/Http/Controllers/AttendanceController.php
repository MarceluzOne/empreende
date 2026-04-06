<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        // Eager Loading do usuário que atendeu para evitar o problema de N+1 queries
        $attendances = Attendance::with('user')->latest()->paginate(10);

        return view('attendances.index', compact('attendances'));
    }

    public function create()
    {
        $services = [
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

        return view('attendances.create', compact('services'));
    }

    public function store(Request $request)
    {
        // 1. Validação
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_cpf' => 'nullable|string|max:14',
            'service_type' => 'required|string',
            'description' => 'required|string',
            // Validamos 'is_scheduled' para aceitar os valores das tabs
            'scheduled_date' => 'required_if:is_scheduled,true,1|nullable|date',
            'scheduled_time' => 'required_if:is_scheduled,true,1|nullable',
        ]);

        try {
            return DB::transaction(function () use ($request) {

                $cpf = $request->customer_cpf ? preg_replace('/[^0-9]/', '', $request->customer_cpf) : null;

                /**
                 * CORREÇÃO DA LÓGICA:
                 * O filter_var transforma a string "true" do Alpine em um booleano real do PHP.
                 */
                $isScheduled = filter_var($request->is_scheduled, FILTER_VALIDATE_BOOLEAN);

                if ($isScheduled) {
                    // Se for agendado, usamos estritamente o que veio do formulário
                    $scheduledAt = Carbon::parse($request->scheduled_date.' '.$request->scheduled_time);
                    $status = 'scheduled';
                } else {
                    // Se for "Realizado Agora", usamos o timestamp atual
                    $scheduledAt = now();
                    $status = 'completed';
                }

                Attendance::create([
                    'user_id' => Auth::id(),
                    'customer_name' => $request->customer_name,
                    'customer_cpf' => $cpf,
                    'service_type' => $request->service_type,
                    'description' => $request->description,
                    'scheduled_at' => $scheduledAt,
                    'status' => $status,
                ]);

                $message = $isScheduled ? 'Atendimento agendado com sucesso!' : 'Atendimento registrado com sucesso!';

                return redirect()->route('attendances.index')->with('success', $message);
            });

        } catch (\Exception $e) {
            return back()
                ->withErrors('Erro ao processar: '.$e->getMessage())
                ->withInput();
        }
    }

    public function edit(Attendance $attendance)
    {
        $services = [
            'Formalização MEI',
            'Emissão de Guia',
            'Declaração Anual',
            'Consultoria',
            'Outros',
        ];

        // Verificamos se o atendimento original era um agendamento
        // ou se a data agendada é diferente da data de criação
        $isScheduled = $attendance->status === 'scheduled';

        return view('attendances.edit', compact('attendance', 'services', 'isScheduled'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'service_type' => 'required',
            'description' => 'required',
        ]);

        $isScheduled = filter_var($request->is_scheduled, FILTER_VALIDATE_BOOLEAN);

        // Se mudou para "Agora", o status deve virar 'completed' e a data ser 'now'
        // A menos que o usuário tenha mudado o status manualmente no select final.
        $scheduledAt = $isScheduled
            ? Carbon::parse($request->scheduled_date.' '.$request->scheduled_time)
            : $attendance->scheduled_at;

        $attendance->update([
            'customer_name' => $request->customer_name,
            'customer_cpf' => preg_replace('/[^0-9]/', '', $request->customer_cpf),
            'service_type' => $request->service_type,
            'description' => $request->description,
            'scheduled_at' => $scheduledAt,
            'status' => $request->status, // Respeita o select de status
        ]);

        return redirect()->route('attendances.index')->with('success', 'Atendimento atualizado!');
    }
}
