{{--
  Pedido de CPF para contas criadas antes de o cadastro exigi-lo.

  O middleware EnsureAccountHasCpf manda para cá qualquer tela do portal
  enquanto a conta estiver sem CPF. Sem ele, o atendimento não consegue gerar
  link de senha caso a pessoa a esqueça.
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Informe seu CPF — Empreende Vitória</title>
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
    --radius:14px;
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Lato',system-ui,sans-serif;color:var(--ink);background:var(--cream);min-height:100vh;display:flex;flex-direction:column;-webkit-font-smoothing:antialiased}
  a{color:inherit;text-decoration:none}

  .topbar{display:flex;align-items:center;justify-content:space-between;padding:16px 32px;background:var(--paper);box-shadow:var(--shadow-sm)}
  .brand{display:flex;align-items:center;gap:10px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:17px;color:var(--ink)}
  .brand-mark{width:34px;height:34px;border-radius:9px;background:var(--brand);color:#fff;display:grid;place-items:center;font-weight:900;font-size:13px}
  .back-link{display:flex;align-items:center;gap:6px;font-size:14px;font-weight:700;color:var(--muted);padding:8px 14px;border-radius:999px;background:none;border:none;cursor:pointer;font-family:inherit;transition:background .15s,color .15s}
  .back-link:hover{background:var(--brand-soft);color:var(--brand)}

  .auth-wrap{flex:1;display:flex;align-items:stretch;min-height:calc(100vh - 65px)}
  .auth-side{width:420px;min-width:380px;background:linear-gradient(145deg,var(--brand-deep) 0%,var(--brand) 100%);color:#fff;display:flex;flex-direction:column;justify-content:center;padding:60px 48px}
  .auth-side-icon{width:72px;height:72px;border-radius:20px;background:rgba(255,255,255,.15);display:grid;place-items:center;margin-bottom:28px;font-size:32px}
  .auth-side h2{font-family:'Plus Jakarta Sans',sans-serif;font-size:28px;font-weight:800;line-height:1.2;margin-bottom:12px}
  .auth-side h2 span{color:var(--yellow)}
  .auth-side p{font-size:15px;opacity:.85;line-height:1.6;margin-bottom:32px}
  .auth-side-features{display:flex;flex-direction:column;gap:14px}
  .auth-side-feature{display:flex;align-items:center;gap:12px;font-size:14px;font-weight:700}
  .auth-side-feature i{width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,.15);display:grid;place-items:center;font-size:13px;flex-shrink:0}

  .auth-main{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 32px}
  .auth-card{width:100%;max-width:460px}
  .auth-card-header{margin-bottom:26px}
  .auth-card-header h1{font-family:'Plus Jakarta Sans',sans-serif;font-size:26px;font-weight:800;margin-bottom:8px}
  .auth-card-header p{font-size:15px;color:var(--ink-soft);line-height:1.6}

  .form-group{margin-bottom:18px}
  .form-group label{display:block;font-size:13px;font-weight:700;color:var(--ink-soft);margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
  .form-control{width:100%;padding:12px 16px;border:1.5px solid var(--line);border-radius:var(--radius);font-family:'Lato',sans-serif;font-size:15px;color:var(--ink);background:var(--paper);transition:border-color .15s,box-shadow .15s;outline:none}
  .form-control:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(7,99,160,.12)}
  .form-control.error{border-color:#e53e3e}
  .form-hint{font-size:12px;color:var(--muted);margin-top:4px}
  .alert-error{background:#fff5f5;border:1px solid #fed7d7;color:#c53030;padding:12px 16px;border-radius:var(--radius);margin-bottom:20px;font-size:14px;display:flex;align-items:flex-start;gap:8px}

  .btn-primary{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:14px 24px;background:var(--brand);color:#fff;border:none;border-radius:999px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:15px;cursor:pointer;transition:background .2s,transform .15s;box-shadow:0 4px 14px rgba(7,99,160,.3)}
  .btn-primary:hover{background:var(--brand-deep);transform:translateY(-1px)}

  .note{margin-top:18px;font-size:13px;color:var(--muted);line-height:1.6;display:flex;gap:8px;align-items:flex-start}
  .note i{margin-top:2px;flex-shrink:0}

  @media(max-width:820px){.auth-side{display:none}.auth-main{padding:32px 20px}.topbar{padding:14px 16px}}
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
  <form method="POST" action="{{ route('usuario.logout') }}">
    @csrf
    <button type="submit" class="back-link"><i class="fas fa-arrow-right-from-bracket"></i> Sair</button>
  </form>
</nav>

<div class="auth-wrap">
  <div class="auth-side">
    <div class="auth-side-icon"><i class="fas fa-id-card"></i></div>
    <h2>Falta só <span>um dado.</span></h2>
    <p>Sua conta foi criada antes de pedirmos o CPF no cadastro. Com ele, a gente reconhece você em qualquer serviço do Empreende Vitória.</p>
    <div class="auth-side-features">
      <div class="auth-side-feature"><i class="fas fa-key"></i> Recuperar a senha pelo atendimento</div>
      <div class="auth-side-feature"><i class="fas fa-link"></i> Currículo, cursos e atendimentos juntos</div>
      <div class="auth-side-feature"><i class="fas fa-certificate"></i> Certificados no seu nome</div>
    </div>
  </div>

  <div class="auth-main">
    <div class="auth-card">
      <div class="auth-card-header">
        <h1>Informe seu CPF</h1>
        <p>Olá, {{ auth()->user()->name }}. Antes de continuar, precisamos do seu CPF. É só desta vez.</p>
      </div>

      @if($errors->any())
        <div class="alert-error">
          <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0"></i>
          <div>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
        </div>
      @endif

      <form action="{{ route('portal.usuario.cpf.store') }}" method="POST">
        @csrf
        <div class="form-group">
          <label for="cpf">CPF</label>
          <input type="text" id="cpf" name="cpf" class="form-control {{ $errors->has('cpf') ? 'error' : '' }}"
                 value="{{ old('cpf') }}" placeholder="000.000.000-00" inputmode="numeric" autocomplete="off" required autofocus>
          <div class="form-hint">Confira com atenção: depois de salvo, só o atendimento consegue corrigir.</div>
        </div>

        <button type="submit" class="btn-primary">
          Salvar e continuar <i class="fas fa-arrow-right"></i>
        </button>
      </form>

      <div class="note">
        <i class="fas fa-shield-halved"></i>
        <div>Se você esquecer a senha, é o CPF que o atendente confere antes de liberar o link para criar uma nova.</div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js"></script>
<script>
  var cpfInput = document.getElementById('cpf');
  if (cpfInput) IMask(cpfInput, { mask: '000.000.000-00' });
</script>

</body>
</html>
