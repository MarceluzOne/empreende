<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\JobApplication;
use App\Models\JobSeeker;
use App\Models\JobVacancy;
use App\Services\JobSeekerService;
use Illuminate\Http\Request;

class PortalUsuarioController extends Controller
{
    private array $interestAreas = [
        'Administração', 'Tecnologia da Informação', 'Saúde', 'Educação',
        'Construção Civil', 'Comércio e Vendas', 'Indústria', 'Logística',
        'Gastronomia', 'Serviços Gerais', 'Jurídico', 'Contabilidade / Finanças', 'Outros',
    ];

    private array $experienceLevels = [
        'Sem experiência', 'Até 6 meses', '6 meses a 1 ano',
        '1 ano a 2 anos', '2 anos a 3 anos', '3 anos a 5 anos', 'Mais de 5 anos',
    ];

    public function __construct(private JobSeekerService $service) {}

    public function index()
    {
        $user = auth()->user();

        $perfil = JobSeeker::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        $cpf = $this->profileCpf($perfil);

        // Casa participações por e-mail OU CPF, para reconhecer a mesma pessoa
        // mesmo que ela tenha se inscrito no evento usando outro e-mail.
        $meusEventos = EventParticipant::where(fn($q) => $this->matchUser($q, $user, $cpf))
            ->with(['event.speaker', 'attendances'])
            ->latest()
            ->get();

        $eventosDisponiveis = Event::where('status', 'active')
            ->whereNotIn('id', $meusEventos->pluck('event_id'))
            ->with('speaker')
            ->latest()
            ->take(6)
            ->get();

        $minhasCandidaturas = $perfil
            ? JobApplication::where('job_seeker_id', $perfil->id)
                ->with('vacancy.user.empresa')
                ->latest()
                ->get()
            : collect();

        $vagasCandidadasIds = $minhasCandidaturas->pluck('job_vacancy_id');

        $vagas = JobVacancy::where('status', 'active')
            ->whereNotIn('id', $vagasCandidadasIds)
            ->with('user.empresa')
            ->latest()
            ->take(6)
            ->get();

        return view('portal.usuario.index', compact(
            'meusEventos',
            'eventosDisponiveis',
            'vagas',
            'perfil',
            'minhasCandidaturas',
            'vagasCandidadasIds',
        ));
    }

    // ── Currículo ──────────────────────────────────────────────

    public function createCurriculo()
    {
        if (JobSeeker::where('user_id', auth()->id())->exists()) {
            return redirect()->route('portal.usuario')->with('info', 'Você já possui um currículo. Use a opção Editar.');
        }

        return view('portal.usuario.curriculo.create', [
            'interestAreas' => $this->interestAreas,
            'experienceLevels' => $this->experienceLevels,
        ]);
    }

    public function storeCurriculo(Request $request)
    {
        $request->validate([
            'name'                         => 'required|string|max:255',
            'cpf'                          => 'nullable|string|max:14',
            'job_function'                 => 'required|string|max:255',
            'interest_area'                => 'required|string|max:100',
            'city'                         => 'nullable|string|max:100',
            'state'                        => 'nullable|string|max:2',
            'phone'                        => 'nullable|string|max:20',
            'email'                        => 'nullable|email|max:255',
            'linkedin_url'                 => 'nullable|url|max:255',
            'github_url'                   => 'nullable|url|max:255',
            'summary'                      => 'nullable|string|max:2000',
            'skills'                       => 'nullable|string|max:1000',
            'experience'                   => 'nullable|string|max:50',
            'experiences'                  => 'nullable|array',
            'experiences.*.company'        => 'nullable|string|max:255',
            'experiences.*.role'           => 'nullable|string|max:255',
            'experiences.*.start'          => 'nullable|string|max:20',
            'experiences.*.end'            => 'nullable|string|max:20',
            'experiences.*.activities'     => 'nullable|string|max:2000',
            'education'                    => 'nullable|array',
            'education.*.course'           => 'nullable|string|max:255',
            'education.*.institution'      => 'nullable|string|max:255',
            'education.*.year'             => 'nullable|string|max:50',
            'languages'                    => 'nullable|array',
            'languages.*.language'         => 'nullable|string|max:100',
            'languages.*.level'            => 'nullable|string|max:50',
            'certifications'               => 'nullable|array',
            'certifications.*'             => 'nullable|string|max:255',
        ], [
            'name.required'          => 'O nome é obrigatório.',
            'job_function.required'  => 'Informe a função desejada.',
            'interest_area.required' => 'Selecione uma área de interesse.',
        ]);

        $this->service->store($request->all());

        return redirect()->route('portal.usuario')->with('success', 'Currículo criado com sucesso!');
    }

    public function editCurriculo()
    {
        $perfil = JobSeeker::where('user_id', auth()->id())
            ->orWhere('email', auth()->user()->email)
            ->firstOrFail();

        return view('portal.usuario.curriculo.edit', [
            'perfil'          => $perfil,
            'interestAreas'   => $this->interestAreas,
            'experienceLevels'=> $this->experienceLevels,
        ]);
    }

    public function updateCurriculo(Request $request)
    {
        $perfil = JobSeeker::where('user_id', auth()->id())
            ->orWhere('email', auth()->user()->email)
            ->firstOrFail();

        $request->validate([
            'name'                         => 'required|string|max:255',
            'cpf'                          => 'nullable|string|max:14',
            'job_function'                 => 'required|string|max:255',
            'interest_area'                => 'required|string|max:100',
            'city'                         => 'nullable|string|max:100',
            'state'                        => 'nullable|string|max:2',
            'phone'                        => 'nullable|string|max:20',
            'email'                        => 'nullable|email|max:255',
            'linkedin_url'                 => 'nullable|url|max:255',
            'github_url'                   => 'nullable|url|max:255',
            'summary'                      => 'nullable|string|max:2000',
            'skills'                       => 'nullable|string|max:1000',
            'experience'                   => 'nullable|string|max:50',
            'experiences'                  => 'nullable|array',
            'experiences.*.company'        => 'nullable|string|max:255',
            'experiences.*.role'           => 'nullable|string|max:255',
            'experiences.*.start'          => 'nullable|string|max:20',
            'experiences.*.end'            => 'nullable|string|max:20',
            'experiences.*.activities'     => 'nullable|string|max:2000',
            'education'                    => 'nullable|array',
            'education.*.course'           => 'nullable|string|max:255',
            'education.*.institution'      => 'nullable|string|max:255',
            'education.*.year'             => 'nullable|string|max:50',
            'languages'                    => 'nullable|array',
            'languages.*.language'         => 'nullable|string|max:100',
            'languages.*.level'            => 'nullable|string|max:50',
            'certifications'               => 'nullable|array',
            'certifications.*'             => 'nullable|string|max:255',
        ], [
            'name.required'          => 'O nome é obrigatório.',
            'job_function.required'  => 'Informe a função desejada.',
            'interest_area.required' => 'Selecione uma área de interesse.',
        ]);

        $this->service->update($perfil, $request->all());

        return redirect()->route('portal.usuario')->with('success', 'Currículo atualizado com sucesso!');
    }

    public function destroyCurriculo()
    {
        $perfil = JobSeeker::where('user_id', auth()->id())
            ->orWhere('email', auth()->user()->email)
            ->firstOrFail();

        $this->service->destroy($perfil);

        return redirect()->route('portal.usuario')->with('success', 'Currículo excluído com sucesso.');
    }

    // ── Eventos ────────────────────────────────────────────────

    public function inscreverEvento(Request $request, Event $event)
    {
        if ($event->isFull()) {
            return back()->with('error', 'As vagas para este evento estão esgotadas.');
        }

        $user   = auth()->user();
        $perfil = JobSeeker::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        $cpf = $this->profileCpf($perfil);

        $jaInscrito = EventParticipant::where('event_id', $event->id)
            ->where(fn($q) => $this->matchUser($q, $user, $cpf))
            ->exists();

        if ($jaInscrito) {
            return back()->with('info', 'Você já está inscrito neste evento.');
        }

        EventParticipant::create([
            'event_id' => $event->id,
            'name'     => $perfil->name ?? $user->name,
            'email'    => $user->email,
            'cpf'      => $cpf,
            'whatsapp' => $perfil->phone ?? null,
        ]);

        return redirect()->to(route('portal.usuario') . '#meus-eventos')->with('success', 'Inscrição realizada com sucesso!');
    }

    public function cancelarEvento(Event $event)
    {
        $this->findParticipant($event)->delete();

        return redirect()->route('portal.usuario')->with('success', 'Inscrição cancelada.');
    }

    /**
     * Certificado do próprio candidato (PDF inline). Reconhece o participante
     * pelo e-mail OU CPF do usuário logado, e apenas para eventos concluídos.
     */
    public function certificado(Event $event, \App\Services\CertificateService $certificates)
    {
        abort_unless($event->isCompleted(), 403, 'Certificado disponível apenas para eventos concluídos.');

        $participant = $this->findParticipant($event);

        abort_unless($participant->hasFullAttendance(), 403, 'Certificado disponível apenas para quem teve presença em todos os dias do evento.');

        return $certificates->pdf($event, $participant)->stream($certificates->filename($participant->name));
    }

    /**
     * CPF do perfil normalizado (apenas dígitos), pois o JobSeeker guarda com
     * máscara e o EventParticipant guarda sem.
     */
    private function profileCpf(?JobSeeker $perfil): ?string
    {
        $digits = $perfil && $perfil->cpf ? preg_replace('/\D/', '', $perfil->cpf) : '';

        return $digits !== '' ? $digits : null;
    }

    /**
     * Fecho de query que casa um participante pelo e-mail OU CPF do usuário.
     */
    private function matchUser($query, $user, ?string $cpf): void
    {
        $query->where('email', $user->email);
        if ($cpf) {
            $query->orWhere('cpf', $cpf);
        }
    }

    /**
     * Localiza o participante do evento correspondente ao usuário logado,
     * pelo e-mail OU CPF do perfil.
     */
    private function findParticipant(Event $event): EventParticipant
    {
        $user   = auth()->user();
        $perfil = JobSeeker::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        return EventParticipant::where('event_id', $event->id)
            ->where(fn($q) => $this->matchUser($q, $user, $this->profileCpf($perfil)))
            ->firstOrFail();
    }
}
