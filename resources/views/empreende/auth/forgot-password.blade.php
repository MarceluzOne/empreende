{{--
  Orientação, não formulário.

  A redefinição por e-mail nunca foi ligada, e uma tela que aceitava o endereço
  e respondia "enviamos o link" deixava a pessoa esperando algo que não vinha.
  Aqui ela é direcionada ao atendimento, onde um funcionário confere a
  identidade e gera o link (ver AssistedPasswordResetService).
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Recuperar Senha — Empreende Vitória</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  :root{
    --brand:#0763A0;--brand-deep:#044a7a;--brand-soft:#e8f1f9;
    --yellow:#ffc52d;--ink:#0c1822;--ink-soft:#38475a;--muted:#6c7a8a;
    --wa:#25d366;--wa-deep:#1ebe5d;
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
  .back-link{display:flex;align-items:center;gap:6px;font-size:14px;font-weight:700;color:var(--muted);padding:8px 14px;border-radius:999px;transition:background .15s,color .15s}
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

  .steps{list-style:none;margin:0 0 26px;padding:0;display:flex;flex-direction:column;gap:14px}
  .steps li{display:flex;gap:12px;align-items:flex-start;font-size:14.5px;color:var(--ink-soft);line-height:1.5}
  .steps .num{width:26px;height:26px;border-radius:50%;background:var(--brand-soft);color:var(--brand);font-weight:800;font-size:13px;display:grid;place-items:center;flex-shrink:0;margin-top:1px}

  .btn-wa{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:14px 24px;background:var(--wa);color:#fff;border:none;border-radius:999px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:15px;cursor:pointer;transition:background .2s,transform .15s;box-shadow:0 4px 14px rgba(37,211,102,.32)}
  .btn-wa:hover{background:var(--wa-deep);transform:translateY(-1px)}
  .btn-wa i{font-size:19px}

  .presencial{margin-top:22px;padding:16px 18px;background:var(--paper);border:1px solid var(--line);border-radius:var(--radius);font-size:14px;color:var(--ink-soft);line-height:1.6}
  .presencial strong{display:flex;align-items:center;gap:8px;font-family:'Plus Jakarta Sans',sans-serif;font-size:14px;color:var(--ink);margin-bottom:6px}
  .presencial strong i{color:var(--brand)}

  .note{margin-top:18px;font-size:13px;color:var(--muted);line-height:1.6;display:flex;gap:8px;align-items:flex-start}
  .note i{margin-top:2px;flex-shrink:0}

  .auth-footer{margin-top:26px;text-align:center;font-size:14px;color:var(--muted)}
  .auth-footer a{color:var(--brand);font-weight:700}
  .auth-footer a:hover{text-decoration:underline}

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
  <a href="{{ route('login') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Voltar ao login
  </a>
</nav>

<div class="auth-wrap">
  <div class="auth-side">
    <div class="auth-side-icon"><i class="fas fa-headset"></i></div>
    <h2>A equipe destrava <span>o seu acesso.</span></h2>
    <p>Por segurança, a senha do portal é redefinida com a ajuda de um atendente — que confirma quem você é antes de liberar o link.</p>
    <div class="auth-side-features">
      <div class="auth-side-feature"><i class="fab fa-whatsapp"></i> Pelo WhatsApp da Sala do Empreendedor</div>
      <div class="auth-side-feature"><i class="fas fa-building"></i> Ou presencialmente, no atendimento</div>
      <div class="auth-side-feature"><i class="fas fa-user-shield"></i> Ninguém da equipe vê a sua senha</div>
    </div>
  </div>

  <div class="auth-main">
    <div class="auth-card">
      <div class="auth-card-header">
        <h1>Esqueci minha senha</h1>
        <p>A redefinição é feita pelo atendimento do Empreende Vitória. Fale com a equipe e você recebe um link para criar uma senha nova.</p>
      </div>

      <ul class="steps">
        <li><span class="num">1</span><div>Chame a equipe pelo WhatsApp ou vá até o atendimento presencial.</div></li>
        <li><span class="num">2</span><div>Informe o seu CPF. O atendente confere se ele bate com o cadastro.</div></li>
        <li><span class="num">3</span><div>Você recebe um link, válido por 24 horas, e escolhe a senha nova.</div></li>
      </ul>

      @if($whatsapp)
        <a class="btn-wa" target="_blank" rel="noopener noreferrer"
           href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode('Olá! Perdi a senha da minha conta no portal Empreende Vitória e gostaria de redefinir.') }}">
          <i class="fab fa-whatsapp"></i> Falar com o atendimento
        </a>
      @endif

      <div class="presencial">
        <strong><i class="fas fa-map-marker-alt"></i> Atendimento presencial</strong>
        Rua Rui Barbosa, nº 26 — Centro Comercial, Vitória de Santo Antão<br>
        Segunda a sexta, das 8h às 14h.
      </div>

      <div class="note">
        <i class="fas fa-shield-halved"></i>
        <div>O link é enviado apenas para o telefone já cadastrado no seu nome. Se o seu número mudou, procure o atendimento presencial.</div>
      </div>

      <div class="auth-footer">
        Lembrou a senha? <a href="{{ route('login') }}">Entrar agora</a>
        <br><br>
        <a href="{{ route('usuario.login') }}" style="color:var(--muted)"><i class="fas fa-user"></i> Usuário</a>
        &nbsp;·&nbsp;
        <a href="{{ route('empresa.login') }}" style="color:var(--muted)"><i class="fas fa-building"></i> Empresa</a>
        &nbsp;·&nbsp;
        <a href="{{ route('login') }}" style="color:var(--muted)"><i class="fas fa-user-shield"></i> Funcionário</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>
