<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Inscrição — {{ $event->title }} — Empreende Vitória</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  :root{
    --brand:#0763A0; --brand-deep:#044a7a; --brand-soft:#e8f1f9;
    --yellow:#ffc52d; --ink:#0c1822; --ink-soft:#38475a; --muted:#6c7a8a;
    --line:#e4e9ef; --shadow-md:0 8px 30px rgba(12,24,34,.10);
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Lato',sans-serif;background:#f4f7fa;color:var(--ink);line-height:1.5;min-height:100vh}
  a{text-decoration:none;color:inherit}
  .topbar{background:var(--brand);color:#fff;padding:14px 20px;display:flex;align-items:center;justify-content:space-between}
  .topbar a{font-weight:700;font-size:14px;display:inline-flex;align-items:center;gap:8px}
  .wrap{max-width:720px;margin:32px auto;padding:0 18px}
  .headline{font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:26px;margin-bottom:4px}
  .subline{color:var(--muted);font-size:14px;margin-bottom:22px}
  .card{background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow-md);overflow:hidden}
  .event-head{display:flex;gap:16px;padding:20px;border-bottom:1px solid var(--line);align-items:center}
  .event-thumb{width:84px;height:84px;border-radius:12px;flex:none;background:linear-gradient(135deg,var(--brand) 0%,var(--brand-deep) 100%);
    display:flex;align-items:center;justify-content:center;color:#fff;font-size:26px;overflow:hidden}
  .event-thumb img{width:100%;height:100%;object-fit:cover}
  .event-head h2{font-family:'Plus Jakarta Sans',sans-serif;font-size:18px;font-weight:800;margin-bottom:6px}
  .event-metas{display:flex;flex-wrap:wrap;gap:6px 16px;font-size:13px;color:var(--ink-soft)}
  .event-metas span{display:inline-flex;align-items:center;gap:6px}
  .event-metas i{color:var(--brand);width:14px;text-align:center}
  .form{padding:22px 20px}
  .field{margin-bottom:16px}
  .field label{display:block;font-weight:700;font-size:13px;margin-bottom:6px;text-transform:uppercase;letter-spacing:.02em;color:var(--ink-soft)}
  .field input{width:100%;padding:11px 14px;border:1px solid var(--line);border-radius:10px;font-size:15px;font-family:inherit;outline:none;transition:border .15s,box-shadow .15s}
  .field input:focus{border-color:var(--brand);box-shadow:0 0 0 3px var(--brand-soft)}
  .field input.is-error{border-color:#dc2626}
  .err{color:#dc2626;font-size:12px;margin-top:4px;display:block}
  .row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
  @media(max-width:520px){.row{grid-template-columns:1fr}}
  .btn{width:100%;background:var(--brand);color:#fff;border:none;border-radius:10px;padding:13px;font-size:16px;
    font-weight:800;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;justify-content:center;gap:8px}
  .btn:hover{background:var(--brand-deep)}
  .alert{padding:13px 16px;border-radius:10px;font-size:14px;font-weight:600;margin:0 20px 18px;display:flex;align-items:center;gap:8px}
  .alert--error{background:#fee2e2;color:#991b1b}
  .notice{padding:26px 20px;text-align:center}
  .notice i{font-size:32px;color:var(--muted);margin-bottom:10px}
  .notice h3{font-family:'Plus Jakarta Sans',sans-serif;font-size:18px;margin-bottom:6px}
  .notice p{color:var(--muted);font-size:14px}
  .notice a{color:var(--brand);font-weight:700}
  .notice--ok .ok-check{width:64px;height:64px;border-radius:50%;background:#dcfce7;color:#16a34a;
    display:flex;align-items:center;justify-content:center;font-size:30px;margin:0 auto 12px}
  .notice--ok .ok-check i{font-size:30px;color:#16a34a;margin:0}
  .notice--ok h3{color:#15803d}
  .btn--wa{background:#25d366;display:flex;max-width:360px;margin:18px auto 0}
  .btn--wa:hover{background:#1eb85a}
  .link-muted{display:inline-block;margin-top:14px;color:var(--muted)!important;font-weight:600;font-size:13px}
  .foot-note{text-align:center;color:var(--muted);font-size:12px;margin-top:18px}
</style>
</head>
<body>
  @php
    $registered = session('registered');
    $closed = $event->registrationsClosed();
    $full   = $event->isFull();
    $allDates = $event->allDates();
  @endphp

  <div class="topbar">
    <a href="{{ route('cursos') }}"><i class="fas fa-arrow-left"></i> Voltar aos eventos</a>
    <span style="font-weight:800;font-family:'Plus Jakarta Sans',sans-serif">Empreende Vitória</span>
  </div>

  <div class="wrap">
    <h1 class="headline">Inscrição no evento</h1>
    <p class="subline">Preencha seus dados para garantir sua vaga. É gratuito.</p>

    <div class="card">
      {{-- Resumo do evento --}}
      <div class="event-head">
        <div class="event-thumb">
          @if($event->image_url)
            <img src="{{ $event->image_url }}" alt="{{ $event->title }}">
          @else
            <i class="fas fa-calendar-day"></i>
          @endif
        </div>
        <div>
          <h2>{{ $event->title }}</h2>
          <div class="event-metas">
            <span><i class="fas fa-calendar"></i>
              @if(count($allDates) > 1)
                {{ \Carbon\Carbon::parse($allDates[0])->format('d/m/Y') }} a {{ \Carbon\Carbon::parse(end($allDates))->format('d/m/Y') }} ({{ count($allDates) }} dias)
              @else
                {{ $event->date->format('d/m/Y') }}
              @endif
            </span>
            <span><i class="fas fa-clock"></i> {{ substr($event->start_time, 0, 5) }}h às {{ $event->endTime() }}h</span>
            @if($event->speaker)
              <span><i class="fas fa-user"></i> {{ $event->speaker->name }}</span>
            @endif
            <span><i class="fas fa-map-marker-alt"></i> Auditório — Empreende Vitória</span>
            <span><i class="fas fa-users"></i> {{ $event->availableSpots() }} de {{ $event->max_capacity }} vagas</span>
          </div>
        </div>
      </div>

      @if($registered)
        @php
          $waPhone = $registered['phone'] ?? '';
          if ($waPhone && strlen($waPhone) <= 11) { $waPhone = '55'.$waPhone; }
          $firstName = trim(strtok($registered['name'] ?? '', ' '));
          $periodo = count($allDates) > 1
              ? \Carbon\Carbon::parse($allDates[0])->format('d/m/Y').' a '.\Carbon\Carbon::parse(end($allDates))->format('d/m/Y')
              : $event->date->format('d/m/Y');
          $waMsg = "Olá, {$firstName}! ✅ Sua inscrição no evento *{$event->title}* está confirmada."
                 . "\n\n📅 {$periodo}"
                 . "\n🕐 ".substr($event->start_time, 0, 5)."h às ".$event->endTime()."h"
                 . "\n📍 Auditório — Empreende Vitória"
                 . "\n\nGuarde esta mensagem. Nos vemos lá!";
          $waUrl = $waPhone ? 'https://wa.me/'.$waPhone.'?text='.rawurlencode($waMsg) : null;
        @endphp
        <div class="notice notice--ok">
          <div class="ok-check"><i class="fas fa-check"></i></div>
          <h3>Inscrição confirmada!</h3>
          <p>{{ $firstName ? $firstName.', sua' : 'Sua' }} vaga está garantida. Anote os detalhes acima.</p>
          {{-- Com grupo cadastrado o convite ao grupo tem prioridade; sem ele,
               mantém a mensagem de confirmação no WhatsApp do inscrito. --}}
          @if($event->whatsapp_group_link)
            <a href="{{ $event->whatsapp_group_link }}" target="_blank" rel="noopener" class="btn btn--wa">
              <i class="fab fa-whatsapp"></i> Entrar no grupo do WhatsApp
            </a>
          @elseif($waUrl)
            <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="btn btn--wa">
              <i class="fab fa-whatsapp"></i> Receber confirmação no WhatsApp
            </a>
          @endif
          <a href="{{ route('cursos') }}" class="link-muted">Ver outros eventos</a>
          <p style="font-size:12px;color:var(--muted);margin-top:12px">
            Após a conclusão do evento, baixe seu certificado em
            <a href="{{ route('public.certificates') }}" style="color:var(--brand);font-weight:700">Meus Certificados</a>.
          </p>
        </div>
      @elseif($closed || $full)
        <div class="notice">
          <i class="fas fa-lock"></i>
          <h3>{{ $closed ? 'Inscrições encerradas' : 'Vagas esgotadas' }}</h3>
          <p>
            {{ $closed ? 'Este evento não está mais recebendo inscrições.' : 'Todas as vagas deste evento já foram preenchidas.' }}
            <br><a href="{{ route('cursos') }}">Ver outros eventos</a>
          </p>
        </div>
      @else
        @if($errors->has('capacity'))
          <div class="alert alert--error"><i class="fas fa-exclamation-circle"></i>{{ $errors->first('capacity') }}</div>
        @endif

        <form action="{{ route('public.events.register.store', $event->share_token) }}" method="POST" class="form">
          @csrf
          <div class="field">
            <label for="name">Nome completo *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                   class="{{ $errors->has('name') ? 'is-error' : '' }}" placeholder="Seu nome completo" required>
            @error('name') <span class="err">{{ $message }}</span> @enderror
          </div>

          <div class="row">
            <div class="field">
              <label for="cpf">CPF *</label>
              <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}"
                     class="{{ $errors->has('cpf') ? 'is-error' : '' }}" placeholder="000.000.000-00" required>
              @error('cpf') <span class="err">{{ $message }}</span> @enderror
            </div>
            <div class="field">
              <label for="whatsapp">WhatsApp *</label>
              <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}"
                     class="{{ $errors->has('whatsapp') ? 'is-error' : '' }}" placeholder="(XX) 9 0000-0000" required>
              @error('whatsapp') <span class="err">{{ $message }}</span> @enderror
            </div>
          </div>

          <div class="field">
            <label for="email">E-mail (opcional)</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="{{ $errors->has('email') ? 'is-error' : '' }}" placeholder="email@exemplo.com">
            @error('email') <span class="err">{{ $message }}</span> @enderror
          </div>

          <button type="submit" class="btn"><i class="fas fa-check-circle"></i> Confirmar inscrição</button>
        </form>
      @endif
    </div>

    <p class="foot-note">Seus dados serão usados apenas para a organização do evento.</p>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js"></script>
  <script>
    var cpf = document.getElementById('cpf');
    if (cpf) IMask(cpf, { mask: '000.000.000-00' });
    var wpp = document.getElementById('whatsapp');
    if (wpp) IMask(wpp, { mask: '(00) 0 0000-0000' });
  </script>
</body>
</html>
