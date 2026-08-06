<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Portal da Empresa — Empreende Vitória</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<style>
  :root{
    --brand:#0763A0;--brand-deep:#044a7a;--brand-soft:#e8f1f9;
    --yellow:#ffc52d;--ink:#0c1822;--ink-soft:#38475a;--muted:#6c7a8a;
    --paper:#ffffff;--cream:#f3f6fa;--line:rgba(12,24,34,.08);
    --shadow-sm:0 1px 2px rgba(12,24,34,.06),0 2px 8px rgba(12,24,34,.04);
    --shadow-md:0 8px 28px rgba(12,24,34,.10);--radius:14px;
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Lato',system-ui,sans-serif;color:var(--ink);background:var(--cream);min-height:100vh;-webkit-font-smoothing:antialiased}
  a{color:inherit;text-decoration:none}

  .topbar{display:flex;align-items:center;justify-content:space-between;padding:0 32px;height:65px;background:var(--paper);box-shadow:var(--shadow-sm);position:sticky;top:0;z-index:40}
  .brand{display:flex;align-items:center;gap:10px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:17px;color:var(--ink)}
  .brand-mark{width:34px;height:34px;border-radius:9px;background:var(--brand);color:#fff;display:grid;place-items:center;font-weight:900;font-size:13px}
  .topbar-right{display:flex;align-items:center;gap:8px}
  .user-chip{display:flex;align-items:center;gap:8px;padding:6px 14px 6px 8px;border-radius:999px;background:var(--cream);font-size:14px;font-weight:700}
  .user-avatar{width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#0f2942,var(--brand-deep));color:#fff;display:grid;place-items:center;font-size:11px;font-weight:900}
  .btn-logout{padding:8px 16px;border-radius:999px;font-size:13px;font-weight:700;color:var(--muted);border:none;background:none;cursor:pointer;transition:background .15s,color .15s}
  .btn-logout:hover{background:var(--brand-soft);color:var(--brand)}

  /* ── Menu da conta ── */
  .user-menu{position:relative}
  .user-chip-btn{border:none;cursor:pointer;font-family:'Lato',sans-serif;color:var(--ink);transition:background .15s}
  .user-chip-btn:hover{background:var(--brand-soft)}
  .user-chip-name{max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
  .user-chip-caret{font-size:11px;color:var(--muted);transition:transform .15s}
  .user-menu.open .user-chip-caret{transform:rotate(180deg)}
  .dropdown{
    display:none;position:absolute;top:calc(100% + 8px);right:0;min-width:230px;z-index:120;
    background:var(--paper);border:1px solid var(--line);border-radius:14px;padding:6px;
    box-shadow:0 12px 32px rgba(12,24,34,.16);animation:dropIn .15s ease;
  }
  .user-menu.open .dropdown{display:block}
  @keyframes dropIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
  .dropdown-head{padding:10px 12px 8px;border-bottom:1px solid var(--line);margin-bottom:6px}
  .dropdown-head strong{display:block;font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .dropdown-head span{display:block;font-size:11px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .dropdown-item{
    display:flex;align-items:center;gap:10px;width:100%;padding:10px 12px;border:none;background:none;
    border-radius:10px;font-family:'Lato',sans-serif;font-size:14px;font-weight:700;color:var(--ink);
    cursor:pointer;text-align:left;transition:background .15s,color .15s;
  }
  .dropdown-item:hover{background:var(--brand-soft);color:var(--brand)}
  .dropdown-item i{width:16px;text-align:center;color:var(--muted);flex-shrink:0}
  .dropdown-item:hover i{color:var(--brand)}
  .dropdown-item.danger{color:#991b1b}
  .dropdown-item.danger:hover{background:#fee2e2;color:#991b1b}
  .dropdown-item.danger i{color:#991b1b}
  .dropdown-sep{height:1px;background:var(--line);margin:6px 4px}

  .hero-banner{background:linear-gradient(135deg,#0f2942 0%,var(--brand-deep) 60%,var(--brand) 100%);color:#fff;padding:48px 40px;display:flex;align-items:center;justify-content:space-between;gap:24px}
  .hero-banner h1{font-family:'Plus Jakarta Sans',sans-serif;font-size:28px;font-weight:800;margin-bottom:6px}
  .hero-banner h1 span{color:var(--yellow)}
  .hero-banner p{font-size:15px;opacity:.85}
  .hero-actions{display:flex;gap:12px;margin-top:20px;flex-wrap:wrap}
  .btn-hero{display:inline-flex;align-items:center;gap:8px;padding:12px 22px;border-radius:999px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:14px;border:none;cursor:pointer;transition:background .2s,transform .15s}
  .btn-hero:hover{transform:translateY(-1px)}
  .btn-hero-yellow{background:var(--yellow);color:var(--ink)}
  .btn-hero-ghost{background:rgba(255,255,255,.15);color:#fff;border:1.5px solid rgba(255,255,255,.3)}
  .hero-stats{display:flex;gap:32px;flex-shrink:0}
  .hero-stat{text-align:center}
  .hero-stat-num{font-family:'Plus Jakarta Sans',sans-serif;font-size:28px;font-weight:800;color:var(--yellow)}
  .hero-stat-label{font-size:12px;opacity:.8;margin-top:2px}

  .content{max-width:1200px;margin:0 auto;padding:40px 32px}
  .section{margin-bottom:48px}
  .section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
  .section-title{font-family:'Plus Jakarta Sans',sans-serif;font-size:20px;font-weight:800;color:var(--ink);display:flex;align-items:center;gap:10px}
  .section-title i{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--brand),var(--brand-deep));color:#fff;display:grid;place-items:center;font-size:14px;flex-shrink:0}
  .btn-add{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:999px;background:var(--brand);color:#fff;font-size:13px;font-weight:700;border:none;cursor:pointer;box-shadow:0 2px 8px rgba(7,99,160,.2);transition:background .15s,transform .1s}
  .btn-add:hover{background:var(--brand-deep);transform:translateY(-1px)}

  .cards-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px}
  .card{background:var(--paper);border:1px solid var(--line);border-radius:18px;padding:20px;transition:transform .15s,box-shadow .15s}
  .card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md)}
  .card-badge{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;padding:4px 10px;border-radius:999px;margin-bottom:12px}
  .badge-green{background:#dcfce7;color:#166534}
  .badge-yellow{background:#fefce8;color:#854d0e}
  .badge-blue{background:var(--brand-soft);color:var(--brand)}
  .badge-red{background:#fee2e2;color:#991b1b}
  .badge-gray{background:var(--cream);color:var(--muted)}
  .card h3{font-family:'Plus Jakarta Sans',sans-serif;font-size:16px;font-weight:700;color:var(--ink);margin-bottom:6px;line-height:1.3}
  .card-meta{font-size:13px;color:var(--muted);display:flex;flex-direction:column;gap:4px;margin-bottom:16px}
  .card-meta span{display:flex;align-items:center;gap:6px}
  .card-meta i{width:14px;color:var(--brand);flex-shrink:0}
  .card-actions{display:flex;gap:8px}
  .btn-sm{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:999px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:background .15s,transform .1s}
  .btn-sm:hover{transform:translateY(-1px)}
  .btn-brand{background:var(--brand);color:#fff}
  .btn-brand:hover{background:var(--brand-deep)}
  .btn-outline{background:transparent;color:var(--brand);border:1.5px solid var(--brand)}
  .btn-outline:hover{background:var(--brand-soft)}

  /* Candidatos table */
  .table-card{background:var(--paper);border:1px solid var(--line);border-radius:18px;overflow:hidden}
  .table-card table{width:100%;border-collapse:collapse}
  .table-card th{background:var(--cream);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);padding:12px 20px;text-align:left;border-bottom:1px solid var(--line)}
  .table-card td{padding:14px 20px;font-size:14px;border-bottom:1px solid var(--line)}
  .table-card tr:last-child td{border-bottom:none}
  .table-card tr:hover td{background:var(--cream)}

  /* ── Formulário (modal da vitrine) ── */
  .form-group{margin-bottom:16px}
  .form-group label{display:block;font-size:13px;font-weight:700;color:var(--ink);margin-bottom:6px}
  .form-group label span.req{color:#dc2626}
  .form-control{width:100%;padding:10px 14px;border:1.5px solid var(--line);border-radius:10px;font-size:14px;font-family:'Lato',sans-serif;color:var(--ink);background:var(--paper);transition:border-color .15s,box-shadow .15s;outline:none}
  .form-control:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(7,99,160,.1)}
  .form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  .form-hint{display:block;margin-top:6px;font-size:12px;color:var(--muted)}
  .form-check{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--muted);cursor:pointer;user-select:none;margin-bottom:4px}
  .form-static{padding:10px 14px;border:1.5px dashed var(--line);border-radius:10px;font-size:14px;color:var(--muted);background:var(--cream)}
  @media(max-width:768px){.user-chip-name{max-width:120px}}
  .modal-footer{display:flex;gap:10px;justify-content:flex-end;margin-top:8px;padding:18px 28px;border-top:1px solid var(--line)}
  .btn-cancel{padding:10px 20px;border-radius:999px;font-size:14px;font-weight:700;color:var(--muted);border:1.5px solid var(--line);background:none;cursor:pointer}
  .btn-cancel:hover{background:var(--cream)}
  .btn-submit{padding:10px 24px;border-radius:999px;font-size:14px;font-weight:700;background:var(--brand);color:#fff;border:none;cursor:pointer}
  .btn-submit:hover{background:var(--brand-deep)}
  .alert-inline{padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;display:flex;gap:8px;align-items:flex-start}
  .alert-inline-error{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b}
  .alert-inline-info{background:var(--brand-soft);border:1px solid #93c5fd;color:var(--brand)}

  /* ── Upload + recorte 16:9 (mesmo padrão da página de Empresas Locais) ── */
  .upload-preview{display:none;position:relative;border-radius:10px;overflow:hidden;aspect-ratio:16/9;background:#000;margin-bottom:8px}
  .upload-preview img{width:100%;height:100%;object-fit:cover;display:block}
  .upload-label{display:flex;flex-direction:column;align-items:center;justify-content:center;border:2px dashed var(--line);border-radius:12px;padding:18px;cursor:pointer;background:#fafbfc;transition:border-color .15s,background .15s;min-height:76px}
  .upload-label:hover{border-color:var(--brand);background:var(--brand-soft)}
  .upload-label i{font-size:20px;color:var(--muted);margin-bottom:6px}
  .upload-label span{font-size:13px;color:var(--muted)}

  .crop-modal-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.72);z-index:200;align-items:center;justify-content:center;padding:20px}
  .crop-modal-backdrop.open{display:flex}
  .crop-modal{background:var(--paper);border-radius:18px;width:100%;max-width:680px;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,.3);display:flex;flex-direction:column}
  .crop-modal__head{padding:16px 20px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between}
  .crop-modal__head h3{margin:0;font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:16px}
  .crop-modal__head p{margin:0;font-size:12px;color:var(--muted)}
  .crop-modal__close{background:none;border:0;cursor:pointer;font-size:18px;color:var(--muted);line-height:1}
  .crop-modal__body{padding:16px;background:#1a1a2e;max-height:55vh;display:flex;align-items:center;justify-content:center}
  .crop-modal__body img{max-width:100%;max-height:50vh;display:block}
  .crop-modal__foot{padding:14px 20px;border-top:1px solid var(--line);display:flex;justify-content:flex-end;gap:12px}

  .empty-state{text-align:center;padding:40px 20px;color:var(--muted)}
  .empty-state i{font-size:40px;margin-bottom:12px;opacity:.4}
  .empty-state p{font-size:14px}

  /* ── Modal ── */
  .modal-backdrop{
    display:none;position:fixed;inset:0;background:rgba(12,24,34,.45);
    z-index:100;align-items:center;justify-content:center;padding:20px;
    backdrop-filter:blur(3px);
  }
  .modal-backdrop.open{display:flex}
  .modal{
    background:var(--paper);border-radius:20px;width:100%;max-width:860px;
    max-height:90vh;display:flex;flex-direction:column;
    box-shadow:0 24px 60px rgba(12,24,34,.22);animation:modalIn .2s ease;
  }
  @keyframes modalIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
  .modal-header{
    display:flex;align-items:center;justify-content:space-between;
    padding:22px 28px;border-bottom:1px solid var(--line);flex-shrink:0;
  }
  .modal-title{font-family:'Plus Jakarta Sans',sans-serif;font-size:18px;font-weight:800;color:var(--ink)}
  .modal-subtitle{font-size:13px;color:var(--muted);margin-top:2px}
  .modal-close{width:34px;height:34px;border-radius:50%;border:none;background:var(--cream);color:var(--muted);cursor:pointer;font-size:16px;display:grid;place-items:center;transition:background .15s,color .15s;flex-shrink:0}
  .modal-close:hover{background:#fee2e2;color:#991b1b}
  .modal-body{overflow-y:auto;padding:24px 28px;flex:1}

  /* candidato card inside modal */
  .applicant-card{
    border:1px solid var(--line);border-radius:16px;padding:20px;
    margin-bottom:12px;display:flex;flex-direction:column;gap:14px;
    transition:box-shadow .15s,border-color .15s;
  }
  .applicant-card:last-child{margin-bottom:0}
  .applicant-card:hover{box-shadow:var(--shadow-sm);border-color:rgba(7,99,160,.18)}
  .app-top{display:flex;align-items:center;gap:14px}
  .app-avatar{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--brand),var(--brand-deep));color:#fff;display:grid;place-items:center;font-size:18px;flex-shrink:0}
  .app-identity{flex:1;min-width:0}
  .app-name{font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:15px;color:var(--ink);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .app-role{font-size:13px;color:var(--muted)}
  .status-badge{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;padding:4px 10px;border-radius:999px;flex-shrink:0}
  .status-pending{background:#fefce8;color:#854d0e}
  .status-accepted{background:#dcfce7;color:#166534}
  .status-rejected{background:#fee2e2;color:#991b1b}
  .app-tags{display:flex;flex-wrap:wrap;gap:6px}
  .app-tag{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;padding:4px 10px;border-radius:999px;background:var(--cream);color:var(--muted)}
  .app-tag i{color:var(--brand);font-size:10px}
  .app-message{font-size:13px;color:var(--ink-soft);background:var(--cream);border-radius:10px;padding:12px 14px;line-height:1.6;border-left:3px solid var(--brand)}
  .app-footer{display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;padding-top:4px;border-top:1px solid var(--line)}
  .app-time{font-size:12px;color:var(--muted);display:flex;align-items:center;gap:5px}
  .app-btns{display:flex;gap:6px;flex-wrap:wrap}
  .modal-empty{text-align:center;padding:48px 20px;color:var(--muted)}
  .modal-empty i{font-size:44px;opacity:.3;display:block;margin-bottom:14px}
  .modal-empty p{font-size:14px}

  @media(max-width:768px){
    .hero-banner{flex-direction:column;padding:32px 24px}
    .hero-stats{gap:20px}
    .content{padding:24px 16px}
    .cards-grid{grid-template-columns:1fr}
    .table-card{overflow-x:auto}
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
  <div class="topbar-right">
    @php $nomeEmpresa = auth()->user()->empresa?->razao_social ?? auth()->user()->name; @endphp
    <div class="user-menu" id="userMenu">
      <button type="button" class="user-chip user-chip-btn" id="userMenuToggle"
              aria-haspopup="true" aria-expanded="false">
        <div class="user-avatar"><i class="fas fa-building" style="font-size:12px"></i></div>
        <span class="user-chip-name">{{ $nomeEmpresa }}</span>
        <i class="fas fa-chevron-down user-chip-caret"></i>
      </button>

      <div class="dropdown" role="menu">
        <div class="dropdown-head">
          <strong>{{ $nomeEmpresa }}</strong>
          <span>{{ auth()->user()->email }}</span>
        </div>

        <button type="button" class="dropdown-item" role="menuitem" onclick="openModal('modal-dados-empresa')">
          <i class="fas fa-building"></i> Meus dados
        </button>
        <button type="button" class="dropdown-item" role="menuitem" onclick="openModal('modal-senha-empresa')">
          <i class="fas fa-key"></i> Alterar senha
        </button>
        <a href="#vitrine" class="dropdown-item" role="menuitem">
          <i class="fas fa-store"></i> Minha vitrine
        </a>

        <div class="dropdown-sep"></div>

        <form action="{{ route('empresa.logout') }}" method="POST">
          @csrf
          <button type="submit" class="dropdown-item danger" role="menuitem">
            <i class="fas fa-sign-out-alt"></i> Sair
          </button>
        </form>
      </div>
    </div>
  </div>
</nav>

<div class="hero-banner">
  <div>
    <h1>Portal da <span>Empresa</span></h1>
    <p>Gerencie suas vagas e encontre os melhores talentos de Vitória de Santo Antão.</p>
    <div class="hero-actions">
      <a href="{{ route('portal.empresa.vagas.create') }}" class="btn-hero btn-hero-yellow">
        <i class="fas fa-plus"></i> Publicar nova vaga
      </a>
    </div>
  </div>
  <div class="hero-stats">
    <div class="hero-stat">
      <div class="hero-stat-num">{{ $minhasVagas->count() }}</div>
      <div class="hero-stat-label">Vagas publicadas</div>
    </div>
    <div class="hero-stat">
      <div class="hero-stat-num">{{ $minhasVagas->where('status','active')->count() }}</div>
      <div class="hero-stat-label">Vagas ativas</div>
    </div>
    <div class="hero-stat">
      <div class="hero-stat-num">{{ $totalCandidaturas }}</div>
      <div class="hero-stat-label">Candidaturas</div>
    </div>
  </div>
</div>

<div class="content">

  @if(session('success'))
    <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;border-radius:10px;padding:12px 18px;margin-bottom:20px;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:10px;padding:12px 18px;margin-bottom:20px;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px">
      <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
  @endif

  {{-- Minhas Vagas --}}
  <div class="section">
    <div class="section-header">
      <div class="section-title"><i class="fas fa-briefcase"></i> Minhas Vagas</div>
      <a href="{{ route('portal.empresa.vagas.create') }}" class="btn-add"><i class="fas fa-plus"></i> Nova vaga</a>
    </div>
    @if($minhasVagas->isEmpty())
      <div class="empty-state" style="background:var(--paper);border:1px solid var(--line);border-radius:18px">
        <i class="fas fa-briefcase"></i>
        <p>Você ainda não publicou nenhuma vaga.</p>
        <a href="{{ route('portal.empresa.vagas.create') }}" class="btn-sm btn-brand" style="margin:12px auto 0">
          <i class="fas fa-plus"></i> Publicar primeira vaga
        </a>
      </div>
    @else
      <div class="cards-grid">
        @foreach($minhasVagas as $vaga)
          <div class="card">
            <div class="card-badge {{ $vaga->status === 'active' ? 'badge-green' : ($vaga->status === 'filled' ? 'badge-blue' : 'badge-red') }}">
              <i class="fas fa-circle" style="font-size:7px"></i> {{ $vaga->status_label }}
            </div>
            <h3>{{ $vaga->position }}</h3>
            <div class="card-meta">
              @if($vaga->remuneration && (float) $vaga->remuneration > 0)
                <span><i class="fas fa-money-bill-wave"></i> R$ {{ number_format((float) $vaga->remuneration, 2, ',', '.') }}</span>
              @endif
              <span><i class="fas fa-users"></i> {{ $vaga->quantity }} vaga{{ $vaga->quantity > 1 ? 's' : '' }}</span>
              @if($vaga->interest_area)
                <span><i class="fas fa-tag"></i> {{ $vaga->interest_area }}</span>
              @endif
            </div>
            <div class="card-meta" style="margin-top:8px;margin-bottom:0">
              <span><i class="fas fa-paper-plane"></i>
                <strong>{{ $vaga->applications_count }}</strong> candidatura{{ $vaga->applications_count !== 1 ? 's' : '' }}
              </span>
            </div>
            <div class="card-actions" style="margin-top:12px;flex-wrap:wrap">
              <a href="{{ route('portal.empresa.vagas.edit', $vaga) }}" class="btn-sm btn-outline">
                <i class="fas fa-pen"></i> Editar
              </a>
              <button type="button" class="btn-sm btn-brand" onclick="openModal('modal-{{ $vaga->id }}')">
                <i class="fas fa-users"></i> Ver candidatos
              </button>
              @if($vaga->status !== 'inactive')
                <form method="POST" action="{{ route('portal.empresa.vagas.encerrar', $vaga) }}"
                  onsubmit="return confirm('Encerrar esta vaga?')" style="display:contents">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn-sm" style="background:#fff7ed;color:#c2410c;border:1.5px solid #fed7aa">
                    <i class="fas fa-ban"></i> Encerrar
                  </button>
                </form>
              @endif
              <form method="POST" action="{{ route('portal.empresa.vagas.destroy', $vaga) }}"
                onsubmit="return confirm('Excluir esta vaga permanentemente?')" style="display:contents">
                @csrf @method('DELETE')
                <button type="submit" class="btn-sm" style="background:#fff1f2;color:#be123c;border:1.5px solid #fecdd3;padding:8px 12px">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Minha Vitrine (cadastro público em /empresas-locais) --}}
  <div class="section" id="vitrine">
    <div class="section-header">
      <div class="section-title"><i class="fas fa-store"></i> Minha Vitrine</div>
      <a href="{{ route('empresas-locais') }}#cadastro" class="btn-add" target="_blank">
        <i class="fas fa-external-link-alt"></i> Ver página
      </a>
    </div>

    @if($minhasVitrines->isEmpty())
      <div class="empty-state" style="background:var(--paper);border:1px solid var(--line);border-radius:18px">
        <i class="fas fa-store"></i>
        <p>
          Nenhum cadastro em Empresas Locais vinculado à sua conta.<br>
          <a href="{{ route('empresas-locais') }}#cadastro" target="_blank" style="color:var(--brand);font-weight:700">Cadastre sua empresa</a>
          usando o mesmo CNPJ ou e-mail para poder editá-la por aqui.
        </p>
      </div>
    @else
      <div class="cards-grid">
        @foreach($minhasVitrines as $vitrine)
          <div class="card"
            data-vitrine-id="{{ $vitrine->id }}"
            data-url="{{ route('portal.empresa.servicos.update', $vitrine) }}"
            data-name="{{ e($vitrine->name) }}"
            data-service-title="{{ e($vitrine->service_title) }}"
            data-whatsapp="{{ $vitrine->whatsapp }}"
            data-instagram="{{ $vitrine->instagram }}"
            data-optional-info="{{ e($vitrine->optional_info) }}"
            data-image="{{ $vitrine->business_image ? Storage::disk('public')->url($vitrine->business_image) : '' }}">
            <div class="card-badge
              @if($vitrine->status === 'active') badge-green
              @elseif($vitrine->status === 'pending') badge-yellow
              @else badge-gray
              @endif">
              <i class="fas fa-circle" style="font-size:7px"></i>
              {{ $vitrine->status === 'pending' ? 'Em análise' : $vitrine->status_label }}
            </div>
            <h3>{{ $vitrine->service_title }}</h3>
            <div class="card-meta">
              <span><i class="fas fa-building"></i> {{ $vitrine->name }}</span>
              <span><i class="fab fa-whatsapp"></i> {{ $vitrine->whatsapp }}</span>
              @if($vitrine->instagram)
                <span><i class="fab fa-instagram"></i> {{ $vitrine->instagram }}</span>
              @endif
            </div>
            <div class="card-actions">
              <button type="button" class="btn-sm btn-brand" onclick="abrirEditarVitrine(this.closest('.card'))">
                <i class="fas fa-pen"></i> Editar
              </button>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

</div>

{{-- ── Modal: dados cadastrais da empresa ── --}}
<div class="modal-backdrop" id="modal-dados-empresa">
  <div class="modal" role="dialog" aria-modal="true" style="max-width:560px">
    <div class="modal-header">
      <div>
        <div class="modal-title"><i class="fas fa-building" style="color:var(--brand);margin-right:8px"></i>Dados da Empresa</div>
        <div class="modal-subtitle">Informações da conta e de contato</div>
      </div>
      <button class="modal-close" onclick="closeModal('modal-dados-empresa')"><i class="fas fa-times"></i></button>
    </div>

    <form method="POST" action="{{ route('portal.empresa.dados.update') }}" style="display:contents">
      @csrf
      @method('PUT')

      <div class="modal-body">
        @if($errors->empresa->any())
          <div class="alert-inline alert-inline-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
              @foreach($errors->empresa->all() as $erro)
                <div>{{ $erro }}</div>
              @endforeach
            </div>
          </div>
        @endif

        <div class="form-group">
          <label>Razão social <span class="req">*</span></label>
          <input type="text" name="razao_social" class="form-control" required
                 value="{{ old('razao_social', $empresa->razao_social ?? auth()->user()->name) }}">
        </div>

        <div class="form-group">
          <label>E-mail de acesso <span class="req">*</span></label>
          <input type="email" name="email" class="form-control" required
                 value="{{ old('email', auth()->user()->email) }}">
          <span class="form-hint">É com este e-mail que a empresa entra no portal.</span>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Telefone</label>
            <input type="text" name="telefone" class="form-control" placeholder="(81) 99999-9999"
                   value="{{ old('telefone', $empresa->telefone ?? '') }}">
          </div>
          <div class="form-group">
            <label>Cidade</label>
            <input type="text" name="cidade" class="form-control" placeholder="Vitória de Santo Antão"
                   value="{{ old('cidade', $empresa->cidade ?? '') }}">
          </div>
        </div>

        <div class="form-group">
          <label>Descrição da empresa</label>
          <textarea name="descricao" class="form-control" rows="3"
                    placeholder="Ramo de atuação, porte, diferenciais...">{{ old('descricao', $empresa->descricao ?? '') }}</textarea>
          <span class="form-hint">Aparece para os candidatos junto das suas vagas.</span>
        </div>

        <div class="form-group">
          <label>CNPJ</label>
          <div class="form-static">{{ $empresa->cnpj ?? 'Não informado' }}</div>
          <span class="form-hint">
            O CNPJ identifica a conta e liga a vitrine de Empresas Locais a ela, por isso não pode ser
            alterado por aqui. Se estiver errado, fale com a equipe do Empreende Vitória.
          </span>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('modal-dados-empresa')">Cancelar</button>
        <button type="submit" class="btn-submit"><i class="fas fa-check"></i> Salvar dados</button>
      </div>
    </form>
  </div>
</div>

{{-- ── Modal: alterar senha ── --}}
<div class="modal-backdrop" id="modal-senha-empresa">
  <div class="modal" role="dialog" aria-modal="true" style="max-width:460px">
    <div class="modal-header">
      <div>
        <div class="modal-title"><i class="fas fa-key" style="color:var(--brand);margin-right:8px"></i>Alterar Senha</div>
        <div class="modal-subtitle">Senha de acesso ao portal</div>
      </div>
      <button class="modal-close" onclick="closeModal('modal-senha-empresa')"><i class="fas fa-times"></i></button>
    </div>

    <form method="POST" action="{{ route('portal.empresa.senha.update') }}" style="display:contents">
      @csrf
      @method('PUT')

      <div class="modal-body">
        <div class="form-group">
          <label>Senha atual <span class="req">*</span></label>
          <input type="password" name="current_password" autocomplete="current-password" required
                 class="form-control campo-senha" style="{{ $errors->senha->has('current_password') ? 'border-color:#dc2626' : '' }}">
          @error('current_password', 'senha') <span class="form-hint" style="color:#dc2626;font-weight:700">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label>Nova senha <span class="req">*</span></label>
          <input type="password" name="password" autocomplete="new-password" minlength="8" required
                 class="form-control campo-senha" style="{{ $errors->senha->has('password') ? 'border-color:#dc2626' : '' }}">
          @error('password', 'senha')
            <span class="form-hint" style="color:#dc2626;font-weight:700">{{ $message }}</span>
          @else
            <span class="form-hint">Mínimo de 8 caracteres.</span>
          @enderror
        </div>

        <div class="form-group">
          <label>Confirmar nova senha <span class="req">*</span></label>
          <input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required
                 class="form-control campo-senha">
        </div>

        <label class="form-check">
          <input type="checkbox" onchange="mostrarSenhasEmpresa(this.checked)"> Mostrar senhas
        </label>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('modal-senha-empresa')">Cancelar</button>
        <button type="submit" class="btn-submit"><i class="fas fa-check"></i> Salvar nova senha</button>
      </div>
    </form>
  </div>
</div>

{{-- ── Modal: editar vitrine ── --}}
<div class="modal-backdrop" id="modal-editar-vitrine">
  <div class="modal" role="dialog" aria-modal="true" style="max-width:560px">
    <div class="modal-header">
      <div>
        <div class="modal-title"><i class="fas fa-store" style="color:var(--brand);margin-right:8px"></i>Editar Vitrine</div>
        <div class="modal-subtitle">Como sua empresa aparece na página de Empresas Locais</div>
      </div>
      <button class="modal-close" onclick="closeModal('modal-editar-vitrine')"><i class="fas fa-times"></i></button>
    </div>

    <form method="POST" action="" id="form-editar-vitrine" style="display:contents">
      @csrf
      @method('PUT')

      <div class="modal-body">
        @if($errors->servico->any())
          <div class="alert-inline alert-inline-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
              @foreach($errors->servico->all() as $erro)
                <div>{{ $erro }}</div>
              @endforeach
            </div>
          </div>
        @endif

        <div class="alert-inline alert-inline-info">
          <i class="fas fa-info-circle"></i>
          <div>Ao salvar, o cadastro volta para análise da equipe e fica fora do site até ser aprovado de novo.</div>
        </div>

        <input type="hidden" name="_service_id" id="editar-vitrine-id">

        <div class="form-group">
          <label>Nome da empresa <span class="req">*</span></label>
          <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
          <label>O que sua empresa oferece? <span class="req">*</span></label>
          <input type="text" name="service_title" class="form-control" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>WhatsApp <span class="req">*</span></label>
            <input type="text" name="whatsapp" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Instagram</label>
            <input type="text" name="instagram" class="form-control" placeholder="@suaempresa">
          </div>
        </div>

        <div class="form-group">
          <label>Informações adicionais</label>
          <textarea name="optional_info" class="form-control" rows="3"
                    placeholder="Horários de atendimento, área de atuação, diferenciais..."></textarea>
        </div>

        <div class="form-group">
          <label>Imagem da empresa <span style="font-weight:400;color:var(--muted)">(16:9)</span></label>
          <div class="upload-preview" id="vitrine-preview">
            <img id="vitrine-preview-img" src="" alt="">
          </div>
          <label class="upload-label" for="vitrine-file">
            <i class="fas fa-camera"></i>
            <span id="vitrine-upload-texto">Clique para adicionar a imagem da empresa</span>
            <span style="font-size:11px;margin-top:2px">JPG, PNG ou WEBP · máx. 5MB</span>
          </label>
          <input type="file" id="vitrine-file" accept="image/jpeg,image/png,image/webp" style="display:none">
          <input type="hidden" name="business_image" id="vitrine-base64">
          <span class="form-hint">Sem escolher uma imagem nova, a atual é mantida.</span>
        </div>

        <p class="form-hint">
          E-mail e CNPJ não podem ser alterados aqui: são eles que ligam o cadastro à sua conta.
          Se precisar corrigi-los, fale com a equipe do Empreende Vitória.
        </p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('modal-editar-vitrine')">Cancelar</button>
        <button type="submit" class="btn-submit"><i class="fas fa-check"></i> Salvar alterações</button>
      </div>
    </form>
  </div>
</div>

{{-- ── Modal: recorte 16:9 da imagem da vitrine ── --}}
<div class="crop-modal-backdrop" id="vitrine-crop-backdrop">
  <div class="crop-modal">
    <div class="crop-modal__head">
      <div>
        <h3>Ajustar imagem</h3>
        <p>Arraste e redimensione para enquadrar no formato 16:9</p>
      </div>
      <button type="button" class="crop-modal__close" onclick="fecharCropVitrine()"><i class="fas fa-times"></i></button>
    </div>
    <div class="crop-modal__body">
      <img id="vitrine-crop-img" src="" alt="">
    </div>
    <div class="crop-modal__foot">
      <button type="button" class="btn-cancel" onclick="fecharCropVitrine()">Cancelar</button>
      <button type="button" class="btn-submit" onclick="aplicarCropVitrine()"><i class="fas fa-check"></i> Aplicar</button>
    </div>
  </div>
</div>

{{-- ── Modais de candidatos por vaga ── --}}
@foreach($minhasVagas as $vaga)
  <div class="modal-backdrop" id="modal-{{ $vaga->id }}">
    <div class="modal" role="dialog" aria-modal="true">

      <div class="modal-header">
        <div>
          <div class="modal-title"><i class="fas fa-users" style="color:var(--brand);margin-right:8px"></i>Candidatos — {{ $vaga->position }}</div>
          <div class="modal-subtitle">{{ $vaga->company_name }} · {{ $vaga->applications_count }} candidatura{{ $vaga->applications_count !== 1 ? 's' : '' }}</div>
        </div>
        <button class="modal-close" onclick="closeModal('modal-{{ $vaga->id }}')">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="modal-body">
        @if($vaga->applications->isEmpty())
          <div class="modal-empty">
            <i class="fas fa-inbox"></i>
            <p>Nenhuma candidatura recebida ainda para esta vaga.</p>
          </div>
        @else
          @foreach($vaga->applications->sortByDesc('created_at') as $app)
            <div class="applicant-card">

              {{-- Topo: avatar + nome/cargo + status --}}
              <div class="app-top">
                <div class="app-avatar"><i class="fas fa-user-tie"></i></div>
                <div class="app-identity">
                  <div class="app-name">{{ $app->seeker?->name ?? '—' }}</div>
                  <div class="app-role">{{ $app->seeker?->job_function ?? 'Cargo não informado' }}</div>
                </div>
                <span class="status-badge status-{{ $app->status }}">
                  <i class="fas fa-circle" style="font-size:7px"></i> {{ $app->status_label }}
                </span>
              </div>

              {{-- Tags de info --}}
              @if($app->seeker?->interest_area || $app->seeker?->city || $app->seeker?->experience)
                <div class="app-tags">
                  @if($app->seeker?->interest_area)
                    <span class="app-tag"><i class="fas fa-tag"></i>{{ $app->seeker->interest_area }}</span>
                  @endif
                  @if($app->seeker?->city)
                    <span class="app-tag"><i class="fas fa-map-marker-alt"></i>{{ $app->seeker->city }}{{ $app->seeker->state ? ', '.$app->seeker->state : '' }}</span>
                  @endif
                  @if($app->seeker?->experience)
                    <span class="app-tag"><i class="fas fa-briefcase"></i>{{ $app->seeker->experience }}</span>
                  @endif
                </div>
              @endif

              {{-- Mensagem de candidatura --}}
              @if($app->message)
                <div class="app-message">"{{ $app->message }}"</div>
              @endif

              {{-- Rodapé: data + ações --}}
              <div class="app-footer">
                <span class="app-time"><i class="fas fa-clock"></i> {{ $app->created_at->diffForHumans() }}</span>
                <div class="app-btns">
                  @if($app->seeker)
                    <a href="{{ route('portal.empresa.candidato.show', $app->seeker) }}"
                      class="btn-sm btn-outline" style="font-size:12px;padding:7px 14px">
                      <i class="fas fa-id-card"></i> Ver perfil
                    </a>
                  @endif
                  @if($app->status !== 'accepted')
                    <form method="POST" action="{{ route('empresa.job-applications.status', $app) }}">
                      @csrf @method('PATCH')
                      <input type="hidden" name="status" value="accepted">
                      <button type="submit" class="btn-sm" style="background:#dcfce7;color:#166534;font-size:12px;padding:7px 14px">
                        <i class="fas fa-check"></i> Aceitar
                      </button>
                    </form>
                  @endif
                  @if($app->status !== 'rejected')
                    <form method="POST" action="{{ route('empresa.job-applications.status', $app) }}">
                      @csrf @method('PATCH')
                      <input type="hidden" name="status" value="rejected">
                      <button type="submit" class="btn-sm" style="background:#fee2e2;color:#991b1b;font-size:12px;padding:7px 14px">
                        <i class="fas fa-times"></i> Recusar
                      </button>
                    </form>
                  @endif
                </div>
              </div>

            </div>
          @endforeach
        @endif
      </div>

    </div>
  </div>
@endforeach

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
  // ── Menu da conta (dropdown do nome da empresa) ──
  const userMenu       = document.getElementById('userMenu');
  const userMenuToggle = document.getElementById('userMenuToggle');

  function fecharUserMenu() {
    userMenu.classList.remove('open');
    userMenuToggle.setAttribute('aria-expanded', 'false');
  }

  userMenuToggle.addEventListener('click', function (e) {
    e.stopPropagation();
    const aberto = userMenu.classList.toggle('open');
    this.setAttribute('aria-expanded', aberto ? 'true' : 'false');
  });

  // Escolher uma opção fecha o menu (inclusive as que abrem modal).
  userMenu.querySelectorAll('.dropdown-item').forEach(item => {
    item.addEventListener('click', fecharUserMenu);
  });

  document.addEventListener('click', function (e) {
    if (!userMenu.contains(e.target)) fecharUserMenu();
  });

  function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
  }
  // Fechar clicando fora do modal
  document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
    backdrop.addEventListener('click', e => {
      if (e.target === backdrop) closeModal(backdrop.id);
    });
  });
  // Fechar com ESC
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      fecharUserMenu();
      // Com o recorte aberto, ESC fecha só ele — o modal de edição continua.
      if (document.getElementById('vitrine-crop-backdrop').classList.contains('open')) {
        fecharCropVitrine();
        return;
      }
      document.querySelectorAll('.modal-backdrop.open').forEach(m => closeModal(m.id));
    }
  });

  // ── Vitrine: edição do cadastro público de Empresas Locais ──
  function abrirEditarVitrine(card) {
    const d = card.dataset;
    const f = document.getElementById('form-editar-vitrine');

    f.action = d.url;
    f.querySelector('[name="_service_id"]').value   = d.vitrineId;
    f.querySelector('[name="name"]').value          = d.name;
    f.querySelector('[name="service_title"]').value = d.serviceTitle;
    f.querySelector('[name="whatsapp"]').value      = d.whatsapp;
    f.querySelector('[name="instagram"]').value     = d.instagram || '';
    f.querySelector('[name="optional_info"]').value = d.optionalInfo || '';

    document.getElementById('vitrine-base64').value = '';
    document.getElementById('vitrine-file').value   = '';
    mostrarPreviaVitrine(d.image || '');
    document.getElementById('vitrine-upload-texto').textContent =
      d.image ? 'Clique para trocar a imagem da empresa' : 'Clique para adicionar a imagem da empresa';

    openModal('modal-editar-vitrine');
  }

  function mostrarPreviaVitrine(src) {
    document.getElementById('vitrine-preview-img').src = src;
    document.getElementById('vitrine-preview').style.display = src ? 'block' : 'none';
  }

  let vitrineCropper = null;

  document.getElementById('vitrine-file').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
      alert('A imagem deve ter no máximo 5MB.');
      this.value = '';
      return;
    }

    const reader = new FileReader();
    reader.onload = e => abrirCropVitrine(e.target.result);
    reader.readAsDataURL(file);
  });

  function abrirCropVitrine(src) {
    const img = document.getElementById('vitrine-crop-img');
    img.src = src;
    document.getElementById('vitrine-crop-backdrop').classList.add('open');

    // Espera o render da imagem antes de iniciar o cropper.
    setTimeout(() => {
      if (vitrineCropper) vitrineCropper.destroy();
      vitrineCropper = new Cropper(img, {
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

  function fecharCropVitrine() {
    document.getElementById('vitrine-crop-backdrop').classList.remove('open');
    if (vitrineCropper) { vitrineCropper.destroy(); vitrineCropper = null; }
    document.getElementById('vitrine-file').value = '';
  }

  function aplicarCropVitrine() {
    if (!vitrineCropper) return;

    const base64 = vitrineCropper.getCroppedCanvas({ width: 1280, height: 720 }).toDataURL('image/jpeg', 0.88);
    document.getElementById('vitrine-base64').value = base64;
    mostrarPreviaVitrine(base64);
    document.getElementById('vitrine-upload-texto').textContent = 'Clique para trocar a imagem da empresa';
    fecharCropVitrine();
  }

  document.getElementById('vitrine-crop-backdrop').addEventListener('click', function (e) {
    if (e.target === this) fecharCropVitrine();
  });

  function mostrarSenhasEmpresa(visivel) {
    document.querySelectorAll('.campo-senha').forEach(function (campo) {
      campo.type = visivel ? 'text' : 'password';
    });
  }

  {{-- Dados ou senha recusados: reabre o modal correspondente com os erros. --}}
  @if($errors->empresa->any())
    openModal('modal-dados-empresa');
  @endif
  @if($errors->senha->any())
    openModal('modal-senha-empresa');
  @endif

  {{-- Edição recusada: reabre o modal do cadastro certo com o que foi digitado. --}}
  @if($errors->servico->any())
    (function () {
      const card = document.querySelector('[data-vitrine-id="{{ old('_service_id') }}"]');
      if (!card) return;
      abrirEditarVitrine(card);
      const f = document.getElementById('form-editar-vitrine');
      f.querySelector('[name="name"]').value          = @json(old('name'));
      f.querySelector('[name="service_title"]').value = @json(old('service_title'));
      f.querySelector('[name="whatsapp"]').value      = @json(old('whatsapp'));
      f.querySelector('[name="instagram"]').value     = @json(old('instagram', ''));
      f.querySelector('[name="optional_info"]').value = @json(old('optional_info', ''));
      card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    })();
  @endif
</script>
</body>
</html>
