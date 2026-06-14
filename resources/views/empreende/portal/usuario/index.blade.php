<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Portal do Candidato — Empreende Vitória</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  :root{
    --brand:#0763A0;--brand-deep:#044a7a;--brand-soft:#e8f1f9;
    --yellow:#ffc52d;--ink:#0c1822;--muted:#6c7a8a;
    --paper:#ffffff;--cream:#f3f6fa;--line:rgba(12,24,34,.08);
    --sidebar:240px;
    --topbar:64px;
    --shadow-sm:0 1px 2px rgba(12,24,34,.06),0 2px 8px rgba(12,24,34,.04);
    --shadow-md:0 8px 28px rgba(12,24,34,.10);
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Lato',system-ui,sans-serif;color:var(--ink);background:var(--cream);min-height:100vh;-webkit-font-smoothing:antialiased}
  a{color:inherit;text-decoration:none}

  /* ── Topbar ── */
  .topbar{
    position:fixed;top:0;left:0;right:0;height:var(--topbar);z-index:50;
    background:var(--paper);box-shadow:var(--shadow-sm);
    display:flex;align-items:center;justify-content:space-between;padding:0 24px;
  }
  .brand{display:flex;align-items:center;gap:10px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:16px;color:var(--ink)}
  .brand-mark{width:34px;height:34px;border-radius:9px;background:var(--brand);color:#fff;display:grid;place-items:center;font-weight:900;font-size:13px;flex-shrink:0}
  .topbar-right{display:flex;align-items:center;gap:8px}
  .user-chip{display:flex;align-items:center;gap:8px;padding:6px 14px 6px 8px;border-radius:999px;background:var(--cream);font-size:14px;font-weight:700}
  .user-avatar{width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--brand),var(--brand-deep));color:#fff;display:grid;place-items:center;font-size:11px;font-weight:900;flex-shrink:0}
  .btn-logout{padding:8px 16px;border-radius:999px;font-size:13px;font-weight:700;color:var(--muted);border:none;background:none;cursor:pointer;transition:background .15s,color .15s}
  .btn-logout:hover{background:var(--brand-soft);color:var(--brand)}
  .menu-toggle{display:none;background:none;border:none;cursor:pointer;font-size:20px;color:var(--ink);padding:6px}

  /* ── Layout ── */
  .layout{display:flex;padding-top:var(--topbar);min-height:100vh}

  /* ── Sidebar ── */
  .sidebar{
    position:fixed;top:var(--topbar);left:0;bottom:0;width:var(--sidebar);
    background:var(--paper);border-right:1px solid var(--line);
    display:flex;flex-direction:column;overflow-y:auto;z-index:40;
    transition:transform .25s ease;
  }
  .sidebar-user{padding:24px 20px 16px;border-bottom:1px solid var(--line)}
  .sidebar-avatar{width:52px;height:52px;border-radius:16px;background:linear-gradient(135deg,var(--brand),var(--brand-deep));color:#fff;display:grid;place-items:center;font-size:20px;margin-bottom:10px}
  .sidebar-name{font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:15px;color:var(--ink);margin-bottom:2px}
  .sidebar-role{font-size:12px;color:var(--muted)}

  .sidebar-stats{display:grid;grid-template-columns:1fr 1fr;gap:8px;padding:16px 20px;border-bottom:1px solid var(--line)}
  .stat-pill{background:var(--cream);border-radius:10px;padding:10px 12px;text-align:center}
  .stat-pill-num{font-family:'Plus Jakarta Sans',sans-serif;font-size:20px;font-weight:800;color:var(--brand)}
  .stat-pill-label{font-size:10px;color:var(--muted);margin-top:2px;text-transform:uppercase;letter-spacing:.05em}

  .sidebar-nav{padding:12px 12px;flex:1}
  .nav-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);padding:8px 8px 4px}
  .nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;font-size:14px;font-weight:700;color:var(--muted);cursor:pointer;transition:background .15s,color .15s;border:none;background:none;width:100%;text-align:left}
  .nav-item:hover{background:var(--brand-soft);color:var(--brand)}
  .nav-item.active{background:var(--brand-soft);color:var(--brand)}
  .nav-item i{width:18px;text-align:center;flex-shrink:0}
  .nav-badge{margin-left:auto;background:var(--brand);color:#fff;font-size:10px;font-weight:800;padding:2px 7px;border-radius:999px}

  .sidebar-footer{padding:16px 20px;border-top:1px solid var(--line)}

  /* ── Main content ── */
  .main{margin-left:var(--sidebar);flex:1;padding:32px;min-width:0}

  /* ── Section panels ── */
  .panel{display:none}
  .panel.active{display:block}

  .panel-header{margin-bottom:24px}
  .panel-title{font-family:'Plus Jakarta Sans',sans-serif;font-size:22px;font-weight:800;color:var(--ink);margin-bottom:4px}
  .panel-sub{font-size:14px;color:var(--muted)}

  /* ── Cards ── */
  .cards-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px}
  .card{background:var(--paper);border:1px solid var(--line);border-radius:18px;padding:20px;display:flex;flex-direction:column;transition:transform .15s,box-shadow .15s}
  .card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md)}
  .card-badge{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;padding:4px 10px;border-radius:999px;margin-bottom:12px;align-self:flex-start}
  .badge-green{background:#dcfce7;color:#166534}
  .badge-blue{background:var(--brand-soft);color:var(--brand)}
  .badge-yellow{background:#fefce8;color:#854d0e}
  .badge-red{background:#fee2e2;color:#991b1b}
  .badge-gray{background:var(--cream);color:var(--muted)}
  .card h3{font-family:'Plus Jakarta Sans',sans-serif;font-size:15px;font-weight:700;color:var(--ink);margin-bottom:8px;line-height:1.35}
  .card-meta{font-size:13px;color:var(--muted);display:flex;flex-direction:column;gap:4px;margin-bottom:16px;flex:1}
  .card-meta span{display:flex;align-items:center;gap:6px}
  .card-meta i{width:14px;color:var(--brand);flex-shrink:0}
  .card-actions{display:flex;gap:8px;flex-wrap:wrap}
  .btn-sm{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:999px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:background .15s,transform .1s;white-space:nowrap}
  .btn-sm:hover{transform:translateY(-1px)}
  .btn-brand{background:var(--brand);color:#fff;box-shadow:0 2px 8px rgba(7,99,160,.2)}
  .btn-brand:hover{background:var(--brand-deep)}
  .btn-outline{background:transparent;color:var(--brand);border:1.5px solid var(--brand)}
  .btn-outline:hover{background:var(--brand-soft)}
  .btn-yellow{background:var(--yellow);color:var(--ink)}
  .btn-yellow:hover{background:#e6b000}

  /* ── Profile card ── */
  .profile-card{background:var(--paper);border:1px solid var(--line);border-radius:18px;padding:28px;display:flex;align-items:center;gap:24px}
  .profile-avatar{width:68px;height:68px;border-radius:18px;background:linear-gradient(135deg,var(--brand),var(--brand-deep));color:#fff;display:grid;place-items:center;font-size:26px;flex-shrink:0}
  .profile-info h3{font-family:'Plus Jakarta Sans',sans-serif;font-size:18px;font-weight:800;color:var(--ink);margin-bottom:4px}
  .profile-info p{font-size:14px;color:var(--muted)}
  .profile-actions{margin-left:auto;display:flex;gap:8px;flex-shrink:0}

  /* ── Alert ── */
  .alert{padding:12px 18px;border-radius:10px;margin-bottom:20px;font-size:14px;font-weight:600;display:flex;align-items:center;gap:10px}
  .alert-success{background:#dcfce7;border:1px solid #86efac;color:#166534}
  .alert-error{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b}
  .alert-info{background:var(--brand-soft);border:1px solid #93c5fd;color:var(--brand)}

  /* ── Empty state ── */
  .empty-state{text-align:center;padding:48px 20px;color:var(--muted)}
  .empty-state i{font-size:44px;margin-bottom:14px;opacity:.35;display:block}
  .empty-state p{font-size:14px}

  /* ── Overlay (mobile) ── */
  .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:39}

  /* ── Modal ── */
  .modal-backdrop{display:none;position:fixed;inset:0;background:rgba(12,24,34,.45);backdrop-filter:blur(3px);z-index:100;align-items:center;justify-content:center;padding:16px}
  .modal-backdrop.open{display:flex}
  .modal{background:var(--paper);border-radius:20px;padding:32px;width:100%;max-width:560px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(12,24,34,.18);animation:modal-in .2s ease}
  @keyframes modal-in{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
  .modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px}
  .modal-title{font-family:'Plus Jakarta Sans',sans-serif;font-size:18px;font-weight:800;color:var(--ink)}
  .modal-close{background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted);padding:4px;line-height:1;transition:color .15s}
  .modal-close:hover{color:var(--ink)}
  .form-group{margin-bottom:16px}
  .form-group label{display:block;font-size:13px;font-weight:700;color:var(--ink);margin-bottom:6px}
  .form-group label span.req{color:#dc2626}
  .form-control{width:100%;padding:10px 14px;border:1.5px solid var(--line);border-radius:10px;font-size:14px;font-family:'Lato',sans-serif;color:var(--ink);background:var(--paper);transition:border-color .15s,box-shadow .15s;outline:none}
  .form-control:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(7,99,160,.1)}
  .form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  .modal-footer{display:flex;gap:10px;justify-content:flex-end;margin-top:24px;padding-top:20px;border-top:1px solid var(--line)}
  .btn-cancel{padding:10px 20px;border-radius:999px;font-size:14px;font-weight:700;color:var(--muted);border:1.5px solid var(--line);background:none;cursor:pointer;transition:background .15s}
  .btn-cancel:hover{background:var(--cream)}
  .btn-submit{padding:10px 24px;border-radius:999px;font-size:14px;font-weight:700;background:var(--brand);color:#fff;border:none;cursor:pointer;transition:background .15s}
  .btn-submit:hover{background:var(--brand-deep)}
  @media(max-width:600px){.form-row{grid-template-columns:1fr}}

  /* ── Responsive ── */
  @media(max-width:900px){
    .sidebar{transform:translateX(-100%)}
    .sidebar.open{transform:translateX(0)}
    .sidebar-overlay.open{display:block}
    .main{margin-left:0}
    .menu-toggle{display:block}
  }
  @media(max-width:600px){
    .main{padding:20px 16px}
    .cards-grid{grid-template-columns:1fr}
    .profile-card{flex-direction:column;text-align:center}
    .profile-actions{margin:0 auto}
  }
</style>
</head>
<body>

{{-- Topbar --}}
<nav class="topbar">
  <div style="display:flex;align-items:center;gap:12px">
    <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
    <a href="{{ route('home') }}" class="brand">
      <div class="brand-mark">EV</div>
      <div>Empreende Vitória
        <small style="display:block;font-size:10px;font-weight:600;opacity:.5;text-transform:uppercase;letter-spacing:.08em;font-family:'Lato',sans-serif">Portal do Candidato</small>
      </div>
    </a>
  </div>
  <div class="topbar-right">
    <div class="user-chip">
      <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
      <span style="display:none;display:inline">{{ explode(' ', auth()->user()->name)[0] }}</span>
    </div>
  </div>
</nav>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="layout">

  {{-- Sidebar --}}
  <aside class="sidebar" id="sidebar">

    <div class="sidebar-user">
      <div class="sidebar-avatar"><i class="fas fa-user-tie"></i></div>
      <div class="sidebar-name">{{ auth()->user()->name }}</div>
      <div class="sidebar-role">Candidato</div>
    </div>

    <div class="sidebar-stats">
      <div class="stat-pill">
        <div class="stat-pill-num">{{ $meusEventos->count() }}</div>
        <div class="stat-pill-label">Eventos</div>
      </div>
      <div class="stat-pill">
        <div class="stat-pill-num">{{ $minhasCandidaturas->count() }}</div>
        <div class="stat-pill-label">Candidat.</div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-label">Menu</div>

      <button class="nav-item active" data-panel="perfil">
        <i class="fas fa-id-card"></i> Meu Perfil
      </button>

      <button class="nav-item" data-panel="candidaturas">
        <i class="fas fa-paper-plane"></i> Candidaturas
        @if($minhasCandidaturas->count())
          <span class="nav-badge">{{ $minhasCandidaturas->count() }}</span>
        @endif
      </button>

      <button class="nav-item" data-panel="vagas">
        <i class="fas fa-briefcase"></i> Vagas Disponíveis
      </button>

      <div class="nav-label" style="margin-top:8px">Eventos</div>

      <button class="nav-item" data-panel="meus-eventos">
        <i class="fas fa-ticket-alt"></i> Meus Eventos
        @if($meusEventos->count())
          <span class="nav-badge">{{ $meusEventos->count() }}</span>
        @endif
      </button>

      <button class="nav-item" data-panel="eventos-disponiveis">
        <i class="fas fa-calendar-check"></i> Eventos Abertos
      </button>
    </nav>

    <div class="sidebar-footer">
      <form action="{{ route('usuario.logout') }}" method="POST">
        @csrf
        <button type="submit" class="nav-item" style="color:#991b1b;width:100%">
          <i class="fas fa-sign-out-alt"></i> Sair
        </button>
      </form>
    </div>

  </aside>

  {{-- Main --}}
  <main class="main">

    {{-- Alertas globais --}}
    @if(session('success'))
      <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if(session('info'))
      <div class="alert alert-info"><i class="fas fa-info-circle"></i> {{ session('info') }}</div>
    @endif

    {{-- ── Painel: Perfil ── --}}
    <div class="panel active" id="panel-perfil">
      <div class="panel-header">
        <div class="panel-title">Meu Perfil de Candidato</div>
        <div class="panel-sub">Seu currículo visível para empresas parceiras.</div>
      </div>

      @if($perfil)
        <div class="profile-card">
          <div class="profile-avatar"><i class="fas fa-user-tie"></i></div>
          <div class="profile-info">
            <h3>{{ $perfil->name }}</h3>
            <p>{{ $perfil->job_function }} · {{ $perfil->city }}{{ $perfil->state ? ', '.$perfil->state : '' }}</p>
            <p style="margin-top:6px">
              <span class="card-badge {{ $perfil->status === 'active' ? 'badge-green' : 'badge-gray' }}">
                <i class="fas fa-circle" style="font-size:7px"></i> {{ $perfil->status_label }}
              </span>
            </p>
          </div>
          <div class="profile-actions">
            <a href="{{ route('portal.usuario.curriculo.edit') }}" class="btn-sm btn-brand">
              <i class="fas fa-pen"></i> Editar
            </a>
            <form method="POST" action="{{ route('portal.usuario.curriculo.destroy') }}"
              onsubmit="return confirm('Excluir seu currículo permanentemente?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn-sm" style="background:#fee2e2;color:#991b1b;border:none;cursor:pointer">
                <i class="fas fa-trash-alt"></i>
              </button>
            </form>
          </div>
        </div>
      @else
        <div class="profile-card" style="justify-content:center;flex-direction:column;text-align:center;gap:16px;padding:48px">
          <div style="font-size:52px;opacity:.25"><i class="fas fa-id-card"></i></div>
          <div>
            <h3 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:17px;font-weight:700;margin-bottom:6px">Crie seu perfil de candidato</h3>
            <p style="font-size:14px;color:var(--muted)">Apareça para empresas que buscam profissionais como você.</p>
          </div>
          <a href="{{ route('portal.usuario.curriculo.create') }}" class="btn-sm btn-brand" style="margin:0 auto">
            <i class="fas fa-plus"></i> Criar perfil agora
          </a>
        </div>
      @endif
    </div>

    {{-- ── Painel: Candidaturas ── --}}
    <div class="panel" id="panel-candidaturas">
      <div class="panel-header">
        <div class="panel-title">Minhas Candidaturas</div>
        <div class="panel-sub">Acompanhe o status de cada vaga que você se candidatou.</div>
      </div>

      @if($minhasCandidaturas->isEmpty())
        <div class="empty-state">
          <i class="fas fa-paper-plane"></i>
          <p>Você ainda não se candidatou a nenhuma vaga.</p>
        </div>
      @else
        <div class="cards-grid">
          @foreach($minhasCandidaturas as $candidatura)
            @if($candidatura->vacancy)
              @php $v = $candidatura->vacancy; @endphp
              <div class="card" style="cursor:pointer"
                onclick="abrirDetalheVaga(this)"
                data-id="{{ $v->id }}"
                data-position="{{ $v->position }}"
                data-company="{{ $v->company_name }}"
                data-cnpj="{{ $v->formatted_cnpj }}"
                data-area="{{ $v->interest_area }}"
                data-remuneration="{{ $v->remuneration && (float)$v->remuneration > 0 ? number_format((float)$v->remuneration,2,',','.') : '' }}"
                data-quantity="{{ $v->quantity }}"
                data-experience="{{ $v->min_experience }}"
                data-requirements="{{ e($v->requirements) }}"
                data-benefits="{{ e(implode(', ', $v->benefits ?? [])) }}"
                data-candidatou="1"
                data-apply-url="{{ route('job-vacancies.apply', $v) }}"
                data-empresa-telefone="{{ optional($v->user->empresa)->formatted_telefone ?? '' }}"
                data-empresa-cidade="{{ $v->user->empresa->cidade ?? '' }}"
                data-empresa-descricao="{{ e($v->user->empresa->descricao ?? '') }}"
                data-empresa-email="{{ $v->user->email ?? '' }}"
                data-unapply-url="{{ route('job-vacancies.unapply', $v) }}"
                data-status-label="{{ $candidatura->status_label }}"
                data-status="{{ $candidatura->status }}">
                <div class="card-badge
                  @if($candidatura->status === 'accepted') badge-green
                  @elseif($candidatura->status === 'rejected') badge-red
                  @else badge-yellow
                  @endif">
                  <i class="fas fa-circle" style="font-size:7px"></i>
                  {{ $candidatura->status_label }}
                </div>
                <h3>{{ $v->position }}</h3>
                <div class="card-meta">
                  <span><i class="fas fa-building"></i> {{ $v->company_name }}</span>
                  @if($v->interest_area)
                    <span><i class="fas fa-tag"></i> {{ $v->interest_area }}</span>
                  @endif
                  <span><i class="fas fa-clock"></i> {{ $candidatura->created_at->diffForHumans() }}</span>
                </div>
                <div class="card-actions" onclick="event.stopPropagation()">
                  <button type="button" class="btn-sm btn-outline btn-cancelar-candidatura"
                    data-action="{{ route('job-vacancies.unapply', $v) }}"
                    data-vaga="{{ $v->position }}">
                    <i class="fas fa-times"></i> Cancelar
                  </button>
                </div>
              </div>
            @endif
          @endforeach
        </div>
      @endif
    </div>

    {{-- ── Painel: Vagas ── --}}
    <div class="panel" id="panel-vagas">
      <div class="panel-header">
        <div class="panel-title">Vagas Disponíveis</div>
        <div class="panel-sub">Vagas ativas no momento. Candidate-se com um clique.</div>
      </div>

      @if($vagas->isEmpty())
        <div class="empty-state">
          <i class="fas fa-briefcase"></i>
          <p>Nenhuma vaga ativa no momento.</p>
        </div>
      @else
        <div class="cards-grid">
          @foreach($vagas as $vaga)
            @php $jaCandidatou = $vagasCandidadasIds->contains($vaga->id); @endphp
            <div class="card" style="cursor:pointer"
              onclick="abrirDetalheVaga(this)"
              data-id="{{ $vaga->id }}"
              data-position="{{ $vaga->position }}"
              data-company="{{ $vaga->company_name }}"
              data-cnpj="{{ $vaga->formatted_cnpj }}"
              data-area="{{ $vaga->interest_area }}"
              data-remuneration="{{ $vaga->remuneration && (float)$vaga->remuneration > 0 ? number_format((float)$vaga->remuneration,2,',','.') : '' }}"
              data-quantity="{{ $vaga->quantity }}"
              data-experience="{{ $vaga->min_experience }}"
              data-requirements="{{ e($vaga->requirements) }}"
              data-benefits="{{ e(implode(', ', $vaga->benefits ?? [])) }}"
              data-candidatou="{{ $jaCandidatou ? '1' : '0' }}"
              data-apply-url="{{ route('job-vacancies.apply', $vaga) }}"
              data-empresa-telefone="{{ optional($vaga->user->empresa)->formatted_telefone ?? '' }}"
              data-empresa-cidade="{{ $vaga->user->empresa->cidade ?? '' }}"
              data-empresa-descricao="{{ e($vaga->user->empresa->descricao ?? '') }}"
              data-empresa-email="{{ $vaga->user->email ?? '' }}">
              <div class="card-badge badge-blue"><i class="fas fa-building"></i> {{ $vaga->company_name }}</div>
              <h3>{{ $vaga->position }}</h3>
              <div class="card-meta">
                @if($vaga->remuneration && (float) $vaga->remuneration > 0)
                  <span><i class="fas fa-money-bill-wave"></i> R$ {{ number_format((float) $vaga->remuneration, 2, ',', '.') }}</span>
                @endif
                @if($vaga->interest_area)
                  <span><i class="fas fa-tag"></i> {{ $vaga->interest_area }}</span>
                @endif
                <span><i class="fas fa-users"></i> {{ $vaga->quantity }} vaga{{ $vaga->quantity > 1 ? 's' : '' }}</span>
              </div>
              <div class="card-actions" onclick="event.stopPropagation()">
                @if($jaCandidatou)
                  <span class="btn-sm" style="background:#dcfce7;color:#166534;cursor:default">
                    <i class="fas fa-check"></i> Candidatado
                  </span>
                @else
                  <form method="POST" action="{{ route('job-vacancies.apply', $vaga) }}">
                    @csrf
                    <button type="submit" class="btn-sm btn-brand">
                      <i class="fas fa-paper-plane"></i> Candidatar-se
                    </button>
                  </form>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>

    {{-- ── Painel: Meus Eventos ── --}}
    <div class="panel" id="panel-meus-eventos">
      <div class="panel-header">
        <div class="panel-title">Meus Eventos</div>
        <div class="panel-sub">Eventos nos quais você está inscrito.</div>
      </div>

      @if($meusEventos->isEmpty())
        <div class="empty-state">
          <i class="fas fa-calendar-times"></i>
          <p>Você ainda não está inscrito em nenhum evento.</p>
        </div>
      @else
        <div class="cards-grid">
          @foreach($meusEventos as $participante)
            @if($participante->event)
              @php
                $ev = $participante->event;
                $datasEv = collect($ev->allDates())->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m/Y'))->join(', ');
                $vagasEv = $ev->availableSpots();
              @endphp
              <div class="card" style="cursor:pointer"
                onclick="abrirDetalheEvento(this)"
                data-title="{{ $ev->title }}"
                data-datas="{{ $datasEv }}"
                data-start-time="{{ $ev->start_time ? \Carbon\Carbon::createFromTimeString($ev->start_time)->format('H:i') : '' }}"
                data-end-time="{{ $ev->start_time ? $ev->endTime() : '' }}"
                data-duration="{{ $ev->duration_minutes }}"
                data-vagas="{{ $vagasEv }}"
                data-capacity="{{ $ev->max_capacity }}"
                data-speaker="{{ $ev->speaker->name ?? '' }}"
                data-speaker-bio="{{ e($ev->speaker->bio ?? '') }}"
                data-image="{{ $ev->image_url ?? '' }}"
                data-inscrito="1"
                data-status="{{ $ev->status }}"
                data-cancelar-url="{{ route('portal.usuario.eventos.cancelar', $ev) }}"
                data-certificado-url="{{ route('portal.usuario.eventos.certificado', $ev) }}">
                <div class="card-badge badge-blue">
                  <i class="fas fa-calendar"></i> {{ $datasEv }}
                </div>
                <h3>{{ $ev->title }}</h3>
                <div class="card-meta">
                  <span><i class="fas fa-clock"></i> {{ \Carbon\Carbon::createFromTimeString($ev->start_time)->format('H:i') }} — {{ $ev->endTime() }}</span>
                  <span><i class="fas fa-user-check"></i> Inscrito como: {{ $participante->name }}</span>
                </div>
                <div class="card-actions" onclick="event.stopPropagation()">
                  @if($ev->status === 'completed')
                    <a href="{{ route('portal.usuario.eventos.certificado', $ev) }}" class="btn-sm btn-yellow" target="_blank">
                      <i class="fas fa-certificate"></i> Certificado
                    </a>
                  @else
                    <button type="button" class="btn-sm" style="background:#fee2e2;color:#991b1b;border:none;cursor:pointer"
                      onclick="abrirModalCancelarEvento('{{ route('portal.usuario.eventos.cancelar', $ev) }}', '{{ addslashes($ev->title) }}')">
                      <i class="fas fa-times"></i> Cancelar
                    </button>
                  @endif
                </div>
              </div>
            @endif
          @endforeach
        </div>
      @endif
    </div>

    {{-- ── Painel: Eventos Disponíveis ── --}}
    <div class="panel" id="panel-eventos-disponiveis">
      <div class="panel-header">
        <div class="panel-title">Eventos Disponíveis</div>
        <div class="panel-sub">Inscreva-se nos cursos e eventos abertos.</div>
      </div>

      @if($eventosDisponiveis->isEmpty())
        <div class="empty-state">
          <i class="fas fa-calendar"></i>
          <p>Nenhum evento disponível no momento. Volte em breve!</p>
        </div>
      @else
        <div class="cards-grid">
          @foreach($eventosDisponiveis as $evento)
            @php
              $datas = collect($evento->allDates())->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m/Y'))->join(', ');
              $vagas = $evento->availableSpots();
            @endphp
            <div class="card" style="cursor:pointer"
              onclick="abrirDetalheEvento(this)"
              data-evento-id="{{ $evento->id }}"
              data-title="{{ $evento->title }}"
              data-datas="{{ $datas }}"
              data-start-time="{{ $evento->start_time ? \Carbon\Carbon::createFromTimeString($evento->start_time)->format('H:i') : '' }}"
              data-end-time="{{ $evento->start_time ? $evento->endTime() : '' }}"
              data-duration="{{ $evento->duration_minutes }}"
              data-vagas="{{ $vagas }}"
              data-capacity="{{ $evento->max_capacity }}"
              data-speaker="{{ $evento->speaker->name ?? '' }}"
              data-speaker-bio="{{ e($evento->speaker->bio ?? '') }}"
              data-inscrever-url="{{ route('portal.usuario.eventos.inscrever', $evento) }}"
              data-image="{{ $evento->image_url ?? '' }}">
              <div class="card-badge badge-green"><i class="fas fa-circle" style="font-size:7px"></i> Inscrições abertas</div>
              <h3>{{ $evento->title }}</h3>
              <div class="card-meta">
                @if($evento->date)
                  <span><i class="fas fa-calendar"></i> {{ $datas }}</span>
                @endif
                <span><i class="fas fa-clock"></i> {{ \Carbon\Carbon::createFromTimeString($evento->start_time)->format('H:i') }} — {{ $evento->endTime() }}</span>
                <span><i class="fas fa-users"></i> {{ $vagas }} vaga{{ $vagas !== 1 ? 's' : '' }} disponível{{ $vagas !== 1 ? 'is' : '' }}</span>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>

  </main>
</div>

{{-- Modal: Detalhe da Vaga --}}
<div class="modal-backdrop" id="modal-detalhe-vaga">
  <div class="modal" role="dialog" aria-modal="true" style="max-width:620px">
    <div class="modal-header">
      <div>
        <div class="modal-title" id="mdv-title"></div>
        <div style="font-size:13px;color:var(--muted);margin-top:3px" id="mdv-company"></div>
      </div>
      <button class="modal-close" onclick="closeModal('modal-detalhe-vaga')" aria-label="Fechar">&times;</button>
    </div>

    <div style="padding:24px 32px;overflow-y:auto;max-height:calc(90vh - 140px)">

      {{-- Status da candidatura (quando aplicável) --}}
      <div id="mdv-status-wrap" style="display:none;margin-bottom:14px"></div>

      {{-- Badges de info --}}
      <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px" id="mdv-badges"></div>

      {{-- Requisitos --}}
      <div style="margin-bottom:18px">
        <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:8px">Requisitos</div>
        <div id="mdv-requirements" style="font-size:14px;color:var(--ink);line-height:1.7;white-space:pre-wrap;background:var(--cream);border-radius:10px;padding:14px 16px"></div>
      </div>

      {{-- Benefícios --}}
      <div id="mdv-benefits-wrap" style="margin-bottom:20px;display:none">
        <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:8px">Benefícios</div>
        <div id="mdv-benefits" style="font-size:14px;color:var(--ink)"></div>
      </div>

      {{-- Dados da empresa --}}
      <div id="mdv-empresa-wrap" style="margin-bottom:18px;display:none">
        <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:8px">Dados da Empresa</div>
        <div style="background:var(--cream);border-radius:10px;padding:14px 16px;display:flex;flex-direction:column;gap:6px;font-size:14px;color:var(--ink)" id="mdv-empresa-info"></div>
      </div>

      {{-- CNPJ --}}
      <div style="font-size:12px;color:var(--muted)" id="mdv-cnpj"></div>
    </div>

    <div style="padding:16px 32px;border-top:1px solid var(--line);display:flex;justify-content:flex-end;gap:10px">
      <button class="btn-cancel" onclick="closeModal('modal-detalhe-vaga')">Fechar</button>
      <div id="mdv-action"></div>
    </div>
  </div>
</div>

{{-- Modal: Confirmar Cancelamento de Candidatura --}}
<div class="modal-backdrop" id="modal-cancelar-candidatura">
  <div class="modal" role="dialog" aria-modal="true" style="max-width:420px">
    <div class="modal-header">
      <div class="modal-title"><i class="fas fa-exclamation-triangle" style="color:#dc2626;margin-right:8px"></i>Cancelar Candidatura</div>
      <button class="modal-close" onclick="closeModal('modal-cancelar-candidatura')" aria-label="Fechar">&times;</button>
    </div>
    <p style="font-size:14px;color:var(--muted);line-height:1.6">
      Tem certeza que deseja cancelar sua candidatura para <strong id="modal-cancelar-vaga" style="color:var(--ink)"></strong>?
      Esta ação não pode ser desfeita.
    </p>
    <div class="modal-footer">
      <button type="button" class="btn-cancel" onclick="closeModal('modal-cancelar-candidatura')">Voltar</button>
      <form id="form-cancelar-candidatura" method="POST">
        @csrf @method('DELETE')
        <button type="submit" class="btn-submit" style="background:#dc2626">
          <i class="fas fa-times"></i> Sim, cancelar
        </button>
      </form>
    </div>
  </div>
</div>

{{-- Modal: Confirmar Cancelamento de Inscrição em Evento --}}
<div class="modal-backdrop" id="modal-cancelar-evento">
  <div class="modal" role="dialog" aria-modal="true" style="max-width:420px">
    <div class="modal-header">
      <div class="modal-title"><i class="fas fa-exclamation-triangle" style="color:#dc2626;margin-right:8px"></i>Cancelar Inscrição</div>
      <button class="modal-close" onclick="closeModal('modal-cancelar-evento')" aria-label="Fechar">&times;</button>
    </div>
    <p style="font-size:14px;color:var(--muted);line-height:1.6">
      Tem certeza que deseja cancelar sua inscrição no evento <strong id="modal-cancelar-evento-nome" style="color:var(--ink)"></strong>?
      Esta ação não pode ser desfeita.
    </p>
    <div class="modal-footer">
      <button type="button" class="btn-cancel" onclick="closeModal('modal-cancelar-evento')">Voltar</button>
      <form id="form-cancelar-evento" method="POST">
        @csrf @method('DELETE')
        <button type="submit" class="btn-submit" style="background:#dc2626">
          <i class="fas fa-times"></i> Sim, cancelar
        </button>
      </form>
    </div>
  </div>
</div>

{{-- Modal: Detalhe do Evento --}}
<div class="modal-backdrop" id="modal-detalhe-evento">
  <div class="modal" role="dialog" aria-modal="true" style="max-width:580px;padding:0;overflow:hidden">

    {{-- Hero com imagem --}}
    <div id="mde-hero" style="position:relative;height:200px;background:linear-gradient(135deg,var(--brand),var(--brand-deep));background-size:cover;background-position:center;flex-shrink:0">
      <div style="position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,.15),rgba(0,0,0,.6));border-radius:0"></div>
      <button class="modal-close" onclick="closeModal('modal-detalhe-evento')" aria-label="Fechar"
        style="position:absolute;top:12px;right:14px;color:#fff;font-size:22px;opacity:.85;z-index:2">&times;</button>
      <div style="position:absolute;bottom:20px;left:24px;right:48px;z-index:2">
        <div id="mde-title" style="font-family:'Plus Jakarta Sans',sans-serif;font-size:20px;font-weight:800;color:#fff;line-height:1.3;margin-bottom:4px"></div>
        <div id="mde-datas" style="font-size:13px;color:rgba(255,255,255,.8)"></div>
      </div>
    </div>

    {{-- Corpo --}}
    <div style="padding:24px 28px 8px;overflow-y:auto;max-height:calc(90vh - 340px)">

      {{-- Badges info --}}
      <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px" id="mde-badges"></div>

      {{-- Palestrante --}}
      <div id="mde-speaker-wrap" style="margin-bottom:18px;display:none">
        <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:8px">Palestrante</div>
        <div style="background:var(--cream);border-radius:10px;padding:14px 16px">
          <div style="font-size:14px;font-weight:700;color:var(--ink);margin-bottom:4px" id="mde-speaker-name"></div>
          <div style="font-size:13px;color:var(--muted);line-height:1.6" id="mde-speaker-bio"></div>
        </div>
      </div>

    </div>

    <div style="padding:16px 28px;border-top:1px solid var(--line);display:flex;justify-content:flex-end;gap:10px">
      <div id="mde-action"></div>
    </div>
  </div>
</div>

{{-- Modal: Criar Perfil de Candidato --}}
<div class="modal-backdrop" id="modal-criar-perfil">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-criar-perfil-title">
    <div class="modal-header">
      <div class="modal-title" id="modal-criar-perfil-title"><i class="fas fa-id-card" style="color:var(--brand);margin-right:8px"></i>Criar Perfil de Candidato</div>
      <button class="modal-close" onclick="closeModal('modal-criar-perfil')" aria-label="Fechar">&times;</button>
    </div>

    <form method="POST" action="{{ route('portal.usuario.curriculo.store') }}">
      @csrf

      <div class="form-group">
        <label>Nome completo <span class="req">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required placeholder="Seu nome completo">
      </div>

      <div class="form-group">
        <label>Função / Cargo desejado <span class="req">*</span></label>
        <input type="text" name="job_function" class="form-control" required placeholder="Ex: Desenvolvedor Web, Auxiliar Administrativo...">
      </div>

      <div class="form-group">
        <label>Área de interesse <span class="req">*</span></label>
        <select name="interest_area" class="form-control" required>
          <option value="">Selecione uma área</option>
          @foreach(['Administração','Tecnologia da Informação','Saúde','Educação','Construção Civil','Comércio e Vendas','Indústria','Logística','Gastronomia','Serviços Gerais','Jurídico','Contabilidade / Finanças','Outros'] as $area)
            <option value="{{ $area }}">{{ $area }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Cidade</label>
          <input type="text" name="city" class="form-control" placeholder="Sua cidade">
        </div>
        <div class="form-group">
          <label>Estado (UF)</label>
          <input type="text" name="state" class="form-control" maxlength="2" placeholder="ES">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Telefone</label>
          <input type="text" name="phone" class="form-control" placeholder="(27) 99999-9999">
        </div>
        <div class="form-group">
          <label>E-mail de contato</label>
          <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" placeholder="seu@email.com">
        </div>
      </div>

      <div class="form-group">
        <label>Resumo profissional</label>
        <textarea name="summary" class="form-control" rows="3" placeholder="Descreva brevemente sua experiência e objetivos..."></textarea>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('modal-criar-perfil')">Cancelar</button>
        <button type="submit" class="btn-submit"><i class="fas fa-check"></i> Criar perfil</button>
      </div>
    </form>
  </div>
</div>

<script>
  const navItems  = document.querySelectorAll('.nav-item[data-panel]');
  const panels    = document.querySelectorAll('.panel');
  const sidebar   = document.getElementById('sidebar');
  const overlay   = document.getElementById('sidebarOverlay');
  const menuToggle = document.getElementById('menuToggle');

  function activate(panelId) {
    navItems.forEach(n => n.classList.toggle('active', n.dataset.panel === panelId));
    panels.forEach(p => p.classList.toggle('active', p.id === 'panel-' + panelId));
    history.replaceState(null, '', '#' + panelId);
  }

  navItems.forEach(item => {
    item.addEventListener('click', () => {
      activate(item.dataset.panel);
      if (window.innerWidth <= 900) {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
      }
    });
  });

  menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');
  });

  overlay.addEventListener('click', () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
  });

  function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
  }
  document.getElementById('modal-criar-perfil').addEventListener('click', function(e) {
    if (e.target === this) closeModal('modal-criar-perfil');
  });
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeModal('modal-criar-perfil');
      closeModal('modal-cancelar-candidatura');
      closeModal('modal-cancelar-evento');
      closeModal('modal-detalhe-vaga');
      closeModal('modal-detalhe-evento');
    }
  });

  function abrirDetalheVaga(card) {
    const d = card.dataset;

    document.getElementById('mdv-title').textContent = d.position;
    document.getElementById('mdv-company').innerHTML = '<i class="fas fa-building" style="color:var(--brand);margin-right:5px"></i>' + d.company;
    document.getElementById('mdv-requirements').textContent = d.requirements;
    document.getElementById('mdv-cnpj').innerHTML = d.cnpj ? '<i class="fas fa-id-card" style="margin-right:4px"></i> CNPJ: ' + d.cnpj : '';

    // badges
    const badges = document.getElementById('mdv-badges');
    badges.innerHTML = '';
    const addBadge = (icon, text, cls) => {
      if (!text) return;
      badges.innerHTML += `<span class="card-badge ${cls}" style="font-size:12px"><i class="${icon}"></i> ${text}</span>`;
    };
    addBadge('fas fa-money-bill-wave', d.remuneration ? 'R$ ' + d.remuneration : null, 'badge-green');
    addBadge('fas fa-tag', d.area, 'badge-blue');
    addBadge('fas fa-users', d.quantity + ' vaga' + (parseInt(d.quantity) > 1 ? 's' : ''), 'badge-gray');
    addBadge('fas fa-briefcase', d.experience || null, 'badge-gray');

    // dados da empresa
    const eWrap = document.getElementById('mdv-empresa-wrap');
    const eInfo = document.getElementById('mdv-empresa-info');
    const empresaLines = [];
    if (d.empresaTelefone) empresaLines.push(`<span><i class="fas fa-phone" style="width:16px;color:var(--brand)"></i> ${d.empresaTelefone}</span>`);
    if (d.empresaEmail)    empresaLines.push(`<span><i class="fas fa-envelope" style="width:16px;color:var(--brand)"></i> ${d.empresaEmail}</span>`);
    if (d.empresaCidade)   empresaLines.push(`<span><i class="fas fa-map-marker-alt" style="width:16px;color:var(--brand)"></i> ${d.empresaCidade}</span>`);
    if (d.empresaDescricao) empresaLines.push(`<span style="margin-top:4px;color:var(--muted)">${d.empresaDescricao}</span>`);
    if (empresaLines.length) {
      eWrap.style.display = 'block';
      eInfo.innerHTML = empresaLines.join('');
    } else {
      eWrap.style.display = 'none';
    }

    // benefícios
    const bWrap = document.getElementById('mdv-benefits-wrap');
    const bDiv  = document.getElementById('mdv-benefits');
    if (d.benefits) {
      bWrap.style.display = 'block';
      bDiv.innerHTML = d.benefits.split(', ').map(b =>
        `<span class="card-badge badge-blue" style="margin-right:4px;margin-bottom:4px">${b}</span>`
      ).join('');
    } else {
      bWrap.style.display = 'none';
    }

    // status badge (candidaturas)
    const statusWrap = document.getElementById('mdv-status-wrap');
    if (d.statusLabel) {
      const cls = d.status === 'accepted' ? 'badge-green' : d.status === 'rejected' ? 'badge-red' : 'badge-yellow';
      statusWrap.innerHTML = `<span class="card-badge ${cls}" style="margin-bottom:0"><i class="fas fa-circle" style="font-size:7px"></i> ${d.statusLabel}</span>`;
      statusWrap.style.display = 'flex';
    } else {
      statusWrap.innerHTML = '';
      statusWrap.style.display = 'none';
    }

    // botão de ação
    const action = document.getElementById('mdv-action');
    if (d.unapplyUrl) {
      action.innerHTML = `
        <button type="button" class="btn-sm btn-outline btn-cancelar-candidatura"
          style="border-color:#dc2626;color:#dc2626"
          data-action="${d.unapplyUrl}"
          data-vaga="${d.position}">
          <i class="fas fa-times"></i> Cancelar candidatura
        </button>`;
      // rebind after render
      action.querySelector('.btn-cancelar-candidatura').addEventListener('click', () => {
        closeModal('modal-detalhe-vaga');
        document.getElementById('modal-cancelar-vaga').textContent = d.position;
        document.getElementById('form-cancelar-candidatura').action = d.unapplyUrl;
        openModal('modal-cancelar-candidatura');
      });
    } else if (d.candidatou === '1') {
      action.innerHTML = '<span class="btn-sm" style="background:#dcfce7;color:#166534;cursor:default"><i class="fas fa-check"></i> Já candidatado</span>';
    } else {
      action.innerHTML = `
        <form method="POST" action="${d.applyUrl}">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Candidatar-se</button>
        </form>`;
    }

    openModal('modal-detalhe-vaga');
  }

  document.getElementById('modal-detalhe-vaga').addEventListener('click', function(e) {
    if (e.target === this) closeModal('modal-detalhe-vaga');
  });

  document.querySelectorAll('.btn-cancelar-candidatura').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('modal-cancelar-vaga').textContent = btn.dataset.vaga;
      document.getElementById('form-cancelar-candidatura').action = btn.dataset.action;
      openModal('modal-cancelar-candidatura');
    });
  });

  document.getElementById('modal-cancelar-candidatura').addEventListener('click', function(e) {
    if (e.target === this) closeModal('modal-cancelar-candidatura');
  });

  function abrirDetalheEvento(card) {
    const d = card.dataset;

    document.getElementById('mde-title').textContent = d.title;
    document.getElementById('mde-datas').innerHTML = '<i class="fas fa-calendar" style="margin-right:5px"></i>' + d.datas;

    // hero image
    const hero = document.getElementById('mde-hero');
    if (d.image) {
      hero.style.backgroundImage = `url('${d.image}')`;
    } else {
      hero.style.backgroundImage = 'none';
    }

    // badges
    const badges = document.getElementById('mde-badges');
    badges.innerHTML = '';
    const addBadge = (icon, text, cls) => {
      if (!text) return;
      badges.innerHTML += `<span class="card-badge ${cls}" style="font-size:12px"><i class="${icon}"></i> ${text}</span>`;
    };
    if (d.startTime) addBadge('fas fa-clock', d.startTime + (d.endTime ? ' — ' + d.endTime : ''), 'badge-blue');
    if (d.duration)  { var _dm = parseInt(d.duration); var _dh = Math.floor(_dm/60); var _dmin = _dm%60; var _dlabel = _dh > 0 ? _dh + 'h' + (_dmin > 0 ? ' ' + _dmin + 'min' : '') : _dmin + 'min'; addBadge('fas fa-hourglass-half', _dlabel, 'badge-gray'); }
    addBadge('fas fa-users', d.vagas + ' vaga' + (parseInt(d.vagas) !== 1 ? 's disponíveis' : ' disponível'), parseInt(d.vagas) > 0 ? 'badge-green' : 'badge-red');
    addBadge('fas fa-ticket-alt', 'Capacidade: ' + d.capacity, 'badge-gray');

    // palestrante
    const sWrap = document.getElementById('mde-speaker-wrap');
    if (d.speaker) {
      document.getElementById('mde-speaker-name').textContent = d.speaker;
      document.getElementById('mde-speaker-bio').textContent = d.speakerBio || '';
      sWrap.style.display = 'block';
    } else {
      sWrap.style.display = 'none';
    }

    // botão de ação
    const mdeAction = document.getElementById('mde-action');
    if (d.inscrito === '1') {
      const certBtn = d.status === 'completed'
        ? `<a href="${d.certificadoUrl}" class="btn-sm btn-yellow" target="_blank" style="text-decoration:none">
             <i class="fas fa-certificate"></i> Certificado
           </a>`
        : `<span class="btn-sm" style="background:var(--cream);color:var(--muted);cursor:default">
             <i class="fas fa-certificate"></i> Certificado disponível após conclusão
           </span>`;
      const cancelBtn = d.status !== 'completed'
        ? `<button type="button" class="btn-sm" style="background:#fee2e2;color:#991b1b;border:none;cursor:pointer"
             onclick="closeModal('modal-detalhe-evento');abrirModalCancelarEvento('${d.cancelarUrl}','${d.title.replace(/'/g,"\\'")}')" >
             <i class="fas fa-times"></i> Cancelar inscrição
           </button>`
        : '';
      mdeAction.innerHTML = certBtn + cancelBtn;
    } else {
      mdeAction.innerHTML = `
        <form method="POST" action="${d.inscreverUrl}">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <button type="submit" class="btn-submit"><i class="fas fa-check"></i> Inscrever-se</button>
        </form>`;
    }

    openModal('modal-detalhe-evento');
  }

  document.getElementById('modal-detalhe-evento').addEventListener('click', function(e) {
    if (e.target === this) closeModal('modal-detalhe-evento');
  });

  function abrirModalCancelarEvento(url, titulo) {
    document.getElementById('modal-cancelar-evento-nome').textContent = titulo;
    document.getElementById('form-cancelar-evento').action = url;
    openModal('modal-cancelar-evento');
  }

  document.getElementById('modal-cancelar-evento').addEventListener('click', function(e) {
    if (e.target === this) closeModal('modal-cancelar-evento');
  });

// Restaura painel pelo hash da URL
  const hash = location.hash.replace('#', '');
  if (hash && document.getElementById('panel-' + hash)) activate(hash);

  @if(session('success') || session('error') || session('info'))
    @if(session('success') && str_contains(session('success'), 'Perfil'))
      activate('perfil');
    @elseif(session('success') && str_contains(session('success'), 'Inscrição'))
      activate('meus-eventos');
    @else
      activate('vagas');
    @endif
  @endif

  // "Garantir minha vaga" (vindo de /cursos): abre a aba de eventos e o card
  // do evento indicado por ?evento=ID, pronto para inscrição.
  const _evParams = new URLSearchParams(location.search);
  const _eventoId = _evParams.get('evento');
  if (_eventoId) {
    activate('eventos-disponiveis');
    const _evCard = document.querySelector('.card[data-evento-id="' + _eventoId + '"]');
    if (_evCard) {
      _evCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
      abrirDetalheEvento(_evCard);
    }
  }
</script>

</body>
</html>
