<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Candidatos — {{ $jobVacancy->position }}</title>
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
  .btn-back{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:999px;font-size:13px;font-weight:700;color:var(--brand);border:1.5px solid var(--brand);background:none;cursor:pointer;transition:background .15s}
  .btn-back:hover{background:var(--brand-soft)}

  .hero{background:linear-gradient(135deg,#0f2942 0%,var(--brand-deep) 60%,var(--brand) 100%);color:#fff;padding:40px;display:flex;align-items:center;justify-content:space-between;gap:24px}
  .hero h1{font-family:'Plus Jakarta Sans',sans-serif;font-size:24px;font-weight:800;margin-bottom:6px}
  .hero p{font-size:14px;opacity:.85}
  .hero-badge{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);color:#fff;padding:6px 14px;border-radius:999px;font-size:13px;font-weight:700;margin-bottom:12px}
  .stat-box{text-align:center;background:rgba(255,255,255,.12);border-radius:14px;padding:20px 32px}
  .stat-num{font-family:'Plus Jakarta Sans',sans-serif;font-size:32px;font-weight:800;color:var(--yellow)}
  .stat-label{font-size:12px;opacity:.8;margin-top:4px}

  .content{max-width:1100px;margin:0 auto;padding:40px 32px}

  .alert{padding:12px 18px;border-radius:10px;margin-bottom:20px;font-size:14px;font-weight:600;display:flex;align-items:center;gap:10px}
  .alert-success{background:#dcfce7;border:1px solid #86efac;color:#166534}
  .alert-error{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b}

  .table-wrap{background:var(--paper);border:1px solid var(--line);border-radius:18px;overflow:hidden}
  table{width:100%;border-collapse:collapse}
  th{background:var(--cream);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);padding:14px 20px;text-align:left;border-bottom:1px solid var(--line)}
  td{padding:16px 20px;font-size:14px;border-bottom:1px solid var(--line);vertical-align:middle}
  tr:last-child td{border-bottom:none}
  tr:hover td{background:var(--cream)}

  .badge{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;padding:4px 10px;border-radius:999px}
  .badge-pending{background:#fefce8;color:#854d0e}
  .badge-accepted{background:#dcfce7;color:#166534}
  .badge-rejected{background:#fee2e2;color:#991b1b}

  .btn-sm{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border-radius:999px;font-size:12px;font-weight:700;border:none;cursor:pointer;transition:background .15s}
  .btn-accept{background:#dcfce7;color:#166534}
  .btn-accept:hover{background:#bbf7d0}
  .btn-reject{background:#fee2e2;color:#991b1b}
  .btn-reject:hover{background:#fecaca}
  .btn-profile{background:var(--brand-soft);color:var(--brand)}
  .btn-profile:hover{background:#c7dff0}

  .empty-state{text-align:center;padding:60px 20px;color:var(--muted)}
  .empty-state i{font-size:48px;margin-bottom:16px;opacity:.3}
  .empty-state p{font-size:15px}

  .msg-preview{max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--muted);font-size:13px}

  @media(max-width:768px){
    .hero{flex-direction:column;padding:28px 20px}
    .content{padding:24px 16px}
    .table-wrap{overflow-x:auto}
    th,td{white-space:nowrap}
  }
</style>
</head>
<body>

<nav class="topbar">
  <a href="{{ route('portal.empresa') }}" class="brand">
    <div class="brand-mark">EV</div>
    <div>Empreende Vitória
      <small style="display:block;font-size:11px;font-weight:600;opacity:.6;text-transform:uppercase;letter-spacing:.08em;font-family:'Lato',sans-serif">Portal da Empresa</small>
    </div>
  </a>
  <a href="{{ route('portal.empresa') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Voltar ao portal</a>
</nav>

<div class="hero">
  <div>
    <div class="hero-badge"><i class="fas fa-briefcase"></i> {{ $jobVacancy->company_name }}</div>
    <h1>{{ $jobVacancy->position }}</h1>
    <p>
      @if($jobVacancy->interest_area) <i class="fas fa-tag"></i> {{ $jobVacancy->interest_area }} &nbsp;·&nbsp; @endif
      <i class="fas fa-users"></i> {{ $jobVacancy->quantity }} vaga{{ $jobVacancy->quantity > 1 ? 's' : '' }}
    </p>
  </div>
  <div class="stat-box">
    <div class="stat-num">{{ $applications->count() }}</div>
    <div class="stat-label">Candidatura{{ $applications->count() !== 1 ? 's' : '' }} recebida{{ $applications->count() !== 1 ? 's' : '' }}</div>
  </div>
</div>

<div class="content">

  @if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
  @endif

  @if($applications->isEmpty())
    <div class="empty-state">
      <i class="fas fa-inbox"></i>
      <p>Nenhuma candidatura recebida ainda para esta vaga.</p>
    </div>
  @else
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Candidato</th>
            <th>Área de interesse</th>
            <th>Experiência</th>
            <th>Mensagem</th>
            <th>Data</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          @foreach($applications as $app)
            <tr>
              <td>
                <div style="font-weight:700;color:var(--ink)">{{ $app->seeker?->name ?? $app->user?->name ?? '—' }}</div>
                <div style="font-size:12px;color:var(--muted)">{{ $app->seeker?->job_function }}</div>
              </td>
              <td>{{ $app->seeker?->interest_area ?? '—' }}</td>
              <td>{{ $app->seeker?->experience ?? '—' }}</td>
              <td>
                @if($app->message)
                  <span class="msg-preview" title="{{ $app->message }}">{{ $app->message }}</span>
                @else
                  <span style="color:var(--muted);font-size:13px">—</span>
                @endif
              </td>
              <td style="white-space:nowrap;color:var(--muted);font-size:13px">
                {{ $app->created_at->format('d/m/Y') }}
              </td>
              <td>
                <span class="badge badge-{{ $app->status }}">
                  <i class="fas fa-circle" style="font-size:7px"></i>
                  {{ $app->status_label }}
                </span>
              </td>
              <td>
                <div style="display:flex;gap:6px;flex-wrap:wrap">
                  @if($app->seeker)
                    <a href="{{ route('job-seekers.show', $app->seeker) }}" class="btn-sm btn-profile">
                      <i class="fas fa-user"></i> Perfil
                    </a>
                  @endif
                  @if($app->status !== 'accepted')
                    <form method="POST" action="{{ route('job-applications.status', $app) }}">
                      @csrf @method('PATCH')
                      <input type="hidden" name="status" value="accepted">
                      <button type="submit" class="btn-sm btn-accept"><i class="fas fa-check"></i> Aceitar</button>
                    </form>
                  @endif
                  @if($app->status !== 'rejected')
                    <form method="POST" action="{{ route('job-applications.status', $app) }}">
                      @csrf @method('PATCH')
                      <input type="hidden" name="status" value="rejected">
                      <button type="submit" class="btn-sm btn-reject"><i class="fas fa-times"></i> Recusar</button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

</div>
</body>
</html>
