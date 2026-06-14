<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Empreende Vitória — Prefeitura da Vitória de Santo Antão</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script>tailwind = { config: { corePlugins: { preflight: false } } }</script>
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
<script src="https://unpkg.com/imask"></script>
<style>
  :root{
    --brand: #0763A0;
    --brand-deep: #044a7a;
    --brand-soft: #e8f1f9;
    --yellow: #ffc52d;
    --ink: #0c1822;
    --ink-soft: #38475a;
    --muted: #6c7a8a;
    --paper: #ffffff;
    --cream: #f3f6fa;
    --line: rgba(12,24,34,.08);
    --shadow-sm: 0 1px 2px rgba(12,24,34,.06), 0 2px 8px rgba(12,24,34,.04);
    --shadow-md: 0 8px 28px rgba(12,24,34,.10);
    --radius: 14px;
  }

  *{box-sizing:border-box}
  html{scroll-behavior:smooth}
  html,body{margin:0;padding:0}
  body{
    font-family:'Lato', system-ui, sans-serif;
    color:var(--ink);
    background:var(--paper);
    -webkit-font-smoothing:antialiased;
    line-height:1.55;
  }
  img{max-width:100%;display:block}
  a{color:inherit;text-decoration:none}

  /* ============ HEADER / NAV ============ */
  .topbar{
    position:fixed;
    top:0;left:0;right:0;
    z-index:50;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:14px 32px;
    transition:background .25s ease, box-shadow .25s ease, padding .25s ease, color .25s ease;
    color:#fff;
  }
  .topbar::before{
    content:"";
    position:absolute;inset:0;
    background:linear-gradient(180deg, rgba(0,0,0,.55) 0%, rgba(0,0,0,0) 100%);
    pointer-events:none;
    transition:opacity .25s ease;
    z-index:-1;
  }
  .topbar.scrolled{
    background:rgba(255,255,255,.96);
    backdrop-filter:saturate(140%) blur(8px);
    -webkit-backdrop-filter:saturate(140%) blur(8px);
    box-shadow:var(--shadow-sm);
    color:var(--ink);
    padding:10px 32px;
  }
  .topbar.scrolled::before{opacity:0}

  .brand{
    display:flex;align-items:center;gap:10px;
    font-family:'Plus Jakarta Sans', sans-serif;
    font-weight:800;
    letter-spacing:-0.01em;
    font-size:18px;
  }
  .brand-mark{
    width:36px;height:36px;border-radius:10px;
    background:var(--brand);
    color:#fff;
    display:grid;place-items:center;
    font-weight:900;font-size:14px;
    box-shadow:inset 0 0 0 2px rgba(255,255,255,.15);
  }
  .brand small{
    display:block;
    font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;
    opacity:.75;
    font-family:'Lato', sans-serif;
  }

  .nav{display:flex;align-items:center;gap:4px}
  .nav a{
    padding:8px 14px;
    border-radius:999px;
    font-weight:700;
    font-size:14px;
    transition:background .15s ease, color .15s ease;
  }
  .nav a:hover{background:rgba(255,255,255,.16)}
  .topbar.scrolled .nav a:hover{background:rgba(7,99,160,.08); color:var(--brand)}
  .nav a.active{
    background:var(--yellow);
    color:var(--ink) !important;
  }
  .nav a.nav-login{
    margin-left:8px;
    border:1.5px solid rgba(255,255,255,.45);
    padding:7px 16px;
  }
  .nav a.nav-login i{font-size:12px;margin-right:4px}
  .topbar.scrolled .nav a.nav-login{
    border-color:var(--brand);
    color:var(--brand);
  }
  .topbar.scrolled .nav a.nav-login:hover{
    background:var(--brand);
    color:#fff !important;
  }

  .menu-toggle{
    display:none;
    background:none;border:0;color:inherit;
    width:42px;height:42px;border-radius:10px;
    font-size:18px;cursor:pointer;
  }
  .menu-toggle:hover{background:rgba(255,255,255,.15)}

  /* ============ HERO / BANNER ============ */
  .hero{
    position:relative;
    width:100%;
    height:min(78vh, 720px);
    min-height:520px;
    overflow:hidden;
    background:#03192b;
  }
  .hero__img{
    position:absolute;inset:0;
    width:100%;height:100%;
    object-fit:cover;
    object-position:center;
  }
  .hero__overlay{
    position:absolute;inset:0;
    background:
      linear-gradient(180deg, rgba(4,30,55,.15) 0%, rgba(4,30,55,0) 30%, rgba(4,30,55,.55) 100%),
      linear-gradient(90deg, rgba(4,30,55,.55) 0%, rgba(4,30,55,0) 55%);
  }
  .hero__content{
    position:relative;
    height:100%;
    max-width:1280px;
    margin:0 auto;
    padding:0 32px 64px;
    display:flex;
    flex-direction:column;
    justify-content:flex-end;
    color:#fff;
  }
  .hero__eyebrow{
    display:inline-flex;align-items:center;gap:8px;
    background:rgba(255,255,255,.12);
    backdrop-filter:blur(8px);
    border:1px solid rgba(255,255,255,.25);
    padding:6px 14px;border-radius:999px;
    font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
    width:fit-content;
    margin-bottom:18px;
  }
  .hero__eyebrow .dot{width:6px;height:6px;border-radius:50%;background:var(--yellow)}
  .hero__title{
    font-family:'Plus Jakarta Sans', sans-serif;
    font-weight:800;
    font-size:clamp(40px, 5.6vw, 76px);
    line-height:1.02;
    letter-spacing:-0.025em;
    margin:0 0 14px;
    max-width:14ch;
    text-wrap:balance;
  }
  .hero__title em{
    font-style:normal;
    color:var(--yellow);
  }
  .hero__sub{
    font-size:clamp(16px, 1.4vw, 19px);
    max-width:56ch;
    color:rgba(255,255,255,.88);
    margin:0 0 28px;
    text-wrap:pretty;
  }
  .hero__cta-row{
    display:flex;flex-wrap:wrap;gap:12px;
  }

  /* ============ BUTTONS ============ */
  .btn{
    display:inline-flex;align-items:center;gap:10px;
    padding:14px 22px;
    border-radius:999px;
    font-weight:800;
    font-size:15px;
    letter-spacing:.01em;
    border:0;cursor:pointer;
    transition:transform .15s ease, box-shadow .15s ease, background .15s ease;
    font-family:inherit;
  }
  .btn i{font-size:14px}
  .btn--primary{
    background:var(--yellow);
    color:var(--ink);
    box-shadow:0 6px 18px rgba(255,197,45,.35);
  }
  .btn--primary:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(255,197,45,.45)}
  .btn--ghost{
    background:rgba(255,255,255,.12);
    color:#fff;
    border:1px solid rgba(255,255,255,.35);
    backdrop-filter:blur(6px);
  }
  .btn--ghost:hover{background:rgba(255,255,255,.2)}
  .btn--dark{
    background:var(--brand);
    color:#fff;
  }
  .btn--dark:hover{background:var(--brand-deep)}
  .btn--outline{
    background:transparent;
    color:#fff;
    border:1.5px solid #fff;
  }
  .btn--outline:hover{background:rgba(255,255,255,.15)}

  /* ============ ACTION TILES ============ */
  .actions{
    max-width:1280px;
    margin:-72px auto 0;
    padding:0 32px;
    position:relative;
    z-index:5;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
  }
  .tile{
    background:#fff;
    border:1px solid var(--line);
    border-radius:18px;
    padding:28px 30px;
    box-shadow:var(--shadow-md);
    display:flex;align-items:center;gap:22px;
    transition:transform .2s ease, box-shadow .2s ease;
    position:relative;
    overflow:hidden;
  }
  .tile:hover{
    transform:translateY(-3px);
    box-shadow:0 18px 40px rgba(14,26,19,.14);
  }
  .tile__icon{
    flex:none;
    width:64px;height:64px;
    border-radius:18px;
    display:grid;place-items:center;
    font-size:24px;
    background:linear-gradient(135deg, var(--brand) 0%, var(--brand-deep) 100%);
    color:#fff;
  }
  .tile--alt .tile__icon{
    background:linear-gradient(135deg, var(--yellow) 0%, #e8a900 100%);
    color:var(--ink);
  }
  .tile__body{flex:1;min-width:0}
  .tile__kicker{
    font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
    color:var(--muted);
    margin-bottom:4px;
  }
  .tile__title{
    font-family:'Plus Jakarta Sans', sans-serif;
    font-weight:700;font-size:20px;
    margin:0 0 4px;
    letter-spacing:-0.01em;
  }
  .tile__desc{
    font-size:14px;color:var(--ink-soft);margin:0;
  }
  .tile__arrow{
    flex:none;
    width:42px;height:42px;border-radius:50%;
    border:1px solid var(--line);
    display:grid;place-items:center;
    color:var(--ink-soft);
    transition:background .15s ease, color .15s ease, border-color .15s ease;
  }
  .tile:hover .tile__arrow{
    background:var(--ink);
    color:#fff;
    border-color:var(--ink);
  }

  /* ============ SECTION ============ */
  .section{
    max-width:1280px;
    margin:0 auto;
    padding:96px 32px;
  }
  .section--cream{
    background:var(--cream);
    max-width:none;
    padding-left:0;padding-right:0;
  }
  .section--cream > .inner{
    max-width:1280px;
    margin:0 auto;
    padding:96px 32px;
  }

  .section-head{
    display:flex;flex-direction:column;gap:10px;
    margin-bottom:48px;
    max-width:780px;
  }
  .section-eyebrow{
    font-size:12px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;
    color:var(--brand);
  }
  .section-title{
    font-family:'Plus Jakarta Sans', sans-serif;
    font-weight:800;
    font-size:clamp(30px, 3.4vw, 46px);
    line-height:1.08;
    letter-spacing:-0.02em;
    margin:0;
    text-wrap:balance;
  }

  .two-col{
    display:grid;
    grid-template-columns:1.05fr 1fr;
    gap:64px;
    align-items:start;
  }
  .two-col p{
    margin:0 0 16px;
    color:var(--ink-soft);
    font-size:16.5px;
  }
  .two-col p strong{color:var(--ink)}
  .two-col .photo{
    border-radius:20px;
    overflow:hidden;
    box-shadow:var(--shadow-md);
    aspect-ratio: 16/10;
    background:#ddd;
  }
  .two-col .photo img{width:100%;height:100%;object-fit:cover}

  .info-card{
    margin-top:24px;
    padding:20px 22px;
    border-radius:14px;
    background:#fff;
    border:1px solid var(--line);
    display:flex;align-items:center;gap:16px;
  }
  .info-card .pin{
    width:44px;height:44px;border-radius:12px;
    background:var(--brand);color:#fff;
    display:grid;place-items:center;flex:none;
  }
  .info-card .label{font-size:12px;color:var(--muted);font-weight:700;letter-spacing:.08em;text-transform:uppercase}
  .info-card .addr{font-weight:700;color:var(--ink);font-size:15px;margin-top:2px}

  /* ============ SERVICES ============ */
  .services{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
  }
  .svc-card{
    background:#fff;
    border:1px solid var(--line);
    border-radius:18px;
    padding:30px 32px;
    display:flex;flex-direction:column;
  }
  .svc-card__head{
    display:flex;align-items:center;gap:14px;
    margin-bottom:22px;
  }
  .svc-card__ico{
    width:48px;height:48px;border-radius:14px;
    background:var(--brand-soft);color:var(--brand);
    display:grid;place-items:center;font-size:18px;flex:none;
  }
  .svc-card--alt .svc-card__ico{background:#fff5d6;color:#a06900}
  .svc-card__kicker{
    font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;
    color:var(--muted);margin-bottom:2px;
  }
  .svc-card h3{
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:22px;font-weight:800;margin:0;letter-spacing:-0.01em;
  }
  .svc-list{
    list-style:none;padding:0;margin:0;
    display:grid;grid-template-columns:1fr;gap:2px;
  }
  .svc-list li{
    display:flex;align-items:flex-start;gap:12px;
    padding:11px 0;
    border-bottom:1px dashed var(--line);
    font-size:15px;
    color:var(--ink);
  }
  .svc-list li:last-child{border-bottom:0}
  .svc-list li .bullet{
    width:22px;height:22px;border-radius:50%;
    background:var(--brand-soft);color:var(--brand);
    display:grid;place-items:center;font-size:10px;flex:none;
    margin-top:1px;
  }
  .svc-list li .badge{
    margin-left:auto;
    font-size:11px;font-weight:700;letter-spacing:.04em;
    background:var(--yellow);color:var(--ink);
    padding:3px 9px;border-radius:999px;
    white-space:nowrap;
  }
  .svc-list li .partner{
    margin-left:auto;
    font-size:11px;font-weight:700;
    color:var(--muted);
    background:#f3f6fa;
    padding:3px 9px;border-radius:999px;
    text-transform:uppercase;letter-spacing:.06em;
  }

  .booking{
    margin-top:22px;
    display:grid;
    grid-template-columns:auto 1fr auto;
    align-items:center;gap:22px;
    background:linear-gradient(135deg, var(--brand) 0%, var(--brand-deep) 100%);
    color:#fff;
    border-radius:18px;
    padding:24px 28px;
  }
  .booking__ico{
    width:54px;height:54px;border-radius:16px;
    background:rgba(255,255,255,.16);
    display:grid;place-items:center;font-size:20px;
    border:1px solid rgba(255,255,255,.25);
  }
  .booking h4{
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:20px;font-weight:800;margin:0 0 4px;letter-spacing:-0.01em;
  }
  .booking p{margin:0;color:rgba(255,255,255,.85);font-size:14.5px}

  /* ============ VAGAS ============ */
  .jobs-paths{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
    margin-bottom:48px;
  }
  .jobs-card{
    position:relative;
    background:#fff;
    border:1px solid var(--line);
    border-radius:18px;
    padding:32px;
    overflow:hidden;
    transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    display:flex;flex-direction:column;
  }
  .jobs-card:hover{
    transform:translateY(-3px);
    box-shadow:0 18px 40px rgba(12,24,34,.10);
    border-color:transparent;
  }
  .jobs-card--dark{
    background:linear-gradient(135deg, var(--brand) 0%, var(--brand-deep) 100%);
    color:#fff;
    border-color:transparent;
  }
  .jobs-card__tag{
    font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;
    color:var(--brand);
    margin-bottom:10px;
  }
  .jobs-card--dark .jobs-card__tag{color:var(--yellow)}
  .jobs-card h3{
    font-family:'Plus Jakarta Sans', sans-serif;
    font-size:26px;font-weight:800;letter-spacing:-0.02em;
    margin:0 0 10px;
    line-height:1.1;
  }
  .jobs-card p{
    margin:0 0 22px;
    color:var(--ink-soft);
    font-size:15.5px;
  }
  .jobs-card--dark p{color:rgba(255,255,255,.85)}
  .jobs-card ul{
    list-style:none;padding:0;margin:0 0 24px;
    display:flex;flex-direction:column;gap:8px;
    font-size:14.5px;
  }
  .jobs-card ul li{display:flex;align-items:center;gap:10px}
  .jobs-card ul li i{
    width:20px;height:20px;border-radius:50%;
    background:var(--brand-soft);color:var(--brand);
    display:grid;place-items:center;font-size:9px;flex:none;
  }
  .jobs-card--dark ul li i{background:rgba(255,255,255,.15);color:var(--yellow)}
  .jobs-card .btn{margin-top:auto;align-self:flex-start}

  /* ============ FEATURE GRID ============ */
  .features{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:18px;
    margin-top:36px;
  }
  .feat{
    background:#fff;
    border:1px solid var(--line);
    border-radius:14px;
    padding:24px;
  }
  .feat__ico{
    width:44px;height:44px;border-radius:12px;
    background:var(--brand-soft);
    color:var(--brand);
    display:grid;place-items:center;
    font-size:18px;
    margin-bottom:14px;
  }
  .feat h4{
    font-family:'Plus Jakarta Sans', sans-serif;
    font-size:17px;font-weight:700;margin:0 0 6px;letter-spacing:-0.01em;
  }
  .feat p{margin:0;color:var(--ink-soft);font-size:14.5px}

  /* ============ FOOTER ============ */
  .footer-banner{
    width:100%;
    display:block;
    background:#ffffff;
    border-top:1px solid var(--line);
    padding:8px 0;
  }
  .footer-banner img{
    width:100%;
    display:block;
    height:auto;
    filter: invert(1) hue-rotate(180deg) saturate(1.4) contrast(1.05);
  }
  .footer-bar{background:var(--brand);color:rgba(255,255,255,.92);padding:22px 32px;display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:16px;font-size:13.5px}
  .footer-bar__brand{
    display:flex;align-items:center;gap:12px;
    font-family:'Plus Jakarta Sans',sans-serif;
    font-weight:800;
    font-size:14.5px;
    letter-spacing:.01em;
    color:#fff;
  }
  .footer-bar__brand small{
    display:block;
    font-weight:600;font-size:11px;
    letter-spacing:.08em;text-transform:uppercase;
    opacity:.78;
    font-family:'Lato',sans-serif;
  }
  .footer-bar__copy{text-align:center;color:rgba(255,255,255,.85)}
  @media (max-width: 720px){
    .footer-bar{grid-template-columns:1fr;text-align:center}
    .footer-bar__copy{order:2}
  }

  /* ============ MOBILE NAV ============ */
  .mobile-panel{
    position:fixed;top:0;right:0;
    width:min(86vw,360px);height:100vh;
    background:#fff;
    z-index:60;
    transform:translateX(100%);
    transition:transform .3s ease;
    padding:80px 28px 28px;
    box-shadow:-20px 0 50px rgba(0,0,0,.12);
    display:flex;flex-direction:column;gap:6px;
  }
  .mobile-panel.open{transform:translateX(0)}
  .mobile-panel a{
    padding:14px 16px;
    border-radius:12px;
    font-weight:700;color:var(--ink);
    display:flex;align-items:center;justify-content:space-between;
  }
  .mobile-panel a:hover{background:var(--cream)}
  .mobile-panel a.active{background:var(--yellow)}
  .scrim{
    position:fixed;inset:0;
    background:rgba(0,0,0,.4);
    z-index:55;
    opacity:0;pointer-events:none;
    transition:opacity .25s;
  }
  .scrim.open{opacity:1;pointer-events:auto}
  .mobile-close{
    position:absolute;top:18px;right:18px;
    width:42px;height:42px;border-radius:10px;
    border:0;background:transparent;font-size:18px;cursor:pointer;
  }
  .mobile-close:hover{background:var(--cream)}

  /* ============ RESPONSIVE ============ */
  @media (max-width: 980px){
    .nav{display:none}
    .menu-toggle{display:grid;place-items:center}
    .actions{grid-template-columns:1fr; margin-top:-48px}
    .two-col{grid-template-columns:1fr;gap:36px}
    .features{grid-template-columns:1fr 1fr}
    .jobs-paths{grid-template-columns:1fr}
    .services{grid-template-columns:1fr}
    .booking{grid-template-columns:1fr;text-align:center;justify-items:center}
    .booking__ico{margin:0 auto}
    .nav a.nav-login{display:none}
    .section{padding:64px 24px}
    .section--cream > .inner{padding:64px 24px}
    .topbar{padding:12px 20px}
    .topbar.scrolled{padding:10px 20px}
    .hero__content{padding:0 24px 48px}
  }
  /* ============ FEAT CAROUSEL ============ */
  .feat-carousel-wrap{position:relative}
  .feat-carousel-nav{
    display:none;
    justify-content:center;align-items:center;
    gap:10px;margin-top:22px;
  }
  .feat-carousel-btn{
    width:36px;height:36px;border-radius:50%;
    border:1.5px solid var(--line);
    background:#fff;
    display:grid;place-items:center;
    cursor:pointer;font-size:13px;
    color:var(--ink-soft);
    transition:background .15s, border-color .15s, color .15s;
  }
  .feat-carousel-btn:hover{background:var(--brand);border-color:var(--brand);color:#fff}
  .feat-carousel-dot{
    width:8px;height:8px;border-radius:50%;
    background:var(--line);
    border:0;padding:0;cursor:pointer;
    transition:background .2s, transform .2s;
  }
  .feat-carousel-dot.active{background:var(--brand);transform:scale(1.3)}

  @media (max-width: 980px){
    .features.feat-carousel{
      display:flex !important;
      overflow-x:auto;
      scroll-snap-type:x mandatory;
      scroll-behavior:smooth;
      gap:14px;
      padding-bottom:4px;
      scrollbar-width:none;
      -ms-overflow-style:none;
    }
    .features.feat-carousel::-webkit-scrollbar{display:none}
    .features.feat-carousel .feat{
      flex:0 0 78vw;
      max-width:300px;
      scroll-snap-align:start;
    }
    .feat-carousel-nav{display:flex}
  }

  @media (max-width: 560px){
    .features{grid-template-columns:1fr}
    .footer__inner{grid-template-columns:1fr}
    .tile{padding:22px;gap:16px}
    .tile__icon{width:54px;height:54px;font-size:20px;border-radius:14px}
    .tile__title{font-size:18px}
    .hero{min-height:480px;height:72vh}
    .btn{padding:12px 18px;font-size:14px}
  }
</style>
</head>
<body id="topo" x-data="{ openAgendamento: false, scheduledDate: '', scheduledTime: '' }" @date-selected.window="scheduledDate = $event.detail.date" @time-selected.window="scheduledTime = $event.detail.start" @open-agendamento.window="openAgendamento = true">

  <!-- ============ TOP NAV ============ -->
  <header class="topbar" id="topbar">
    <a class="brand" href="{{ route('home') }}">
      <span class="brand-mark"><img src="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}" alt="Empreende Vitória" style="width:28px;height:28px;object-fit:contain"></span>
      <span>
        Empreende Vitória
        <small>Prefeitura · Vitória de Santo Antão</small>
      </span>
    </a>

    <nav class="nav">
      <a href="#topo" class="active">Home</a>
      <a href="#sobre">O Que é?</a>
      <a href="#servicos">Serviços</a>
      <a href="#vagas">Vagas</a>
      <a href="{{ route('cursos') }}">Cursos</a>
      <a href="{{ route('contato') }}">Contato</a>
      <a href="{{ route('usuario.login') }}" class="nav-login"> Entrar</a>
    </nav>

    <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">
      <i class="fas fa-bars"></i>
    </button>
  </header>

  <div class="scrim" id="scrim"></div>
  <aside class="mobile-panel" id="mobilePanel">
    <button class="mobile-close" id="mobileClose" aria-label="Fechar menu"><i class="fas fa-times"></i></button>
    <a href="#topo" class="active">Home <i class="fas fa-chevron-right"></i></a>
    <a href="#sobre">O Que é? <i class="fas fa-chevron-right"></i></a>
    <a href="#servicos">Serviços <i class="fas fa-chevron-right"></i></a>
    <a href="#vagas">Vagas <i class="fas fa-chevron-right"></i></a>
    <a href="{{ route('cursos') }}">Cursos <i class="fas fa-chevron-right"></i></a>
    <a href="{{ route('contato') }}">Contato <i class="fas fa-chevron-right"></i></a>
    <a href="{{ route('usuario.login') }}" style="margin-top:12px;background:var(--brand);color:#fff;justify-content:center">Entrar</a>
  </aside>

  <!-- ============ HERO ============ -->
  <section class="hero">
    <img class="hero__img"
         src="{{ asset('assets/Capa-site-_-Empreende-Vitoria.png') }}"
         alt="Empreende Vitória — Salão do Empreendedor"
         onerror="this.style.display='none'">
    <div class="hero__overlay"></div>
    <div class="hero__content">
      <span class="hero__eyebrow"><span class="dot"></span> Salão do Empreendedor</span>
      <h1 class="hero__title">Formalize. Cresça. <em>Empreenda.</em></h1>
      <p class="hero__sub">O ambiente virtual da Prefeitura da Vitória de Santo Antão para você abrir seu MEI, encontrar oportunidades e gerenciar seu negócio com apoio do Sebrae, Amupe e Rede de Atendimento.</p>
      <div class="hero__cta-row">
        <a href="{{ route('servicos') }}" class="btn btn--primary">
          <i class="fas fa-tools"></i> Oferecer meu serviço
        </a>
        <a href="{{ route('empresas-locais') }}" class="btn btn--ghost">
          <i class="fas fa-store"></i> Empresas locais
        </a>
      </div>
    </div>
  </section>

  <!-- ============ ACTION TILES ============ -->
  <div class="actions">
    <a type="button" @click="openAgendamento = true" class="tile" style="padding: 2px 30px 28px; margin: 30px 0px 0px">
      <div class="tile__icon"><i class="fas fa-id-card"></i></div>
      <div class="tile__body">
        <div class="tile__kicker">Para empreendedores</div>
        <h3 class="tile__title">Formalize seu MEI</h3>
        <p class="tile__desc">Passo a passo gratuito para abrir sua empresa e ter CNPJ.</p>
      </div>
      <div class="tile__arrow"><i class="fas fa-arrow-right"></i></div>
    </a>
    <a href="{{ route('usuario.login') }}" class="tile tile--alt" style="margin: 30px 0px 0px">
      <div class="tile__icon"><i class="fas fa-briefcase"></i></div>
      <div class="tile__body">
        <div class="tile__kicker">Trabalho e renda</div>
        <h3 class="tile__title">Banco de Vagas</h3>
        <p class="tile__desc">Busque oportunidades de emprego ou cadastre vagas para sua empresa.</p>
      </div>
      <div  class="tile__arrow"><i class="fas fa-arrow-right"></i></div>
    </a>
  </div>

  <!-- ============ SOBRE ============ -->
  <section class="section" id="sobre">
    <div class="section-head">
      <span class="section-eyebrow">O que é o Empreende Vitória</span>
      <h2 class="section-title">Um espaço criado para apoiar quem decide empreender na Vitória de Santo Antão.</h2>
    </div>

    <div class="two-col">
      <div>
        <p>Este é o ambiente virtual do <strong>Empreende Vitória — Salão do Empreendedor</strong>, criado para auxiliar empreendedores a <strong>formalizarem seus negócios</strong> e a obterem informações sobre como gerenciá-los de forma eficiente.</p>
        <p>A iniciativa é da <strong>Prefeitura da Vitória de Santo Antão</strong>, através da Secretaria de Desenvolvimento Econômico, com apoio do <strong>Sebrae</strong>, <strong>Amupe</strong> e a <strong>Rede de Atendimento</strong>, visando incentivar o empreendedorismo e a geração de empregos.</p>
        <p>A formalização garante acesso a benefícios como aposentadoria, licença-maternidade e auxílio-doença — além de mais credibilidade junto a clientes e investidores.</p>

        <div class="info-card">
          <div class="pin"><i class="fas fa-map-marker-alt"></i></div>
          <div>
            <div class="label">Salão do Empreendedor</div>
            <div class="addr">Rua Rui Barbosa, nº 26 · Centro Comercial · Vitória de Santo Antão</div>
          </div>
        </div>
      </div>

      <div class="photo">
        <img src="https://www.prefeituradavitoria.pe.gov.br/empreendevitoria/wp-content/uploads/2023/08/222222222-1024x556.jpg"
             alt="Atendimento no Salão do Empreendedor"
             onerror="this.parentElement.style.background='repeating-linear-gradient(45deg,#e3ddd0,#e3ddd0 10px,#ede8dc 10px,#ede8dc 20px)'; this.style.display='none'">
      </div>
    </div>
  </section>

  <!-- ============ SERVIÇOS ============ -->
  <section class="section--cream" id="servicos">
    <div class="inner">
      <div class="section-head">
        <span class="section-eyebrow">Serviços</span>
        <h2 class="section-title">Tudo que o Salão do Empreendedor faz por você.</h2>
        <p style="color:var(--ink-soft);font-size:16.5px;margin:6px 0 0;max-width:62ch">
          Atendimento presencial e online para quem quer abrir, regularizar ou expandir o próprio negócio em Vitória de Santo Antão.
        </p>
      </div>

      <div class="services">
        <div class="svc-card">
          <div class="svc-card__head">
            <div class="svc-card__ico"><i class="fas fa-clipboard-list"></i></div>
            <div>
              <div class="svc-card__kicker">Atendimento direto</div>
              <h3>Serviços Oferecidos</h3>
            </div>
          </div>
          <ul class="svc-list">
            <li><span class="bullet"><i class="fas fa-check"></i></span> Orientação ao microempreendedor individual</li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> Consulta prévia municipal</li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> Formalização do MEI no Portal</li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> Emissão de carnê e pagamentos</li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> Alteração de dados cadastrais do MEI</li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> Declaração anual</li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> Orientação para acesso a Microcrédito</li>
          </ul>
        </div>

        <div class="svc-card svc-card--alt">
          <div class="svc-card__head">
            <div class="svc-card__ico"><i class="fas fa-store"></i></div>
            <div>
              <div class="svc-card__kicker">Sala do Empreendedor</div>
              <h3>Serviços para Empreendedores</h3>
            </div>
          </div>
          <ul class="svc-list">
            <li><span class="bullet"><i class="fas fa-check"></i></span> Cadastro MEI</li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> Serviços financeiros</li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> Capacitações</li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> Consultorias</li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> Emissão de DAS</li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> Coworking</li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> Sala do Empreendedor</li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> JUCEPE <span class="partner">Parceiro</span></li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> AGE — Microcrédito Popular <span class="partner">Parceiro</span></li>
            <li><span class="bullet"><i class="fas fa-check"></i></span> PAV — Receita Federal <span class="partner">Parceiro</span></li>
          </ul>
        </div>
      </div>

      <!-- Agendamento callout -->
      <div class="booking">
        <div class="booking__ico"><i class="fas fa-calendar-check"></i></div>
        <div>
          <h4>Agende seu atendimento</h4>
          <p>Agendamentos de atendimento são feitos diretamente pela dashboard de atendimento — sem fila, sem ligação.</p>
        </div>
        <button type="button" @click="openAgendamento = true" class="btn btn--primary"><i class="fas fa-calendar-plus"></i> Agendar agora</button>
      </div>
    </div>
  </section>

  <!-- ============ VAGAS ============ -->
  <section class="section" id="vagas">
    <div class="section-head">
      <span class="section-eyebrow">Trabalho e renda</span>
      <h2 class="section-title">Banco de Vagas da Vitória de Santo Antão.</h2>
      <p style="color:var(--ink-soft);font-size:16.5px;margin:6px 0 0;max-width:62ch">
        Conectamos pessoas que buscam emprego a empresas locais que precisam contratar. Acesse o ambiente certo para você abaixo.
      </p>
    </div>

    <div class="jobs-paths">
      <a href="{{ route('usuario.login') }}" class="jobs-card">
        <span class="jobs-card__tag">Para você</span>
        <h3>Estou procurando emprego</h3>
        <p>Crie seu currículo digital gratuito, inscreva-se em eventos e candidate-se às vagas abertas no município.</p>
        <ul>
          <li><i class="fas fa-check"></i> Gerador de currículo passo a passo</li>
          <li><i class="fas fa-check"></i> Inscrição em cursos e eventos</li>
          <li><i class="fas fa-check"></i> Solicite seus certificados online</li>
        </ul>
        <span class="btn btn--dark"><i class="fas fa-user"></i> Acessar área do candidato</span>
      </a>

      <a href="{{ route('empresa.login') }}" class="jobs-card jobs-card--dark">
        <span class="jobs-card__tag">Para empresas</span>
        <h3>Quero contratar profissionais</h3>
        <p>Cadastre vagas gratuitamente e encontre candidatos qualificados na Vitória de Santo Antão.</p>
        <ul>
          <li><i class="fas fa-check"></i> Cadastro e gestão de vagas em tempo real</li>
          <li><i class="fas fa-check"></i> Busca e filtro do banco de currículos</li>
          <li><i class="fas fa-check"></i> Painel de candidaturas recebidas</li>
        </ul>
        <span class="btn btn--primary"><i class="fas fa-building"></i> Acessar área da empresa</span>
      </a>
    </div>
  </section>

  <!-- ============ BENEFITS ============ -->
  <section class="section--cream">
    <div class="inner">
      <div class="section-head">
        <span class="section-eyebrow">Por que formalizar</span>
        <h2 class="section-title">Quatro motivos para dar o próximo passo no seu negócio.</h2>
      </div>

      <div class="feat-carousel-wrap">
        <div class="features feat-carousel" id="featCarousel" style="grid-template-columns:repeat(4,1fr)">
          <div class="feat">
            <div class="feat__ico"><i class="fas fa-bolt"></i></div>
            <h4>Agilidade</h4>
            <p>Processo de formalização rápido e gratuito, com atendimento presencial.</p>
          </div>
          <div class="feat">
            <div class="feat__ico"><i class="fas fa-handshake"></i></div>
            <h4>Networking</h4>
            <p>Encontre outros empresários e troque experiências que aceleram seu negócio.</p>
          </div>
          <div class="feat">
            <div class="feat__ico"><i class="fas fa-shield-alt"></i></div>
            <h4>Benefícios</h4>
            <p>Acesso a aposentadoria, licença-maternidade e auxílio-doença pelo INSS.</p>
          </div>
          <div class="feat">
            <div class="feat__ico"><i class="fas fa-chart-line"></i></div>
            <h4>Crescimento</h4>
            <p>Linhas de crédito, emissão de NF e programas de incentivo ao empreendedor.</p>
          </div>
        </div>
        <div class="feat-carousel-nav">
          <button class="feat-carousel-btn" id="featPrev" aria-label="Anterior"><i class="fas fa-chevron-left"></i></button>
          <button class="feat-carousel-dot active" data-index="0"></button>
          <button class="feat-carousel-dot" data-index="1"></button>
          <button class="feat-carousel-dot" data-index="2"></button>
          <button class="feat-carousel-dot" data-index="3"></button>
          <button class="feat-carousel-btn" id="featNext" aria-label="Próximo"><i class="fas fa-chevron-right"></i></button>
        </div>
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:40px">
        <button type="button" @click="openAgendamento = true" class="btn btn--dark"><i class="fas fa-arrow-right"></i> Quero formalizar meu MEI</button>
        <a href="https://transparencia.prefeituradavitoria.pe.gov.br/app/pe/vitoria-de-santo-antao/1/avisos-de-licitacao" target="_blank" class="btn btn--outline" style="color:var(--brand);border-color:var(--brand)"><i class="fas fa-file-alt"></i> Ver licitações para MEI</a>
      </div>
    </div>
  </section>

  <!-- ============ FOOTER OFICIAL ============ -->
  <div class="footer-banner">
    <img src="https://www.prefeituradavitoria.pe.gov.br/empreendevitoria/wp-content/uploads/2023/06/rodape002-1.png"
         alt="Prefeitura da Vitória de Santo Antão — Empreende Vitória">
  </div>
  <div class="footer-bar">
    <div class="footer-bar__brand">
      <span>
        Prefeitura da Vitória de Santo Antão
        <small>Secretaria de Desenvolvimento Econômico</small>
      </span>
    </div>
    <div class="footer-bar__copy">© {{ date('Y') }} — Todos os direitos reservados</div><div></div>
  </div>

{{-- Flash de sucesso --}}
@if(session('success'))
<div style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;background:#16a34a;color:#fff;padding:14px 24px;border-radius:12px;font-weight:700;box-shadow:0 4px 20px rgba(0,0,0,.2);font-family:'Plus Jakarta Sans',sans-serif">
    <i class="fas fa-check-circle" style="margin-right:8px"></i>{{ session('success') }}
</div>
@endif

{{-- Modal de Agendamento Público --}}
<div
    x-show="openAgendamento"
    x-cloak
    style="display:none;position:fixed;inset:0;z-index:8000;background:rgba(4,74,122,.55);backdrop-filter:blur(4px);overflow-y:auto;padding:24px 16px"
    @keydown.escape.window="openAgendamento = false">

    <div style="max-width:680px;margin:0 auto;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,.2)">

        {{-- Header --}}
        <div style="background:#0763A0;padding:20px 28px;display:flex;align-items:center;justify-content:space-between">
            <div>
                <h2 style="color:#fff;font-family:'Plus Jakarta Sans',sans-serif;font-size:1.1rem;font-weight:800;margin:0">Agendar Atendimento</h2>
                <p style="color:rgba(255,255,255,.75);font-size:.8rem;margin:4px 0 0;font-family:'Lato',sans-serif">Preencha seus dados e escolha o melhor dia e horário</p>
            </div>
            <button type="button" @click="openAgendamento = false" style="background:rgba(255,255,255,.15);border:none;color:#fff;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Formulário --}}
        <form action="{{ route('public.attendance.store') }}" method="POST" style="padding:28px">
            @csrf

            @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#b91c1c;font-size:.85rem;font-family:'Lato',sans-serif">
                <strong>Por favor, corrija os erros abaixo:</strong>
                <ul style="margin:6px 0 0;padding-left:18px">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div style="display:grid;gap:16px">

                {{-- Nome --}}
                <div>
                    <label style="display:block;font-size:.7rem;font-weight:900;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;font-family:'Plus Jakarta Sans',sans-serif">Nome completo *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                        style="width:100%;padding:14px 18px;background:#f9fafb;border:2px solid transparent;border-radius:14px;outline:none;font-weight:700;color:#1f2937;font-family:'Lato',sans-serif;box-sizing:border-box"
                        placeholder="Seu nome completo"
                        onfocus="this.style.borderColor='#0763A0';this.style.background='#fff'"
                        onblur="this.style.borderColor='transparent';this.style.background='#f9fafb'">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    {{-- CPF --}}
                    <div>
                        <label style="display:block;font-size:.7rem;font-weight:900;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;font-family:'Plus Jakarta Sans',sans-serif">CPF / CNPJ (opcional)</label>
                        <input type="text" name="customer_cpf" id="pub_cpf" value="{{ old('customer_cpf') }}"
                            style="width:100%;padding:14px 18px;background:#f9fafb;border:2px solid {{ $errors->has('customer_cpf') ? '#ef4444' : 'transparent' }};border-radius:14px;outline:none;font-weight:700;color:#1f2937;font-family:'Lato',sans-serif;box-sizing:border-box"
                            placeholder="CPF ou CNPJ"
                            onfocus="this.style.borderColor='#0763A0';this.style.background='#fff'"
                            onblur="this.style.borderColor='transparent';this.style.background='#f9fafb'">
                        @error('customer_cpf') <span style="display:block;color:#ef4444;font-size:.75rem;margin-top:4px;font-family:'Lato',sans-serif">{{ $message }}</span> @enderror
                    </div>
                    {{-- Telefone --}}
                    <div>
                        <label style="display:block;font-size:.7rem;font-weight:900;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;font-family:'Plus Jakarta Sans',sans-serif">Telefone (opcional)</label>
                        <input type="text" name="customer_phone" id="pub_phone" value="{{ old('customer_phone') }}"
                            style="width:100%;padding:14px 18px;background:#f9fafb;border:2px solid transparent;border-radius:14px;outline:none;font-weight:700;color:#1f2937;font-family:'Lato',sans-serif;box-sizing:border-box"
                            placeholder="(00) 00000-0000"
                            onfocus="this.style.borderColor='#0763A0';this.style.background='#fff'"
                            onblur="this.style.borderColor='transparent';this.style.background='#f9fafb'">
                    </div>
                </div>

                {{-- Serviço --}}
                <div>
                    <label style="display:block;font-size:.7rem;font-weight:900;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;font-family:'Plus Jakarta Sans',sans-serif">Serviço desejado *</label>
                    <select name="service_type" required
                        style="width:100%;padding:14px 18px;background:#f9fafb;border:2px solid transparent;border-radius:14px;outline:none;font-weight:700;color:#374151;font-family:'Lato',sans-serif;box-sizing:border-box">
                        <option value="" disabled {{ old('service_type') ? '' : 'selected' }}>Selecione o serviço...</option>
                        <option value="Formalização MEI"     {{ old('service_type') === 'Formalização MEI'     ? 'selected' : '' }}>Formalização MEI</option>
                        <option value="Emissão de DAS"       {{ old('service_type') === 'Emissão de DAS'       ? 'selected' : '' }}>Emissão de Guia (DAS)</option>
                        <option value="Declaração Anual (DASN)" {{ old('service_type') === 'Declaração Anual (DASN)' ? 'selected' : '' }}>Declaração Anual (DASN)</option>
                        <option value="Parcelamento de Débitos" {{ old('service_type') === 'Parcelamento de Débitos' ? 'selected' : '' }}>Parcelamento de Débitos</option>
                        <option value="Alteração Cadastral"  {{ old('service_type') === 'Alteração Cadastral'  ? 'selected' : '' }}>Alteração Cadastral</option>
                        <option value="Baixa de Empresa"     {{ old('service_type') === 'Baixa de Empresa'     ? 'selected' : '' }}>Baixa de Empresa</option>
                        <option value="Consultoria Sebrae"   {{ old('service_type') === 'Consultoria Sebrae'   ? 'selected' : '' }}>Consultoria Sebrae</option>
                        <option value="Outros"               {{ old('service_type') === 'Outros'               ? 'selected' : '' }}>Outros</option>
                    </select>
                </div>

                {{-- Descrição --}}
                <div>
                    <label style="display:block;font-size:.7rem;font-weight:900;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;font-family:'Plus Jakarta Sans',sans-serif">Descreva sua situação *</label>
                    <textarea name="description" rows="3" required
                        style="width:100%;padding:14px 18px;background:#f9fafb;border:2px solid transparent;border-radius:14px;outline:none;font-weight:600;color:#1f2937;font-family:'Lato',sans-serif;resize:vertical;box-sizing:border-box"
                        placeholder="O que você precisa resolver?"
                        onfocus="this.style.borderColor='#0763A0';this.style.background='#fff'"
                        onblur="this.style.borderColor='transparent';this.style.background='#f9fafb'">{{ old('description') }}</textarea>
                </div>

                {{-- Calendário --}}
                <div>
                    <label style="display:block;font-size:.7rem;font-weight:900;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;font-family:'Plus Jakarta Sans',sans-serif">Data e horário desejados *</label>
                    @include('bookings.partials._calendar', ['calendarFetchUrl' => route('public.attendance.availability')])
                    <input type="hidden" name="scheduled_date" x-model="scheduledDate">
                    <input type="hidden" name="scheduled_time" x-model="scheduledTime">

                    <div x-show="scheduledDate && scheduledTime"
                         style="margin-top:10px;padding:10px 16px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;font-size:.85rem;color:#1d4ed8;font-weight:700;font-family:'Lato',sans-serif">
                        <i class="fas fa-calendar-check" style="margin-right:6px"></i>
                        Agendado para <span x-text="scheduledDate ? scheduledDate.split('-').reverse().join('/') : ''"></span>
                        às <span x-text="scheduledTime"></span>
                    </div>
                </div>

                {{-- Botão --}}
                <button type="submit"
                    style="width:100%;padding:16px;background:#0763A0;color:#fff;border:none;border-radius:14px;font-weight:800;font-size:1rem;letter-spacing:.05em;text-transform:uppercase;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;transition:background .2s"
                    onmouseover="this.style.background='#044a7a'"
                    onmouseout="this.style.background='#0763A0'">
                    <i class="fas fa-calendar-check" style="margin-right:8px"></i>Confirmar Agendamento
                </button>
            </div>
        </form>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const cpf = document.getElementById('pub_cpf');
    if (cpf) IMask(cpf, {
        mask: [
            { mask: '000.000.000-00', maxLength: 11 },
            { mask: '00.000.000/0000-00' }
        ],
        dispatch: function (appended, dynamicMasked) {
            const number = (dynamicMasked.value + appended).replace(/\D/g, '');
            return number.length > 11 ? dynamicMasked.compiledMasks[1] : dynamicMasked.compiledMasks[0];
        }
    });
    const phone = document.getElementById('pub_phone');
    if (phone) IMask(phone, { mask: [{mask:'(00) 00000-0000'},{mask:'(00) 0000-0000'}] });

    @if(session('success'))
    setTimeout(function(){
        const flash = document.querySelector('[style*="position:fixed;top:20px"]');
        if(flash) flash.remove();
    }, 5000);
    @endif

    @if($errors->any())
    document.addEventListener('alpine:initialized', function(){
        window.dispatchEvent(new CustomEvent('open-agendamento'));
    });
    @endif
  });
</script>

<script>
  const topbar = document.getElementById('topbar');
  const onScroll = () => {
    if(window.scrollY > 40) topbar.classList.add('scrolled');
    else topbar.classList.remove('scrolled');
  };
  window.addEventListener('scroll', onScroll, {passive:true});
  onScroll();

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

  // ---- Feat Carousel ----
  (function(){
    var carousel = document.getElementById('featCarousel');
    if(!carousel) return;
    var cards = Array.from(carousel.querySelectorAll('.feat'));
    var dots  = Array.from(document.querySelectorAll('.feat-carousel-dot'));
    var current = 0;

    function goTo(index) {
      current = Math.max(0, Math.min(index, cards.length - 1));
      cards[current].scrollIntoView({behavior:'smooth', block:'nearest', inline:'start'});
      dots.forEach(function(d, i){ d.classList.toggle('active', i === current); });
    }

    document.getElementById('featPrev').addEventListener('click', function(){ goTo(current - 1); });
    document.getElementById('featNext').addEventListener('click', function(){ goTo(current + 1); });
    dots.forEach(function(d){ d.addEventListener('click', function(){ goTo(+d.dataset.index); }); });

    carousel.addEventListener('scroll', function(){
      var gap = 14;
      var cardWidth = cards[0].offsetWidth + gap;
      var idx = Math.round(carousel.scrollLeft / cardWidth);
      if(idx !== current){
        current = idx;
        dots.forEach(function(d, i){ d.classList.toggle('active', i === current); });
      }
    }, {passive:true});
  })();
</script>
</body>
</html>
