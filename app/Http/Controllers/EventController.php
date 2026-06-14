<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventParticipantRequest;
use App\Models\Booking;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Speaker;
use App\Services\AuditService;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::with(['speaker', 'participants'])
            ->when($request->search, fn($q) => $q->where('title', 'like', '%'.$request->search.'%'))
            ->when($request->date, fn($q) => $q->whereDate('date', $request->date))
            ->orderBy('date', 'asc')
            ->paginate(10)
            ->withQueryString();

        $speakers = Speaker::orderBy('name')->get();

        return view('events.index', compact('events', 'speakers'));
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
                'reason'           => 'evento',
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

        $imagePath = $request->hasFile('image')
            ? $this->storeEventImage($request->file('image'))
            : null;

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
            'image_path'       => $imagePath,
        ]);

        $event->bookings()->attach($createdBookings->pluck('id'));
        AuditService::log('created', $event);

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
                'reason'           => 'evento',
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

        $updateData = [
            'title'            => $request->input('title'),
            'date'             => $firstDate,
            'start_time'       => $startTime,
            'duration_minutes' => $duration,
            'max_capacity'     => $request->input('max_capacity'),
            'speaker_id'       => $request->input('speaker_id'),
            'booking_id'       => $firstBooking->id,
            'type'             => $type,
            'extra_dates'      => count($extraDates) ? $extraDates : null,
        ];

        if ($request->hasFile('image')) {
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            $updateData['image_path'] = $this->storeEventImage($request->file('image'));
        }

        $event->update($updateData);

        $event->bookings()->attach($createdBookings->pluck('id'));
        AuditService::log('updated', $event);

        return redirect()->route('events.show', $event)->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(Event $event)
    {
        abort_unless(auth()->user()->roles->contains('name', 'admin'), 403);

        AuditService::log('deleted', $event);
        $event->bookings()->each(fn($b) => $b->delete());
        if ($event->booking && !$event->bookings->contains($event->booking)) {
            $event->booking?->delete();
        }
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evento excluído com sucesso!');
    }

    public function storeParticipant(StoreEventParticipantRequest $request, Event $event)
    {
        if ($event->hasEnded()) {
            return back()->withErrors('Não é possível inscrever participantes: o evento já foi encerrado.')->withInput();
        }

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

    public function updateStatus(Request $request, Event $event)
    {
        $request->validate(['status' => 'required|in:active,completed,cancelled']);
        $event->update(['status' => $request->status]);

        $label = $event->fresh()->status_label;
        return redirect()->back()->with('success', "Status do evento atualizado para \"{$label}\".");
    }

    public function certificate(Event $event, EventParticipant $participant)
    {
        abort_if($event->status !== 'completed', 403, 'Certificados disponíveis apenas para eventos concluídos.');
        abort_if($participant->event_id !== $event->id, 404);

        $event->load('speaker');
        $dates      = $event->allDates();
        $startDate  = \Carbon\Carbon::parse($dates[0])->format('d/m/Y');
        $endDate    = \Carbon\Carbon::parse(end($dates))->format('d/m/Y');
        $totalHours = $event->totalHours();

        $cpfFormatted = $participant->cpf
            ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $participant->cpf)
            : null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('events.certificate', compact(
            'event', 'participant', 'startDate', 'endDate', 'totalHours', 'cpfFormatted'
        ))->setPaper('a4', 'landscape');

        $slug = \Illuminate\Support\Str::slug($participant->name);
        return $pdf->download("certificado-{$slug}.pdf");
    }

    public function pdf(Event $event)
    {
        $event->load(['speaker', 'participants', 'booking']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('events.pdf', compact('event'));
        return $pdf->download('ata-evento-'.$event->id.'.pdf');
    }

    private function storeEventImage(\Illuminate\Http\UploadedFile $file): string
    {
        $size = 1024;
        $mime = $file->getMimeType();

        $src = match (true) {
            str_contains($mime, 'png')  => imagecreatefrompng($file->getRealPath()),
            str_contains($mime, 'webp') => imagecreatefromwebp($file->getRealPath()),
            default                     => imagecreatefromjpeg($file->getRealPath()),
        };

        $w    = imagesx($src);
        $h    = imagesy($src);
        $side = min($w, $h);
        $x    = (int)(($w - $side) / 2);
        $y    = (int)(($h - $side) / 2);

        $dst = imagecreatetruecolor($size, $size);
        imagecopyresampled($dst, $src, 0, 0, $x, $y, $size, $size, $side, $side);
        imagedestroy($src);

        $filename = 'events/' . \Illuminate\Support\Str::uuid() . '.webp';
        Storage::disk('public')->makeDirectory('events');
        imagewebp($dst, Storage::disk('public')->path($filename), 82);
        imagedestroy($dst);

        return $filename;
    }
}
