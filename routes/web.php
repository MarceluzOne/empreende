<?php

/*
|--------------------------------------------------------------------------
| Rotas globais (sem site)
|--------------------------------------------------------------------------
|
| Plataforma multi-site: as rotas reais de cada site ficam em
| routes/sites/{slug}.php e são carregadas pelo App\Providers\SiteServiceProvider
| conforme o SITE_SLUG do request.
|
| Use este arquivo apenas para rotas verdadeiramente globais (ex.: healthcheck),
| que não pertencem a nenhum site específico.
|
*/

use Illuminate\Support\Facades\Route;

// Conveniência (sobretudo em dev/artisan serve): a raiz "/" redireciona para o
// site padrão. Em produção o Laravel só é acionado sob o slug (via proxy), então
// isto não interfere no domínio raiz da prefeitura nem nos sites legados.
Route::get('/', function () {
    return redirect('/' . env('DEFAULT_SITE_SLUG', 'empreendevitoria'));
});

