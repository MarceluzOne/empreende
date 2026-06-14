<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Currículo — {{ $seeker->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; padding: 40px; }
        .header { border-bottom: 3px solid #0763A0; padding-bottom: 14px; margin-bottom: 22px; }
        .system { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; }
        h1 { font-size: 22px; color: #111827; margin: 6px 0 2px; }
        .role { font-size: 13px; color: #0763A0; font-weight: bold; }
        .contact { font-size: 11px; color: #4b5563; margin-top: 8px; }
        .contact span { margin-right: 14px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: .05em;
            color: #0763A0; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 10px; }
        .item { margin-bottom: 10px; }
        .item-title { font-weight: bold; color: #111827; font-size: 12px; }
        .item-sub { color: #6b7280; font-size: 11px; }
        .item-text { color: #374151; font-size: 11px; margin-top: 3px; line-height: 1.4; }
        .muted { color: #9ca3af; font-style: italic; }
        .chips span { display: inline-block; background: #eef2ff; color: #3730a3; padding: 2px 8px;
            border-radius: 10px; font-size: 11px; margin: 0 4px 4px 0; }
        .meta { display: table; width: 100%; }
        .meta-row { display: table-row; }
        .meta-label { display: table-cell; font-weight: bold; color: #374151; width: 150px; padding: 3px 0; }
        .meta-value { display: table-cell; color: #4b5563; padding: 3px 0; }
        .footer { margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 12px; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>

    <div class="header">
        <div class="system">Empreende Vitória — Vitória de Santo Antão / PE</div>
        <h1>{{ $seeker->name ?: 'Candidato' }}</h1>
        @if($seeker->job_function)<div class="role">{{ $seeker->job_function }}</div>@endif
        <div class="contact">
            @if($seeker->cpf)<span><strong>CPF:</strong> {{ $seeker->cpf }}</span>@endif
            @if($seeker->phone)<span><strong>Tel:</strong> {{ $seeker->formatted_phone ?? $seeker->phone }}</span>@endif
            @if($seeker->email)<span><strong>E-mail:</strong> {{ $seeker->email }}</span>@endif
            @if($seeker->city || $seeker->state)<span><strong>Local:</strong> {{ trim(($seeker->city ?? '').($seeker->state ? ' / '.$seeker->state : '')) }}</span>@endif
        </div>
        @if($seeker->linkedin_url || $seeker->github_url)
        <div class="contact">
            @if($seeker->linkedin_url)<span>LinkedIn: {{ $seeker->linkedin_url }}</span>@endif
            @if($seeker->github_url)<span>GitHub: {{ $seeker->github_url }}</span>@endif
        </div>
        @endif
    </div>

    {{-- Dados gerais --}}
    <div class="section">
        <div class="section-title">Dados</div>
        <div class="meta">
            <div class="meta-row">
                <div class="meta-label">Área de interesse</div>
                <div class="meta-value">{{ $seeker->interest_area ?: '—' }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-label">Experiência</div>
                <div class="meta-value">{{ $seeker->experience ?: '—' }}</div>
            </div>
        </div>
    </div>

    @if($seeker->summary)
    <div class="section">
        <div class="section-title">Resumo</div>
        <div class="item-text">{{ $seeker->summary }}</div>
    </div>
    @endif

    @if(filled($seeker->skills))
    <div class="section">
        <div class="section-title">Habilidades</div>
        <div class="chips">
            @foreach(preg_split('/[,;]+/', $seeker->skills, -1, PREG_SPLIT_NO_EMPTY) as $skill)
                <span>{{ trim($skill) }}</span>
            @endforeach
        </div>
    </div>
    @endif

    @if(is_array($seeker->experiences) && count($seeker->experiences))
    <div class="section">
        <div class="section-title">Experiência profissional</div>
        @foreach($seeker->experiences as $exp)
            @php $exp = (array) $exp; @endphp
            @if(array_filter($exp))
            <div class="item">
                <div class="item-title">{{ $exp['role'] ?? '—' }}@if(!empty($exp['company'])) — {{ $exp['company'] }}@endif</div>
                @if(!empty($exp['start']) || !empty($exp['end']))
                    <div class="item-sub">{{ $exp['start'] ?? '' }}@if(!empty($exp['end'])) até {{ $exp['end'] }}@endif</div>
                @endif
                @if(!empty($exp['activities']))<div class="item-text">{{ $exp['activities'] }}</div>@endif
            </div>
            @endif
        @endforeach
    </div>
    @endif

    @if(is_array($seeker->education) && count($seeker->education))
    <div class="section">
        <div class="section-title">Formação</div>
        @foreach($seeker->education as $edu)
            @php $edu = (array) $edu; @endphp
            @if(array_filter($edu))
            <div class="item">
                <div class="item-title">{{ $edu['course'] ?? '—' }}</div>
                <div class="item-sub">{{ $edu['institution'] ?? '' }}@if(!empty($edu['year'])) — {{ $edu['year'] }}@endif</div>
            </div>
            @endif
        @endforeach
    </div>
    @endif

    @if(is_array($seeker->languages) && count($seeker->languages))
    <div class="section">
        <div class="section-title">Idiomas</div>
        @foreach($seeker->languages as $lang)
            @php $lang = (array) $lang; @endphp
            @if(!empty($lang['language']))
                <div class="item-text">{{ $lang['language'] }}@if(!empty($lang['level'])) — {{ $lang['level'] }}@endif</div>
            @endif
        @endforeach
    </div>
    @endif

    @if(is_array($seeker->certifications) && count(array_filter($seeker->certifications)))
    <div class="section">
        <div class="section-title">Certificações</div>
        @foreach(array_filter($seeker->certifications) as $cert)
            <div class="item-text">• {{ is_array($cert) ? implode(' ', $cert) : $cert }}</div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        Currículo gerado pelo sistema Empreende Vitória em {{ now()->format('d/m/Y \à\s H:i') }}.
    </div>

</body>
</html>
