<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Meus Certificados — Empreende Vitória</title>
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
  .wrap{max-width:760px;margin:32px auto;padding:0 18px}
  .headline{font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:26px;margin-bottom:4px}
  .subline{color:var(--muted);font-size:14px;margin-bottom:22px}
  .card{background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow-md);overflow:hidden}
  .form{padding:22px 20px;display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap}
  .field{flex:1;min-width:220px}
  .field label{display:block;font-weight:700;font-size:13px;margin-bottom:6px;text-transform:uppercase;letter-spacing:.02em;color:var(--ink-soft)}
  .field input{width:100%;padding:11px 14px;border:1px solid var(--line);border-radius:10px;font-size:15px;font-family:inherit;outline:none;transition:border .15s,box-shadow .15s}
  .field input:focus{border-color:var(--brand);box-shadow:0 0 0 3px var(--brand-soft)}
  .field input.is-error{border-color:#dc2626}
  .btn{background:var(--brand);color:#fff;border:none;border-radius:10px;padding:12px 22px;font-size:15px;
    font-weight:800;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:8px}
  .btn:hover{background:var(--brand-deep)}
  .err{color:#dc2626;font-size:12px;margin-top:6px;display:block}
  .results{margin-top:22px;display:flex;flex-direction:column;gap:14px}
  .cert-item{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:var(--shadow-md);
    padding:18px 20px;display:flex;gap:16px;align-items:center;justify-content:space-between;flex-wrap:wrap}
  .cert-info h3{font-family:'Plus Jakarta Sans',sans-serif;font-size:16px;font-weight:800;margin-bottom:6px}
  .cert-metas{display:flex;flex-wrap:wrap;gap:4px 14px;font-size:13px;color:var(--ink-soft)}
  .cert-metas span{display:inline-flex;align-items:center;gap:6px}
  .cert-metas i{color:var(--brand);width:14px;text-align:center}
  .btn-dl{background:#16a34a;color:#fff;border-radius:10px;padding:10px 18px;font-size:14px;font-weight:800;
    font-family:'Plus Jakarta Sans',sans-serif;display:inline-flex;align-items:center;gap:8px;white-space:nowrap}
  .btn-dl:hover{background:#15803d}
  .status-tag{font-size:12.5px;font-weight:700;padding:8px 14px;border-radius:10px;white-space:nowrap}
  .status-tag--wait{background:#e0f2fe;color:#075985}
  .status-tag--miss{background:#fef3c7;color:#92400e}
  .empty{background:#fff;border:1px dashed var(--line);border-radius:14px;padding:26px 20px;text-align:center;color:var(--muted);font-size:14px;margin-top:22px}
  .empty i{font-size:26px;display:block;margin-bottom:8px}
  .foot-note{text-align:center;color:var(--muted);font-size:12px;margin-top:18px}
</style>
</head>
<body>
  <div class="topbar">
    <a href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> Início</a>
    <span style="font-weight:800;font-family:'Plus Jakarta Sans',sans-serif">Empreende Vitória</span>
  </div>

  <div class="wrap">
    <h1 class="headline">Meus certificados</h1>
    <p class="subline">Informe o CPF usado na inscrição para consultar e baixar seus certificados.</p>

    <div class="card">
      <form action="{{ route('public.certificates') }}" method="GET" class="form">
        <div class="field">
          <label for="cpf">CPF</label>
          <input type="text" id="cpf" name="cpf" value="{{ $cpf }}"
                 class="{{ $error ? 'is-error' : '' }}" placeholder="000.000.000-00" required>
          @if($error) <span class="err">{{ $error }}</span> @endif
        </div>
        <button type="submit" class="btn"><i class="fas fa-search"></i> Consultar</button>
      </form>
    </div>

    @if($results !== null)
      @if($results->isEmpty())
        <div class="empty">
          <i class="fas fa-folder-open"></i>
          Nenhuma participação encontrada para este CPF.
        </div>
      @else
        <div class="results">
          @foreach($results as $p)
            @php
              $event = $p->event;
              $dates = $event ? $event->allDates() : [];
              $periodo = count($dates) > 1
                  ? \Carbon\Carbon::parse($dates[0])->format('d/m/Y').' a '.\Carbon\Carbon::parse(end($dates))->format('d/m/Y')
                  : ($event ? $event->date->format('d/m/Y') : '—');
              $completed = $event && $event->status === 'completed';
              $fullAttendance = $completed && $p->hasFullAttendance();
            @endphp
            <div class="cert-item">
              <div class="cert-info">
                <h3>{{ $event->title ?? 'Evento removido' }}</h3>
                <div class="cert-metas">
                  <span><i class="fas fa-calendar"></i> {{ $periodo }}</span>
                  @if($event && $event->speaker)
                    <span><i class="fas fa-user"></i> {{ $event->speaker->name }}</span>
                  @endif
                  @if($event)
                    <span><i class="fas fa-clock"></i> {{ number_format($event->totalHours(), 0) }}h</span>
                  @endif
                </div>
              </div>
              @if($fullAttendance)
                <a href="{{ route('public.certificates.download', $p) }}" class="btn-dl">
                  <i class="fas fa-download"></i> Baixar certificado
                </a>
              @elseif(!$completed)
                <span class="status-tag status-tag--wait"><i class="fas fa-hourglass-half"></i> Aguardando conclusão do evento</span>
              @else
                <span class="status-tag status-tag--miss"><i class="fas fa-exclamation-circle"></i> Presença incompleta</span>
              @endif
            </div>
          @endforeach
        </div>
      @endif
    @endif

    <p class="foot-note">O certificado fica disponível após a conclusão do evento e a confirmação de presença em todos os dias.</p>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js"></script>
  <script>
    var cpf = document.getElementById('cpf');
    if (cpf) IMask(cpf, { mask: '000.000.000-00' });
  </script>
</body>
</html>
