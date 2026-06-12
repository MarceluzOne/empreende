<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Perfil do Candidato — Empreende Vitória</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  :root{
    --brand:#0763A0;--brand-deep:#044a7a;--brand-soft:#e8f1f9;
    --yellow:#ffc52d;--ink:#0c1822;--ink-soft:#38475a;--muted:#6c7a8a;
    --paper:#ffffff;--cream:#f3f6fa;--line:rgba(12,24,34,.08);
    --shadow-sm:0 1px 2px rgba(12,24,34,.06),0 2px 8px rgba(12,24,34,.04);
    --shadow-md:0 8px 28px rgba(12,24,34,.10);
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Lato',system-ui,sans-serif;color:var(--ink);background:var(--cream);min-height:100vh;-webkit-font-smoothing:antialiased}
  a{color:inherit;text-decoration:none}

  .topbar{display:flex;align-items:center;justify-content:space-between;padding:0 32px;height:65px;background:var(--paper);box-shadow:var(--shadow-sm);position:sticky;top:0;z-index:40}
  .brand{display:flex;align-items:center;gap:10px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:17px;color:var(--ink)}
  .brand-mark{width:34px;height:34px;border-radius:9px;background:var(--brand);color:#fff;display:grid;place-items:center;font-weight:900;font-size:13px}
  .btn-back{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:999px;font-size:13px;font-weight:700;color:var(--muted);border:1.5px solid var(--line);background:none;cursor:pointer;transition:background .15s,color .15s;text-decoration:none}
  .btn-back:hover{background:var(--brand-soft);color:var(--brand);border-color:var(--brand)}

  .page{max-width:760px;margin:0 auto;padding:40px 24px}

  /* Hero do candidato */
  .profile-hero{background:var(--paper);border:1px solid var(--line);border-radius:20px;padding:32px;margin-bottom:24px;display:flex;align-items:center;gap:24px;box-shadow:var(--shadow-sm)}
  .profile-avatar{width:72px;height:72px;border-radius:20px;background:linear-gradient(135deg,var(--brand),var(--brand-deep));color:#fff;display:grid;place-items:center;font-size:28px;flex-shrink:0}
  .profile-info{flex:1;min-width:0}
  .profile-name{font-family:'Plus Jakarta Sans',sans-serif;font-size:22px;font-weight:800;color:var(--ink);margin-bottom:4px}
  .profile-role{font-size:15px;color:var(--ink-soft);margin-bottom:10px}
  .profile-meta{display:flex;flex-wrap:wrap;gap:10px}
  .profile-meta-item{display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--muted)}
  .profile-meta-item i{color:var(--brand);width:14px}
  .status-pill{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;padding:4px 10px;border-radius:999px}
  .status-active{background:#dcfce7;color:#166534}
  .status-inactive{background:var(--cream);color:var(--muted)}

  /* Seções */
  .section-card{background:var(--paper);border:1px solid var(--line);border-radius:16px;padding:24px;margin-bottom:16px}
  .section-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:14px;display:flex;align-items:center;gap:8px}
  .section-label i{color:var(--brand)}

  /* Tags */
  .tags{display:flex;flex-wrap:wrap;gap:8px}
  .tag{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;padding:5px 12px;border-radius:999px;background:var(--brand-soft);color:var(--brand)}
  .tag i{font-size:10px}

  /* Contato */
  .contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  .contact-item{display:flex;align-items:center;gap:10px;font-size:14px;color:var(--ink)}
  .contact-icon{width:34px;height:34px;border-radius:10px;background:var(--brand-soft);color:var(--brand);display:grid;place-items:center;flex-shrink:0;font-size:13px}
  .contact-text{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
  .contact-link{color:var(--brand);text-decoration:underline;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}

  /* Resumo */
  .summary-text{font-size:14px;color:var(--ink-soft);line-height:1.75;white-space:pre-wrap}

  /* Timeline (experiências, formação) */
  .timeline{display:flex;flex-direction:column;gap:14px}
  .timeline-item{background:var(--cream);border-radius:12px;padding:14px 16px;border-left:3px solid var(--brand)}
  .timeline-title{font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:14px;color:var(--ink);margin-bottom:2px}
  .timeline-sub{font-size:13px;color:var(--muted)}

  /* Lista simples (idiomas, certificações) */
  .simple-list{display:flex;flex-direction:column;gap:8px}
  .simple-item{display:flex;align-items:center;gap:10px;font-size:14px;color:var(--ink)}
  .simple-item::before{content:'';width:6px;height:6px;border-radius:50%;background:var(--brand);flex-shrink:0}

  @media(max-width:600px){
    .topbar{padding:0 16px}
    .page{padding:24px 16px}
    .profile-hero{flex-direction:column;text-align:center}
    .profile-meta{justify-content:center}
    .contact-grid{grid-template-columns:1fr}
  }
</style>
</head>
<body>

<nav class="topbar">
  <a href="{{ route('home') }}" class="brand">
    <div class="brand-mark">EV</div>
    <div>Empreende Vitória
      <small style="display:block;font-size:11px;font-weight:600;opacity:.6;text-transform:uppercase;letter-spacing:.08em;font-family:'Lato',sans-serif">Portal da Empresa</small>
    </div>
  </a>
  <a href="{{ route('portal.empresa') }}" class="btn-back">
    <i class="fas fa-arrow-left"></i> Voltar ao portal
  </a>
</nav>

<div class="page">

  {{-- Hero --}}
  <div class="profile-hero">
    <div class="profile-avatar"><i class="fas fa-user-tie"></i></div>
    <div class="profile-info">
      <div class="profile-name">{{ $jobSeeker->name }}</div>
      <div class="profile-role">{{ $jobSeeker->job_function ?? 'Cargo não informado' }}</div>
      <div class="profile-meta">
        @if($jobSeeker->city)
          <span class="profile-meta-item"><i class="fas fa-map-marker-alt"></i> {{ $jobSeeker->city }}{{ $jobSeeker->state ? ', '.$jobSeeker->state : '' }}</span>
        @endif
        @if($jobSeeker->interest_area)
          <span class="profile-meta-item"><i class="fas fa-tag"></i> {{ $jobSeeker->interest_area }}</span>
        @endif
        @if($jobSeeker->experience)
          <span class="profile-meta-item"><i class="fas fa-briefcase"></i> {{ $jobSeeker->experience }}</span>
        @endif
        <span class="status-pill {{ $jobSeeker->status === 'active' ? 'status-active' : 'status-inactive' }}">
          <i class="fas fa-circle" style="font-size:7px"></i> {{ $jobSeeker->status_label }}
        </span>
      </div>
    </div>
  </div>

  {{-- Resumo profissional --}}
  @if($jobSeeker->summary)
    <div class="section-card">
      <div class="section-label"><i class="fas fa-align-left"></i> Resumo profissional</div>
      <p class="summary-text">{{ $jobSeeker->summary }}</p>
    </div>
  @endif

  {{-- Contato --}}
  @if($jobSeeker->email || $jobSeeker->phone || $jobSeeker->linkedin_url || $jobSeeker->github_url)
    <div class="section-card">
      <div class="section-label"><i class="fas fa-address-book"></i> Contato</div>
      <div class="contact-grid">
        @if($jobSeeker->email)
          <div class="contact-item">
            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
            <span class="contact-text">{{ $jobSeeker->email }}</span>
          </div>
        @endif
        @if($jobSeeker->phone)
          @php
            $phoneDigits = preg_replace('/\D/', '', $jobSeeker->phone);
            if (strlen($phoneDigits) <= 11) { $phoneDigits = '55' . $phoneDigits; }
            $statusLabel = $aplicacao->status_label ?? 'Pendente';
            $vagaPosition = $aplicacao->vacancy->position ?? 'vaga';
            $waMsg = "Olá, {$jobSeeker->name}! Entramos em contato referente à sua candidatura para a vaga de *{$vagaPosition}*. O status atual da sua candidatura é: *{$statusLabel}*.";
            $waUrl = 'https://wa.me/' . $phoneDigits . '?text=' . rawurlencode($waMsg);
          @endphp
          <div class="contact-item">
            <div class="contact-icon" style="background:#dcfce7;color:#166534"><i class="fab fa-whatsapp"></i></div>
            <a href="{{ $waUrl }}" target="_blank" class="contact-link" title="Abrir WhatsApp">
              {{ $jobSeeker->formatted_phone ?? $jobSeeker->phone }}
            </a>
          </div>
        @endif
        @if($jobSeeker->linkedin_url)
          <div class="contact-item">
            <div class="contact-icon"><i class="fab fa-linkedin-in"></i></div>
            <a href="{{ $jobSeeker->linkedin_url }}" target="_blank" class="contact-link">LinkedIn</a>
          </div>
        @endif
        @if($jobSeeker->github_url)
          <div class="contact-item">
            <div class="contact-icon"><i class="fab fa-github"></i></div>
            <a href="{{ $jobSeeker->github_url }}" target="_blank" class="contact-link">GitHub</a>
          </div>
        @endif
      </div>
    </div>
  @endif

  {{-- Habilidades --}}
  @if($jobSeeker->skills)
    <div class="section-card">
      <div class="section-label"><i class="fas fa-star"></i> Habilidades</div>
      <div class="tags">
        @foreach(array_filter(array_map('trim', explode(',', $jobSeeker->skills))) as $skill)
          <span class="tag"><i class="fas fa-check"></i> {{ $skill }}</span>
        @endforeach
      </div>
    </div>
  @endif

  {{-- Experiências profissionais --}}
  @if(!empty($jobSeeker->experiences))
    <div class="section-card">
      <div class="section-label"><i class="fas fa-briefcase"></i> Experiências profissionais</div>
      <div class="timeline">
        @foreach($jobSeeker->experiences as $exp)
          <div class="timeline-item">
            <div class="timeline-title">
              {{ $exp['position'] ?? ($exp['cargo'] ?? '') }}
              @if(!empty($exp['company']) || !empty($exp['empresa']))
                <span style="font-weight:400;color:var(--muted)"> — {{ $exp['company'] ?? $exp['empresa'] }}</span>
              @endif
            </div>
            @if(!empty($exp['period']) || !empty($exp['periodo']))
              <div class="timeline-sub">{{ $exp['period'] ?? $exp['periodo'] }}</div>
            @endif
            @if(!empty($exp['description']) || !empty($exp['descricao']))
              <div style="font-size:13px;color:var(--ink-soft);margin-top:6px;line-height:1.6">{{ $exp['description'] ?? $exp['descricao'] }}</div>
            @endif
          </div>
        @endforeach
      </div>
    </div>
  @endif

  {{-- Formação --}}
  @if(!empty($jobSeeker->education))
    <div class="section-card">
      <div class="section-label"><i class="fas fa-graduation-cap"></i> Formação</div>
      <div class="timeline">
        @foreach($jobSeeker->education as $edu)
          <div class="timeline-item">
            <div class="timeline-title">
              {{ $edu['course'] ?? ($edu['curso'] ?? '') }}
              @if(!empty($edu['institution']) || !empty($edu['instituicao']))
                <span style="font-weight:400;color:var(--muted)"> — {{ $edu['institution'] ?? $edu['instituicao'] }}</span>
              @endif
            </div>
            @if(!empty($edu['period']) || !empty($edu['periodo']))
              <div class="timeline-sub">{{ $edu['period'] ?? $edu['periodo'] }}</div>
            @endif
          </div>
        @endforeach
      </div>
    </div>
  @endif

  {{-- Idiomas --}}
  @if(!empty($jobSeeker->languages))
    <div class="section-card">
      <div class="section-label"><i class="fas fa-language"></i> Idiomas</div>
      <div class="simple-list">
        @foreach($jobSeeker->languages as $lang)
          <div class="simple-item">
            {{ $lang['language'] ?? ($lang['idioma'] ?? $lang) }}
            @if(!empty($lang['level']) || !empty($lang['nivel']))
              <span style="color:var(--muted);font-size:13px"> — {{ $lang['level'] ?? $lang['nivel'] }}</span>
            @endif
          </div>
        @endforeach
      </div>
    </div>
  @endif

  {{-- Certificações --}}
  @if(!empty($jobSeeker->certifications))
    <div class="section-card">
      <div class="section-label"><i class="fas fa-certificate"></i> Certificações</div>
      <div class="simple-list">
        @foreach($jobSeeker->certifications as $cert)
          <div class="simple-item">
            {{ $cert['name'] ?? ($cert['nome'] ?? $cert) }}
            @if(!empty($cert['issuer']) || !empty($cert['emissor']))
              <span style="color:var(--muted);font-size:13px"> — {{ $cert['issuer'] ?? $cert['emissor'] }}</span>
            @endif
          </div>
        @endforeach
      </div>
    </div>
  @endif

</div>
</body>
</html>
