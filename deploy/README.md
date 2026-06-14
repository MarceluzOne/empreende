# Deploy — Plataforma multi-site (Empreende Vitória)

Servidor: hospedagem compartilhada, **acesso só por FTP** (FileZilla). Sem SSH,
sem `artisan` remoto. Tudo é preparado localmente e enviado pronto.

## Layout do servidor (validado)

```
/home/prefeituradavito/            ← raiz do FTP
├── laravel/                       ← Laravel ÚNICO (fora do docroot) — SUBIR AQUI
│   └── public/index.php
└── www/                           ← docroot
    ├── empreende-teste/           ← pasta de TESTE (Fase 2)
    ├── empreendevitoria/          ← site REAL (trocar só no switchover, Fase 3)
    └── ... (sites legados, INTOCADOS)
```

Confirmado pelo `probe.php`: o PHP em `/www/<slug>/` lê `../../laravel/...`
(open_basedir libera toda a home; pastas irmãs de `www` ficam fora do docroot).

## Regra de ouro

O design atual suporta **um site dinâmico ativo por vez** (os nomes de rota não
são namespaced). Portanto a pasta `/laravel/config/sites/` deve conter:

- **durante o teste:** SOMENTE `empreende-teste.php`
- **em produção:**     SOMENTE `empreendevitoria.php`

Nunca os dois juntos.

---

## Build local (antes de subir)

Não precisa de `npm run build`: o layout usa CDN (Tailwind/Alpine/FontAwesome/
SweetAlert/imask). Só:

```
composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

> NÃO usar `config:cache`/`route:cache` no servidor: o `.env` e os caminhos
> mudam. Deixe sem cache (ou gere o cache só depois, com o `.env` de produção).

---

## FASE 2 — Teste em /www/empreende-teste/

1. **Subir o Laravel** para `/laravel/` (tudo do projeto), PULANDO:
   `.env*`, `node_modules/`, `.git/`, `tests/`, `deploy/`, `storage/logs/*`.

2. **Subir o `.env` de produção** como `/laravel/.env`. Conferir:
   - `APP_KEY=` preenchido
   - `APP_ENV=production`, `APP_DEBUG=false`
   - `APP_URL=https://prefeituradavitoria.pe.gov.br`  (sem `www.`, sem `/v2`)
   - `EMPREENDE_APP_URL=https://prefeituradavitoria.pe.gov.br`
   - DB de produção correto

3. **config/sites** no servidor: subir SOMENTE
   `deploy/empreende-teste/config-sites/empreende-teste.php`
   → `/laravel/config/sites/empreende-teste.php`
   e **remover** `/laravel/config/sites/empreendevitoria.php` (se subiu junto).

4. **routes/sites**: subir
   `deploy/empreende-teste/routes-sites/empreende-teste.php` e
   `empreende-teste.api.php` → `/laravel/routes/sites/`
   (os `empreendevitoria.php(.api.php)` precisam continuar lá — o teste faz
   `require` deles).

5. **Permissões de escrita** (chmod 775 ou 777 via FileZilla) em:
   `/laravel/storage/` (recursivo) e `/laravel/bootstrap/cache/`.
   Criar `/laravel/storage/logs/sites/empreende-teste/`.

6. **Pasta de teste** `/www/empreende-teste/`: subir
   - `deploy/empreende-teste/www/index.php`
   - `deploy/empreende-teste/www/.htaccess`
   - `assets/` ← copiar o conteúdo de `public/assets/` (as imagens) e
     `public/favicon.ico`.

7. **Migration nova** (`add_active_to_empresas_table`): rodar no phpMyAdmin
   do servidor (a coluna `active` na tabela `empresas`). Ver SQL no fim deste
   arquivo. Depois inserir a linha na tabela `migrations` para o Laravel não
   tentar rodar de novo.

8. **Testar** em `https://prefeituradavitoria.pe.gov.br/empreende-teste/`:
   landing, login (3 perfis), navegação, CRUDs, agendamento, PDF, e que os
   **sites legados continuam no ar**. Logs em
   `/laravel/storage/logs/sites/empreende-teste/laravel.log`.

---

## FASE 3 — Switchover para /www/empreendevitoria/

Só depois do teste 100%. Janela de baixo tráfego.

1. **Backup**: baixar `/www/empreendevitoria/` inteiro + dump do banco (phpMyAdmin).
2. Página de manutenção temporária em `/www/empreendevitoria/index.html`.
3. **Trocar a config ativa**: em `/laravel/config/sites/` remover
   `empreende-teste.php` e subir `empreendevitoria.php` (volta a ser o único).
4. **Esvaziar** `/www/empreendevitoria/` (o Laravel antigo) e subir:
   - `deploy/empreendevitoria/www/index.php`
   - `deploy/empreendevitoria/www/.htaccess`
   - `assets/` (mesmas imagens de `public/assets/` + favicon)
5. **Testar** na URL real `https://prefeituradavitoria.pe.gov.br/empreendevitoria/`.
6. Remover `/www/empreende-teste/` e os `empreende-teste.*` de `/laravel/routes/sites/`.

### Rollback (se algo crítico em ~15 min)
Restaurar `/www/empreendevitoria/` do backup do passo 1 e voltar a config antiga.

---

## SQL da migration nova (rodar no phpMyAdmin)

```sql
-- adiciona a coluna `active` (boolean, default 1) logo após `cidade`
ALTER TABLE empresas ADD COLUMN active TINYINT(1) NOT NULL DEFAULT 1 AFTER cidade;

-- registrar como aplicada para o Laravel não tentar rodar de novo
INSERT INTO migrations (migration, batch)
VALUES ('2026_06_14_120000_add_active_to_empresas_table',
        (SELECT COALESCE(MAX(batch),0)+1 FROM (SELECT batch FROM migrations) m));
```

> Corresponde a `up()` em
> `database/migrations/2026_06_14_120000_add_active_to_empresas_table.php`:
> `$table->boolean('active')->default(true)->after('cidade');`
