<?php

namespace App\Http\Controllers;

use App\Models\EventParticipant;
use App\Rules\Cpf;
use App\Services\CertificateService;
use Illuminate\Http\Request;

/**
 * Emissão pública de certificados: participantes sem conta baixam seus
 * certificados informando o CPF. Só libera para eventos concluídos e com
 * presença completa.
 */
class PublicCertificateController extends Controller
{
    public function index(Request $request)
    {
        $cpf     = $request->query('cpf');
        $results = null;
        $error   = null;

        if ($cpf !== null && $cpf !== '') {
            if ((new Cpf)->passes('cpf', $cpf)) {
                $digits  = preg_replace('/\D/', '', $cpf);
                $results = EventParticipant::with(['event.speaker', 'attendances'])
                    ->where('cpf', $digits)
                    ->get()
                    ->sortByDesc(fn ($p) => optional($p->event)->date)
                    ->values();
            } else {
                $error = 'Informe um CPF válido.';
            }
        }

        return view('certificates.index', compact('results', 'cpf', 'error'));
    }

    public function download(EventParticipant $participant, CertificateService $certificates)
    {
        $event = $participant->event;

        abort_if(!$event || !$event->isCompleted(), 403, 'Certificado disponível apenas para eventos concluídos.');
        abort_unless($participant->hasFullAttendance(), 403, 'Certificado disponível apenas para quem teve presença em todos os dias do evento.');

        return $certificates->pdf($event, $participant)->download($certificates->filename($participant));
    }
}
