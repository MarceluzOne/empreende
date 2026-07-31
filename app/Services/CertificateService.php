<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Speaker;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
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
        $sealYear   = $lastDate->format('Y');

        // Código de validação determinístico a partir do id do participante.
        $validationCode = sprintf(
            'EV-%s-%06d',
            $sealYear,
            crc32((string) $participant->id) % 1000000
        );

        $cpfFormatted = $participant->cpf
            ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $participant->cpf)
            : null;

        $signatures  = $this->signatures($event);
        $courseLabel = $this->courseLabel($event);

        return Pdf::loadView('events.certificate', compact(
            'event', 'participant', 'startDate', 'endDate', 'totalHours',
            'cpfFormatted', 'validationCode', 'signatures', 'courseLabel'
        ))->setPaper('a4', 'landscape');
    }

    /**
     * Blocos de assinatura do certificado. O diretor (palestrante marcado com
     * is_director) assina todos os certificados; o palestrante do evento ganha
     * um segundo bloco, exceto quando o curso é ministrado pelo próprio diretor
     * — aí a assinatura dele é a única, sem repetir o mesmo nome nos dois lados.
     */
    private function signatures(Event $event): array
    {
        $director = Speaker::director()->first();
        $speaker  = $event->speaker;

        // Sem diretor cadastrado, cai para o nome de config/certificate.php:
        // o certificado continua saindo, só sem a assinatura digitalizada.
        $signatures = [[
            'name'  => mb_strtoupper($director->name ?? config('certificate.director.name'), 'UTF-8'),
            'role'  => mb_strtoupper(config('certificate.director.role'), 'UTF-8'),
            'image' => $this->signatureDataUri($director),
        ]];

        if ($speaker && !($director && $speaker->is($director))) {
            $signatures[] = [
                'name'  => mb_strtoupper($speaker->name, 'UTF-8'),
                'role'  => mb_strtoupper(config('certificate.speaker_role'), 'UTF-8'),
                'image' => $this->signatureDataUri($speaker),
            ];
        }

        return $signatures;
    }

    /**
     * Título do evento em caixa alta, prefixado por "CURSO" quando ele próprio
     * ainda não começa com a palavra (evita "CURSO CURSO DE VENDAS").
     */
    private function courseLabel(Event $event): string
    {
        $title = mb_strtoupper(trim($event->title), 'UTF-8');

        return Str::startsWith($title, 'CURSO') ? $title : 'CURSO ' . $title;
    }

    /**
     * Lê a assinatura do palestrante e devolve como data URI (base64), ou null.
     */
    private function signatureDataUri(?Speaker $speaker): ?string
    {
        if (!$speaker || !$speaker->signature_path || !Storage::disk('public')->exists($speaker->signature_path)) {
            return null;
        }

        $content = Storage::disk('public')->get($speaker->signature_path);
        $mime    = Storage::disk('public')->mimeType($speaker->signature_path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode($content);
    }

    public function filename(EventParticipant $participant): string
    {
        return 'certificado-' . Str::slug($participant->name) . '.pdf';
    }
}
