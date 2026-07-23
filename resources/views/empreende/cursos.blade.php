<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cursos & Eventos — Empreende Vitória</title>
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

  /* ====== HERO CURSOS ====== */
  .cursos-hero{
    background:linear-gradient(135deg,#0763A0 0%,#044a7a 100%);
    padding:120px 32px 64px;
    color:#fff;
  }
  .cursos-hero__inner{max-width:1280px;margin:0 auto}
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
  .cursos-hero h1{
    font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;
    font-size:clamp(34px,4.5vw,58px);line-height:1.05;letter-spacing:-0.025em;
    margin:0 0 14px;max-width:18ch;
  }
  .cursos-hero h1 em{font-style:normal;color:var(--yellow)}
  .cursos-hero p{font-size:16.5px;color:rgba(255,255,255,.85);max-width:56ch;margin:0 0 32px}
  .search-bar{
    display:flex;align-items:center;gap:0;
    background:#fff;border-radius:14px;
    max-width:640px;
    box-shadow:0 8px 32px rgba(0,0,0,.18);
    overflow:hidden;
  }
  .search-bar i{padding:0 16px;color:var(--muted);font-size:15px;flex:none}
  .search-bar input{
    flex:1;border:0;outline:none;padding:16px 0;
    font-size:15px;font-family:'Lato',sans-serif;color:var(--ink);
  }
  .search-bar input::placeholder{color:var(--muted)}
  .search-bar button{
    background:var(--brand);color:#fff;border:0;cursor:pointer;
    padding:0 28px;height:54px;
    font-weight:800;font-size:14px;font-family:'Lato',sans-serif;
    transition:background .15s;
    white-space:nowrap;
  }
  .search-bar button:hover{background:var(--brand-deep)}

  /* ====== TABS ====== */
  .tabs-wrap{
    background:#fff;border-bottom:1px solid var(--line);
    position:sticky;top:64px;z-index:40;
  }
  .tabs{max-width:1280px;margin:0 auto;padding:0 32px;display:flex;gap:4px;
    overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;}
  .tabs::-webkit-scrollbar{display:none}
  .tab-btn{
    padding:16px 22px;border:0;background:none;cursor:pointer;
    font-family:'Lato',sans-serif;font-weight:700;font-size:14px;color:var(--muted);
    border-bottom:3px solid transparent;transition:color .15s,border-color .15s;
    white-space:nowrap;
  }
  .tab-btn:hover{color:var(--ink)}
  .tab-btn.active{color:var(--brand);border-bottom-color:var(--brand)}

  /* ====== MAIN CONTENT ====== */
  .page-body{max-width:1280px;margin:0 auto;padding:52px 32px 80px}

  /* ====== FEATURED EVENT ====== */
  .featured-card{
    display:grid;grid-template-columns:1fr 1fr;
    gap:0;border-radius:20px;overflow:hidden;
    border:1px solid var(--line);box-shadow:var(--shadow-md);
    margin-bottom:56px;
    background:#fff;
  }
  .featured-img{
    position:relative;min-height:320px;background:linear-gradient(135deg,var(--brand) 0%,var(--brand-deep) 100%);
    display:flex;align-items:center;justify-content:center;overflow:hidden;
  }
  .featured-img img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0}
  .featured-img__text{
    position:relative;z-index:2;color:#fff;font-family:'Plus Jakarta Sans',sans-serif;
    font-weight:800;font-size:26px;text-align:center;padding:32px;
    text-shadow:0 2px 12px rgba(0,0,0,.3);
  }
  .featured-date-badge{
    position:absolute;bottom:20px;left:20px;z-index:3;
    background:#fff;border-radius:12px;padding:10px 16px;
    font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;
    color:var(--ink);text-align:center;box-shadow:var(--shadow-sm);
    font-size:15px;
  }
  .featured-date-badge span{display:block;font-size:26px;line-height:1;margin-bottom:2px}
  .featured-info{padding:36px 40px;display:flex;flex-direction:column;justify-content:center}
  .featured-meta{font-size:12px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:var(--brand);margin-bottom:10px}
  .featured-info h2{
    font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;
    font-size:clamp(22px,2.4vw,30px);letter-spacing:-0.02em;margin:0 0 20px;
    line-height:1.15;
  }
  .speaker-card{
    display:flex;align-items:flex-start;gap:14px;
    background:var(--cream);border-radius:12px;padding:14px 16px;margin-bottom:20px;
  }
  .speaker-avatar{
    width:42px;height:42px;border-radius:50%;background:var(--brand);
    color:#fff;display:grid;place-items:center;font-weight:800;font-size:14px;flex:none;
  }
  .speaker-card h4{font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:15px;margin:0 0 3px}
  .speaker-card p{font-size:13px;color:var(--ink-soft);margin:0;line-height:1.4}
  .event-meta-row{
    display:grid;grid-template-columns:1fr 1fr;gap:10px 20px;margin-bottom:24px;font-size:14px;
  }
  .event-meta-item{display:flex;align-items:center;gap:8px;color:var(--ink-soft)}
  .event-meta-item i{color:var(--brand);width:16px;text-align:center;flex:none}
  .event-meta-item strong{color:var(--ink)}

  /* ====== EVENTS GRID ====== */
  .section-title{
    font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;
    font-size:24px;letter-spacing:-0.02em;margin:0 0 24px;
  }
  .events-grid{
    display:grid;grid-template-columns:repeat(3,1fr);gap:24px;
  }
  .event-card{
    background:#fff;border:1px solid var(--line);border-radius:18px;overflow:hidden;
    transition:transform .2s,box-shadow .2s;display:flex;flex-direction:column;
  }
  .event-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-md)}
  .event-card__img{
    position:relative;height:180px;
    background:linear-gradient(135deg,var(--brand) 0%,var(--brand-deep) 100%);
    display:flex;align-items:center;justify-content:center;overflow:hidden;
  }
  .event-card__img--green{background:linear-gradient(135deg,#1a7a4a 0%,#0f5c35 100%)}
  .event-card__img--purple{background:linear-gradient(135deg,#6b3fa0 0%,#4a2b72 100%)}
  .event-card__img img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0}
  .event-card__img-title{
    position:relative;z-index:2;color:#fff;font-family:'Plus Jakarta Sans',sans-serif;
    font-weight:800;font-size:18px;text-align:center;padding:20px;
    text-shadow:0 2px 8px rgba(0,0,0,.3);
  }
  .event-date-badge{
    position:absolute;top:12px;left:12px;z-index:3;
    background:#fff;border-radius:10px;padding:6px 12px;
    font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;color:var(--ink);
    text-align:center;box-shadow:var(--shadow-sm);
  }
  .event-date-badge .month{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--brand)}
  .event-date-badge .day{font-size:22px;line-height:1.1}
  .event-card__body{padding:20px;flex:1;display:flex;flex-direction:column}
  .event-card h3{
    font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:16px;
    margin:0 0 10px;line-height:1.3;
  }
  .event-card__speaker{display:flex;align-items:center;gap:10px;margin-bottom:12px}
  .mini-avatar{
    width:30px;height:30px;border-radius:50%;background:var(--brand);
    color:#fff;display:grid;place-items:center;font-size:11px;font-weight:800;flex:none;
  }
  .event-card__speaker-name{font-size:13px;font-weight:700;color:var(--ink)}
  .event-card__speaker-bio{font-size:12px;color:var(--muted)}
  .event-card__metas{display:flex;flex-direction:column;gap:4px;margin-bottom:14px}
  .event-card__meta{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--ink-soft)}
  .event-card__meta i{color:var(--brand);width:14px;text-align:center;flex:none;font-size:12px}
  .event-card__footer{margin-top:auto;display:flex;align-items:center;justify-content:space-between;padding-top:14px;border-top:1px solid var(--line)}
  .spots-info{font-size:13px;font-weight:700;color:var(--ink)}
  .spots-info span{color:var(--brand)}
  .spots-full{color:#e53e3e}
  .btn-inscricao{
    display:inline-flex;align-items:center;gap:6px;
    background:var(--brand);color:#fff;border:0;cursor:pointer;
    padding:9px 18px;border-radius:999px;font-weight:800;font-size:13px;
    font-family:'Lato',sans-serif;transition:background .15s;white-space:nowrap;
  }
  .btn-inscricao:hover{background:var(--brand-deep)}
  .btn-inscricao--disabled{background:var(--cream);color:var(--muted);cursor:default;pointer-events:none}

  /* ====== STATUS BADGE ====== */
  .status-badge{
    display:inline-flex;align-items:center;gap:5px;
    padding:3px 10px;border-radius:999px;font-size:11px;font-weight:800;letter-spacing:.05em;
    text-transform:uppercase;position:absolute;top:12px;right:12px;z-index:3;
  }
  .status-badge--completed{background:rgba(0,0,0,.45);color:rgba(255,255,255,.9)}
  .status-badge--active{background:rgba(39,174,96,.9);color:#fff}

  /* ====== EMPTY STATE ====== */
  .empty-state{
    text-align:center;padding:64px 32px;color:var(--muted);
  }
  .empty-state i{font-size:48px;margin-bottom:16px;opacity:.4}
  .empty-state p{font-size:16px;margin:0}

  /* ====== TABS CONTENT ====== */
  .tab-content{display:none}
  .tab-content.active{display:block}

  /* ====== FOOTER ====== */
  .footer-banner{width:100%;display:block;background:#fff;border-top:1px solid var(--line);padding:8px 0}
  .footer-banner img{width:100%;display:block;height:auto;filter:invert(1) hue-rotate(180deg) saturate(1.4) contrast(1.05)}
  .footer-bar{background:var(--brand);color:rgba(255,255,255,.92);padding:22px 32px;display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:16px;font-size:13.5px}
  .footer-bar__brand{display:flex;align-items:center;gap:12px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:14.5px;color:#fff}
  .footer-bar__brand small{display:block;font-weight:600;font-size:11px;letter-spacing:.08em;text-transform:uppercase;opacity:.78;font-family:'Lato',sans-serif}
  .footer-bar__copy{text-align:center;color:rgba(255,255,255,.85)}

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

  /* ====== RESPONSIVE ====== */
  @media(max-width:980px){
    .nav{display:none}
    .menu-toggle{display:grid;place-items:center}
    .featured-card{grid-template-columns:1fr}
    .featured-img{min-height:220px}
    .events-grid{grid-template-columns:1fr 1fr}
    .page-body{padding:36px 20px 60px}
    .tabs{padding:0 16px}
    .cursos-hero{padding:100px 20px 48px}
  }
  @media(max-width:640px){
    .events-grid{grid-template-columns:1fr}
    .event-meta-row{grid-template-columns:1fr}
    .featured-info{padding:24px}
    .topbar{padding:12px 16px}
    .footer-bar{grid-template-columns:1fr;text-align:center;gap:10px;padding:22px 20px}
    .footer-bar__brand{justify-content:center}
  }
</style>
</head>
<body>

<!-- ====== TOP NAV ====== -->
<header class="topbar" id="topbar">
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
    <a href="{{ route('home') }}#servicos">Serviços</a>
    <a href="{{ route('home') }}#vagas">Vagas</a>
    <a href="{{ route('cursos') }}" class="active">Cursos</a>
    <a href="{{ route('public.certificates') }}">Certificados</a>
    <a href="{{ route('contato') }}">Contato</a>
    <a href="{{ route('usuario.login') }}" class="nav-login"><i class="fas fa-user-lock"></i> Entrar</a>
  </nav>
  <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">
    <i class="fas fa-bars"></i>
  </button>
</header>

<div class="scrim" id="scrim"></div>
<aside class="mobile-panel" id="mobilePanel">
  <button class="mobile-close" id="mobileClose"><i class="fas fa-times"></i></button>
  <a href="{{ route('home') }}">Home <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('home') }}#sobre">O Que é? <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('home') }}#servicos">Serviços <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('home') }}#vagas">Vagas <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('cursos') }}" class="active">Cursos <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('public.certificates') }}">Certificados <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('contato') }}">Contato <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('usuario.login') }}" style="margin-top:12px;background:var(--brand);color:#fff;justify-content:center">Entrar</a>
</aside>

<!-- ====== HERO ====== -->
<section class="cursos-hero">
  <div class="cursos-hero__inner">
    <div class="breadcrumb">
      <a href="{{ route('home') }}"><i class="fas fa-home"></i> Início</a>
      <i class="fas fa-chevron-right"></i>
      <span>Cursos & Eventos</span>
    </div>
    <div class="hero-badge"><span class="dot"></span> Capacitação</div>
    <h1>Palestras, oficinas e cursos<br>para <em>empreender melhor.</em></h1>
    <p>Agenda de eventos do Salão do Empreendedor — capacitações, workshops e palestras gratuitas para quem é MEI, quer abrir um negócio ou já tem empresa em Vitória de Santo Antão.</p>
    <div class="search-bar">
      <i class="fas fa-search"></i>
      <input type="text" id="searchInput" placeholder="Buscar evento, palestrante ou tema...">
      <button onclick="doSearch()">Buscar</button>
    </div>
  </div>
</section>

<!-- ====== TABS ====== -->
<div class="tabs-wrap">
  <div class="tabs">
    <button class="tab-btn active" data-tab="proximos">Próximos eventos</button>
    <button class="tab-btn" data-tab="semana">Esta semana</button>
    <button class="tab-btn" data-tab="mes">Este mês</button>
    <button class="tab-btn" data-tab="realizados">Realizados</button>
  </div>
</div>

<!-- ====== SEARCH RESULTS ====== -->
<div class="page-body" id="searchResultsWrap" style="display:none;padding-bottom:0">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
    <h2 class="section-title" style="margin:0">Resultados para: <em id="searchLabel" style="color:var(--brand);font-style:normal"></em></h2>
    <button onclick="clearSearch()" style="background:none;border:1.5px solid var(--line);border-radius:999px;padding:8px 18px;font-size:13px;font-weight:700;cursor:pointer;color:var(--ink-soft)">
      <i class="fas fa-times"></i> Limpar busca
    </button>
  </div>
  <div class="events-grid" id="searchResultsGrid"></div>
  <div class="empty-state" id="searchEmpty" style="display:none">
    <i class="fas fa-search"></i>
    <p>Nenhum evento encontrado para esta busca.</p>
  </div>
</div>

<!-- ====== CONTENT ====== -->
<div class="page-body" id="mainContent">

  <!-- TAB: Próximos eventos -->
  <div class="tab-content active" id="tab-proximos">
    @if($featured)
      <div class="featured-card">
        <div class="featured-img">
          @if($featured->image_url)
            <img src="{{ $featured->image_url }}" alt="{{ $featured->title }}">
          @endif
          <div class="featured-img__text">{{ $featured->title }}</div>
          @php
            $allDates   = $featured->allDates();
            $firstDate  = \Carbon\Carbon::parse($allDates[0]);
            $lastDate   = \Carbon\Carbon::parse(end($allDates));
            $multiDay   = count($allDates) > 1;
          @endphp
          <div class="featured-date-badge">
            @if($multiDay)
              <span style="font-size:14px;line-height:1.2">
                {{ $firstDate->format('d') }} {{ $firstDate->translatedFormat('M') }}
                &ndash;
                {{ $lastDate->format('d') }} {{ $lastDate->translatedFormat('M') }}
              </span>
              <small style="font-size:11px;font-weight:600;color:var(--muted)">{{ count($allDates) }} dias · {{ substr($featured->start_time, 0, 5) }}h</small>
            @else
              <span>{{ $firstDate->format('d') }}</span>
              {{ $firstDate->translatedFormat('M · D') }} · {{ substr($featured->start_time, 0, 5) }}h
            @endif
          </div>
        </div>
        <div class="featured-info">
          <div class="featured-meta">Auditório do Empreende</div>
          <h2>{{ $featured->title }}</h2>
          @if($featured->speaker)
            <div class="speaker-card">
              @if($featured->speaker->photoUrl())
                <img src="{{ $featured->speaker->photoUrl() }}" class="speaker-avatar" style="object-fit:cover;padding:0">
              @else
                <div class="speaker-avatar">{{ strtoupper(substr($featured->speaker->name, 0, 1)) }}{{ strtoupper(substr(strstr($featured->speaker->name, ' ') ?: ' ', 1, 1)) }}</div>
              @endif
              <div>
                <h4>{{ $featured->speaker->name }}</h4>
                @if($featured->speaker->bio)
                  <p>{{ Str::limit($featured->speaker->bio, 120) }}</p>
                @endif
              </div>
            </div>
          @endif
          <div class="event-meta-row">
            <div class="event-meta-item">
              <i class="fas fa-clock"></i>
              <span><strong>{{ substr($featured->start_time, 0, 5) }}h</strong> às <strong>{{ $featured->endTime() }}h</strong></span>
            </div>
            <div class="event-meta-item">
              <i class="fas fa-users"></i>
              <span><strong>{{ $featured->max_capacity }}</strong> participantes</span>
            </div>
            <div class="event-meta-item">
              <i class="fas fa-calendar-alt"></i>
              @if($multiDay)
                <span>{{ $firstDate->translatedFormat('d/m') }} a {{ $lastDate->translatedFormat('d/m/Y') }}</span>
              @else
                <span>{{ $firstDate->translatedFormat('l, d/m/Y') }}</span>
              @endif
            </div>
            <div class="event-meta-item">
              <i class="fas fa-ticket-alt"></i>
              @if($featured->isFull())
                <span style="color:#e53e3e;font-weight:700">Esgotado</span>
              @else
                <span><strong>{{ $featured->availableSpots() }}</strong> vagas livres</span>
              @endif
            </div>
          </div>
          <a href="{{ route('usuario.login', ['evento' => $featured->id]) }}" class="btn-inscricao" style="align-self:flex-start">
            <i class="fas fa-check"></i> Garantir minha vaga
          </a>
        </div>
      </div>
    @endif

    @if($upcoming->count() > 0)
      <h2 class="section-title">{{ $featured ? 'Todos os próximos eventos' : 'Próximos eventos' }}</h2>
      <div class="events-grid" id="eventsGrid">
        @foreach($upcoming as $event)
          @include('partials.event-card', ['event' => $event, 'showActions' => true])
        @endforeach
      </div>
    @else
      <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <p>Nenhum evento programado no momento.<br>Acompanhe a agenda em breve!</p>
      </div>
    @endif
  </div>

  <!-- TAB: Esta semana -->
  <div class="tab-content" id="tab-semana">
    @if($thisWeek->count() > 0)
      <div class="events-grid">
        @foreach($thisWeek as $event)
          @include('partials.event-card', ['event' => $event, 'showActions' => true])
        @endforeach
      </div>
    @else
      <div class="empty-state">
        <i class="fas fa-calendar-week"></i>
        <p>Nenhum evento esta semana.</p>
      </div>
    @endif
  </div>

  <!-- TAB: Este mês -->
  <div class="tab-content" id="tab-mes">
    @if($thisMonth->count() > 0)
      <div class="events-grid">
        @foreach($thisMonth as $event)
          @include('partials.event-card', ['event' => $event, 'showActions' => true])
        @endforeach
      </div>
    @else
      <div class="empty-state">
        <i class="fas fa-calendar"></i>
        <p>Nenhum evento este mês.</p>
      </div>
    @endif
  </div>

  <!-- TAB: Realizados -->
  <div class="tab-content" id="tab-realizados">
    @if($completed->count() > 0)
      <div class="events-grid">
        @foreach($completed as $event)
          @include('partials.event-card', ['event' => $event, 'showActions' => false])
        @endforeach
      </div>
    @else
      <div class="empty-state">
        <i class="fas fa-history"></i>
        <p>Nenhum evento realizado ainda.</p>
      </div>
    @endif
  </div>

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

<script>
  // Tabs
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
      btn.classList.add('active');
      document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
    });
  });

  // Search
  const searchResultsWrap = document.getElementById('searchResultsWrap');
  const mainContent       = document.getElementById('mainContent');
  const searchResultsGrid = document.getElementById('searchResultsGrid');
  const searchEmpty       = document.getElementById('searchEmpty');
  const searchLabel       = document.getElementById('searchLabel');

  function doSearch() {
    var q = document.getElementById('searchInput').value.toLowerCase().trim();
    if (!q) { clearSearch(); return; }

    var allCards = document.querySelectorAll('#mainContent .event-card');
    var seen = new Set();
    var matches = [];
    allCards.forEach(function(card) {
      var id = card.dataset.eventId;
      if (!seen.has(id) && card.innerText.toLowerCase().includes(q)) {
        seen.add(id);
        matches.push(card);
      }
    });

    searchLabel.textContent = '"' + document.getElementById('searchInput').value.trim() + '"';
    searchResultsGrid.innerHTML = '';
    if (matches.length > 0) {
      matches.forEach(function(card) {
        searchResultsGrid.appendChild(card.cloneNode(true));
      });
      searchResultsGrid.style.display = '';
      searchEmpty.style.display = 'none';
    } else {
      searchResultsGrid.style.display = 'none';
      searchEmpty.style.display = '';
    }

    searchResultsWrap.style.display = '';
    mainContent.style.display = 'none';
  }

  function clearSearch() {
    document.getElementById('searchInput').value = '';
    searchResultsWrap.style.display = 'none';
    mainContent.style.display = '';
  }

  document.getElementById('searchInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') doSearch();
    if (e.key === 'Escape') clearSearch();
  });

  document.getElementById('searchInput').addEventListener('input', function() {
    if (this.value.trim() === '') clearSearch();
  });

  // Mobile nav
  const toggle = document.getElementById('menuToggle');
  const panel = document.getElementById('mobilePanel');
  const scrim = document.getElementById('scrim');
  const close = document.getElementById('mobileClose');
  const openMenu = () => { panel.classList.add('open'); scrim.classList.add('open'); };
  const closeMenu = () => { panel.classList.remove('open'); scrim.classList.remove('open'); };
  toggle.addEventListener('click', openMenu);
  scrim.addEventListener('click', closeMenu);
  close.addEventListener('click', closeMenu);
  panel.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMenu));
</script>
</body>
</html>
