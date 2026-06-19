<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Empresas Locais — Empreende Vitória</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
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
    --company:#1a7a4a;
    --company-deep:#0f5c35;
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
    background:linear-gradient(135deg,var(--company) 0%,var(--company-deep) 100%);
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

  /* ====== SEARCH BAR ====== */
  .search-wrap{max-width:1280px;margin:0 auto;padding:32px 32px 0}
  .search-bar{
    display:flex;align-items:center;
    background:#fff;border-radius:14px;
    max-width:560px;
    box-shadow:var(--shadow-sm);
    border:1px solid var(--line);
    overflow:hidden;
  }
  .search-bar i{padding:0 16px;color:var(--muted);font-size:15px;flex:none}
  .search-bar input{
    flex:1;border:0;outline:none;padding:14px 0;
    font-size:15px;font-family:'Lato',sans-serif;color:var(--ink);
  }
  .search-bar input::placeholder{color:var(--muted)}

  /* ====== MAIN ====== */
  .page-body{max-width:1280px;margin:0 auto;padding:40px 32px 80px}
  .count-label{font-size:14px;color:var(--muted);margin-bottom:24px}
  .count-label strong{color:var(--ink)}

  /* ====== CARDS GRID ====== */
  .cards-grid{
    display:grid;grid-template-columns:repeat(3,1fr);gap:24px;
  }
  .provider-card{
    background:#fff;border:1px solid var(--line);border-radius:18px;overflow:hidden;
    transition:transform .2s,box-shadow .2s;display:flex;flex-direction:column;
  }
  .provider-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-md)}
  .provider-card__header{
    background:linear-gradient(135deg,var(--company) 0%,var(--company-deep) 100%);
    padding:28px 24px 20px;
    display:flex;align-items:center;gap:16px;
    position:relative;overflow:hidden;height:180px;
  }
  .provider-card__header.has-image{
    background:none;
  }
  .provider-header-bg{
    position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0;
  }
  .provider-header-overlay{
    position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,rgba(0,0,0,.15) 100%);z-index:1;
  }
  .provider-card__header > *:not(.provider-header-bg):not(.provider-header-overlay){
    position:relative;z-index:2;
  }
  .provider-avatar{
    width:52px;height:52px;border-radius:50%;
    background:rgba(255,255,255,.2);border:2px solid rgba(255,255,255,.4);
    color:#fff;display:grid;place-items:center;
    font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:20px;flex:none;
    overflow:hidden;
  }
  .provider-card__header-info{flex:1;min-width:0}
  .provider-card__name{
    font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:15px;
    color:#fff;margin:0 0 4px;line-height:1.2;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  }
  .provider-card__service{
    font-size:12px;color:rgba(255,255,255,.8);
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  }
  .provider-card__body{padding:18px 20px;flex:1;display:flex;flex-direction:column;gap:10px}
  .provider-service-title{
    font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:15px;
    color:var(--ink);line-height:1.3;
  }
  .provider-contacts{display:flex;flex-direction:column;gap:6px;margin-top:4px}
  .provider-contact-item{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-soft)}
  .provider-contact-item i{color:var(--company);width:14px;text-align:center;flex:none;font-size:13px}
  .provider-contact-item a{color:var(--company);font-weight:700}
  .provider-contact-item a:hover{text-decoration:underline}
  .provider-card__footer{
    padding:12px 20px;border-top:1px solid var(--line);
    display:flex;align-items:center;gap:8px;
  }
  .btn-whatsapp{
    display:inline-flex;align-items:center;gap:7px;
    background:#25d366;color:#fff;border:0;cursor:pointer;
    padding:9px 18px;border-radius:999px;font-weight:800;font-size:13px;
    font-family:'Lato',sans-serif;transition:background .15s;text-decoration:none;flex:1;justify-content:center;
  }
  .btn-whatsapp:hover{background:#1ebe5d}
  .btn-instagram{
    display:inline-flex;align-items:center;justify-content:center;
    width:38px;height:38px;border-radius:50%;
    background:linear-gradient(135deg,#f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);
    color:#fff;font-size:16px;transition:opacity .15s;flex:none;
  }
  .btn-instagram:hover{opacity:.85}

  /* ====== EMPTY STATE ====== */
  .empty-state{
    text-align:center;padding:80px 32px;color:var(--muted);
  }
  .empty-state i{font-size:52px;margin-bottom:18px;opacity:.35}
  .empty-state p{font-size:16px;margin:0;line-height:1.6}

  /* ====== CTA CADASTRO ====== */
  .cta-section{
    background:var(--cream);border-radius:20px;
    padding:48px 40px;margin-top:64px;
  }
  .cta-section__inner{max-width:640px;margin:0 auto}
  .cta-badge{
    display:inline-flex;align-items:center;gap:8px;
    background:#d1fae5;color:var(--company);
    padding:5px 14px;border-radius:999px;
    font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;
    margin-bottom:16px;
  }
  .cta-section h2{
    font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;
    font-size:clamp(22px,3vw,32px);letter-spacing:-0.02em;margin:0 0 10px;
  }
  .cta-section h2 em{font-style:normal;color:var(--company)}
  .cta-section > .cta-section__inner > p{font-size:15px;color:var(--ink-soft);margin:0 0 32px}

  /* Form */
  .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
  .form-group{display:flex;flex-direction:column;gap:6px}
  .form-group.full{grid-column:1/-1}
  .form-group label{font-size:13px;font-weight:700;color:var(--ink-soft)}
  .form-group input,.form-group select,.form-group textarea{
    border:1.5px solid var(--line);border-radius:10px;
    padding:11px 14px;font-size:14px;font-family:'Lato',sans-serif;color:var(--ink);
    background:#fff;outline:none;transition:border-color .15s;
  }
  .form-group input:focus,.form-group select:focus{border-color:var(--company)}

  /* Image upload */
  .img-upload-area{
    border:2px dashed var(--line);border-radius:12px;
    padding:20px;display:flex;flex-direction:column;align-items:center;justify-content:center;
    gap:8px;cursor:pointer;transition:border-color .15s,background .15s;
    background:#fafafa;text-align:center;
  }
  .img-upload-area:hover{border-color:var(--company);background:#f0faf5}
  .img-upload-area i{font-size:22px;color:var(--muted)}
  .img-upload-area span{font-size:13px;color:var(--muted)}
  .img-preview{
    width:100%;border-radius:10px;overflow:hidden;
    aspect-ratio:16/9;display:none;position:relative;
  }
  .img-preview img{width:100%;height:100%;object-fit:cover;display:block}
  .img-preview-change{
    position:absolute;bottom:8px;right:8px;
    background:rgba(0,0,0,.6);color:#fff;border:0;cursor:pointer;
    padding:5px 12px;border-radius:999px;font-size:12px;font-weight:700;font-family:'Lato',sans-serif;
  }

  /* Crop modal */
  .crop-modal{
    display:none;position:fixed;inset:0;z-index:200;
    background:rgba(0,0,0,.75);align-items:center;justify-content:center;padding:16px;
  }
  .crop-modal.open{display:flex}
  .crop-modal__box{
    background:#fff;border-radius:18px;overflow:hidden;
    width:100%;max-width:640px;max-height:90vh;display:flex;flex-direction:column;
  }
  .crop-modal__head{
    display:flex;align-items:center;justify-content:space-between;
    padding:16px 20px;border-bottom:1px solid var(--line);
  }
  .crop-modal__head h4{margin:0;font-family:'Plus Jakarta Sans',sans-serif;font-size:16px;font-weight:800}
  .crop-modal__close{background:none;border:0;font-size:20px;cursor:pointer;color:var(--muted);line-height:1}
  .crop-modal__body{flex:1;overflow:auto;padding:16px;background:#f3f6fa;display:flex;align-items:center;justify-content:center;min-height:260px}
  .crop-modal__body img{max-width:100%;display:block}
  .crop-modal__foot{display:flex;justify-content:flex-end;gap:10px;padding:14px 20px;border-top:1px solid var(--line)}
  .btn-cancel-crop{background:none;border:1.5px solid var(--line);border-radius:999px;padding:8px 20px;font-weight:700;font-size:14px;cursor:pointer;font-family:'Lato',sans-serif}
  .btn-apply-crop{background:var(--company);color:#fff;border:0;border-radius:999px;padding:9px 22px;font-weight:800;font-size:14px;cursor:pointer;font-family:'Lato',sans-serif;display:flex;align-items:center;gap:6px}
  .btn-submit{
    display:inline-flex;align-items:center;gap:8px;justify-content:center;
    background:var(--company);color:#fff;border:0;cursor:pointer;
    padding:13px 28px;border-radius:999px;font-weight:800;font-size:15px;
    font-family:'Lato',sans-serif;transition:background .15s;margin-top:8px;width:100%;
  }
  .btn-submit:hover{background:var(--company-deep)}
  .btn-submit:disabled{background:var(--muted);cursor:not-allowed}

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
    .cards-grid{grid-template-columns:1fr 1fr}
    .page-body{padding:32px 20px 60px}
    .page-hero{padding:100px 20px 48px}
    .search-wrap{padding:24px 20px 0}
    .cta-section{padding:32px 24px}
    .form-grid{grid-template-columns:1fr}
  }
  @media(max-width:640px){
    .cards-grid{grid-template-columns:1fr}
    .topbar{padding:12px 16px}
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
    <a href="{{ route('home') }}#vagas">Vagas</a>
    <a href="{{ route('cursos') }}">Cursos</a>
    <a href="{{ route('contato') }}">Contato</a>
    <a href="{{ route('login') }}" class="nav-login"><i class="fas fa-user-lock"></i> Entrar</a>
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
  <a href="{{ route('servicos') }}">Serviços <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('home') }}#vagas">Vagas <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('cursos') }}">Cursos <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('contato') }}">Contato <i class="fas fa-chevron-right"></i></a>
  <a href="{{ route('usuario.login') }}" style="margin-top:12px;background:var(--brand);color:#fff;justify-content:center">Entrar</a>
</aside>

<!-- ====== HERO ====== -->
<section class="page-hero">
  <div class="page-hero__inner">
    <div class="breadcrumb">
      <a href="{{ route('home') }}"><i class="fas fa-home"></i> Início</a>
      <i class="fas fa-chevron-right"></i>
      <span>Empresas Locais</span>
    </div>
    <div class="hero-badge"><span class="dot"></span> Empresas & Produtos</div>
    <h1>Conheça as empresas<br>locais de <em>Vitória de Sto. Antão.</em></h1>
    <p>Negócios cadastrados no Salão do Empreendedor — produtos e empresas da nossa cidade para você valorizar o comércio local.</p>
  </div>
</section>

<!-- ====== SEARCH ====== -->
<div class="search-wrap">
  <div class="search-bar">
    <i class="fas fa-search"></i>
    <input type="text" id="searchInput" placeholder="Buscar por nome ou serviço...">
  </div>
</div>

<!-- ====== CONTENT ====== -->
<div class="page-body">

  @if($empresas->count() > 0)
    <p class="count-label" id="countLabel" style="display:none">Exibindo <strong>{{ $empresas->count() }}</strong> empresa{{ $empresas->count() !== 1 ? 's' : '' }} local{{ $empresas->count() !== 1 ? 'is' : '' }}</p>
    <div class="cards-grid" id="cardsGrid">
      @foreach($empresas as $e)
        <div class="provider-card" data-search="{{ strtolower($e->name . ' ' . $e->service_title) }}">
          <div class="provider-card__header {{ $e->business_image ? 'has-image' : '' }}">
            @if($e->business_image)
              <img class="provider-header-bg" src="{{ asset('storage/' . $e->business_image) }}" alt="{{ $e->name }}">
              <div class="provider-header-overlay"></div>
            @endif
            <div class="provider-avatar">{{ strtoupper(substr($e->name, 0, 1)) }}</div>
            <div class="provider-card__header-info">
              <div class="provider-card__name">{{ $e->name }}</div>
              <div class="provider-card__service"><i class="fas fa-store" style="margin-right:5px;font-size:11px"></i>Empresa Local</div>
            </div>
          </div>
          <div class="provider-card__body">
            <div class="provider-service-title">{{ $e->service_title }}</div>
            <div class="provider-contacts">
              @if($e->email)
                <div class="provider-contact-item">
                  <i class="fas fa-envelope"></i>
                  <span>{{ $e->email }}</span>
                </div>
              @endif
              @if($e->instagram)
                <div class="provider-contact-item">
                  <i class="fab fa-instagram"></i>
                  <a href="https://instagram.com/{{ ltrim($e->instagram, '@') }}" target="_blank" rel="noopener">
                    {{ $e->instagram }}
                  </a>
                </div>
              @endif
            </div>
            @if($e->optional_info)
              <p style="font-size:13px;color:var(--ink-soft);margin:4px 0 0;line-height:1.5">{{ $e->optional_info }}</p>
            @endif
          </div>
          <div class="provider-card__footer">
            <a href="https://wa.me/55{{ preg_replace('/\D/', '', $e->whatsapp) }}" target="_blank" rel="noopener" class="btn-whatsapp">
              <i class="fab fa-whatsapp"></i> Contato
            </a>
          </div>
        </div>
      @endforeach
    </div>
    <div class="empty-state" id="emptySearch" style="display:none">
      <i class="fas fa-search"></i>
      <p>Nenhuma empresa encontrada para esta busca.</p>
    </div>
  @else
    <div class="empty-state">
      <i class="fas fa-store"></i>
      <p>Nenhuma empresa cadastrada ainda.<br>Seja a primeira a se cadastrar!</p>
    </div>
  @endif

  <!-- CTA: Cadastro -->
  <div class="cta-section" id="cadastro">
    <div class="cta-section__inner">
      <div class="cta-badge"><i class="fas fa-plus-circle"></i> Cadastrar minha empresa</div>
      <h2>Coloque sua empresa no<br><em>mapa do empreendedorismo.</em></h2>
      <p>Preencha o formulário e sua empresa será analisada pela equipe do Empreende Vitória. Após aprovação, aparecerá nesta página.</p>

      <form id="registerForm" novalidate>
        <div class="form-grid">
          <div class="form-group full">
            <label for="reg_name">Nome da empresa *</label>
            <input type="text" id="reg_name" name="name" placeholder="Ex: Padaria Santo Antão" required>
          </div>
          <div class="form-group full">
            <label for="reg_service_title">O que sua empresa oferece? *</label>
            <input type="text" id="reg_service_title" name="service_title" placeholder="Ex: Pães artesanais, confeitaria e salgados" required>
          </div>
          <div class="form-group">
            <label for="reg_email">E-mail *</label>
            <input type="email" id="reg_email" name="email" placeholder="contato@empresa.com" required>
          </div>
          <div class="form-group">
            <label for="reg_whatsapp">WhatsApp *</label>
            <input type="text" id="reg_whatsapp" name="whatsapp" placeholder="(81) 99999-9999" maxlength="16" required>
          </div>
          <div class="form-group full">
            <label for="reg_instagram">Instagram <span style="font-weight:400;color:var(--muted)">(opcional)</span></label>
            <input type="text" id="reg_instagram" name="instagram" placeholder="@suaempresa">
          </div>
          <div class="form-group full">
            <label for="reg_optional_info">Informações adicionais <span style="font-weight:400;color:var(--muted)">(opcional)</span></label>
            <textarea id="reg_optional_info" name="optional_info" placeholder="Ex: horários de atendimento, área de atuação, diferenciais..." rows="3" style="resize:vertical"></textarea>
          </div>
          <div class="form-group full">
            <label>Imagem da empresa <span style="font-weight:400;color:var(--muted)">(opcional · recortada em 16:9)</span></label>
            <div class="img-preview" id="imgPreview">
              <img id="imgPreviewImg" src="" alt="Preview">
              <button type="button" class="img-preview-change" id="imgChangeBtn"><i class="fas fa-crop-alt"></i> Trocar</button>
            </div>
            <label class="img-upload-area" id="imgUploadArea" for="imgFileInput">
              <i class="fas fa-crop-alt"></i>
              <span>Clique para selecionar e recortar a imagem</span>
              <span style="font-size:11px">JPG, PNG ou WEBP</span>
            </label>
            <input type="file" id="imgFileInput" accept="image/jpeg,image/png,image/webp" style="display:none">
          </div>
          <div class="form-group full">
            <button type="submit" class="btn-submit" id="submitBtn">
              <i class="fas fa-paper-plane"></i> Enviar cadastro
            </button>
          </div>
        </div>
      </form>
      {{-- Toast de feedback (estilo do flash de agendamento da landing) --}}
      <div id="formToast"
           style="display:none;position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;color:#fff;padding:14px 24px;border-radius:12px;font-weight:700;box-shadow:0 4px 20px rgba(0,0,0,.2);font-family:'Plus Jakarta Sans',sans-serif;max-width:90vw;text-align:center">
        <i id="formToastIcon" class="fas fa-check-circle" style="margin-right:8px"></i><span id="formToastMsg"></span>
      </div>
    </div>
  </div>

</div>

<!-- ====== CROP MODAL ====== -->
<div class="crop-modal" id="cropModal">
  <div class="crop-modal__box">
    <div class="crop-modal__head">
      <h4><i class="fas fa-crop-alt" style="color:var(--company);margin-right:6px"></i>Recortar imagem</h4>
      <button class="crop-modal__close" id="cropClose">&times;</button>
    </div>
    <div class="crop-modal__body">
      <img id="cropImage" src="" alt="">
    </div>
    <div class="crop-modal__foot">
      <button type="button" class="btn-cancel-crop" id="cropCancel">Cancelar</button>
      <button type="button" class="btn-apply-crop" id="cropApply"><i class="fas fa-check"></i> Aplicar corte</button>
    </div>
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
  // Mobile nav
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

  // WhatsApp mask: (00) 00000-0000
  document.getElementById('reg_whatsapp').addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').slice(0, 11);
    if (v.length > 10) {
      v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
    } else if (v.length > 6) {
      v = v.replace(/^(\d{2})(\d{4})(\d*)$/, '($1) $2-$3');
    } else if (v.length > 2) {
      v = v.replace(/^(\d{2})(\d*)$/, '($1) $2');
    } else if (v.length > 0) {
      v = v.replace(/^(\d*)$/, '($1');
    }
    this.value = v;
  });

  // Search filter
  const searchInput = document.getElementById('searchInput');
  const cards       = document.querySelectorAll('.provider-card');
  const emptySearch = document.getElementById('emptySearch');
  const countLabel  = document.getElementById('countLabel');

  searchInput && searchInput.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    let visible = 0;
    cards.forEach(card => {
      const match = !q || card.dataset.search.includes(q);
      card.style.display = match ? '' : 'none';
      if (match) visible++;
    });
    if (emptySearch) emptySearch.style.display = (q && visible === 0) ? '' : 'none';
    // Mostra o contador apenas quando há busca ativa (com resultados).
    if (countLabel) {
      if (q && visible > 0) {
        countLabel.innerHTML = 'Exibindo <strong>' + visible + '</strong> empresa' + (visible !== 1 ? 's' : '') + ' local' + (visible !== 1 ? 'is' : '');
        countLabel.style.display = '';
      } else {
        countLabel.style.display = 'none';
      }
    }
  });

  // ===== IMAGE CROP =====
  let cropper       = null;
  let croppedBase64 = null;

  const fileInput     = document.getElementById('imgFileInput');
  const uploadArea    = document.getElementById('imgUploadArea');
  const previewBox    = document.getElementById('imgPreview');
  const previewImg    = document.getElementById('imgPreviewImg');
  const changeBtn     = document.getElementById('imgChangeBtn');
  const cropModal     = document.getElementById('cropModal');
  const cropImage     = document.getElementById('cropImage');
  const cropClose     = document.getElementById('cropClose');
  const cropCancel    = document.getElementById('cropCancel');
  const cropApply     = document.getElementById('cropApply');

  function openCropModal(src) {
    cropImage.src = src;
    cropModal.classList.add('open');
    setTimeout(() => {
      if (cropper) cropper.destroy();
      cropper = new Cropper(cropImage, { aspectRatio: 16/9, viewMode: 1, autoCropArea: 1 });
    }, 100);
  }

  function closeCropModal() {
    if (cropper) { cropper.destroy(); cropper = null; }
    cropModal.classList.remove('open');
    fileInput.value = '';
  }

  fileInput.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => openCropModal(e.target.result);
    reader.readAsDataURL(file);
  });

  changeBtn.addEventListener('click', () => fileInput.click());
  cropClose.addEventListener('click', closeCropModal);
  cropCancel.addEventListener('click', closeCropModal);

  cropApply.addEventListener('click', () => {
    if (!cropper) return;
    croppedBase64 = cropper.getCroppedCanvas({ width: 1280, height: 720 }).toDataURL('image/jpeg', 0.92);
    previewImg.src = croppedBase64;
    previewBox.style.display = 'block';
    uploadArea.style.display = 'none';
    closeCropModal();
  });

  // Registration form
  const form      = document.getElementById('registerForm');
  const submitBtn = document.getElementById('submitBtn');

  // Toast de feedback (mesmo visual do flash de agendamento da landing)
  const toast     = document.getElementById('formToast');
  const toastIcon = document.getElementById('formToastIcon');
  const toastMsg  = document.getElementById('formToastMsg');
  let   toastTimer;
  function showToast(message, type) {
    toastMsg.textContent = message;
    toast.style.background = type === 'success' ? '#16a34a' : '#dc2626';
    toastIcon.className = 'fas ' + (type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle');
    toast.style.display = 'block';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { toast.style.display = 'none'; }, 6000);
  }

  form && form.addEventListener('submit', async function(e) {
    e.preventDefault();
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

    const data = {
      name:           document.getElementById('reg_name').value,
      provider_type:  'company',
      service_title:  document.getElementById('reg_service_title').value,
      email:          document.getElementById('reg_email').value,
      whatsapp:       document.getElementById('reg_whatsapp').value,
      instagram:      document.getElementById('reg_instagram').value || null,
      optional_info:  document.getElementById('reg_optional_info').value || null,
      business_image: croppedBase64 || null,
    };

    try {
      const resp = await fetch('{{ route('api.services.external-register') }}', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body:    JSON.stringify(data),
      });
      const json = await resp.json();

      if (resp.ok && json.success) {
        showToast('Cadastro enviado com sucesso! Após a aprovação da nossa equipe, ele será publicado no site.', 'success');
        form.reset();
        croppedBase64 = null;
        previewBox.style.display = 'none';
        uploadArea.style.display = '';
      } else {
        const errs = json.errors ? Object.values(json.errors).flat().join(' ') : (json.message || 'Erro ao enviar.');
        showToast(errs, 'error');
      }
    } catch {
      showToast('Erro de conexão. Tente novamente.', 'error');
    }

    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar cadastro';
  });
</script>
</body>
</html>
