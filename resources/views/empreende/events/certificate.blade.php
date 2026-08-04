<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
@php
    $font = function (string $file) {
        return str_replace('\\', '/', public_path('assets/fonts/' . $file));
    };
@endphp
<style>
    @page { margin: 0; }

    @font-face {
        font-family: 'Poppins';
        font-style: normal;
        font-weight: normal;
        src: url("{{ $font('Poppins-SemiBold.ttf') }}") format("truetype");
    }
    @font-face {
        font-family: 'Poppins';
        font-style: normal;
        font-weight: bold;
        src: url("{{ $font('Poppins-Bold.ttf') }}") format("truetype");
    }
    @font-face {
        font-family: 'PoppinsExtraBold';
        font-style: normal;
        font-weight: normal;
        src: url("{{ $font('Poppins-ExtraBold.ttf') }}") format("truetype");
    }
    @font-face {
        font-family: 'MrsSaintDelafield';
        font-style: normal;
        font-weight: normal;
        src: url("{{ $font('MrsSaintDelafield-Regular.ttf') }}") format("truetype");
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body { font-family: 'Poppins', sans-serif; color: #1478C4; }

    /* position:fixed ancora à página inteira e mantém o conteúdo fora do fluxo,
       evitando que o DomPDF gere uma 2ª página por estouro de altura/largura.
       Medidas em px seguem o handoff (A4 paisagem = 1123x794px a 96dpi). */
    .sheet {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: #ffffff;
    }

    /* Moldura: barras navy no topo/esquerda e faixa dupla (ciano + navy) à direita. */
    .bar { position: absolute; }
    .bar-top   { top: 0; left: 0; width: 1023px; height: 10px; background: #0F3D73; }
    .bar-left  { top: 0; left: 0; width: 10px; height: 794px; background: #0F3D73; }
    .bar-cyan  { top: 0; left: 1074px; width: 20px; height: 794px; background: #3FB6E8; }
    .bar-navy  { top: 0; left: 1094px; width: 29px; height: 794px; background: #0F3D73; }

    /* Marca d'água (ícone da logo). O alfa de 8% e o cinza já vêm gravados no PNG:
       o DomPDF não suporta filter: grayscale() nem opacity confiável em <img>. */
    .watermark { position: absolute; bottom: -20px; width: 130px; height: 130px; }
    .watermark.left  { left: -20px; }
    .watermark.right { left: 973px; }

    .content {
        position: absolute;
        top: 36px; left: 100px;
        width: 933px;
        text-align: center;
    }

    .logo { height: 96px; width: auto; margin-top: 6px; }

    .title-cert {
        font-family: 'PoppinsExtraBold', sans-serif;
        font-size: 40px;
        color: #12ABE0;
        letter-spacing: 2px;
        margin-top: 22px;
    }

    /* ATENÇÃO ao line-height: o DomPDF não usa o valor declarado como altura da
       linha — ele calcula `alturaNaturalDaFonte * (line-height / font-size)`.
       Como o Poppins tem altura natural ~1,65em, os valores abaixo já vêm
       divididos por 1,65 para render o espaçamento do handoff.
       Ex.: body = 19px com entrelinha 38px  ->  38 / 1,65 = 23px. */
    .body-text {
        margin-top: 34px;
        font-size: 19px;
        line-height: 23px; /* = 38px reais */
        font-weight: bold;
        color: #1478C4;
        text-align: left;
    }
    /* Sem `height` de propósito: com box-sizing: border-box uma altura fixa
       menor que a linha de 34px do script espreme a caixa, e o DomPDF desenha a
       border-bottom no fim da caixa espremida — a régua saía cortando o nome ao
       meio, com os glifos vazando por baixo. Deixando a altura automática, a
       régua cai na base do texto e o nome se apoia sobre ela. */
    .body-text .name {
        display: inline-block;
        width: 600px;
        line-height: 34px;
        font-family: 'MrsSaintDelafield', cursive;
        font-weight: normal;
        font-size: 34px;
        color: #0F3D73;
        border-bottom: 1.5px solid #6EC6E8;
        padding: 0 6px 2px;
        text-align: center;
    }

    /* Blocos de baixo ancorados por `top`: o DomPDF não resolve `bottom` para
       elementos absolutos cuja altura só é conhecida depois do layout. */

    /* Assinaturas: 280px cada, separadas por 90px (total 650px, centralizado). */
    .signatures {
        position: absolute;
        top: 548px; left: 100px;
        width: 933px;
        text-align: center;
    }
    .signatures table { margin: 0 auto; border-collapse: collapse; }
    .signatures .block { width: 280px; text-align: center; vertical-align: bottom; }
    .signatures .gap   { width: 90px; }
    .sig-space { height: 70px; text-align: center; vertical-align: bottom; }
    .sig-space img { max-height: 70px; max-width: 260px; width: auto; }
    .sig-line {
        border-top: 1.5px solid #6EC6E8;
        padding-top: 8px;
    }
    .sig-name { font-size: 16px; line-height: 13px; font-weight: bold; color: #0F3D73; letter-spacing: 0.5px; }
    .sig-role { font-size: 13px; line-height: 11px; font-weight: normal; color: #7FB9DE; letter-spacing: 0.5px; }

    /* Selo institucional (rodapé). */
    

    .validation {
        position: absolute;
        top: 772px; left: 100px;
        width: 933px;
        text-align: center;
        font-size: 7px;
        font-weight: normal;
        color: #b9c6d4;
        letter-spacing: 0.3px;
    }
</style>
</head>
<body>
<div class="sheet">
    <div class="bar bar-top"></div>
    <div class="bar bar-left"></div>

    <img class="watermark left"  src="{{ public_path('assets/empreende-watermark.png') }}" alt="">
    <img class="watermark right" src="{{ public_path('assets/empreende-watermark.png') }}" alt="">

    {{-- As faixas da direita vêm depois para cobrirem a marca d'água daquele canto. --}}
    <div class="bar bar-cyan"></div>
    <div class="bar bar-navy"></div>

    <div class="content">
        <img class="logo" src="{{ public_path('assets/empreende-logo-horizontal.png') }}" alt="Empreende Vitória — Salão do Empreendedor">

        <div class="title-cert">CERTIFICADO</div>

        {{-- $action e $roleClause vêm do CertificateService: o participante
             "PARTICIPOU DO" curso; o palestrante "MINISTROU O" curso, com a
             cláusula extra registrando a condição. --}}
        <div class="body-text">
            CERTIFICAMOS QUE <span class="name">{{ $recipientName }}</span>,
            @if($cpfFormatted)  INSCRITO SOB O CPF DE Nº {{ $cpfFormatted }}, @endif
            {{ $action }} {{ $courseLabel }}, REALIZADO NO EMPREENDE VITÓRIA,
            @if($roleClause) {{ $roleClause }}, @endif
            COM A CARGA HORÁRIA DE {{ number_format($totalHours, 0, ',', '.') }} HORAS AULA,
            @if($startDate === $endDate)
                REALIZADO NO DIA {{ $startDate }}.
            @else
                COM INÍCIO NO DIA {{ $startDate }} E ENCERRAMENTO NO DIA {{ $endDate }}.
            @endif
        </div>
    </div>

    {{-- Blocos montados no CertificateService: o diretor assina sempre; o
         palestrante só entra no certificado de participação, e ainda assim
         apenas quando não é o próprio diretor. --}}
    <div class="signatures">
        <table style="width: {{ count($signatures) * 280 + (count($signatures) - 1) * 90 }}px;">
            <tr>
                @foreach($signatures as $signature)
                    @if(!$loop->first)<td class="gap"></td>@endif
                    <td class="block">
                        <table style="width:100%;border-collapse:collapse;">
                            <tr>
                                <td class="sig-space">
                                    @if($signature['image'])
                                        <img src="{{ $signature['image'] }}" alt="Assinatura">
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="sig-line">
                                    <div class="sig-name">{{ $signature['name'] }}</div>
                                    <div class="sig-role">{{ $signature['role'] }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                @endforeach
            </tr>
        </table>
    </div>



    <div class="validation">Código de validação: {{ $validationCode }}</div>
</div>
</body>
</html>
