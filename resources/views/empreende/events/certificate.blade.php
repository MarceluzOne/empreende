<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'DejaVu Sans', sans-serif;
        background: #fff;
        color: #1a1a2e;
        width: 100%;
        height: 100%;
    }
    .page {
        width: 277mm;
        height: 190mm;
        padding: 14mm 18mm;
        position: relative;
        border: 1px solid #e0e0e0;
    }
    .border-top {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 8px;
        background: linear-gradient(to right, #1e3a5f, #2563eb);
    }
    .border-bottom {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 4px;
        background: #1e3a5f;
    }
    .header {
        text-align: center;
        margin-bottom: 10mm;
    }
    .header .org {
        font-size: 11pt;
        font-weight: bold;
        color: #1e3a5f;
        letter-spacing: 2px;
        text-transform: uppercase;
    }
    .header .subtitle {
        font-size: 8pt;
        color: #6b7280;
        margin-top: 2px;
    }
    .divider {
        border: none;
        border-top: 1.5px solid #2563eb;
        margin: 4mm 0;
    }
    .title-cert {
        text-align: center;
        font-size: 22pt;
        font-weight: bold;
        color: #1e3a5f;
        letter-spacing: 4px;
        text-transform: uppercase;
        margin-bottom: 8mm;
    }
    .body-text {
        text-align: center;
        font-size: 11pt;
        color: #374151;
        line-height: 1.8;
    }
    .body-text .name {
        font-size: 16pt;
        font-weight: bold;
        color: #1e3a5f;
        display: block;
        margin: 3mm 0 1mm;
    }
    .body-text .cpf {
        font-size: 9pt;
        color: #6b7280;
    }
    .body-text .event-title {
        font-weight: bold;
        color: #2563eb;
    }
    .body-text .period {
        font-weight: bold;
    }
    .signature-area {
        margin-top: 10mm;
        display: flex;
        justify-content: center;
    }
    .signature-block {
        text-align: center;
        width: 160px;
    }
    .signature-line {
        border-top: 1px solid #374151;
        margin-bottom: 3px;
    }
    .signature-label {
        font-size: 8pt;
        color: #6b7280;
    }
    .footer {
        position: absolute;
        bottom: 12mm;
        left: 18mm;
        right: 18mm;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .footer-left {
        font-size: 7.5pt;
        color: #9ca3af;
    }
    .footer-right {
        font-size: 7.5pt;
        color: #9ca3af;
        text-align: right;
    }
</style>
</head>
<body>
<div class="page">
    <div class="border-top"></div>

    <div class="header">
        <div class="org">Empreende Vitória</div>
        <div class="subtitle">Programa de Apoio ao Empreendedorismo</div>
    </div>

    <hr class="divider">

    <div class="title-cert">Certificado de Participação</div>

    <div class="body-text">
        Certificamos que
        <span class="name">{{ $participant->name }}</span>
        @if($cpfFormatted)
            <span class="cpf">CPF: {{ $cpfFormatted }}</span><br>
        @endif
        participou do evento
        <span class="event-title">"{{ $event->title }}"</span>,
        @if($startDate === $endDate)
            realizado em <span class="period">{{ $startDate }}</span>,
        @else
            realizado no período de <span class="period">{{ $startDate }}</span> a <span class="period">{{ $endDate }}</span>,
        @endif
        com carga horária de <span class="period">{{ number_format($totalHours, 0) }}h</span>.
    </div>

    <div class="signature-area">
        <div class="signature-block">
            <br><br>
            <div class="signature-line"></div>
            <div class="signature-label">Coordenação — Empreende Vitória</div>
        </div>
    </div>

    <div class="footer">
        <div class="footer-left">Emitido em {{ now()->format('d/m/Y') }}</div>
        <div class="footer-right">Documento gerado eletronicamente</div>
    </div>

    <div class="border-bottom"></div>
</div>
</body>
</html>
