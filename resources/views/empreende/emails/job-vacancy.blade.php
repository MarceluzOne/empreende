<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Vaga de Emprego</title>
    <style>
        body { margin: 0; padding: 0; background: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 580px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #1e3a5f; padding: 32px 40px; }
        .header-title { color: #ffffff; font-size: 22px; font-weight: 700; margin: 0 0 4px; }
        .header-sub { color: #93c5fd; font-size: 14px; margin: 0; }
        .body { padding: 32px 40px; }
        .greeting { font-size: 15px; color: #374151; margin-bottom: 20px; }
        .intro { font-size: 15px; color: #6b7280; line-height: 1.6; margin-bottom: 28px; }
        .card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
        .card-title { font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 4px; }
        .card-company { font-size: 13px; color: #6b7280; margin: 0 0 20px; }
        .detail-row { display: flex; gap: 12px; margin-bottom: 12px; }
        .detail-label { font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; min-width: 130px; padding-top: 2px; }
        .detail-value { font-size: 14px; color: #374151; line-height: 1.5; }
        .badge { display: inline-block; background: #dbeafe; color: #1d4ed8; font-size: 12px; font-weight: 600; padding: 2px 10px; border-radius: 20px; }
        .requirements { font-size: 14px; color: #374151; line-height: 1.6; white-space: pre-wrap; }
        .benefits { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px; }
        .benefit-tag { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-size: 12px; font-weight: 500; padding: 3px 10px; border-radius: 20px; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
        .footer { background: #f9fafb; padding: 24px 40px; border-top: 1px solid #e5e7eb; }
        .footer-text { font-size: 12px; color: #9ca3af; line-height: 1.6; margin: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <p class="header-title">{{ config('app.name') }}</p>
            <p class="header-sub">Nova oportunidade de emprego para você</p>
        </div>

        <div class="body">
            <p class="greeting">Olá, <strong>{{ $seeker->name }}</strong>!</p>
            <p class="intro">
                Encontramos uma nova vaga de emprego na área de
                <strong>{{ $vacancy->interest_area }}</strong> que combina com o seu perfil.
                Confira os detalhes abaixo:
            </p>

            <div class="card">
                <p class="card-title">{{ $vacancy->position }}</p>
                <p class="card-company">{{ $vacancy->company_name }} &mdash; CNPJ: {{ $vacancy->formatted_cnpj }}</p>

                <div class="detail-row">
                    <span class="detail-label">Área</span>
                    <span class="detail-value"><span class="badge">{{ $vacancy->interest_area }}</span></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Vagas disponíveis</span>
                    <span class="detail-value">{{ $vacancy->quantity }} {{ $vacancy->quantity === 1 ? 'vaga' : 'vagas' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Remuneração</span>
                    <span class="detail-value">{{ $vacancy->remuneration ?? 'A combinar' }}</span>
                </div>

                @if($vacancy->min_experience)
                <div class="detail-row">
                    <span class="detail-label">Experiência mínima</span>
                    <span class="detail-value">{{ $vacancy->min_experience }}</span>
                </div>
                @endif

                <hr class="divider">

                <div class="detail-row">
                    <span class="detail-label">Requisitos</span>
                    <span class="detail-value requirements">{{ $vacancy->requirements }}</span>
                </div>

                @if(!empty($vacancy->benefits))
                <div class="detail-row">
                    <span class="detail-label">Benefícios</span>
                    <span class="detail-value">
                        <div class="benefits">
                            @foreach($vacancy->benefits as $benefit)
                                <span class="benefit-tag">{{ $benefit }}</span>
                            @endforeach
                        </div>
                    </span>
                </div>
                @endif
            </div>

            <p style="font-size: 14px; color: #6b7280; line-height: 1.6;">
                Caso tenha interesse, entre em contato com a empresa ou compareça ao nosso escritório
                para mais informações sobre como se candidatar a esta vaga.
            </p>
        </div>

        <div class="footer">
            <p class="footer-text">
                Você recebeu este e-mail porque seu cadastro está associado à área de
                <strong>{{ $vacancy->interest_area }}</strong>.<br>
                {{ config('app.name') }} &mdash; Todos os direitos reservados.
            </p>
        </div>
    </div>
</body>
</html>
