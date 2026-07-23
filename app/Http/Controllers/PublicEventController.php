<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicEventParticipantRequest;
use App\Models\Event;
use App\Services\EventService;

/**
 * Inscrição pública em eventos (sem login), a partir da página /cursos.
 */
class PublicEventController extends Controller
{
    public function __construct(private EventService $events) {}

    public function create(Event $event)
    {
        $event->load('speaker');
        return view('events.public-register', compact('event'));
    }

    public function store(StorePublicEventParticipantRequest $request, Event $event)
    {
        $participant = $this->events->registerParticipant($event, $request->validated());

        // Retorna à página do evento com estado de sucesso (tela de confirmação).
        return redirect()->route('public.events.register', $event->share_token)
            ->with('registered', [
                'name'  => $participant->name,
                'phone' => preg_replace('/\D/', '', (string) $participant->whatsapp),
            ]);
    }
}
