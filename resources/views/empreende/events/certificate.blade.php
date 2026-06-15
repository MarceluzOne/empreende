<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body {
        font-family: 'DejaVu Sans', sans-serif;
        color: #1a2238;
    }
    /* position:fixed ancora à página inteira e mantém o conteúdo fora do fluxo,
       evitando que o DomPDF gere uma 2ª página por estouro de altura/largura. */
    .sheet {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: #fbfbf9;
    }
    /* Moldura azul externa + moldura interna */
    .frame {
        position: absolute;
        top: 7mm; left: 7mm; right: 7mm; bottom: 7mm;
        border: 2px solid #1e3a5f;
    }
    .frame-inner {
        position: absolute;
        top: 11mm; left: 11mm; right: 11mm; bottom: 11mm;
        border: 1px solid #c9d4e3;
        background: #ffffff;
    }
    /* Cantos dourados */
    .corner {
        position: absolute;
        width: 26px; height: 26px;
        z-index: 5;
    }
    .corner.tl { top: 9mm;  left: 9mm;  border-top: 3px solid #c9a227; border-left: 3px solid #c9a227; }
    .corner.tr { top: 9mm;  right: 9mm; border-top: 3px solid #c9a227; border-right: 3px solid #c9a227; }
    .corner.bl { bottom: 9mm; left: 9mm;  border-bottom: 3px solid #c9a227; border-left: 3px solid #c9a227; }
    .corner.br { bottom: 9mm; right: 9mm; border-bottom: 3px solid #c9a227; border-right: 3px solid #c9a227; }

    .content {
        position: absolute;
        top: 16mm; left: 22mm; right: 22mm;
        text-align: center;
    }
    .logo-badge {
        margin: 0 auto 6mm;
    }
    .logo-badge img {
        height: 95px; width: auto;
        object-fit: contain;
    }
    .title-cert {
        font-family: 'DejaVu Serif', serif;
        font-size: 38pt;
        color: #1a2238;
        margin: 3mm 0 2mm;
    }
    .title-rule {
        width: 70px;
        border: none;
        border-top: 2px solid #c9a227;
        margin: 0 auto 7mm;
    }
    .lead {
        font-size: 11pt;
        color: #4b5563;
        margin-bottom: 3mm;
    }
    .name {
        font-family: 'DejaVu Serif', serif;
        font-size: 30pt;
        font-weight: bold;
        color: #1e3a5f;
        margin-bottom: 3mm;
    }
    .name-rule {
        width: 320px;
        border: none;
        border-top: 1px solid #b8bcc4;
        margin: 0 auto 7mm;
    }
    .body-text {
        font-size: 11pt;
        color: #374151;
        line-height: 1.7;
        max-width: 200mm;
        margin: 0 auto;
    }
    .body-text .event-title { font-weight: bold; color: #1a2238; }

    /* Bloco carga horária / conclusão */
    .meta {
        margin: 9mm auto 0;
    }
    .meta td {
        padding: 0 12mm;
        text-align: center;
        vertical-align: top;
    }
    .meta .label {
        font-size: 8pt;
        color: #6b7280;
        letter-spacing: 2px;
        text-transform: uppercase;
    }
    .meta .value {
        font-size: 13pt;
        font-weight: bold;
        color: #1a2238;
        margin-top: 2mm;
    }

    /* Assinatura (inferior esquerda) */
    .signature {
        position: absolute;
        bottom: 22mm; left: 26mm;
        width: 75mm;
        text-align: center;
    }
    .signature .sig-line {
        border-top: 1px solid #1a2238;
        margin-bottom: 2mm;
    }
    .signature .sig-name {
        font-size: 11pt;
        font-weight: bold;
        color: #1a2238;
    }
    .signature .sig-role {
        font-size: 8.5pt;
        color: #6b7280;
        margin-top: 1mm;
    }

    /* Rodapé central */
    .validation {
        position: absolute;
        bottom: 13mm; left: 0; right: 0;
        text-align: center;
        font-size: 7.5pt;
        color: #9ca3af;
    }
</style>
</head>
<body>
<div class="sheet">
    <div class="frame"></div>
    <div class="frame-inner"></div>

    <div class="corner tl"></div>
    <div class="corner tr"></div>
    <div class="corner bl"></div>
    <div class="corner br"></div>

    <div class="content">
        <div class="logo-badge">
            <img src="{{ public_path('assets/empreende-vitoria-logo.png') }}" alt="Empreende Vitória — Salão do Empreendedor">
        </div>

        <div class="title-cert">Certificado</div>
        <hr class="title-rule">

        <div class="lead">Certificamos que</div>
        <div class="name">{{ $participant->name }}</div>
        <hr class="name-rule">

        <div class="body-text">
            concluiu com aproveitamento o
            <span class="event-title">{{ $event->title }}</span>,
            promovido pelo Empreende Vitória — Salão do Empreendedor.
        </div>

        <table class="meta" align="center">
            <tr>
                <td>
                    <div class="label">Carga Horária</div>
                    <div class="value">{{ number_format($totalHours, 0) }} horas</div>
                </td>
                <td>
                    <div class="label">Concluído em</div>
                    <div class="value">{{ $concludedLong }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if($event->speaker)
        <div class="signature">
            <div class="sig-line"></div>
            <div class="sig-name">{{ $event->speaker->name }}</div>
            <div class="sig-role">Palestrante responsável</div>
        </div>
    @endif

    <div class="validation">
        Código de validação: {{ $validationCode }}@if($cpfFormatted) &nbsp;·&nbsp; CPF: {{ $cpfFormatted }}@endif
    </div>
</div>
</body>
</html>
