<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ata do Evento — {{ $event->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; padding: 40px; }
        .header { border-bottom: 3px solid #1d4ed8; padding-bottom: 16px; margin-bottom: 24px; }
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .system-name { font-size: 18px; font-weight: bold; color: #1d4ed8; }
        .system-sub { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .doc-title { font-size: 11px; text-align: right; color: #6b7280; }
        h1 { font-size: 20px; font-weight: bold; color: #111827; margin: 16px 0 4px; }
        .event-date { font-size: 13px; color: #4b5563; margin-bottom: 24px; }
        .section { margin-bottom: 24px; }
        .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; margin-bottom: 12px; }
        .info-grid { display: table; width: 100%; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; font-weight: bold; color: #374151; width: 160px; padding: 4px 0; }
        .info-value { display: table-cell; color: #4b5563; padding: 4px 0; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #1d4ed8; color: white; padding: 8px 12px; text-align: left; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: bold; }
        .badge-full { background: #fee2e2; color: #dc2626; }
        .badge-ok { background: #d1fae5; color: #059669; }
        .footer { margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
        .signature-area { margin-top: 40px; }
        .signature-line { border-top: 1px solid #374151; width: 280px; margin-top: 40px; padding-top: 6px; font-size: 11px; color: #6b7280; }
        .page-info { font-size: 10px; color: #9ca3af; text-align: right; margin-top: 20px; }
        .auditorio-badge { background: #dbeafe; color: #1d4ed8; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>

    {{-- Cabeçalho --}}
    <div class="header">
        <div class="header-top">
            <div>
                <div class="system-name">Empreende Vitória</div>
                <div class="system-sub">Vitória de Santo Antão — PE</div>
            </div>
            <div class="doc-title">
                ATA DE EVENTO<br>
                Documento gerado em {{ now()->format('d/m/Y \à\s H:i') }}
            </div>
        </div>
    </div>

    {{-- Título do evento --}}
    <h1>{{ $event->title }}</h1>
    @php $allDates = $event->allDates(); @endphp
    <p class="event-date">
        @if(count($allDates) > 1)
            {{ \Carbon\Carbon::parse($allDates[0])->format('d/m/Y') }} a {{ \Carbon\Carbon::parse(end($allDates))->format('d/m/Y') }}
            ({{ count($allDates) }} dias)
        @else
            {{ $event->date->format('d/m/Y') }}
        @endif
        — {{ substr($event->start_time, 0, 5) }} até {{ $event->endTime() }}
        ({{ $event->duration_minutes }} minutos)
        &nbsp;&nbsp;<span class="auditorio-badge">Auditório</span>
    </p>

    {{-- Palestrante --}}
    <div class="section">
        <div class="section-title">Palestrante</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nome:</div>
                <div class="info-value">{{ $event->speaker->name }}</div>
            </div>
        </div>
    </div>

    {{-- Auditório --}}
    @if($event->booking)
    <div class="section">
        <div class="section-title">Reserva do Auditório</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Data e Hora Início:</div>
                <div class="info-value">{{ $event->booking->booking_date->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Hora Término:</div>
                <div class="info-value">{{ $event->booking->end_date->format('H:i') }}</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Participantes --}}
    @php $allDates2 = $event->allDates(); $multiday = count($allDates2) > 1; @endphp

    @if($multiday)
        @foreach($allDates2 as $dayIndex => $dayDate)
        <div class="section" @if(!$loop->first) style="margin-top:32px;" @endif>
            <div class="section-title">
                Lista de Presença — Dia {{ $dayIndex + 1 }}: {{ \Carbon\Carbon::parse($dayDate)->format('d/m/Y') }}
                @if($loop->first)
                    &nbsp;({{ $event->participants->count() }}/{{ $event->max_capacity }}
                    @if($event->isFull())
                        <span class="badge badge-full">Lotado</span>)
                    @else
                        <span class="badge badge-ok">{{ $event->availableSpots() }} vagas disponíveis</span>)
                    @endif
                @endif
            </div>
            @if($event->participants->isEmpty())
                <p style="color:#9ca3af;font-style:italic;">Nenhum participante inscrito.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            <th style="width:35%;">Nome</th>
                            <th style="width:25%;">CPF</th>
                            <th>Assinatura — Dia {{ $dayIndex + 1 }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($event->participants as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $p->name }}</td>
                            <td style="font-family:monospace;">
                                {{ $p->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $p->cpf) : '—' }}
                            </td>
                            <td style="border-bottom:1px solid #9ca3af;"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        @endforeach
    @else
        <div class="section">
            <div class="section-title">
                Lista de Participantes —
                {{ $event->participants->count() }}/{{ $event->max_capacity }}
                @if($event->isFull())
                    <span class="badge badge-full">Lotado</span>
                @else
                    <span class="badge badge-ok">{{ $event->availableSpots() }} vagas disponíveis</span>
                @endif
            </div>
            @if($event->participants->isEmpty())
                <p style="color:#9ca3af;font-style:italic;">Nenhum participante inscrito.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            <th style="width:35%;">Nome</th>
                            <th style="width:25%;">CPF</th>
                            <th>Assinatura</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($event->participants as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $p->name }}</td>
                            <td style="font-family:monospace;">
                                {{ $p->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $p->cpf) : '—' }}
                            </td>
                            <td style="border-bottom:1px solid #9ca3af;"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif

    {{-- Assinatura --}}
    <div class="footer">
        <div class="signature-area">
            <div class="signature-line">Responsável pelo Evento</div>
        </div>
        <div class="page-info">
            Total de participantes: {{ $event->participants->count() }} &nbsp;|&nbsp;
            Gerado por: {{ auth()->user()->name }} &nbsp;|&nbsp;
            {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

</body>
</html>
