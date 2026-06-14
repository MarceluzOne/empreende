<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Acesso Funcionário — Empreende Vitória</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
  :root{
    --brand:#0763A0;--brand-deep:#044a7a;--brand-soft:#e8f1f9;
    --yellow:#ffc52d;--ink:#0c1822;--ink-soft:#38475a;--muted:#6c7a8a;
    --paper:#ffffff;--cream:#f3f6fa;--line:rgba(12,24,34,.08);
    --shadow-sm:0 1px 2px rgba(12,24,34,.06),0 2px 8px rgba(12,24,34,.04);
    --shadow-md:0 8px 28px rgba(12,24,34,.10);--radius:14px;
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Lato',system-ui,sans-serif;color:var(--ink);background:var(--cream);min-height:100vh;display:flex;flex-direction:column;-webkit-font-smoothing:antialiased}
  a{color:inherit;text-decoration:none}
  [x-cloak]{display:none!important}

  .topbar{display:flex;align-items:center;justify-content:space-between;padding:16px 32px;background:var(--paper);box-shadow:var(--shadow-sm)}
  .brand{display:flex;align-items:center;gap:10px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:17px;color:var(--ink)}
  .brand-mark{width:34px;height:34px;border-radius:9px;background:var(--brand);color:#fff;display:grid;place-items:center;font-weight:900;font-size:13px}
  .back-link{display:flex;align-items:center;gap:6px;font-size:14px;font-weight:700;color:var(--muted);padding:8px 14px;border-radius:999px;transition:background .15s,color .15s}
  .back-link:hover{background:var(--brand-soft);color:var(--brand)}

  .auth-wrap{flex:1;display:flex;align-items:stretch;min-height:calc(100vh - 65px)}
  .auth-side{width:420px;min-width:380px;background:linear-gradient(145deg,var(--brand-deep) 0%,var(--brand) 100%);color:#fff;display:flex;flex-direction:column;justify-content:center;padding:60px 48px;position:relative;overflow:hidden}
  .auth-side::before{content:"";position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")}
  .auth-side-icon{width:72px;height:72px;border-radius:20px;background:rgba(255,255,255,.15);backdrop-filter:blur(6px);display:grid;place-items:center;margin-bottom:28px;font-size:32px}
  .auth-side h2{font-family:'Plus Jakarta Sans',sans-serif;font-size:28px;font-weight:800;line-height:1.2;margin-bottom:12px}
  .auth-side h2 span{color:var(--yellow)}
  .auth-side p{font-size:15px;opacity:.8;line-height:1.6;margin-bottom:32px}
  .auth-side-features{display:flex;flex-direction:column;gap:14px}
  .auth-side-feature{display:flex;align-items:center;gap:12px;font-size:14px;font-weight:700}
  .auth-side-feature i{width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,.15);display:grid;place-items:center;font-size:13px;flex-shrink:0}

  .auth-main{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 32px}
  .auth-card{width:100%;max-width:440px}
  .auth-card-header{margin-bottom:32px}
  .auth-card-header h1{font-family:'Plus Jakarta Sans',sans-serif;font-size:26px;font-weight:800;color:var(--ink);margin-bottom:6px}
  .auth-card-header p{font-size:15px;color:var(--muted)}

  .form-group{margin-bottom:20px}
  .form-group label{display:block;font-size:13px;font-weight:700;color:var(--ink-soft);margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
  .form-control{width:100%;padding:12px 16px;border:1.5px solid var(--line);border-radius:var(--radius);font-family:'Lato',sans-serif;font-size:15px;color:var(--ink);background:var(--paper);transition:border-color .15s,box-shadow .15s;outline:none}
  .form-control:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(7,99,160,.12)}
  .form-control.error{border-color:#e53e3e}
  .input-wrap{position:relative}
  .input-wrap .form-control{padding-right:44px}
  .input-wrap button{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);padding:4px;transition:color .15s}
  .input-wrap button:hover{color:var(--brand)}

  .btn-primary{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:14px 24px;background:var(--brand);color:#fff;border:none;border-radius:999px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:15px;cursor:pointer;transition:background .2s,transform .15s,box-shadow .2s;box-shadow:0 4px 14px rgba(7,99,160,.3)}
  .btn-primary:hover{background:var(--brand-deep);transform:translateY(-1px);box-shadow:0 6px 20px rgba(7,99,160,.35)}
  .btn-primary:disabled{opacity:.6;cursor:not-allowed;transform:none}

  .divider{display:flex;align-items:center;gap:12px;margin:24px 0;color:var(--muted);font-size:13px}
  .divider::before,.divider::after{content:"";flex:1;height:1px;background:var(--line)}

  .alert-error{background:#fff5f5;border:1px solid #fed7d7;color:#c53030;padding:12px 16px;border-radius:var(--radius);margin-bottom:20px;font-size:14px;display:flex;align-items:flex-start;gap:8px}
  .alert-success{background:#f0fff4;border:1px solid #9ae6b4;color:#276749;padding:12px 16px;border-radius:var(--radius);margin-bottom:20px;font-size:14px;display:flex;align-items:center;gap:8px}

  .auth-footer{margin-top:24px;text-align:center;font-size:14px;color:var(--muted)}
  .auth-footer a{color:var(--brand);font-weight:700}
  .auth-footer a:hover{text-decoration:underline}

  @media(max-width:820px){
    .auth-side{display:none}
    .auth-main{padding:32px 20px}
  }
</style>
</head>
<body>

<nav class="topbar">
  <a href="{{ route('home') }}" class="brand">
    <div class="brand-mark">EV</div>
    <div>
      Empreende Vitória
      <small style="display:block;font-size:11px;font-weight:600;opacity:.6;text-transform:uppercase;letter-spacing:.08em;font-family:'Lato',sans-serif">Prefeitura de Vitória de Santo Antão</small>
    </div>
  </a>
  <a href="{{ route('home') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Voltar ao início
  </a>
</nav>

<div class="auth-wrap">
  <div class="auth-side">
    <div class="auth-side-icon"><i class="fas fa-user-shield"></i></div>
    <h2>Área restrita <span>de funcionários.</span></h2>
    <p>Acesse o painel administrativo do Empreende Vitória para gerenciar eventos, candidatos, empresas e muito mais.</p>
    <div class="auth-side-features">
      <div class="auth-side-feature"><i class="fas fa-calendar-alt"></i> Gestão de eventos e cursos</div>
      <div class="auth-side-feature"><i class="fas fa-users"></i> Gerenciamento de candidatos</div>
      <div class="auth-side-feature"><i class="fas fa-building"></i> Controle de empresas parceiras</div>
      <div class="auth-side-feature"><i class="fas fa-chart-bar"></i> Relatórios e auditoria</div>
    </div>
  </div>

  <div class="auth-main">
    <div class="auth-card">
      <div class="auth-card-header">
        <h1>Entrar como Funcionário</h1>
        <p>Acesso exclusivo para a equipe do Empreende Vitória.</p>
      </div>

      @if(session('status'))
        <div class="alert-success">
          <i class="fas fa-check-circle"></i>
          <span>{{ session('status') }}</span>
        </div>
      @endif

      @if($errors->any())
        <div class="alert-error">
          <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0"></i>
          <div>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
        </div>
      @endif

      <form action="{{ route('login.post') }}" method="POST" x-data="{loading:false}" @submit="loading=true">
        @csrf

        <div class="form-group">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}"
            class="form-control {{ $errors->has('email') ? 'error' : '' }}"
            placeholder="seu@email.com" required autofocus>
        </div>

        <div class="form-group" x-data="{show:false}">
          <label for="password">Senha</label>
          <div class="input-wrap">
            <input :type="show ? 'text' : 'password'" id="password" name="password"
              class="form-control" placeholder="••••••••" required>
            <button type="button" @click="show=!show" tabindex="-1">
              <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
            </button>
          </div>
        </div>

        <div style="text-align:right;margin-top:-8px;margin-bottom:20px">
          <a href="{{ route('password.request') }}" style="font-size:13px;color:var(--brand);font-weight:700">Esqueci minha senha</a>
        </div>

        <button type="submit" class="btn-primary" :disabled="loading">
          <span x-show="!loading">Entrar <i class="fas fa-arrow-right"></i></span>
          <span x-show="loading" x-cloak><i class="fas fa-spinner fa-spin"></i> Aguarde...</span>
        </button>
      </form>

      <div class="divider">ou</div>

      <div class="auth-footer">
        <a href="{{ route('usuario.login') }}" style="color:var(--muted)">
          <i class="fas fa-user"></i> Sou candidato
        </a>
        &nbsp;·&nbsp;
        <a href="{{ route('empresa.login') }}" style="color:var(--muted)">
          <i class="fas fa-building"></i> Sou uma empresa
        </a>
      </div>
    </div>
  </div>
</div>

</body>
</html>
