# CLAUDE.md — Empreende Vitória

## Stack
- PHP 8.0, Laravel 9
- MySQL (host: 127.0.0.1)
- Deploy: FTP via Hostnet (sem SSH, sem artisan no servidor)
- Frontend: Blade + **Tailwind via CDN** + Alpine.js + SweetAlert2 + IMask + FontAwesome 6
  (carregados em `resources/views/empreende/layouts/app.blade.php`). **Não há Bootstrap.**
- Páginas públicas e de autenticação (landing, login, portal do usuário) são HTML standalone
  com CSS próprio em `<style>`, sem o layout do backoffice.

## Organização multi-site
- Rotas por site: `routes/sites/{slug}.php` (web) e `routes/sites/{slug}.api.php` (API),
  carregadas pelo `SiteServiceProvider` com prefixo `/{slug}` (API em `/{slug}/api`) — hoje
  só `empreendevitoria`.
- Views do site ficam em `resources/views/empreende/`, mas são referenciadas sem o prefixo:
  `view('citizens.index')` → `resources/views/empreende/citizens/index.blade.php`.
- Configuração por site: `config/sites/{slug}.php`.

## Comandos
- Local: `php artisan serve`, `composer install`
- Cache: `php artisan config:clear`, `php artisan cache:clear`
- Schema: ver "Banco de dados" abaixo — **não existe migrate no servidor**.

---

## Convenções de Nomenclatura
- Classes, Controllers, Models: inglês, PascalCase → `BusinessOwner`, `ReportController`
- Variáveis e métodos: inglês, camelCase → `$totalAmount`, `getActiveUsers()`
- Colunas de banco: inglês, snake_case → `created_at`, `owner_name`
- Rotas: português (URLs amigáveis para o usuário) → `/empreendedores`, `/relatorios`
- Views Blade: inglês, kebab-case → `business-list.blade.php`
- Services: sufixo `Service` → `BusinessOwnerService`
- Form Requests: prefixo da ação → `StoreBusinessOwnerRequest`, `UpdateBusinessOwnerRequest`
- Exceções de negócio: sufixo `Exception`, com construtores estáticos que já trazem a
  mensagem → `AssistedResetException::noAccount()`

---

## Boas Práticas — SOLID

- **S** — Single Responsibility: cada classe tem uma única responsabilidade. Lógica de negócio em Services, nunca em Controllers
- **O** — Open/Closed: usar interfaces e abstrações para extensão sem modificar código existente
- **L** — Liskov Substitution: subclasses devem poder substituir a classe pai sem quebrar o comportamento
- **I** — Interface Segregation: interfaces pequenas e específicas, nunca uma interface "faz tudo"
- **D** — Dependency Inversion: receber dependências por injeção do Laravel (construtor ou parâmetro do método)

---

## Estrutura de Camadas

```
Controller  → recebe request, delega para Service, retorna response
Service     → lógica de negócio E acesso a dados via Eloquent (app/Services/)
Model       → relacionamentos, casts, scopes e regras derivadas do próprio registro
Request     → validação de entrada (app/Http/Requests/)
Rule        → validações reutilizáveis (app/Rules/) → `Cpf`, `CpfDisponivel`, `CpfOrCnpj`
Support     → helpers sem estado (app/Support/) → `Document` (CPF/CNPJ), `Phone`, `Email`
```

**Não existe camada Repository** — os Services consultam o Eloquent diretamente
(ex.: `AttendanceService`, `EventService`). Não criar Repositories sem alinhar antes.

### Regras por camada
- Controller: sem lógica de negócio, métodos curtos (alvo: até 5 linhas)
- Model: sem chamadas a Services. Regras que dependem só do registro podem morar nele
  (ex.: `Event::isCompleted()`, `Event::registrationsClosed()`)
- Métodos com no máximo 20 linhas — extrair se necessário
- Validações sempre em Form Requests, nunca inline no Controller
- Nunca acessar `$_GET`, `$_POST` diretamente — usar `$request`
- Recusas de negócio como exceções tipadas, capturadas no Controller e devolvidas como
  aviso na própria tela — nunca strings de erro soltas

### Armadilhas conhecidas do domínio
- **CPF é gravado de dois jeitos:** `job_seekers.cpf` com máscara; `users`, `event_participants`,
  `attendances` só dígitos. Toda consulta que cruza origens usa `Document::cpfVariants()`
  (e `cnpjVariants()` — `empresas.cnpj` também é mascarado). CPF incompleto não cabe nas
  variantes: comparar pelos dígitos.
- **Evento concluído:** usar `Event::isCompleted()` / `registrationsClosed()`, nunca
  `status === 'completed'` — o evento também se conclui pela data, sem o status mudar.
- **`user_id` de registro criado pela equipe é o do FUNCIONÁRIO** (currículo de balcão, vaga
  cadastrada no backoffice). Não usar esse dono para achar a pessoa/empresa: usar CPF/CNPJ.

---

## UX e Frontend (Blade)

### Confirmação de Ações Destrutivas
- Update e Delete NUNCA disparam direto no submit/click
- Sempre exibir modal de confirmação antes de executar
- Tom do modal: `amber` para update, `red` para delete

### Padrão do Modal de Confirmação
Partial reutilizável: `resources/views/empreende/partials/confirm-modal.blade.php`
(Tailwind puro + `window.ConfirmModal`, registrado uma única vez via `@once @push('scripts')`).

```blade
{{-- 1. Incluir o modal na página --}}
@include('partials.confirm-modal', [
    'id'      => 'confirm-cpf',
    'title'   => 'Alterar o CPF?',
    'message' => 'Texto fixo (opcional — pode vir do JS).',
    'icon'    => 'fa-id-card',
    'tone'    => 'amber',          // amber para update, red para exclusão
    'confirm' => 'Alterar e salvar',
])

{{-- 2. Segurar o submit do formulário até o Confirmar --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        ConfirmModal.guard(document.getElementById('meu-form'), 'confirm-cpf', function () {
            // devolver null libera o envio sem perguntar (ex.: o campo sensível não mudou)
            return 'Mensagem montada na hora, com os dados do formulário.';
        });
    });
</script>
@endpush
```

- `ConfirmModal.open(id, mensagem)` / `ConfirmModal.close(id)` para uso manual.
- Um `guard()` por modal: o botão Confirmar submete o formulário vinculado. Vários
  formulários na mesma página precisam de modais com `id` distintos.

### Modais de visualização
- Backoffice: Alpine.js (`x-data`, `x-show`, `@click.self` para fechar ao clicar fora).
- Modal com conteúdo longo: só o corpo rola — caixa em `flex flex-col overflow-hidden`,
  corpo com `flex:1; min-height:0; overflow-y:auto`, cabeçalho e rodapé fixos.

---

## Banco de dados
- Migrations em `database/migrations/` existem **só para versionamento e para o ambiente local**.
- Em produção o schema é alterado **à mão no phpMyAdmin**, com um script em
  `database/AAAA_MM_DD_descricao.sql` que diz o que conferir antes e depois de rodar.
- `GET /setup-inicial` **não roda migrate**: só semeia papéis e permissões.
- O SQL vai para o banco **antes** dos arquivos que dependem dele — código novo com coluna
  velha dá 500.

## Regras de Deploy
- Sem SSH — nunca sugerir comandos remotos
- .htaccess na raiz redireciona para /public
- Nunca commitar .env
- PHP compatível com 8.0 — evitar sintaxes de versões superiores (enums, `readonly`,
  `never`, first-class callable syntax)
- Lista de arquivos para subir: montar a partir da **última versão confirmada no servidor**,
  não do último merge — merges anteriores podem não ter subido.
- Depois de subir por FTP: apagar `storage/framework/views/*.php` (o FTP pode manter a data
  antiga e o Blade não recompila) e, se existirem, `bootstrap/cache/config.php` e
  `bootstrap/cache/routes-v7.php`.
- Erro 500 em produção: ler `storage/logs/laravel.log` no servidor. Nunca ligar `APP_DEBUG`.
