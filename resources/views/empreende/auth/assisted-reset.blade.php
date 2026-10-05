{{--
  Tela aberta pelo link que o funcionário gerou no atendimento.

  Não exibe nome, e-mail nem CPF da conta: a URL carrega só o token, e é o CPF
  digitado aqui que identifica quem está redefinindo. Um link que vaze não diz
  a quem pertence.
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<meta name="referrer" content="no-referrer">
<title>Definir Senha — Empreende Vitória</title>
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
  .auth-card{width:100%;max-width:440px}
  .auth-card-header{margin-bottom:28px}
  .auth-card-header h1{font-family:'Plus Jakarta Sans',sans-serif;font-size:26px;font-weight:800;margin-bottom:6px}
  .auth-card-header p{font-size:15px;color:var(--muted)}

  .form-group{margin-bottom:20px}
  .form-group label{display:block;font-size:13px;font-weight:700;color:var(--ink-soft);margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
  .form-control{width:100%;padding:12px 16px;border:1.5px solid var(--line);border-radius:var(--radius);font-family:'Lato',sans-serif;font-size:15px;color:var(--ink);background:var(--paper);transition:border-color .15s,box-shadow .15s;outline:none}
  .form-control:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(7,99,160,.12)}
  .form-control.error{border-color:#e53e3e}
  .form-hint{font-size:12px;color:var(--muted);margin-top:6px}
  .pw-wrap{position:relative}
  .pw-toggle{position:absolute;right:14px;top:50%;transform:translateY(-50%);border:none;background:none;color:var(--muted);cursor:pointer;font-size:15px;padding:4px}

  .btn-primary{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:14px 24px;background:var(--brand);color:#fff;border:none;border-radius:999px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:15px;cursor:pointer;transition:background .2s,transform .15s;box-shadow:0 4px 14px rgba(7,99,160,.3)}
  .btn-primary:hover{background:var(--brand-deep);transform:translateY(-1px)}

  .alert-error{background:#fff5f5;border:1px solid #fed7d7;color:#c53030;padding:12px 16px;border-radius:var(--radius);margin-bottom:20px;font-size:14px;display:flex;align-items:flex-start;gap:8px}

  .auth-footer{margin-top:28px;text-align:center;font-size:14px;color:var(--muted)}
  .auth-footer a{color:var(--brand);font-weight:700}

  @media(max-width:820px){.auth-side{display:none}.auth-main{padding:32px 20px}}
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
</nav>

<div class="auth-wrap">
  <div class="auth-side">
    <div class="auth-side-icon"><i class="fas fa-key"></i></div>
    <h2>Escolha a sua <span>senha.</span></h2>
    <p>Confirme seu CPF para provarmos que este link chegou às mãos certas e defina a senha que você vai usar no portal.</p>
    <div class="auth-side-features">
      <div class="auth-side-feature"><i class="fas fa-id-card"></i> Confirmação pelo seu CPF</div>
      <div class="auth-side-feature"><i class="fas fa-clock"></i> Link válido por 24 horas</div>
      <div class="auth-side-feature"><i class="fas fa-rotate-left"></i> Depois de usado, o link expira</div>
    </div>
  </div>

  <div class="auth-main">
    <div class="auth-card">
      <div class="auth-card-header">
        <h1>Definir senha</h1>
        <p>Informe o CPF da sua conta e escolha a senha que vai usar para entrar.</p>
      </div>

      @if($errors->any())
        <div class="alert-error">
          <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0"></i>
          <div>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
        </div>
      @endif

      <form action="{{ route('password.assisted.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
          <label for="cpf">CPF</label>
          <input type="text" id="cpf" name="cpf" inputmode="numeric" maxlength="14" autocomplete="off"
            class="form-control {{ $errors->has('cpf') ? 'error' : '' }}"
            placeholder="000.000.000-00" required autofocus>
        </div>

        <div class="form-group">
          <label for="password">Sua senha</label>
          <div class="pw-wrap">
            <input type="password" id="password" name="password"
              class="form-control {{ $errors->has('password') ? 'error' : '' }}"
              placeholder="Mínimo de 8 caracteres" required>
            <button type="button" class="pw-toggle" data-target="password" aria-label="Mostrar senha">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          <div class="form-hint">Use pelo menos 8 caracteres.</div>
        </div>

        <div class="form-group">
          <label for="password_confirmation">Confirme a senha</label>
          <div class="pw-wrap">
            <input type="password" id="password_confirmation" name="password_confirmation"
              class="form-control" placeholder="Repita a senha" required>
            <button type="button" class="pw-toggle" data-target="password_confirmation" aria-label="Mostrar senha">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-primary">
          <i class="fas fa-check"></i> Salvar senha
        </button>
      </form>

      <div class="auth-footer">
        Link vencido ou com problema? <a href="{{ route('password.request') }}">Fale com o atendimento</a>
      </div>
    </div>
  </div>
</div>

<script>
  // Máscara de CPF: 000.000.000-00
  document.getElementById('cpf').addEventListener('input', function () {
    var d = this.value.replace(/\D/g, '').slice(0, 11);
    this.value = d
      .replace(/(\d{3})(\d)/, '$1.$2')
      .replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
      .replace(/(\d{3})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3-$4');
  });

  document.querySelectorAll('.pw-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.getElementById(btn.dataset.target);
      var hidden = input.type === 'password';
      input.type = hidden ? 'text' : 'password';
      btn.querySelector('i').className = hidden ? 'fas fa-eye-slash' : 'fas fa-eye';
    });
  });
</script>

</body>
</html>
