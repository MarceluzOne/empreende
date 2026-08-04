<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Speaker;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Geração do certificado em PDF (compartilhada entre o acesso do funcionário,
 * do candidato e a consulta pública por CPF).
 *
 * São dois destinatários possíveis, sobre o mesmo layout: o participante, que
 * "participou do curso", e o palestrante, que "ministrou o curso" — este último
 * assinado somente pelo diretor.
 */
class CertificateService
{
    /** Memo do diretor: consultado por assinatura e por certificado. */
    private ?Speaker $director = null;
    private bool $directorLoaded = false;

    public function pdf(Event $event, EventParticipant $participant)
    {
        return $this->render($event, [
            'name'       => $participant->name,
            'cpf'        => $participant->cpf,
            'seed'       => (string) $participant->id,
            'action'     => 'PARTICIPOU DO',
            'roleClause' => null,
            'signatures' => $this->signatures($event),
        ]);
    }

    /**
     * Certificado do palestrante. Só o diretor assina — o palestrante não
     * assina o próprio certificado — e o texto registra que ele ministrou.
     */
    public function speakerPdf(Event $event, Speaker $speaker)
    {
        return $this->render($event, [
            'name'       => $speaker->name,
            'cpf'        => $speaker->cpf,
            // Um palestrante ministra vários eventos: o código precisa do par.
            'seed'       => $speaker->id.'|'.$event->id,
            'action'     => 'MINISTROU O',
            'roleClause' => 'NA CONDIÇÃO DE '.mb_strtoupper(config('certificate.speaker_role'), 'UTF-8'),
            'signatures' => [$this->directorSignature()],
        ]);
    }

    /**
     * Certificados de um CPF, como participante e como palestrante, em itens
     * normalizados para a tela pública de consulta.
     *
     * @return Collection<int, array>
     */
    public function findByCpf(string $cpf): Collection
    {
        $digits = preg_replace('/\D/', '', $cpf);

        return $this->participantResults($digits)
            ->concat($this->speakerResults($digits))
            ->sortByDesc(fn (array $item) => max($item['event']->allDates()))
            ->values();
    }

    public function filename(string $name): string
    {
        return 'certificado-'.Str::slug($name).'.pdf';
    }

    /**
     * Participações do CPF. Eventos removidos são descartados: sem o evento não
     * há o que certificar.
     */
    private function participantResults(string $digits): Collection
    {
        return EventParticipant::with(['event.speaker', 'attendances'])
            ->where('cpf', $digits)
            ->get()
            ->filter(fn (EventParticipant $p) => $p->event !== null)
            ->map(function (EventParticipant $p) {
                $completed = $p->event->isCompleted();

                return [
                    'kind'        => 'participant',
                    'role'        => 'Participante',
                    'event'       => $p->event,
                    'participant' => $p,
                    'available'   => $completed && $p->hasFullAttendance(),
                    'blocked'     => !$completed ? 'waiting' : ($p->hasFullAttendance() ? null : 'incomplete'),
                ];
            });
    }

    /**
     * Eventos ministrados pelo CPF. Não há presença a conferir: basta o evento
     * estar concluído.
     */
    private function speakerResults(string $digits): Collection
    {
        return Speaker::with('events.speaker')
            ->where('cpf', $digits)
            ->get()
            ->flatMap(fn (Speaker $speaker) => $speaker->events->map(function (Event $event) use ($speaker) {
                $completed = $event->isCompleted();

                return [
                    'kind'      => 'speaker',
                    'role'      => 'Palestrante',
                    'event'     => $event,
                    'speaker'   => $speaker,
                    'available' => $completed,
                    'blocked'   => $completed ? null : 'waiting',
                ];
            }));
    }

    private function render(Event $event, array $data)
    {
        $event->loadMissing('speaker');

        $dates     = $event->allDates();
        $startDate = Carbon::parse($dates[0])->format('d/m/Y');
        $lastDate  = Carbon::parse(end($dates));
        $endDate   = $lastDate->format('d/m/Y');

        // Código de validação determinístico a partir do destinatário.
        $validationCode = sprintf(
            'EV-%s-%06d',
            $lastDate->format('Y'),
            crc32($data['seed']) % 1000000
        );

        return Pdf::loadView('events.certificate', [
            'event'          => $event,
            'recipientName'  => $data['name'],
            'cpfFormatted'   => $this->formatCpf($data['cpf']),
            'action'         => $data['action'],
            'roleClause'     => $data['roleClause'],
            'signatures'     => $data['signatures'],
            'courseLabel'    => $this->courseLabel($event),
            'startDate'      => $startDate,
            'endDate'        => $endDate,
            'totalHours'     => $event->totalHours(),
            'validationCode' => $validationCode,
        ])->setPaper('a4', 'landscape');
    }

    private function formatCpf(?string $cpf): ?string
    {
        return $cpf
            ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf)
            : null;
    }

    /**
     * Blocos de assinatura do certificado de participação. O diretor assina
     * todos; o palestrante do evento ganha um segundo bloco, exceto quando o
     * curso é ministrado pelo próprio diretor — aí a assinatura dele é a única,
     * sem repetir o mesmo nome nos dois lados.
     */
    private function signatures(Event $event): array
    {
        $director   = $this->director();
        $speaker    = $event->speaker;
        $signatures = [$this->directorSignature()];

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
     * Sem diretor cadastrado, cai para o nome de config/certificate.php: o
     * certificado continua saindo, só sem a assinatura digitalizada.
     */
    private function directorSignature(): array
    {
        $director = $this->director();

        return [
            'name'  => mb_strtoupper($director->name ?? config('certificate.director.name'), 'UTF-8'),
            'role'  => mb_strtoupper(config('certificate.director.role'), 'UTF-8'),
            'image' => $this->signatureDataUri($director),
        ];
    }

    private function director(): ?Speaker
    {
        if (!$this->directorLoaded) {
            $this->director       = Speaker::director()->first();
            $this->directorLoaded = true;
        }

        return $this->director;
    }

    /**
     * Título do evento em caixa alta, prefixado por "CURSO" quando ele próprio
     * ainda não começa com a palavra (evita "CURSO CURSO DE VENDAS").
     */
    private function courseLabel(Event $event): string
    {
        $title = mb_strtoupper(trim($event->title), 'UTF-8');

        return Str::startsWith($title, 'CURSO') ? $title : 'CURSO '.$title;
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
}
