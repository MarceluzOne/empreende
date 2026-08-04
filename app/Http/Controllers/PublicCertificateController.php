<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Rules\Cpf;
use App\Services\CertificateService;
use Illuminate\Http\Request;

/**
 * Emissão pública de certificados: participantes e palestrantes sem conta
 * baixam seus certificados informando o CPF. Só libera para eventos concluídos
 * e, no caso do participante, com presença completa.
 */
class PublicCertificateController extends Controller
{
    public function __construct(private CertificateService $certificates) {}

    public function index(Request $request)
    {
        $cpf     = $request->query('cpf');
        $results = null;
        $error   = null;

        if ($cpf !== null && $cpf !== '') {
            if ((new Cpf)->passes('cpf', $cpf)) {
                $results = $this->certificates->findByCpf($cpf);
            } else {
                $error = 'Informe um CPF válido.';
            }
        }

        return view('certificates.index', compact('results', 'cpf', 'error'));
    }

    public function download(EventParticipant $participant)
    {
        $event = $participant->event;

        abort_if(!$event || !$event->isCompleted(), 403, 'Certificado disponível apenas para eventos concluídos.');
        abort_unless($participant->hasFullAttendance(), 403, 'Certificado disponível apenas para quem teve presença em todos os dias do evento.');

        return $this->certificates->pdf($event, $participant)
            ->download($this->certificates->filename($participant->name));
    }

    /**
     * Certificado do palestrante do evento. Não há presença a conferir — quem
     * ministrou o curso é o próprio palestrante cadastrado no evento.
     */
    public function downloadSpeaker(Event $event)
    {
        abort_unless($event->isCompleted(), 403, 'Certificado disponível apenas para eventos concluídos.');
        abort_unless($event->speaker, 404, 'Este evento não tem palestrante cadastrado.');

        return $this->certificates->speakerPdf($event, $event->speaker)
            ->download($this->certificates->filename($event->speaker->name));
    }
}
