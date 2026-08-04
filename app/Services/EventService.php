<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Event;
use App\Models\EventParticipant;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Regra de negócio dos eventos: cria/atualiza/exclui o evento e as reservas de
 * auditório associadas (uma por dia), além do processamento da imagem.
 *
 * O acesso a dados é feito direto no Eloquent, seguindo o padrão dos demais
 * Services do projeto (ex.: AttendanceService).
 */
class EventService
{
    public function __construct(private BookingService $bookingService) {}

    /**
     * @throws \Exception em caso de conflito de reserva no auditório
     */
    public function create(array $data, ?UploadedFile $image = null): Event
    {
        $bookings  = $this->createBookings($data);
        $imagePath = $image ? $this->storeImage($image) : null;

        $event = Event::create($this->eventAttributes($data, $bookings) + [
            'image_path' => $imagePath,
        ]);

        $event->bookings()->attach($bookings->pluck('id'));

        return $event;
    }

    /**
     * @throws \Exception em caso de conflito de reserva no auditório
     */
    public function update(Event $event, array $data, ?UploadedFile $image = null): Event
    {
        // Remove as reservas antigas antes de recriar a agenda do evento.
        $event->bookings()->each(fn ($b) => $b->delete());
        $event->booking?->delete();
        $event->bookings()->detach();

        $bookings   = $this->createBookings($data);
        $attributes = $this->eventAttributes($data, $bookings);

        if ($image) {
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            $attributes['image_path'] = $this->storeImage($image);
        }

        $event->update($attributes);
        $event->bookings()->attach($bookings->pluck('id'));

        return $event;
    }

    /**
     * Aplica o status escolhido pelo admin.
     *
     * Voltar para "Em andamento" um evento que já passou da data é uma
     * reabertura: sem gravar o marcador, a conclusão automática assumiria de
     * novo no próximo carregamento da tela. Concluir ou cancelar desfaz a
     * reabertura anterior.
     */
    public function changeStatus(Event $event, string $status): Event
    {
        $reopening = $status === 'active' && $event->hasEnded();

        $event->update([
            'status'      => $status,
            'reopened_at' => $reopening ? now() : null,
        ]);

        return $event;
    }

    /**
     * Alterna a presença do participante em uma data: a existência da linha em
     * event_attendances significa "presente naquele dia".
     *
     * @return bool presença DEPOIS da alternância
     */
    public function toggleAttendance(EventParticipant $participant, string $date): bool
    {
        $existing = $participant->attendances()->whereDate('event_date', $date)->first();

        if ($existing) {
            $existing->delete();
        } else {
            $participant->attendances()->create(['event_date' => $date, 'checked_in_at' => now()]);
        }

        // A relação já carregada ficaria desatualizada para hasFullAttendance().
        $participant->unsetRelation('attendances');

        return !$existing;
    }

    /**
     * Inscreve um participante no evento (usado pela inscrição pública e portal).
     * As regras de bloqueio (encerrado, lotado, CPF duplicado) são validadas
     * antes, no Form Request.
     */
    public function registerParticipant(Event $event, array $data): EventParticipant
    {
        return $event->participants()->create([
            'name'     => $data['name'],
            'email'    => $data['email'] ?? null,
            'cpf'      => !empty($data['cpf']) ? preg_replace('/[^0-9]/', '', $data['cpf']) : null,
            'whatsapp' => $data['whatsapp'] ?? null,
        ]);
    }

    public function delete(Event $event): void
    {
        $event->bookings()->each(fn ($b) => $b->delete());
        if ($event->booking && !$event->bookings->contains($event->booking)) {
            $event->booking?->delete();
        }
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }
        $event->delete();
    }

    /**
     * Cria as reservas de auditório do evento e devolve apenas as recém-criadas,
     * ordenadas por data.
     *
     * @throws \Exception
     */
    private function createBookings(array $data): \Illuminate\Support\Collection
    {
        $type      = $data['type'] ?? 'single';
        $beforeIds = Booking::where('resource_type', 'auditorio')->pluck('id');

        $this->bookingService->createBookings([
            'resource_type'    => 'auditorio',
            'responsible_name' => $data['title'],
            'type'             => $type,
            'start_time'       => $data['start_time'],
            'end_time'         => $data['end_time'],
            'single_date'      => $data['single_date'] ?? null,
            'start_date'       => $data['start_date'] ?? null,
            'end_date_period'  => $data['end_date_period'] ?? null,
            'selected_dates'   => $data['selected_dates'] ?? [],
            'guests_count'     => $data['max_capacity'],
            'reason'           => 'evento',
            'observation'      => 'Reserva automática — Evento: '.$data['title'],
        ]);

        return Booking::where('resource_type', 'auditorio')
            ->whereNotIn('id', $beforeIds)
            ->orderBy('booking_date')
            ->get();
    }

    /**
     * Monta os atributos do evento a partir do request e das reservas criadas.
     */
    private function eventAttributes(array $data, \Illuminate\Support\Collection $bookings): array
    {
        $startTime = $data['start_time'];
        $endTime   = $data['end_time'];
        $duration  = (int) ($data['duration_minutes'] ?? 0)
            ?: Carbon::createFromTimeString($startTime)->diffInMinutes(Carbon::createFromTimeString($endTime));

        $firstBooking = $bookings->first();
        $firstDate    = $firstBooking->booking_date->format('Y-m-d');
        $extraDates   = $bookings->skip(1)->map(fn ($b) => $b->booking_date->format('Y-m-d'))->values()->all();

        return [
            'title'            => $data['title'],
            'date'             => $firstDate,
            'start_time'       => $startTime,
            'duration_minutes' => $duration,
            'max_capacity'     => $data['max_capacity'],
            'speaker_id'       => $data['speaker_id'],
            'booking_id'       => $firstBooking->id,
            'type'             => $data['type'] ?? 'single',
            'extra_dates'      => count($extraDates) ? $extraDates : null,
            'visibility'       => $data['visibility'] ?? 'public',
        ];
    }

    /**
     * Recorta a imagem em quadrado (1024px) e salva como webp no disco público.
     */
    private function storeImage(UploadedFile $file): string
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
        $x    = (int) (($w - $side) / 2);
        $y    = (int) (($h - $side) / 2);

        $dst = imagecreatetruecolor($size, $size);
        imagecopyresampled($dst, $src, 0, 0, $x, $y, $size, $size, $side, $side);
        imagedestroy($src);

        $filename = 'events/'.Str::uuid().'.webp';
        Storage::disk('public')->makeDirectory('events');
        imagewebp($dst, Storage::disk('public')->path($filename), 82);
        imagedestroy($dst);

        return $filename;
    }
}
