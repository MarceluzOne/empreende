<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventParticipant;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Geração do certificado de participação em PDF (compartilhada entre o acesso
 * do funcionário e do candidato).
 */
class CertificateService
{
    public function pdf(Event $event, EventParticipant $participant)
    {
        $event->loadMissing('speaker');

        $dates      = $event->allDates();
        $startDate  = Carbon::parse($dates[0])->format('d/m/Y');
        $endDate    = Carbon::parse(end($dates))->format('d/m/Y');
        $lastDate   = Carbon::parse(end($dates));
        $totalHours = $event->totalHours();

        // Texto das datas de realização como intervalo (não lista todos os dias).
        $datesText = count($dates) === 1
            ? 'realizado no dia '.$event->datesLabel()
            : 'realizado no período '.$event->datesLabel();

        // Data de conclusão por extenso (ex.: "12 de junho de 2026").
        $concludedLong = $lastDate->locale('pt_BR')->isoFormat('D [de] MMMM [de] YYYY');
        $sealYear      = $lastDate->format('Y');

        // Código de validação determinístico a partir do id do participante.
        $validationCode = sprintf(
            'EV-%s-%06d',
            $sealYear,
            crc32((string) $participant->id) % 1000000
        );

        $cpfFormatted = $participant->cpf
            ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $participant->cpf)
            : null;

        return Pdf::loadView('events.certificate', compact(
            'event', 'participant', 'startDate', 'endDate', 'totalHours',
            'cpfFormatted', 'concludedLong', 'sealYear', 'validationCode', 'datesText'
        ))->setPaper('a4', 'landscape');
    }

    public function filename(EventParticipant $participant): string
    {
        return 'certificado-' . Str::slug($participant->name) . '.pdf';
    }
}
