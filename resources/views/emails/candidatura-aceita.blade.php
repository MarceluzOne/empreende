<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatura Aceita</title>
    <style>
        body { margin: 0; padding: 0; background: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 580px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #166534; padding: 32px 40px; }
        .header-title { color: #ffffff; font-size: 22px; font-weight: 700; margin: 0 0 4px; }
        .header-sub { color: #86efac; font-size: 14px; margin: 0; }
        .body { padding: 32px 40px; }
        .greeting { font-size: 15px; color: #374151; margin-bottom: 20px; }
        .intro { font-size: 15px; color: #6b7280; line-height: 1.6; margin-bottom: 28px; }
        .card { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
        .card-title { font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 4px; }
        .card-company { font-size: 13px; color: #6b7280; margin: 0 0 20px; }
        .detail-row { display: flex; gap: 12px; margin-bottom: 12px; }
        .detail-label { font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; min-width: 130px; padding-top: 2px; }
        .detail-value { font-size: 14px; color: #374151; line-height: 1.5; }
        .footer { background: #f9fafb; padding: 24px 40px; border-top: 1px solid #e5e7eb; }
        .footer-text { font-size: 12px; color: #9ca3af; line-height: 1.6; margin: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <p class="header-title">{{ config('app.name') }}</p>
            <p class="header-sub">Sua candidatura foi aceita!</p>
        </div>

        <div class="body">
            <p class="greeting">Parabéns, <strong>{{ $application->seeker->name }}</strong>!</p>
            <p class="intro">
                Temos uma ótima notícia: sua candidatura para a vaga abaixo foi <strong>aceita</strong>.
                Em breve a empresa entrará em contato com você.
            </p>

            <div class="card">
                <p class="card-title">{{ $application->vacancy->position }}</p>
                <p class="card-company">{{ $application->vacancy->company_name }}</p>

                @if($application->vacancy->interest_area)
                <div class="detail-row">
                    <span class="detail-label">Área</span>
                    <span class="detail-value">{{ $application->vacancy->interest_area }}</span>
                </div>
                @endif

                @if($application->vacancy->remuneration && (float)$application->vacancy->remuneration > 0)
                <div class="detail-row">
                    <span class="detail-label">Remuneração</span>
                    <span class="detail-value">R$ {{ number_format((float)$application->vacancy->remuneration, 2, ',', '.') }}</span>
                </div>
                @endif
            </div>

            <p style="font-size: 14px; color: #6b7280; line-height: 1.6;">
                Aguarde o contato da empresa. Boa sorte nesta nova etapa!
            </p>
        </div>

        <div class="footer">
            <p class="footer-text">
                {{ config('app.name') }} &mdash; Todos os direitos reservados.
            </p>
        </div>
    </div>
</body>
</html>
