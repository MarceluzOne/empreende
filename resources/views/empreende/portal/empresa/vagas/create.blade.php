<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nova Vaga — Empreende Vitória</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
  :root{
    --brand:#0763A0;--brand-deep:#044a7a;--brand-soft:#e8f1f9;
    --yellow:#ffc52d;--ink:#0c1822;--muted:#6c7a8a;
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
  .btn-back{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:999px;font-size:13px;font-weight:700;color:var(--muted);border:1.5px solid var(--line);background:none;cursor:pointer;transition:background .15s}
  .btn-back:hover{background:var(--brand-soft);color:var(--brand);border-color:var(--brand)}
  .container{max-width:760px;margin:0 auto;padding:40px 24px}
  .page-header{margin-bottom:28px}
  .page-title{font-family:'Plus Jakarta Sans',sans-serif;font-size:22px;font-weight:800;color:var(--ink);margin-bottom:4px}
  .page-sub{font-size:14px;color:var(--muted)}
  .card{background:var(--paper);border:1px solid var(--line);border-radius:18px;padding:28px;margin-bottom:20px}
  .card-title{font-family:'Plus Jakarta Sans',sans-serif;font-size:15px;font-weight:700;color:var(--ink);margin-bottom:20px;display:flex;align-items:center;gap:8px}
  .card-title i{color:var(--brand)}
  .form-group{margin-bottom:18px}
  .form-group label{display:block;font-size:13px;font-weight:700;color:var(--ink);margin-bottom:6px}
  .form-group label .req{color:#dc2626}
  .form-control{width:100%;padding:10px 14px;border:1.5px solid var(--line);border-radius:10px;font-size:14px;font-family:'Lato',sans-serif;color:var(--ink);background:var(--paper);transition:border-color .15s,box-shadow .15s;outline:none}
  .form-control:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(7,99,160,.1)}
  .form-control.error{border-color:#dc2626}
  .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
  .form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}
  .error-msg{font-size:12px;color:#dc2626;margin-top:4px}
  .alert-errors{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:13px}
  .alert-errors ul{list-style:disc;padding-left:18px;margin-top:6px}
  .benefits-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
  .benefit-chip{display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:10px;border:1.5px solid var(--line);cursor:pointer;font-size:13px;font-weight:600;transition:border-color .15s,background .15s}
  .benefit-chip:has(input:checked){border-color:var(--brand);background:var(--brand-soft);color:var(--brand)}
  .benefit-chip input{accent-color:var(--brand)}
  .form-footer{display:flex;align-items:center;justify-content:space-between;padding-top:8px}
  .btn-submit{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:999px;font-size:14px;font-weight:700;background:var(--brand);color:#fff;border:none;cursor:pointer;box-shadow:0 2px 8px rgba(7,99,160,.2);transition:background .15s}
  .btn-submit:hover{background:var(--brand-deep)}
  @media(max-width:600px){.form-row,.form-row-3,.benefits-grid{grid-template-columns:1fr}.topbar{padding:0 16px}.container{padding:24px 16px}}
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
  <a href="{{ route('portal.empresa') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Voltar</a>
</nav>

<div class="container">

  <div class="page-header">
    <div class="page-title">Publicar Nova Vaga</div>
    <div class="page-sub">Preencha os dados da vaga que deseja divulgar.</div>
  </div>

  @if($errors->any())
    <div class="alert-errors">
      <strong><i class="fas fa-exclamation-circle"></i> Corrija os erros abaixo:</strong>
      <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <form method="POST" action="{{ route('portal.empresa.vagas.store') }}"
    x-data="{
      cnpj: '{{ old('cnpj', $empresa->cnpj ?? '') }}',
      remuneration: '{{ old('remuneration', '') }}',
      maskCnpj(v) {
        v = v.replace(/\D/g,'').slice(0,14);
        v = v.replace(/(\d{2})(\d)/,'$1.$2');
        v = v.replace(/(\d{2})\.(\d{3})(\d)/,'$1.$2.$3');
        v = v.replace(/(\d{3})\.(\d{3})(\d)/,'$1.$2/$3');
        v = v.replace(/(\d{4})(\d{1,2})$/,'$1-$2');
        this.cnpj = v;
      },
      maskMoney(v) {
        let n = v.replace(/\D/g,'');
        if(!n){this.remuneration='';return;}
        this.remuneration = (parseInt(n)/100).toLocaleString('pt-BR',{style:'currency',currency:'BRL'});
      }
    }">
    @csrf

    {{-- Dados da empresa --}}
    @if($empresa)
      <input type="hidden" name="cnpj" value="{{ $empresa->cnpj }}">
      <input type="hidden" name="company_name" value="{{ $empresa->razao_social }}">
    @else
      <div class="card">
        <div class="card-title"><i class="fas fa-building"></i> Dados da Empresa</div>
        <div class="form-row">
          <div class="form-group">
            <label>CNPJ <span class="req">*</span></label>
            <input type="text" name="cnpj" x-model="cnpj" @input="maskCnpj($event.target.value)"
              placeholder="00.000.000/0000-00" maxlength="18"
              class="form-control {{ $errors->has('cnpj') ? 'error' : '' }}">
            @error('cnpj')<div class="error-msg">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Nome da Empresa <span class="req">*</span></label>
            <input type="text" name="company_name" value="{{ old('company_name') }}"
              placeholder="Razão social ou nome fantasia"
              class="form-control {{ $errors->has('company_name') ? 'error' : '' }}">
            @error('company_name')<div class="error-msg">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    @endif

    {{-- Detalhes da vaga --}}
    <div class="card">
      <div class="card-title"><i class="fas fa-briefcase"></i> Detalhes da Vaga</div>

      <div class="form-group">
        <label>Título da Vaga <span class="req">*</span></label>
        <input type="text" name="position" value="{{ old('position') }}"
          placeholder="Ex: Desenvolvedor Web, Auxiliar Administrativo..."
          class="form-control {{ $errors->has('position') ? 'error' : '' }}">
        @error('position')<div class="error-msg">{{ $message }}</div>@enderror
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Quantidade de Vagas <span class="req">*</span></label>
          <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1"
            class="form-control {{ $errors->has('quantity') ? 'error' : '' }}">
          @error('quantity')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Remuneração</label>
          <input type="text" name="remuneration" x-model="remuneration"
            @input="maskMoney($event.target.value)" placeholder="R$ 0,00"
            class="form-control">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Área de Interesse <span class="req">*</span></label>
          <select name="interest_area" class="form-control {{ $errors->has('interest_area') ? 'error' : '' }}">
            <option value="">Selecione...</option>
            @foreach($interestAreas as $area)
              <option value="{{ $area }}" {{ old('interest_area') === $area ? 'selected' : '' }}>{{ $area }}</option>
            @endforeach
          </select>
          @error('interest_area')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Experiência Mínima</label>
          <select name="min_experience" class="form-control">
            <option value="">Sem requisito</option>
            @foreach($experiences as $exp)
              <option value="{{ $exp }}" {{ old('min_experience') === $exp ? 'selected' : '' }}>{{ $exp }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Requisitos da Vaga <span class="req">*</span></label>
        <textarea name="requirements" rows="5" placeholder="Descreva os requisitos, conhecimentos e habilidades necessários..."
          class="form-control {{ $errors->has('requirements') ? 'error' : '' }}">{{ old('requirements') }}</textarea>
        @error('requirements')<div class="error-msg">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- Benefícios --}}
    <div class="card">
      <div class="card-title"><i class="fas fa-gift"></i> Benefícios</div>
      <div class="benefits-grid">
        @foreach($benefits as $benefit)
          <label class="benefit-chip">
            <input type="checkbox" name="benefits[]" value="{{ $benefit }}"
              {{ in_array($benefit, old('benefits', [])) ? 'checked' : '' }}>
            {{ $benefit }}
          </label>
        @endforeach
      </div>
    </div>

    <div class="form-footer">
      <a href="{{ route('portal.empresa') }}" style="font-size:14px;color:var(--muted)">Cancelar</a>
      <button type="submit" class="btn-submit"><i class="fas fa-check"></i> Publicar Vaga</button>
    </div>

  </form>
</div>
</body>
</html>
