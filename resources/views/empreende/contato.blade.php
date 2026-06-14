<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Contato — Empreende Vitória</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

  /* ============ PAGE HERO ============ */
  .page-hero{
    background:linear-gradient(135deg, var(--brand) 0%, var(--brand-deep) 100%);
    color:#fff;
    padding:120px 32px 56px;
    position:relative;
    overflow:hidden;
  }
  .page-hero::before{
    content:"";position:absolute;inset:0;
    background:
      radial-gradient(800px 300px at 90% 0%, rgba(255,197,45,.18), transparent 60%),
      radial-gradient(600px 240px at 10% 100%, rgba(255,255,255,.08), transparent 60%);
    pointer-events:none;
  }
  .page-hero__inner{
    max-width:1280px;margin:0 auto;position:relative;
  }
  .crumbs{
    display:flex;align-items:center;gap:10px;
    font-size:13px;font-weight:700;
    color:rgba(255,255,255,.7);
    margin-bottom:18px;
  }
  .crumbs a{color:rgba(255,255,255,.85)}
  .crumbs a:hover{color:#fff;text-decoration:underline}
  .crumbs i{font-size:10px;opacity:.6}
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
  .page-hero__title{
    font-family:'Plus Jakarta Sans', sans-serif;
    font-weight:800;
    font-size:clamp(34px, 4.4vw, 56px);
    line-height:1.05;
    letter-spacing:-0.025em;
    margin:14px 0 14px;
    max-width:20ch;
    text-wrap:balance;
  }
  .page-hero__title em{font-style:normal;color:var(--yellow)}
  .page-hero__sub{
    font-size:17px;
    color:rgba(255,255,255,.85);
    max-width:62ch;
    margin:0 0 28px;
    text-wrap:pretty;
  }

  /* ============ SECTION ============ */
  .section{
    max-width:1280px;
    margin:0 auto;
    padding:80px 32px;
  }

  /* ============ CONTACT GRID ============ */
  .contact-grid{
    display:grid;
    grid-template-columns:1.1fr 1fr;
    gap:36px;
    align-items:start;
    min-width:0;
  }
  .contact-grid > *{min-width:0}
  .contact-form{
    background:#fff;
    border:1px solid var(--line);
    border-radius:20px;
    padding:36px;
    min-width:0;
  }
  .contact-form h2{
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:24px;font-weight:800;margin:0 0 6px;letter-spacing:-0.02em;
  }
  .contact-form p.lede{
    margin:0 0 28px;color:var(--ink-soft);font-size:14.5px;
  }
  .field{
    display:flex;flex-direction:column;gap:8px;margin-bottom:18px;
  }
  .field label{
    font-size:13px;font-weight:800;color:var(--ink);letter-spacing:.01em;
    display:flex;align-items:center;gap:6px;
  }
  .field label .req{color:#a01f1f;font-weight:900}
  .field input,
  .field select,
  .field textarea{
    font-family:inherit;font-size:14.5px;
    padding:12px 14px;
    border:1.5px solid var(--line);
    border-radius:10px;
    background:#fff;color:var(--ink);
    outline:0;
    width:100%;
    transition:border-color .15s ease, box-shadow .15s ease;
  }
  .field input:focus,
  .field select:focus,
  .field textarea:focus{
    border-color:var(--brand);
    box-shadow:0 0 0 3px rgba(7,99,160,.12);
  }
  .field textarea{resize:vertical;min-height:120px;font-family:inherit}
  .field .hint{font-size:12.5px;color:var(--muted)}
  .form-actions{
    display:flex;gap:12px;align-items:center;margin-top:8px;flex-wrap:wrap;
  }
  .form-note{
    font-size:12.5px;color:var(--muted);max-width:36ch;
  }
  .form-note i{color:var(--brand);margin-right:4px}

  /* Buttons */
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
  .btn--dark{
    background:var(--brand);
    color:#fff;
  }
  .btn--dark:hover{background:var(--brand-deep)}

  /* Side: contact info */
  .contact-side{
    display:flex;flex-direction:column;gap:14px;
    min-width:0;
  }
  .contact-card{
    background:#fff;border:1px solid var(--line);
    border-radius:16px;padding:22px 24px;
    display:flex;gap:16px;align-items:flex-start;
    overflow:hidden;
    min-width:0;
  }
  .contact-card > div{
    min-width:0;
    overflow-wrap:break-word;
    word-break:break-word;
  }
  .contact-card .ico{
    width:46px;height:46px;border-radius:12px;
    background:var(--brand-soft);color:var(--brand);
    display:grid;place-items:center;flex:none;font-size:17px;
  }
  .contact-card h3{
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:15.5px;font-weight:800;margin:0 0 4px;letter-spacing:-0.01em;
  }
  .contact-card p,
  .contact-card a{
    margin:0;font-size:14px;color:var(--ink-soft);line-height:1.55;
  }
  .contact-card a:hover{color:var(--brand);text-decoration:underline}

  .contact-card--dark{
    background:linear-gradient(135deg, var(--brand) 0%, var(--brand-deep) 100%);
    border-color:transparent;color:#fff;
  }
  .contact-card--dark .ico{background:rgba(255,255,255,.16);color:#fff}
  .contact-card--dark h3{color:#fff}
  .contact-card--dark p,
  .contact-card--dark a{color:rgba(255,255,255,.88)}

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
    .contact-grid{grid-template-columns:1fr}
    .nav a.nav-login{display:none}
    .section{padding:64px 24px}
    .topbar{padding:12px 20px}
    .topbar.scrolled{padding:10px 20px}
    .page-hero{padding:100px 20px 40px}
  }
  @media (max-width: 560px){
    .contact-form{padding:24px}
    .footer-bar{background:var(--brand);color:rgba(255,255,255,.92);padding:22px 32px;display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:16px;font-size:13.5px}
    .footer-bar__copy{text-align:center;color:rgba(255,255,255,.85)}
  }
</style>
</head>
<body>

  <header class="topbar" id="topbar">
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
      <a href="{{ route('home') }}#servicos">Serviços</a>
      <a href="{{ route('home') }}#vagas">Vagas</a>
      <a href="{{ route('cursos') }}">Cursos</a>
      <a href="{{ route('contato') }}" class="active">Contato</a>
      <a href="{{ route('usuario.login') }}" class="nav-login"><i class="fas fa-user-lock"></i> Entrar</a>
    </nav>

    <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">
      <i class="fas fa-bars"></i>
    </button>
  </header>

  <div class="scrim" id="scrim"></div>
  <aside class="mobile-panel" id="mobilePanel">
    <button class="mobile-close" id="mobileClose" aria-label="Fechar menu"><i class="fas fa-times"></i></button>
    <a href="{{ route('home') }}">Home <i class="fas fa-chevron-right"></i></a>
    <a href="{{ route('home') }}#sobre">O Que é? <i class="fas fa-chevron-right"></i></a>
    <a href="{{ route('home') }}#servicos">Serviços <i class="fas fa-chevron-right"></i></a>
    <a href="{{ route('home') }}#vagas">Vagas <i class="fas fa-chevron-right"></i></a>
    <a href="{{ route('cursos') }}">Cursos <i class="fas fa-chevron-right"></i></a>
    <a href="{{ route('contato') }}" class="active">Contato <i class="fas fa-chevron-right"></i></a>
    <a href="{{ route('usuario.login') }}" style="margin-top:12px;background:var(--brand);color:#fff;justify-content:center">Entrar</a>
  </aside>

  <section class="page-hero">
    <div class="page-hero__inner">
      <nav class="crumbs" aria-label="breadcrumb">
        <a href="{{ route('home') }}"><i class="fas fa-home"></i> Início</a>
        <i class="fas fa-chevron-right"></i>
        <span>Contato</span>
      </nav>
      <span class="hero__eyebrow"><span class="dot"></span> Fale com a gente</span>
      <h1 class="page-hero__title">Como podemos <em>ajudar você</em>?</h1>
      <p class="page-hero__sub">Preencha o formulário abaixo e nossa equipe responde diretamente no seu e-mail. Também é possível ir até o Salão do Empreendedor ou ligar para a Secretaria de Desenvolvimento Econômico.</p>
    </div>
  </section>

  <section class="section" id="contato">
    <div class="contact-grid">
      <form class="contact-form" id="mailtoForm">
        <h2>Envie sua mensagem</h2>
        <p class="lede">Sua mensagem será enviada por e-mail para a Secretaria de Desenvolvimento Econômico.</p>

        <div class="field">
          <label for="nome">Nome completo <span class="req">*</span></label>
          <input id="nome" name="nome" type="text" placeholder="Seu nome" required autocomplete="name">
        </div>

        <div class="field">
          <label for="email">E-mail <span class="req">*</span></label>
          <input id="email" name="email" type="email" placeholder="voce@email.com" required autocomplete="email">
          <span class="hint">Usaremos este endereço para retornar a sua dúvida.</span>
        </div>

        <div class="field">
          <label for="motivo">Motivo do contato <span class="req">*</span></label>
          <select id="motivo" name="motivo" required>
            <option value="">Selecione um motivo</option>
            <option value="Dúvida sobre formalização MEI">Dúvida sobre formalização MEI</option>
            <option value="Agendamento de atendimento">Agendamento de atendimento</option>
            <option value="Reserva de sala de coworking">Reserva de sala de coworking</option>
            <option value="Cadastro de vaga (empresa)">Cadastro de vaga (empresa)</option>
            <option value="Cadastro como prestador de serviços">Cadastro como prestador de serviços</option>
            <option value="Cadastro da minha empresa">Cadastro da minha empresa</option>
            <option value="Inscrição em curso ou evento">Inscrição em curso ou evento</option>
            <option value="Microcrédito e linhas de financiamento">Microcrédito e linhas de financiamento</option>
            <option value="Imprensa e parcerias">Imprensa e parcerias</option>
            <option value="Outro assunto">Outro assunto</option>
          </select>
        </div>

        <div class="field">
          <label for="mensagem">Mensagem</label>
          <textarea id="mensagem" name="mensagem" placeholder="Descreva sua dúvida ou solicitação (opcional)"></textarea>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn--dark"><i class="fas fa-paper-plane"></i> Enviar mensagem</button>
          <span class="form-note"><i class="fas fa-info-circle"></i> O envio abrirá seu aplicativo de e-mail com a mensagem pronta.</span>
        </div>
      </form>

      <aside class="contact-side">
        <div class="contact-card contact-card--dark">
          <div class="ico"><i class="fas fa-envelope"></i></div>
          <div>
            <h3>E-mail</h3>
            <a href="mailto:sec.desindustrial@prefeituradavitoria.pe.gov.br">sec.desindustrial@prefeituradavitoria.pe.gov.br</a>
            <p style="margin-top:6px;font-size:13px">Secretaria de Desenvolvimento Econômico</p>
          </div>
        </div>

        <div class="contact-card">
          <div class="ico"><i class="fas fa-map-marker-alt"></i></div>
          <div>
            <h3>Endereço</h3>
            <p>Rua Rui Barbosa, nº 26<br>Centro Comercial · Vitória de Santo Antão</p>
          </div>
        </div>

        <div class="contact-card">
          <div class="ico"><i class="far fa-clock"></i></div>
          <div>
            <h3>Atendimento presencial</h3>
            <p>Segunda à Sexta · 8h às 14h<br>Atendimento por agendamento na dashboard.</p>
          </div>
        </div>

        <div class="contact-card">
          <div class="ico"><i class="fab fa-whatsapp"></i></div>
          <div>
            <h3>WhatsApp da Sala do Empreendedor</h3>
            <a href="#">(81) 9 0000-0000</a>
          </div>
        </div>
      </aside>
    </div>
  </section>

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

  <script>
    (function(){
      var form = document.getElementById('mailtoForm');
      if(!form) return;
      form.addEventListener('submit', function(ev){
        ev.preventDefault();
        var nome = document.getElementById('nome').value.trim();
        var email = document.getElementById('email').value.trim();
        var motivo = document.getElementById('motivo').value;
        var msg = document.getElementById('mensagem').value.trim();
        var subject = '[Empreende Vitória] ' + (motivo || 'Contato pelo site');
        var bodyLines = [
          'Nome: ' + nome,
          'E-mail: ' + email,
          'Motivo: ' + motivo,
          '',
          'Mensagem:',
          msg || '(sem mensagem)',
          '',
          '---',
          'Enviado pelo site Empreende Vitória'
        ];
        var body = bodyLines.join('\n');
        var to = 'sec.desindustrial@prefeituradavitoria.pe.gov.br';
        var href = 'mailto:' + to
                 + '?subject=' + encodeURIComponent(subject)
                 + '&body='    + encodeURIComponent(body);
        window.location.href = href;
      });
    })();

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
  </script>

</body>
</html>
