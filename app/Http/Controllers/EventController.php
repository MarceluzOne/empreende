<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventParticipantRequest;
use App\Models\Booking;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Speaker;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::with(['speaker', 'participants'])
            ->when($request->search, fn($q) => $q->where('title', 'ilike', '%'.$request->search.'%'))
            ->when($request->date, fn($q) => $q->whereDate('date', $request->date))
            ->orderBy('date', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('events.index', compact('events'));
    }

    public function create()
    {
        $speakers = Speaker::orderBy('name')->get();
        return view('events.create', compact('speakers'));
    }

    public function store(Request $request, BookingService $bookingService)
    {
        $type = $request->input('type', 'single');

        $request->validate([
            'title'            => 'required|string|max:255',
            'type'             => 'required|in:single,consecutive,alternated',
            'start_time'       => 'required',
            'end_time'         => 'required|after:start_time',
            'max_capacity'     => 'required|integer|min:1',
            'speaker_id'       => 'required|exists:speakers,id',
            'single_date'      => 'required_if:type,single|nullable|date',
            'start_date'       => 'required_if:type,consecutive|nullable|date',
            'end_date_period'  => 'required_if:type,consecutive|nullable|date|after_or_equal:start_date',
            'selected_dates'   => 'required_if:type,alternated|nullable|array',
            'selected_dates.*' => 'date',
        ]);

        $startTime = $request->input('start_time');
        $endTime   = $request->input('end_time');
        $duration  = (int) $request->input('duration_minutes') ?: Carbon::createFromTimeString($startTime)->diffInMinutes(Carbon::createFromTimeString($endTime));

        $beforeIds = Booking::where('resource_type', 'auditorio')->pluck('id');

        try {
            $bookingService->createBookings([
                'resource_type'    => 'auditorio',
                'responsible_name' => $request->input('title'),
                'type'             => $type,
                'start_time'       => $startTime,
                'end_time'         => $endTime,
                'single_date'      => $request->input('single_date'),
                'start_date'       => $request->input('start_date'),
                'end_date_period'  => $request->input('end_date_period'),
                'selected_dates'   => $request->input('selected_dates', []),
                'guests_count'     => $request->input('max_capacity'),
                'observation'      => 'Reserva automática — Evento: '.$request->input('title'),
            ]);
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }

        $createdBookings = Booking::where('resource_type', 'auditorio')
            ->whereNotIn('id', $beforeIds)
            ->orderBy('booking_date')
            ->get();

        $firstBooking = $createdBookings->first();
        $firstDate    = $firstBooking->booking_date->format('Y-m-d');

        $extraDates = $createdBookings->skip(1)->map(fn($b) => $b->booking_date->format('Y-m-d'))->values()->all();

        $event = Event::create([
            'title'            => $request->input('title'),
            'date'             => $firstDate,
            'start_time'       => $startTime,
            'duration_minutes' => $duration,
            'max_capacity'     => $request->input('max_capacity'),
            'speaker_id'       => $request->input('speaker_id'),
            'booking_id'       => $firstBooking->id,
            'type'             => $type,
            'extra_dates'      => count($extraDates) ? $extraDates : null,
        ]);

        $event->bookings()->attach($createdBookings->pluck('id'));

        $totalDias = $createdBookings->count();
        $msg = $totalDias > 1
            ? "Evento criado com {$totalDias} dias reservados no auditório!"
            : 'Evento criado com sucesso!';

        return redirect()->route('events.index')->with('success', $msg);
    }

    public function show(Event $event)
    {
        $event->load(['speaker', 'participants', 'booking']);
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $speakers = Speaker::orderBy('name')->get();
        return view('events.edit', compact('event', 'speakers'));
    }

    public function update(Request $request, Event $event, BookingService $bookingService)
    {
        $type = $request->input('type', 'single');

        $request->validate([
            'title'            => 'required|string|max:255',
            'type'             => 'required|in:single,consecutive,alternated',
            'start_time'       => 'required',
            'end_time'         => 'required|after:start_time',
            'max_capacity'     => 'required|integer|min:'.$event->participants()->count(),
            'speaker_id'       => 'required|exists:speakers,id',
            'single_date'      => 'required_if:type,single|nullable|date',
            'start_date'       => 'required_if:type,consecutive|nullable|date',
            'end_date_period'  => 'required_if:type,consecutive|nullable|date|after_or_equal:start_date',
            'selected_dates'   => 'required_if:type,alternated|nullable|array',
            'selected_dates.*' => 'date',
        ]);

        $startTime = $request->input('start_time');
        $endTime   = $request->input('end_time');
        $duration  = (int) $request->input('duration_minutes') ?: Carbon::createFromTimeString($startTime)->diffInMinutes(Carbon::createFromTimeString($endTime));

        // Apaga todos os bookings antigos do evento
        $event->bookings()->each(fn($b) => $b->delete());
        $event->booking?->delete();
        $event->bookings()->detach();

        $beforeIds = Booking::where('resource_type', 'auditorio')->pluck('id');

        try {
            $bookingService->createBookings([
                'resource_type'    => 'auditorio',
                'responsible_name' => $request->input('title'),
                'type'             => $type,
                'start_time'       => $startTime,
                'end_time'         => $endTime,
                'single_date'      => $request->input('single_date'),
                'start_date'       => $request->input('start_date'),
                'end_date_period'  => $request->input('end_date_period'),
                'selected_dates'   => $request->input('selected_dates', []),
                'guests_count'     => $request->input('max_capacity'),
                'observation'      => 'Reserva automática — Evento: '.$request->input('title'),
            ]);
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }

        $createdBookings = Booking::where('resource_type', 'auditorio')
            ->whereNotIn('id', $beforeIds)
            ->orderBy('booking_date')
            ->get();

        $firstBooking = $createdBookings->first();
        $firstDate    = $firstBooking->booking_date->format('Y-m-d');
        $extraDates   = $createdBookings->skip(1)->map(fn($b) => $b->booking_date->format('Y-m-d'))->values()->all();

        $event->update([
            'title'            => $request->input('title'),
            'date'             => $firstDate,
            'start_time'       => $startTime,
            'duration_minutes' => $duration,
            'max_capacity'     => $request->input('max_capacity'),
            'speaker_id'       => $request->input('speaker_id'),
            'booking_id'       => $firstBooking->id,
            'type'             => $type,
            'extra_dates'      => count($extraDates) ? $extraDates : null,
        ]);

        $event->bookings()->attach($createdBookings->pluck('id'));

        return redirect()->route('events.show', $event)->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(Event $event)
    {
        abort_unless(auth()->user()->roles->contains('name', 'admin'), 403);

        $event->bookings()->each(fn($b) => $b->delete());
        if ($event->booking && !$event->bookings->contains($event->booking)) {
            $event->booking?->delete();
        }
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evento excluído com sucesso!');
    }

    public function storeParticipant(StoreEventParticipantRequest $request, Event $event)
    {
        $cpf = $request->cpf ? preg_replace('/[^0-9]/', '', $request->cpf) : null;

        $event->participants()->create([
            'name'     => $request->name,
            'email'    => $request->email,
            'cpf'      => $cpf,
            'whatsapp' => $request->whatsapp,
        ]);

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

        return redirect()->route('events.show', $event)->with('success', 'Participante atualizado com sucesso!');
    }

    public function destroyParticipant(Event $event, EventParticipant $participant)
    {
        abort_if($participant->event_id !== $event->id, 404);
        $participant->delete();

        return redirect()->route('events.show', $event)->with('success', 'Participante removido com sucesso!');
    }

    public function pdf(Event $event)
    {
        $event->load(['speaker', 'participants', 'booking']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('events.pdf', compact('event'));
        return $pdf->download('ata-evento-'.$event->id.'.pdf');
    }
}
