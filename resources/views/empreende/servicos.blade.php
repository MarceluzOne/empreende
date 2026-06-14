<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Prestadores de Serviço — Empreende Vitória</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
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
    background:linear-gradient(135deg,var(--brand) 0%,var(--brand-deep) 100%);
    padding:28px 24px 20px;
    display:flex;align-items:center;gap:16px;
    position:relative;overflow:hidden;
  }
  .provider-card__header.has-image{
    aspect-ratio:16/9;padding:0;align-items:flex-end;
    background-size:cover;background-position:center;
  }
  .provider-card__header.has-image::after{
    content:'';position:absolute;inset:0;
    background:linear-gradient(to top,rgba(4,74,122,.88) 0%,rgba(4,74,122,.2) 60%,transparent 100%);
  }
  .provider-card__header.has-image .provider-avatar,
  .provider-card__header.has-image .provider-card__header-info{
    position:relative;z-index:1;
  }
  .provider-card__header.has-image .provider-card__header-info-wrap{
    padding:16px 20px;display:flex;align-items:center;gap:14px;width:100%;
  }
  .provider-avatar{
    width:52px;height:52px;border-radius:50%;
    background:rgba(255,255,255,.2);border:2px solid rgba(255,255,255,.4);
    color:#fff;display:grid;place-items:center;
    font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:20px;flex:none;
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
  .provider-contact-item i{color:var(--brand);width:14px;text-align:center;flex:none;font-size:13px}
  .provider-contact-item a{color:var(--brand);font-weight:700}
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
    background:var(--brand-soft);color:var(--brand);
    padding:5px 14px;border-radius:999px;
    font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;
    margin-bottom:16px;
  }
  .cta-section h2{
    font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;
    font-size:clamp(22px,3vw,32px);letter-spacing:-0.02em;margin:0 0 10px;
  }
  .cta-section h2 em{font-style:normal;color:var(--brand)}
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
  .form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--brand)}
  .form-group textarea{resize:vertical;min-height:80px}
  .btn-submit{
    display:inline-flex;align-items:center;gap:8px;justify-content:center;
    background:var(--brand);color:#fff;border:0;cursor:pointer;
    padding:13px 28px;border-radius:999px;font-weight:800;font-size:15px;
    font-family:'Lato',sans-serif;transition:background .15s;margin-top:8px;width:100%;
  }
  .btn-submit:hover{background:var(--brand-deep)}
  .btn-submit:disabled{background:var(--muted);cursor:not-allowed}
  .form-feedback{
    display:none;padding:14px 18px;border-radius:12px;margin-top:16px;font-size:14px;font-weight:700;
  }
  .form-feedback.success{background:#d1fae5;color:#065f46;display:block}
  .form-feedback.error{background:#fee2e2;color:#991b1b;display:block}

  /* ====== CROP MODAL ====== */
  .crop-modal-backdrop{
    display:none;position:fixed;inset:0;background:rgba(0,0,0,.72);z-index:200;
    align-items:center;justify-content:center;padding:20px;
  }
  .crop-modal-backdrop.open{display:flex}
  .crop-modal{
    background:#fff;border-radius:18px;width:100%;max-width:680px;
    overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,.3);display:flex;flex-direction:column;
  }
  .crop-modal__head{
    padding:16px 20px;border-bottom:1px solid var(--line);
    display:flex;align-items:center;justify-content:space-between;
  }
  .crop-modal__head h3{margin:0;font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:16px}
  .crop-modal__head p{margin:0;font-size:12px;color:var(--muted)}
  .crop-modal__close{background:none;border:0;cursor:pointer;font-size:18px;color:var(--muted);line-height:1}
  .crop-modal__body{padding:16px;background:#1a1a2e;max-height:55vh;display:flex;align-items:center;justify-content:center}
  .crop-modal__body img{max-width:100%;max-height:50vh;display:block}
  .crop-modal__foot{padding:14px 20px;border-top:1px solid var(--line);display:flex;justify-content:flex-end;gap:12px}
  .btn-crop-cancel{background:none;border:1.5px solid var(--line);padding:9px 20px;border-radius:999px;font-weight:700;font-size:14px;cursor:pointer;color:var(--ink-soft)}
  .btn-crop-apply{background:var(--brand);color:#fff;border:0;padding:9px 22px;border-radius:999px;font-weight:800;font-size:14px;cursor:pointer}
  .btn-crop-apply:hover{background:var(--brand-deep)}

  /* Upload image field */
  .upload-label{
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    border:2px dashed var(--line);border-radius:12px;padding:20px;cursor:pointer;
    background:#fafbfc;transition:border-color .15s,background .15s;min-height:80px;
  }
  .upload-label:hover{border-color:var(--brand);background:var(--brand-soft)}
  .upload-label i{font-size:22px;color:var(--muted);margin-bottom:6px}
  .upload-label span{font-size:13px;color:var(--muted)}
  .upload-preview{
    display:none;position:relative;border-radius:10px;overflow:hidden;
    aspect-ratio:16/9;background:#000;
  }
  .upload-preview img{width:100%;height:100%;object-fit:cover;display:block}
  .upload-preview-remove{
    position:absolute;top:8px;right:8px;width:28px;height:28px;border-radius:50%;
    background:rgba(0,0,0,.6);color:#fff;border:0;cursor:pointer;font-size:13px;
    display:flex;align-items:center;justify-content:center;
  }

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
    <a href="{{ route('servicos') }}" class="active">Serviços</a>
    <a href="{{ route('home') }}#vagas">Vagas</a>
    <a href="{{ route('cursos') }}">Cursos</a>
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
  <a href="{{ route('servicos') }}" class="active">Serviços <i class="fas fa-chevron-right"></i></a>
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
      <span>Prestadores de Serviço</span>
    </div>
    <div class="hero-badge"><span class="dot"></span> Serviços Locais</div>
    <h1>Encontre quem presta<br>serviço em <em>Vitória de Sto. Antão.</em></h1>
    <p>Profissionais autônomos e prestadores cadastrados no Salão do Empreendedor — prontos para atender você.</p>
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

  @if($prestadores->count() > 0)
    <p class="count-label" id="countLabel">Exibindo <strong>{{ $prestadores->count() }}</strong> prestador{{ $prestadores->count() !== 1 ? 'es' : '' }} de serviço</p>
    <div class="cards-grid" id="cardsGrid">
      @foreach($prestadores as $p)
        <div class="provider-card" data-search="{{ strtolower($p->name . ' ' . $p->service_title) }}">
          @if($p->business_image)
          <div class="provider-card__header has-image"
               style="background-image:url('{{ Storage::url($p->business_image) }}')">
            <div class="provider-card__header-info-wrap">
              <div class="provider-avatar">{{ strtoupper(substr($p->name, 0, 1)) }}</div>
              <div class="provider-card__header-info">
                <div class="provider-card__name">{{ $p->name }}</div>
              </div>
            </div>
          </div>
          @else
          <div class="provider-card__header">
            <div class="provider-avatar">{{ strtoupper(substr($p->name, 0, 1)) }}</div>
            <div class="provider-card__header-info">
              <div class="provider-card__name">{{ $p->name }}</div>
            </div>
          </div>
          @endif
          <div class="provider-card__body">
            <div class="provider-service-title">{{ $p->service_title }}</div>
            <div class="provider-contacts">
              @if($p->email)
                <div class="provider-contact-item">
                  <i class="fas fa-envelope"></i>
                  <span>{{ $p->email }}</span>
                </div>
              @endif
              @if($p->instagram)
                <div class="provider-contact-item">
                  <i class="fab fa-instagram"></i>
                  <a href="https://instagram.com/{{ ltrim($p->instagram, '@') }}" target="_blank" rel="noopener">
                    {{ $p->instagram }}
                  </a>
                </div>
              @endif
            </div>
            @if($p->optional_info)
              <p style="font-size:13px;color:var(--ink-soft);margin:4px 0 0;line-height:1.5">{{ $p->optional_info }}</p>
            @endif
          </div>
          <div class="provider-card__footer">
            <a href="https://wa.me/55{{ preg_replace('/\D/', '', $p->whatsapp) }}" target="_blank" rel="noopener" class="btn-whatsapp">
              <i class="fab fa-whatsapp"></i> Contato
            </a>
          </div>
        </div>
      @endforeach
    </div>
    <div class="empty-state" id="emptySearch" style="display:none">
      <i class="fas fa-search"></i>
      <p>Nenhum prestador encontrado para esta busca.</p>
    </div>
  @else
    <div class="empty-state">
      <i class="fas fa-tools"></i>
      <p>Nenhum prestador de serviço cadastrado ainda.<br>Seja o primeiro a se cadastrar!</p>
    </div>
  @endif

  <!-- CTA: Cadastro -->
  <div class="cta-section" id="cadastro">
    <div class="cta-section__inner">
      <div class="cta-badge"><i class="fas fa-plus-circle"></i> Quero me cadastrar</div>
      <h2>Ofereça seu serviço para<br><em>toda a cidade.</em></h2>
      <p>Preencha o formulário abaixo e seu perfil será analisado pela equipe do Empreende Vitória. Após aprovação, aparecerá nesta página.</p>

      <form id="registerForm" novalidate>
        <div class="form-grid">
          <div class="form-group full">
            <label for="reg_name">Seu nome completo *</label>
            <input type="text" id="reg_name" name="name" placeholder="Ex: João da Silva" required>
          </div>
          <div class="form-group full">
            <label for="reg_service_title">O que você oferece? *</label>
            <input type="text" id="reg_service_title" name="service_title" placeholder="Ex: Eletricista residencial, Manicure, Bolo artesanal..." required>
          </div>
          <div class="form-group">
            <label for="reg_email">E-mail *</label>
            <input type="email" id="reg_email" name="email" placeholder="seu@email.com" required>
          </div>
          <div class="form-group">
            <label for="reg_whatsapp">WhatsApp *</label>
            <input type="text" id="reg_whatsapp" name="whatsapp" placeholder="(81) 99999-9999" required>
          </div>
          <div class="form-group full">
            <label for="reg_instagram">Instagram <span style="font-weight:400;color:var(--muted)">(opcional)</span></label>
            <input type="text" id="reg_instagram" name="instagram" placeholder="@seuinstagram">
          </div>
          <div class="form-group full">
            <label for="reg_optional_info">Informações adicionais <span style="font-weight:400;color:var(--muted)">(opcional)</span></label>
            <textarea id="reg_optional_info" name="optional_info" placeholder="Ex: horários de atendimento, área de atuação, diferenciais do serviço..." rows="3" style="resize:vertical"></textarea>
          </div>
          <div class="form-group full">
            <label style="font-size:13px;font-weight:700;color:var(--ink-soft)">
              Foto do negócio <span style="font-weight:400;color:var(--muted)">(opcional · 16:9)</span>
            </label>
            <div class="upload-preview" id="uploadPreview">
              <img id="uploadPreviewImg" src="" alt="">
              <button type="button" class="upload-preview-remove" id="removeImage" title="Remover imagem">
                <i class="fas fa-times"></i>
              </button>
            </div>
            <label class="upload-label" id="uploadLabel" for="reg_business_image">
              <i class="fas fa-camera"></i>
              <span>Clique para adicionar foto do negócio</span>
              <span style="font-size:11px;margin-top:2px">JPG, PNG ou WEBP · máx. 5MB</span>
            </label>
            <input type="file" id="reg_business_image" accept="image/jpeg,image/png,image/webp" style="display:none">
          </div>
          <div class="form-group full">
            <button type="submit" class="btn-submit" id="submitBtn">
              <i class="fas fa-paper-plane"></i> Enviar cadastro
            </button>
          </div>
        </div>
      </form>
      <div class="form-feedback" id="formFeedback"></div>
    </div>
  </div>

</div>

<!-- ====== FOOTER ====== -->
<div class="footer-banner">
  <img src="https://www.prefeituradavitoria.pe.gov.br/empreendevitoria/wp-content/uploads/2023/06/rodape002-1.png"
       alt="Prefeitura da Vitória de Santo Antão">
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

<!-- ====== CROP MODAL ====== -->
<div class="crop-modal-backdrop" id="cropModalBackdrop">
  <div class="crop-modal">
    <div class="crop-modal__head">
      <div>
        <h3>Ajustar imagem</h3>
        <p>Arraste e redimensione para enquadrar no formato 16:9</p>
      </div>
      <button class="crop-modal__close" id="cropModalClose"><i class="fas fa-times"></i></button>
    </div>
    <div class="crop-modal__body">
      <img id="cropImage" src="" alt="">
    </div>
    <div class="crop-modal__foot">
      <button class="btn-crop-cancel" id="cropCancel">Cancelar</button>
      <button class="btn-crop-apply" id="cropApply"><i class="fas fa-check"></i> Aplicar</button>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
  // WhatsApp mask (99) 99999-9999
  const whatsappInput = document.getElementById('reg_whatsapp');
  whatsappInput && whatsappInput.addEventListener('input', function(e) {
    let v = e.target.value.replace(/\D/g, '').slice(0, 11);
    if (v.length > 6) v = '(' + v.slice(0,2) + ') ' + v.slice(2,7) + '-' + v.slice(7);
    else if (v.length > 2) v = '(' + v.slice(0,2) + ') ' + v.slice(2);
    else if (v.length > 0) v = '(' + v;
    e.target.value = v;
  });

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
    if (countLabel) countLabel.style.display = (q && visible === 0) ? 'none' : '';
  });

  // ====== CROP LOGIC ======
  let cropper = null;
  let croppedBase64 = null;

  const cropBackdrop   = document.getElementById('cropModalBackdrop');
  const cropImg        = document.getElementById('cropImage');
  const cropApplyBtn   = document.getElementById('cropApply');
  const cropCancelBtn  = document.getElementById('cropCancel');
  const cropCloseBtn   = document.getElementById('cropModalClose');
  const fileInput      = document.getElementById('reg_business_image');
  const uploadLabel    = document.getElementById('uploadLabel');
  const uploadPreview  = document.getElementById('uploadPreview');
  const uploadPreviewImg = document.getElementById('uploadPreviewImg');
  const removeImageBtn = document.getElementById('removeImage');

  function openCrop(src) {
    cropImg.src = src;
    cropBackdrop.classList.add('open');
    // Aguarda o render da imagem antes de iniciar o cropper
    setTimeout(() => {
      if (cropper) cropper.destroy();
      cropper = new Cropper(cropImg, {
        aspectRatio: 16 / 9,
        viewMode: 1,
        autoCropArea: 1,
        movable: true,
        zoomable: true,
        rotatable: false,
        scalable: false,
      });
    }, 100);
  }

  function closeCrop() {
    cropBackdrop.classList.remove('open');
    if (cropper) { cropper.destroy(); cropper = null; }
    fileInput.value = '';
  }

  fileInput && fileInput.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) {
      alert('A imagem deve ter no máximo 5MB.');
      this.value = '';
      return;
    }
    const reader = new FileReader();
    reader.onload = e => openCrop(e.target.result);
    reader.readAsDataURL(file);
  });

  cropApplyBtn && cropApplyBtn.addEventListener('click', function() {
    if (!cropper) return;
    croppedBase64 = cropper.getCroppedCanvas({ width: 1280, height: 720 }).toDataURL('image/jpeg', 0.88);
    uploadPreviewImg.src = croppedBase64;
    uploadPreview.style.display = 'block';
    uploadLabel.style.display = 'none';
    closeCrop();
  });

  [cropCancelBtn, cropCloseBtn].forEach(btn => btn && btn.addEventListener('click', closeCrop));
  cropBackdrop && cropBackdrop.addEventListener('click', function(e) { if (e.target === this) closeCrop(); });

  removeImageBtn && removeImageBtn.addEventListener('click', function() {
    croppedBase64 = null;
    uploadPreview.style.display = 'none';
    uploadLabel.style.display = '';
    fileInput.value = '';
  });

  // ====== REGISTRATION FORM ======
  const form       = document.getElementById('registerForm');
  const submitBtn  = document.getElementById('submitBtn');
  const feedback   = document.getElementById('formFeedback');

  form && form.addEventListener('submit', async function(e) {
    e.preventDefault();
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    feedback.className = 'form-feedback';
    feedback.style.display = 'none';

    const data = {
      name:            document.getElementById('reg_name').value,
      provider_type:   'individual',
      service_title:   document.getElementById('reg_service_title').value,
      email:           document.getElementById('reg_email').value,
      whatsapp:        document.getElementById('reg_whatsapp').value,
      instagram:       document.getElementById('reg_instagram').value || null,
      optional_info:   document.getElementById('reg_optional_info').value || null,
      business_image:  croppedBase64 || null,
    };

    try {
      const resp = await fetch('{{ route('api.services.external-register') }}', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body:    JSON.stringify(data),
      });
      const json = await resp.json();

      if (resp.ok && json.success) {
        feedback.textContent = '✓ Cadastro enviado com sucesso! Aguarde a aprovação da equipe.';
        feedback.className = 'form-feedback success';
        form.reset();
        croppedBase64 = null;
        uploadPreview.style.display = 'none';
        uploadLabel.style.display = '';
      } else {
        const errs = json.errors ? Object.values(json.errors).flat().join(' ') : (json.message || 'Erro ao enviar.');
        feedback.textContent = errs;
        feedback.className = 'form-feedback error';
      }
    } catch {
      feedback.textContent = 'Erro de conexão. Tente novamente.';
      feedback.className = 'form-feedback error';
    }

    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar cadastro';
    feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  });
</script>
</body>
</html>
