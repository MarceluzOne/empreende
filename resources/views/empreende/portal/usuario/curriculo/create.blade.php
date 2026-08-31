<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="{{ asset('assets/Marca-Empreende-Vitoria_negativada.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Criar Currículo — Empreende Vitória</title>
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
  .card-title{font-family:'Plus Jakarta Sans',sans-serif;font-size:15px;font-weight:700;color:var(--ink);margin-bottom:20px;display:flex;align-items:center;justify-content:space-between}
  .card-title-left{display:flex;align-items:center;gap:8px}
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
  .dynamic-block{border:1.5px solid var(--line);border-radius:12px;padding:18px;background:var(--cream);margin-bottom:12px}
  .btn-add-item{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:var(--brand);background:none;border:none;cursor:pointer;padding:0}
  .btn-add-item:hover{text-decoration:underline}
  .btn-remove-item{display:inline-flex;align-items:center;gap:4px;font-size:12px;color:#dc2626;background:none;border:none;cursor:pointer;margin-top:8px}
  .btn-remove-item:hover{text-decoration:underline}
  .empty-hint{font-size:13px;color:var(--muted);font-style:italic}
  .form-footer{display:flex;align-items:center;justify-content:space-between;padding-top:8px}
  .btn-submit{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:999px;font-size:14px;font-weight:700;background:var(--brand);color:#fff;border:none;cursor:pointer;box-shadow:0 2px 8px rgba(7,99,160,.2);transition:background .15s}
  .btn-submit:hover{background:var(--brand-deep)}
  @media(max-width:600px){.form-row,.form-row-3{grid-template-columns:1fr}.topbar{padding:0 16px}.container{padding:24px 16px}}
</style>
</head>
<body>

<nav class="topbar">
  <a href="{{ route('portal.usuario') }}" class="brand">
    <div class="brand-mark">EV</div>
    <div>Empreende Vitória
      <small style="display:block;font-size:11px;font-weight:600;opacity:.6;text-transform:uppercase;letter-spacing:.08em;font-family:'Lato',sans-serif">Portal do Usuário</small>
    </div>
  </a>
  <a href="{{ route('portal.usuario') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Voltar</a>
</nav>

<div class="container">

  <div class="page-header">
    <div class="page-title">Criar Currículo</div>
    <div class="page-sub">Monte seu perfil de candidato visível para empresas parceiras.</div>
  </div>

  @if($errors->any())
    <div class="alert-errors">
      <strong><i class="fas fa-exclamation-circle"></i> Corrija os erros abaixo:</strong>
      <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <form method="POST" action="{{ route('portal.usuario.curriculo.store') }}"
    x-data="{
      cpf: '{{ old('cpf') }}',
      maskCpf(v){
        v=v.replace(/\D/g,'').slice(0,11);
        if(v.length>9) v=v.slice(0,3)+'.'+v.slice(3,6)+'.'+v.slice(6,9)+'-'+v.slice(9);
        else if(v.length>6) v=v.slice(0,3)+'.'+v.slice(3,6)+'.'+v.slice(6);
        else if(v.length>3) v=v.slice(0,3)+'.'+v.slice(3);
        this.cpf=v;
      },
      phone: '{{ old('phone', auth()->user()->email ? '' : '') }}',
      maskPhone(v){
        v=v.replace(/\D/g,'').slice(0,11);
        v=v.replace(/(\d{2})(\d)/,'($1)$2');
        v=v.replace(/\((\d{2})\)(\d{5})(\d{4})$/,'($1)$2-$3');
        v=v.replace(/\((\d{2})\)(\d{4})(\d{4})$/,'($1)$2-$3');
        this.phone=v;
      },
      experiences: {{ Js::from(old('experiences', [])) }},
      addExp(){ this.experiences.push({company:'',role:'',start:'',end:'',current:false,activities:''}); },
      removeExp(i){ this.experiences.splice(i,1); },
      maskDate(v){ v=v.replace(/\D/g,'').slice(0,6); if(v.length>2) v=v.slice(0,2)+'/'+v.slice(2); return v; },
      education: {{ Js::from(old('education', [])) }},
      addEdu(){ this.education.push({course:'',institution:'',year:''}); },
      removeEdu(i){ this.education.splice(i,1); },
      languages: {{ Js::from(old('languages', [])) }},
      addLang(){ this.languages.push({language:'',level:''}); },
      removeLang(i){ this.languages.splice(i,1); },
      certifications: {{ Js::from(old('certifications', [])) }},
      addCert(){ this.certifications.push(''); },
      removeCert(i){ this.certifications.splice(i,1); },
      updateCert(i,v){ this.certifications[i]=v; }
    }">
    @csrf

    {{-- Dados Pessoais --}}
    <div class="card">
      <div class="card-title"><div class="card-title-left"><i class="fas fa-user"></i> Dados Pessoais</div></div>

      <div class="form-group">
        <label>Nome Completo <span class="req">*</span></label>
        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
          placeholder="Seu nome completo"
          class="form-control {{ $errors->has('name') ? 'error' : '' }}">
        @error('name')<div class="error-msg">{{ $message }}</div>@enderror
      </div>

      <div class="form-row-3">
        <div class="form-group">
          <label>Cargo / Objetivo <span class="req">*</span></label>
          <input type="text" name="job_function" value="{{ old('job_function') }}"
            placeholder="Ex: Dev Full Stack..."
            class="form-control {{ $errors->has('job_function') ? 'error' : '' }}">
          @error('job_function')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Cidade</label>
          <input type="text" name="city" value="{{ old('city') }}" placeholder="Sua cidade" class="form-control">
        </div>
        <div class="form-group">
          <label>Estado (UF)</label>
          <input type="text" name="state" value="{{ old('state') }}" placeholder="PE" maxlength="2" class="form-control" style="text-transform:uppercase">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>CPF</label>
          <input type="text" name="cpf" x-model="cpf" @input="maskCpf($event.target.value)"
            placeholder="000.000.000-00" maxlength="14" class="form-control {{ $errors->has('cpf') ? 'error' : '' }}">
          @error('cpf')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Telefone / WhatsApp</label>
          <input type="text" name="phone" x-model="phone" @input="maskPhone($event.target.value)"
            placeholder="(81) 99999-9999" maxlength="15" class="form-control">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>E-mail de contato</label>
          <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
            placeholder="email@exemplo.com"
            class="form-control {{ $errors->has('email') ? 'error' : '' }}">
          @error('email')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>LinkedIn</label>
          <input type="url" name="linkedin_url" value="{{ old('linkedin_url') }}"
            placeholder="https://linkedin.com/in/..." class="form-control">
        </div>
        <div class="form-group">
          <label>GitHub</label>
          <input type="url" name="github_url" value="{{ old('github_url') }}"
            placeholder="https://github.com/..." class="form-control">
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
          <label>Nível de Experiência</label>
          <select name="experience" class="form-control">
            <option value="">Não informado</option>
            @foreach($experienceLevels as $level)
              <option value="{{ $level }}" {{ old('experience') === $level ? 'selected' : '' }}>{{ $level }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    {{-- Resumo Profissional --}}
    <div class="card">
      <div class="card-title"><div class="card-title-left"><i class="fas fa-align-left"></i> Resumo Profissional</div></div>
      <textarea name="summary" rows="4" placeholder="Descreva brevemente sua trajetória, principais habilidades e objetivos..." class="form-control">{{ old('summary') }}</textarea>
    </div>

    {{-- Experiência Profissional --}}
    <div class="card">
      <div class="card-title">
        <div class="card-title-left"><i class="fas fa-briefcase"></i> Experiência Profissional</div>
        <button type="button" class="btn-add-item" @click="addExp()"><i class="fas fa-plus"></i> Adicionar</button>
      </div>
      <template x-for="(exp, i) in experiences" :key="i">
        <div class="dynamic-block">
          <div class="form-row" style="margin-bottom:10px">
            <input type="text" :name="`experiences[${i}][company]`" x-model="exp.company" placeholder="Empresa" class="form-control">
            <input type="text" :name="`experiences[${i}][role]`" x-model="exp.role" placeholder="Cargo" class="form-control">
          </div>
          <div class="form-row" style="margin-bottom:10px">
            <input type="text" :name="`experiences[${i}][start]`" x-model="exp.start"
              @input="exp.start=maskDate($event.target.value)" placeholder="Início (mm/aaaa)" maxlength="7" class="form-control">
            <div>
              <input type="text" :name="`experiences[${i}][end]`" x-model="exp.end"
                @input="exp.end=maskDate($event.target.value)" placeholder="Fim (mm/aaaa)" maxlength="7"
                :disabled="exp.current" :class="exp.current?'form-control':'form-control'" class="form-control">
              <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted);margin-top:6px;cursor:pointer">
                <input type="checkbox" :name="`experiences[${i}][current]`" x-model="exp.current" @change="if(exp.current)exp.end=''">
                Emprego atual
              </label>
            </div>
          </div>
          <textarea :name="`experiences[${i}][activities]`" x-model="exp.activities"
            placeholder="Atividades e conquistas..." rows="3" class="form-control"></textarea>
          <button type="button" class="btn-remove-item" @click="removeExp(i)"><i class="fas fa-trash-alt"></i> Remover</button>
        </div>
      </template>
      <p x-show="experiences.length===0" class="empty-hint">Nenhuma experiência adicionada. Clique em "Adicionar" para começar.</p>
    </div>

    {{-- Formação Acadêmica --}}
    <div class="card">
      <div class="card-title">
        <div class="card-title-left"><i class="fas fa-graduation-cap"></i> Formação Acadêmica</div>
        <button type="button" class="btn-add-item" @click="addEdu()"><i class="fas fa-plus"></i> Adicionar</button>
      </div>
      <template x-for="(edu, i) in education" :key="i">
        <div class="dynamic-block">
          <div class="form-row-3">
            <input type="text" :name="`education[${i}][course]`" x-model="edu.course" placeholder="Curso" class="form-control">
            <input type="text" :name="`education[${i}][institution]`" x-model="edu.institution" placeholder="Instituição" class="form-control">
            <input type="text" :name="`education[${i}][year]`" x-model="edu.year" placeholder="Ano / Status" class="form-control">
          </div>
          <button type="button" class="btn-remove-item" @click="removeEdu(i)"><i class="fas fa-trash-alt"></i> Remover</button>
        </div>
      </template>
      <p x-show="education.length===0" class="empty-hint">Nenhuma formação adicionada.</p>
    </div>

    {{-- Competências --}}
    <div class="card">
      <div class="card-title"><div class="card-title-left"><i class="fas fa-tools"></i> Competências / Habilidades</div></div>
      <textarea name="skills" rows="3" placeholder="Ex: PHP, Laravel, Excel, Comunicação, Liderança..." class="form-control">{{ old('skills') }}</textarea>
    </div>

    {{-- Idiomas e Certificações --}}
    <div class="card">
      <div class="card-title"><div class="card-title-left"><i class="fas fa-globe"></i> Idiomas e Certificações</div></div>

      <div style="margin-bottom:20px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
          <span style="font-size:13px;font-weight:700">Idiomas</span>
          <button type="button" class="btn-add-item" @click="addLang()"><i class="fas fa-plus"></i> Adicionar</button>
        </div>
        <template x-for="(lang, i) in languages" :key="i">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
            <input type="text" :name="`languages[${i}][language]`" x-model="lang.language" placeholder="Idioma (ex: Inglês)" class="form-control" style="flex:1">
            <select :name="`languages[${i}][level]`" x-model="lang.level" class="form-control" style="width:160px">
              <option value="">Nível...</option>
              <option value="Básico">Básico</option>
              <option value="Intermediário">Intermediário</option>
              <option value="Avançado">Avançado</option>
              <option value="Fluente">Fluente</option>
              <option value="Nativo">Nativo</option>
            </select>
            <button type="button" @click="removeLang(i)" style="color:#dc2626;background:none;border:none;cursor:pointer;font-size:16px"><i class="fas fa-times"></i></button>
          </div>
        </template>
        <p x-show="languages.length===0" class="empty-hint">Nenhum idioma adicionado.</p>
      </div>

      <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
          <span style="font-size:13px;font-weight:700">Certificações / Cursos</span>
          <button type="button" class="btn-add-item" @click="addCert()"><i class="fas fa-plus"></i> Adicionar</button>
        </div>
        <template x-for="(cert, i) in certifications" :key="i">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
            <input type="text" :name="`certifications[${i}]`" :value="cert" @input="updateCert(i,$event.target.value)"
              placeholder="Ex: AWS Cloud Practitioner..." class="form-control" style="flex:1">
            <button type="button" @click="removeCert(i)" style="color:#dc2626;background:none;border:none;cursor:pointer;font-size:16px"><i class="fas fa-times"></i></button>
          </div>
        </template>
        <p x-show="certifications.length===0" class="empty-hint">Nenhuma certificação adicionada.</p>
      </div>
    </div>

    <div class="form-footer">
      <a href="{{ route('portal.usuario') }}" style="font-size:14px;color:var(--muted)">Cancelar</a>
      <button type="submit" class="btn-submit"><i class="fas fa-check"></i> Criar Currículo</button>
    </div>

  </form>
</div>
</body>
</html>
