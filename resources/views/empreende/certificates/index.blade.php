<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Meus Certificados — Empreende Vitória</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  :root{
    --brand:#0763A0;
    --brand-deep:#044a7a;
    --brand-soft:#e8f1f9;
    --yellow:#ffc52d;
    --ink:#0c1822;
    --ink-soft:#38475a;
    --muted:#6c7a8a;
    --paper:#ffffff;
    --cream:#f3f6fa;
    --line:rgba(12,24,34,.08);
    --shadow-sm:0 1px 2px rgba(12,24,34,.06),0 2px 8px rgba(12,24,34,.04);
    --shadow-md:0 8px 28px rgba(12,24,34,.10);
    --radius:14px;
  }
  *{box-sizing:border-box}
  html{scroll-behavior:smooth}
  html,body{margin:0;padding:0}
  body{font-family:'Lato',system-ui,sans-serif;color:var(--ink);background:var(--paper);-webkit-font-smoothing:antialiased;line-height:1.55}
  img{max-width:100%;display:block}
  a{color:inherit;text-decoration:none}

  /* ====== TOPBAR ====== */
  .topbar{
    position:fixed;top:0;left:0;right:0;z-index:50;
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 32px;
    background:rgba(255,255,255,.96);
    backdrop-filter:saturate(140%) blur(8px);
    -webkit-backdrop-filter:saturate(140%) blur(8px);
    box-shadow:var(--shadow-sm);
    color:var(--ink);
  }
  .brand{display:flex;align-items:center;gap:10px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;letter-spacing:-0.01em;font-size:18px}
  .brand-mark{width:36px;height:36px;border-radius:10px;background:var(--brand);color:#fff;display:grid;place-items:center;font-weight:900;font-size:14px}
  .brand small{display:block;font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;opacity:.75;font-family:'Lato',sans-serif}
  .nav{display:flex;align-items:center;gap:4px}
  .nav a{padding:8px 14px;border-radius:999px;font-weight:700;font-size:14px;transition:background .15s,color .15s}
  .nav a:hover{background:rgba(7,99,160,.08);color:var(--brand)}
  .nav a.active{background:var(--yellow);color:var(--ink)!important}
  .nav a.nav-login{margin-left:8px;border:1.5px solid var(--brand);color:var(--brand);padding:7px 16px}
  .nav a.nav-login i{font-size:12px;margin-right:4px}
  .nav a.nav-login:hover{background:var(--brand);color:#fff!important}
  .menu-toggle{display:none;background:none;border:0;color:inherit;width:42px;height:42px;border-radius:10px;font-size:18px;cursor:pointer}
  .menu-toggle:hover{background:rgba(7,99,160,.08)}

  /* ====== HERO ====== */
  .page-hero{
    background:linear-gradient(135deg,#0763A0 0%,#044a7a 100%);
    padding:120px 32px 64px;
    color:#fff;
  }
  .page-hero__inner{max-width:1280px;margin:0 auto}
  .breadcrumb{display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.7);margin-bottom:20px}
  .breadcrumb a{color:rgba(255,255,255,.7)}
  .breadcrumb a:hover{color:#fff}
  .breadcrumb i{font-size:11px}
  .hero-badge{
    display:inline-flex;align-items:center;gap:8px;
    background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);
    padding:5px 14px;border-radius:999px;
    font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;
    margin-bottom:16px;width:fit-content;
  }
  .hero-badge .dot{width:6px;height:6px;border-radius:50%;background:var(--yellow)}
  .page-hero h1{
    font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;
    font-size:clamp(34px,4.5vw,58px);line-height:1.05;letter-spacing:-0.025em;
    margin:0 0 14px;max-width:22ch;
  }
  .page-hero h1 em{font-style:normal;color:var(--yellow)}
  .page-hero p{font-size:16.5px;color:rgba(255,255,255,.85);max-width:60ch;margin:0}

  /* ====== MAIN ====== */
  .page-body{max-width:820px;margin:0 auto;padding:40px 32px 80px}

  /* ====== BUSCA ====== */
  .card{background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow-md);overflow:hidden}
  .form{padding:22px 20px;display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap}
  .field{flex:1;min-width:220px}
  .field label{display:block;font-weight:800;font-size:13px;margin-bottom:6px;letter-spacing:.01em;color:var(--ink)}
  .field input{width:100%;padding:12px 14px;border:1.5px solid var(--line);border-radius:10px;font-size:14.5px;font-family:inherit;outline:0;transition:border-color .15s,box-shadow .15s}
  .field input:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(7,99,160,.12)}
  .field input.is-error{border-color:#dc2626}
  .btn{
    display:inline-flex;align-items:center;gap:10px;
    background:var(--brand);color:#fff;border:0;border-radius:999px;
    padding:13px 24px;font-size:15px;font-weight:800;
    font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;
    transition:background .15s;
  }
  .btn:hover{background:var(--brand-deep)}
  .btn i{font-size:14px}
  .err{color:#dc2626;font-size:12.5px;margin-top:6px;display:block}

  /* ====== RESULTADOS ====== */
  .results{margin-top:22px;display:flex;flex-direction:column;gap:14px}
  .cert-item{
    background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:var(--shadow-sm);
    padding:18px 20px;display:flex;gap:16px;align-items:center;justify-content:space-between;flex-wrap:wrap;
  }
  .cert-info h3{font-family:'Plus Jakarta Sans',sans-serif;font-size:16px;font-weight:800;margin:0 0 6px;letter-spacing:-0.01em}
  .role-tag{
    display:inline-flex;align-items:center;gap:5px;vertical-align:middle;margin-left:8px;
    background:var(--brand-soft);color:var(--brand-deep);border-radius:999px;padding:3px 10px;
    font-family:'Lato',sans-serif;font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.03em;
  }
  .role-tag--speaker{background:#fef3c7;color:#92400e}
  .cert-metas{display:flex;flex-wrap:wrap;gap:4px 14px;font-size:13px;color:var(--ink-soft)}
  .cert-metas span{display:inline-flex;align-items:center;gap:6px}
  .cert-metas i{color:var(--brand);width:14px;text-align:center}
  .btn-dl{
    background:#16a34a;color:#fff;border-radius:999px;padding:11px 20px;font-size:14px;font-weight:800;
    font-family:'Plus Jakarta Sans',sans-serif;display:inline-flex;align-items:center;gap:8px;white-space:nowrap;
    transition:background .15s;
  }
  .btn-dl:hover{background:#15803d}
  .status-tag{font-size:12.5px;font-weight:700;padding:8px 14px;border-radius:10px;white-space:nowrap}
  .status-tag--wait{background:#e0f2fe;color:#075985}
  .status-tag--miss{background:#fef3c7;color:#92400e}
  .empty{background:#fff;border:1px dashed var(--line);border-radius:14px;padding:32px 20px;text-align:center;color:var(--muted);font-size:14px;margin-top:22px}
  .empty i{font-size:28px;display:block;margin-bottom:10px;color:var(--muted)}
  .foot-note{text-align:center;color:var(--muted);font-size:12.5px;margin-top:20px}

  /* ====== MOBILE NAV ====== */
  .mobile-panel{
    position:fixed;top:0;right:0;width:min(86vw,360px);height:100vh;background:#fff;z-index:60;
    transform:translateX(100%);transition:transform .3s;padding:80px 28px 28px;
    box-shadow:-20px 0 50px rgba(0,0,0,.12);display:flex;flex-direction:column;gap:6px;
  }
  .mobile-panel.open{transform:translateX(0)}
  .mobile-panel a{padding:14px 16px;border-radius:12px;font-weight:700;color:var(--ink);display:flex;align-items:center;justify-content:space-between}
  .mobile-panel a:hover{background:var(--cream)}
  .mobile-panel a.active{background:var(--yellow)}
  .scrim{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:55;opacity:0;pointer-events:none;transition:opacity .25s}
  .scrim.open{opacity:1;pointer-events:auto}
  .mobile-close{position:absolute;top:18px;right:18px;width:42px;height:42px;border-radius:10px;border:0;background:transparent;font-size:18px;cursor:pointer}

  /* ====== FOOTER ====== */
  .footer-banner{width:100%;display:block;background:#fff;border-top:1px solid var(--line);padding:8px 0}
  .footer-banner img{width:100%;display:block;height:auto;filter:invert(1) hue-rotate(180deg) saturate(1.4) contrast(1.05)}
  .footer-bar{background:var(--brand);color:rgba(255,255,255,.92);padding:22px 32px;display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:16px;font-size:13.5px}
  .footer-bar__brand{display:flex;align-items:center;gap:12px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:14.5px;color:#fff}
  .footer-bar__brand small{display:block;font-weight:600;font-size:11px;letter-spacing:.08em;text-transform:uppercase;opacity:.78;font-family:'Lato',sans-serif}
  .footer-bar__copy{text-align:center;color:rgba(255,255,255,.85)}

  /* ====== RESPONSIVE ====== */
  @media(max-width:980px){
    .nav{display:none}
    .menu-toggle{display:grid;place-items:center}
    .page-body{padding:32px 20px 60px}
    .page-hero{padding:100px 20px 48px}
  }
  @media(max-width:640px){
    .topbar{padding:12px 16px}
    .footer-bar{grid-template-columns:1fr;text-align:center;justify-items:center}
    .cert-item{align-items:flex-start}
  }
</style>
</head>
<body>

<!-- ====== TOP NAV ====== -->
<header class="topbar">
  <a class="brand" href="{{ route('home') }}">
    <span class="brand-mark"><img src="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}" alt="Empreende Vitória" style="width:28px;height:28px;object-fit:contain"></span>
    <span>
      Empreende Vitória
      <small>Prefeitura · Vitória de Santo Antão</small>
    </span>
  </a>
  <nav class="nav">
    <a href="{{ route('home') }}">Home</a>
    <a href="{{ route('home') }}#sobre">O Que é?</a>
    <a href="{{ route('servicos') }}">Serviços</a>
    <a href="{{ route('empresas-locais') }}">Empresas</a>
    <a href="{{ route('home') }}#vagas">Vagas</a>
    <a href="{{ route('cursos') }}">Cursos</a>
    <a href="{{ route('public.certificates') }}" class="active">Certificados</a>
    <a href="{{ route('contato') }}">Contato</a>
    <a href="{{ route('usuario.login') }}" class="nav-login"><i class="fas fa-user-lock"></i> Entrar</a>
  </nav>
  <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">
    <i class="fas fa-bars"></i>
  </button>
</header>

<div class="scrim" id="scrim"></div>
<aside class="mobile-panel" id="mobilePanel">
  <button class="mobile-close" id="mobileClose" aria-label="Fechar menu"><i class="fas fa-times"></i></button>
  <a href="{{ route('home') }}">Home <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('home') }}#sobre">O Que é? <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('servicos') }}">Serviços <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('empresas-locais') }}">Empresas <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('home') }}#vagas">Vagas <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('cursos') }}">Cursos <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('public.certificates') }}" class="active">Certificados <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('contato') }}">Contato <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('usuario.login') }}" style="margin-top:12px;background:var(--brand);color:#fff;justify-content:center">Entrar</a>
</aside>

<!-- ====== HERO ====== -->
<section class="page-hero">
  <div class="page-hero__inner">
    <div class="breadcrumb">
      <a href="{{ route('home') }}"><i class="fas fa-home"></i> Início</a>
      <i class="fas fa-chevron-right"></i>
      <span>Certificados</span>
    </div>
    <div class="hero-badge"><span class="dot"></span> Emissão de Certificados</div>
    <h1>Baixe o certificado dos <em>seus eventos.</em></h1>
    <p>Informe o CPF para consultar e baixar seus certificados — de participação ou de palestrante.</p>
  </div>
</section>

<!-- ====== CONTENT ====== -->
<div class="page-body">

  <div class="card">
    <form action="{{ route('public.certificates') }}" method="GET" class="form">
      <div class="field">
        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" value="{{ $cpf }}"
               class="{{ $error ? 'is-error' : '' }}" placeholder="000.000.000-00" required>
        @if($error) <span class="err">{{ $error }}</span> @endif
      </div>
      <button type="submit" class="btn"><i class="fas fa-search"></i> Consultar</button>
    </form>
  </div>

  @if($results !== null)
    @if($results->isEmpty())
      <div class="empty">
        <i class="fas fa-folder-open"></i>
        Nenhum evento encontrado para este CPF, como participante ou palestrante.
      </div>
    @else
      <div class="results">
        @foreach($results as $item)
          @php
            $event = $item['event'];
            $dates = $event->allDates();
            $periodo = count($dates) > 1
                ? \Carbon\Carbon::parse($dates[0])->format('d/m/Y').' a '.\Carbon\Carbon::parse(end($dates))->format('d/m/Y')
                : $event->date->format('d/m/Y');
            $isSpeaker = $item['kind'] === 'speaker';
            $downloadUrl = $isSpeaker
                ? route('public.certificates.speaker', $event)
                : route('public.certificates.download', $item['participant']);
          @endphp
          <div class="cert-item">
            <div class="cert-info">
              <h3>
                {{ $event->title }}
                <span class="role-tag {{ $isSpeaker ? 'role-tag--speaker' : '' }}">
                  <i class="fas {{ $isSpeaker ? 'fa-chalkboard-user' : 'fa-user-check' }}"></i> {{ $item['role'] }}
                </span>
              </h3>
              <div class="cert-metas">
                <span><i class="fas fa-calendar"></i> {{ $periodo }}</span>
                @if($event->speaker && !$isSpeaker)
                  <span><i class="fas fa-user"></i> {{ $event->speaker->name }}</span>
                @endif
                <span><i class="fas fa-clock"></i> {{ number_format($event->totalHours(), 0) }}h</span>
              </div>
            </div>
            @if($item['available'])
              <a href="{{ $downloadUrl }}" class="btn-dl">
                <i class="fas fa-download"></i> Baixar certificado
              </a>
            @elseif($item['blocked'] === 'waiting')
              <span class="status-tag status-tag--wait"><i class="fas fa-hourglass-half"></i> Aguardando conclusão do evento</span>
            @else
              <span class="status-tag status-tag--miss"><i class="fas fa-exclamation-circle"></i> Presença incompleta</span>
            @endif
          </div>
        @endforeach
      </div>
    @endif
  @endif

  <p class="foot-note">O certificado fica disponível após a conclusão do evento e a confirmação de presença em todos os dias.</p>

</div>

<!-- ====== FOOTER ====== -->
<div class="footer-banner">
  <img src="{{ asset('assets/rodape002-1.png') }}"
       alt="Prefeitura da Vitória de Santo Antão">
</div>
<div class="footer-bar">
  <div class="footer-bar__brand">
    <span>
      Prefeitura da Vitória de Santo Antão
      <small>Secretaria de Desenvolvimento Econômico</small>
    </span>
  </div>
  <div class="footer-bar__copy">Desenvolvido por AGINTEC © {{ date('Y') }} — Todos os direitos reservados</div><div></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js"></script>
<script>
  var cpf = document.getElementById('cpf');
  if (cpf) IMask(cpf, { mask: '000.000.000-00' });

  const toggle = document.getElementById('menuToggle');
  const panel  = document.getElementById('mobilePanel');
  const scrim  = document.getElementById('scrim');
  const close  = document.getElementById('mobileClose');
  const openMenu  = () => { panel.classList.add('open'); scrim.classList.add('open'); };
  const closeMenu = () => { panel.classList.remove('open'); scrim.classList.remove('open'); };
  toggle.addEventListener('click', openMenu);
  scrim.addEventListener('click', closeMenu);
  close.addEventListener('click', closeMenu);
  panel.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMenu));
</script>

</body>
</html>
