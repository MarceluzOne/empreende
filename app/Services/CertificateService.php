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
        $totalHours = $event->totalHours();

        $cpfFormatted = $participant->cpf
            ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $participant->cpf)
            : null;

        return Pdf::loadView('events.certificate', compact(
            'event', 'participant', 'startDate', 'endDate', 'totalHours', 'cpfFormatted'
        ))->setPaper('a4', 'landscape');
    }

    public function filename(EventParticipant $participant): string
    {
        return 'certificado-' . Str::slug($participant->name) . '.pdf';
    }
}
