<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventParticipantRequest;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Requests\UpdateEventStatusRequest;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Speaker;
use App\Services\AuditService;
use App\Services\CertificateService;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(private EventService $events) {}

    public function index(Request $request)
    {
        // "proximas" (padrão): que ainda não terminaram. "passadas": já encerrados.
        // Filtro pela ÚLTIMA data do evento (correto para eventos de vários dias).
        $periodo = $request->input('periodo') === 'passadas' ? 'passadas' : 'proximas';
        $today   = \Carbon\Carbon::today();

        $events = Event::with(['speaker', 'participants'])
            ->when($request->search, fn($q) => $q->where('title', 'like', '%'.$request->search.'%'))
            ->when($request->date, fn($q) => $q->whereDate('date', $request->date))
            ->get()
            ->filter(function ($e) use ($periodo, $today) {
                $isPast = \Carbon\Carbon::parse(max($e->allDates()))->lt($today);
                return $periodo === 'passadas' ? $isPast : !$isPast;
            })
            ->sortBy(fn($e) => max($e->allDates()), SORT_REGULAR, $periodo === 'passadas')
            ->values();

        $events   = $this->paginateCollection($events, 10);
        $speakers = Speaker::orderBy('name')->get();

        return view('events.index', compact('events', 'speakers', 'periodo'));
    }

    private function paginateCollection(\Illuminate\Support\Collection $items, int $perPage): \Illuminate\Pagination\LengthAwarePaginator
    {
        $page = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function create()
    {
        $speakers = Speaker::orderBy('name')->get();
        return view('events.create', compact('speakers'));
    }

    public function store(StoreEventRequest $request)
    {
        try {
            $event = $this->events->create($request->all(), $request->file('image'));
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }

        AuditService::log('created', $event);

        $totalDias = count($event->allDates());
        $msg = $totalDias > 1
            ? "Evento criado com {$totalDias} dias reservados no auditório!"
            : 'Evento criado com sucesso!';

        return redirect()->route('events.index')->with('success', $msg);
    }

    public function show(Event $event)
    {
        $event->load(['speaker', 'participants.attendances', 'booking']);
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $speakers = Speaker::orderBy('name')->get();
        return view('events.edit', compact('event', 'speakers'));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        try {
            $this->events->update($event, $request->all(), $request->file('image'));
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }

        AuditService::log('updated', $event);

        return redirect()->route('events.show', $event)->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(Event $event)
    {
        abort_unless(auth()->user()->roles->contains('name', 'admin'), 403);

        AuditService::log('deleted', $event);
        $this->events->delete($event);

        return redirect()->route('events.index')->with('success', 'Evento excluído com sucesso!');
    }

    public function storeParticipant(StoreEventParticipantRequest $request, Event $event)
    {
        if ($event->registrationsClosed()) {
            return back()->withErrors('Não é possível inscrever participantes: as inscrições deste evento estão encerradas.')->withInput();
        }

        $cpf = $request->cpf ? preg_replace('/[^0-9]/', '', $request->cpf) : null;

        $participant = $event->participants()->create([
            'name'     => $request->name,
            'email'    => $request->email,
            'cpf'      => $cpf,
            'whatsapp' => $request->whatsapp,
        ]);

        AuditService::log('created', $participant, null, "Inscreveu o participante {$participant->name} no evento {$event->title}");

        return redirect()->route('events.show', $event)->with('success', 'Participante inscrito com sucesso!');
    }

    public function updateParticipant(Request $request, Event $event, EventParticipant $participant)
    {
        abort_if($participant->event_id !== $event->id, 404);

        $request->validate([
            'name'     => 'required|string|max:255',
            'cpf'      => 'nullable|string|max:14',
            'whatsapp' => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:255',
        ]);

        $participant->update([
            'name'     => $request->name,
            'cpf'      => $request->cpf ? preg_replace('/[^0-9]/', '', $request->cpf) : null,
            'whatsapp' => $request->whatsapp,
            'email'    => $request->email,
        ]);

        AuditService::log('updated', $participant, null, "Atualizou o participante {$participant->name} no evento {$event->title}");

        return redirect()->route('events.show', $event)->with('success', 'Participante atualizado com sucesso!');
    }

    public function destroyParticipant(Event $event, EventParticipant $participant)
    {
        abort_if($participant->event_id !== $event->id, 404);

        AuditService::log('deleted', $participant, null, "Removeu o participante {$participant->name} do evento {$event->title}");
        $participant->delete();

        return redirect()->route('events.show', $event)->with('success', 'Participante removido com sucesso!');
    }

    public function toggleAttendance(Request $request, Event $event, EventParticipant $participant)
    {
        abort_if($participant->event_id !== $event->id, 404);

        $date = $request->input('date');
        abort_unless(in_array($date, $event->allDates(), true), 422, 'Data inválida para este evento.');

        $present   = $this->events->toggleAttendance($participant, $date);
        $dateLabel = \Carbon\Carbon::parse($date)->format('d/m/Y');
        $msg       = $present ? 'Presença confirmada em '.$dateLabel.'.' : 'Presença removida em '.$dateLabel.'.';
        $verb      = $present ? 'Confirmou' : 'Removeu';

        AuditService::log('updated', $participant, null, "{$verb} a presença de {$participant->name} em {$dateLabel} — evento {$event->title}");

        // A tela do evento marca presença via fetch, para não recarregar a
        // página e perder a posição da rolagem. Sem JS, o form cai no redirect.
        if ($request->wantsJson()) {
            $full = $participant->hasFullAttendance();

            return response()->json([
                'present'         => $present,
                'message'         => $msg,
                'full'            => $full,
                'certificate_url' => $full && $event->isCompleted()
                    ? route('events.certificate', [$event, $participant])
                    : null,
            ]);
        }

        return redirect()->route('events.show', $event)->with('success', $msg);
    }

    public function regenerateLink(Event $event)
    {
        $event->update(['share_token' => Event::uniqueShareToken()]);

        AuditService::log('updated', $event, null, "Regenerou o link de acesso do evento {$event->title}");

        return redirect()->route('events.show', $event)
            ->with('success', 'Link de acesso regenerado. O link anterior deixou de funcionar.');
    }

    public function updateStatus(UpdateEventStatusRequest $request, Event $event)
    {
        $this->events->changeStatus($event, $request->status);

        $label   = $event->status_label;
        $message = $event->isReopened()
            ? "Evento reaberto — voltou para \"{$label}\" e os certificados foram suspensos."
            : "Status do evento atualizado para \"{$label}\".";

        AuditService::log('updated', $event, null, $event->isReopened()
            ? "Reabriu o evento {$event->title}, que estava concluído pela data"
            : "Alterou o status do evento {$event->title} para \"{$label}\"");

        return redirect()->back()->with('success', $message);
    }

    public function certificate(Event $event, EventParticipant $participant, CertificateService $certificates)
    {
        abort_unless($event->isCompleted(), 403, 'Certificados disponíveis apenas para eventos concluídos.');
        abort_if($participant->event_id !== $event->id, 404);
        abort_unless($participant->hasFullAttendance(), 403, 'Certificado disponível apenas para quem teve presença em todos os dias do evento.');

        // stream = exibe o PDF inline no navegador (visualizar, não baixar).
        return $certificates->pdf($event, $participant)->stream($certificates->filename($participant->name));
    }

    /**
     * Certificado do palestrante do evento: mesmo layout, assinado só pelo
     * diretor e com o texto de quem ministrou. Sem presença a conferir.
     */
    public function speakerCertificate(Event $event, CertificateService $certificates)
    {
        abort_unless($event->isCompleted(), 403, 'Certificados disponíveis apenas para eventos concluídos.');
        abort_unless($event->speaker, 404, 'Este evento não tem palestrante cadastrado.');

        return $certificates->speakerPdf($event, $event->speaker)
            ->stream($certificates->filename($event->speaker->name));
    }

    public function pdf(Event $event)
    {
        $event->load(['speaker', 'participants', 'booking']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('events.pdf', compact('event'));
        return $pdf->download('ata-evento-'.\Illuminate\Support\Str::slug($event->title).'.pdf');
    }
}
