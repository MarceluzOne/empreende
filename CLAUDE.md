# CLAUDE.md — Empreende Vitória

## Stack
- PHP 8.0, Laravel 9
- MySQL (host: 127.0.0.1)
- Deploy: FTP via Hostnet (sem SSH)
- Frontend: Blade + Bootstrap 5

## Comandos
- Migrations: acessar GET /setup-inicial (sem artisan no servidor)
- Build local: `php artisan serve`, `composer install`
- Cache: `php artisan config:clear`, `php artisan cache:clear`

---

## Convenções de Nomenclatura
- Classes, Controllers, Models: inglês, PascalCase → `BusinessOwner`, `ReportController`
- Variáveis e métodos: inglês, camelCase → `$totalAmount`, `getActiveUsers()`
- Colunas de banco: inglês, snake_case → `created_at`, `owner_name`
- Rotas: português (URLs amigáveis para o usuário) → `/empreendedores`, `/relatorios`
- Views Blade: inglês, kebab-case → `business-list.blade.php`
- Services: sufixo `Service` → `BusinessOwnerService`
- Repositories: sufixo `Repository` → `BusinessOwnerRepository`
- Form Requests: prefixo da ação → `StoreBusinessOwnerRequest`, `UpdateBusinessOwnerRequest`

---

## Boas Práticas — SOLID

- **S** — Single Responsibility: cada classe tem uma única responsabilidade. Lógica de negócio em Services, nunca em Controllers
- **O** — Open/Closed: usar interfaces e abstrações para extensão sem modificar código existente
- **L** — Liskov Substitution: subclasses devem poder substituir a classe pai sem quebrar o comportamento
- **I** — Interface Segregation: interfaces pequenas e específicas, nunca uma interface "faz tudo"
- **D** — Dependency Inversion: depender de abstrações (interfaces), não de implementações. Usar injeção de dependência do Laravel

---

## Estrutura de Camadas

```
Controller  → recebe request, delega para Service, retorna response
Service     → lógica de negócio (app/Services/)
Repository  → acesso a dados, queries (app/Repositories/)
Model       → apenas relacionamentos, casts e scopes
Request     → validação de entrada (app/Http/Requests/)
```

### Regras por camada
- Controller: sem lógica de negócio, máximo 5 linhas por método
- Service: sem acesso direto ao banco — usa Repository
- Repository: sem lógica de negócio — só queries
- Model: sem lógica de negócio — sem chamadas a Services
- Métodos com no máximo 20 linhas — extrair se necessário
- Validações sempre em Form Requests, nunca inline no Controller
- Nunca acessar `$_GET`, `$_POST` diretamente — usar `$request`
- Retornar exceções tipadas, nunca strings de erro soltas

---

## UX e Frontend (Blade)

### Confirmação de Ações Destrutivas
- Update e Delete NUNCA disparam direto no submit/click
- Sempre exibir modal de confirmação antes de executar
- Modal reutilizável via `data-action`, `data-method`, `data-message` no botão
- Botão de confirmação: `btn-warning` para update, `btn-danger` para delete

### Padrão do Modal de Confirmação
```html
<!-- Botão que abre o modal -->
<button type="button"
        class="btn btn-warning"
        data-bs-toggle="modal"
        data-bs-target="#confirmModal"
        data-action="{{ route('empreendedores.update', $owner->id) }}"
        data-method="PUT"
        data-message="Deseja atualizar os dados de {{ $owner->name }}?">
    Atualizar
</button>

<!-- Modal reutilizável (incluir no layout principal) -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar ação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmBtn">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Form oculto que executa a ação -->
<form id="confirmForm" method="POST" style="display:none">
    @csrf
    @method('PUT')
</form>
```

```javascript
// Inicializa modal de confirmação (incluir no app.js ou layout)
document.querySelectorAll('[data-bs-target="#confirmModal"]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('confirmMessage').textContent = btn.dataset.message;
        const form = document.getElementById('confirmForm');
        form.action = btn.dataset.action;
        form.querySelector('[name="_method"]').value = btn.dataset.method;
        const confirmBtn = document.getElementById('confirmBtn');
        confirmBtn.className = 'btn';
        confirmBtn.classList.add(btn.dataset.method === 'DELETE' ? 'btn-danger' : 'btn-warning');
    });
});

document.getElementById('confirmBtn').addEventListener('click', () => {
    document.getElementById('confirmForm').submit();
});
```

---

## Regras de Deploy
- Sem SSH — nunca sugerir comandos remotos
- .htaccess na raiz redireciona para /public
- Nunca commitar .env
- PHP compatível com 8.0 — evitar sintaxes de versões superiores
- Migrations via rota GET /setup-inicial (protegida por middleware ou removida após uso)
