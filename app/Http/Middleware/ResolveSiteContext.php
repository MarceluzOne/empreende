<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

/**
 * Resolve o "site atual" da plataforma multi-site no início do request e
 * aplica a configuração de runtime daquele site (banco, sessão, URL, logs).
 *
 * O registro de views/rotas de TODOS os sites é feito no boot pelo
 * SiteServiceProvider; aqui tratamos apenas do que é específico do request.
 */
class ResolveSiteContext
{
    public function handle(Request $request, Closure $next)
    {
        $slug = $this->detectSiteSlug($request);
        $config = config("sites.$slug");

        // Slug desconhecido (ex.: rota fora de qualquer site): segue sem
        // aplicar contexto, em vez de abortar — evita quebrar healthchecks/root.
        if (!is_array($config)) {
            return $next($request);
        }

        // Disponibiliza o site atual globalmente.
        app()->instance('current.site', $slug);
        app()->instance('current.site.config', $config);

        // Conexão de banco do site.
        if (!empty($config['db_connection'])) {
            config(['database.default' => $config['db_connection']]);
        }

        // Sessão isolada por site: o isolamento vem do NOME único do cookie
        // ({slug}_session). O path fica em '/' (não em '/{slug}') porque as rotas
        // de API ficam em /api/{slug} — fora de /{slug} — e um cookie com
        // Path=/{slug} não seria enviado para a API, quebrando sessão/CSRF (419).
        if (!empty($config['session_cookie'])) {
            config(['session.cookie' => $config['session_cookie']]);
        }
        config(['session.path' => '/']);

        // Views do site: prepend do diretório do site no finder, para que
        // chamadas sem namespace (view('portal.x'), @extends('layouts.app'))
        // resolvam automaticamente para resources/views/{namespace}. O
        // namespace explícito ({ns}::) continua disponível via SiteServiceProvider.
        $viewPath = resource_path('views/' . ($config['view_namespace'] ?? $slug));
        if (is_dir($viewPath)) {
            $finder = View::getFinder();
            if (!in_array($viewPath, $finder->getPaths(), true)) {
                $finder->prependLocation($viewPath);
                View::flushFinderCache();
            }
        }

        // URL pública do site.
        //  - app.url   = host base SEM slug  -> route() já injeta o slug pelo prefixo
        //  - asset.url = host base + slug    -> assets estáticos em /www/{slug}/assets
        // Em produção fixa o host canônico; em dev usa o host do request (localhost)
        // para que a navegação local funcione.
        if (app()->environment('production')) {
            // Produção: host canônico (sem slug) para route(); assets servidos
            // pelo Apache em /www/{slug}/assets => asset_url = host + slug.
            $base = rtrim((string) ($config['app_url'] ?? ''), '/');
            if ($base !== '') {
                config(['app.url' => $base]);
                config(['app.asset_url' => $base . '/' . $slug]);
                URL::forceRootUrl($base);
            }
        } else {
            // Dev (artisan serve): docroot é public/, assets em public/assets
            // servidos em /assets (sem slug). route() injeta o slug pelo prefixo.
            $base = $request->getSchemeAndHttpHost();
            config(['app.url' => $base]);
            config(['app.asset_url' => $base]);
        }

        // Canal de log dedicado ao site.
        config(["logging.channels.site" => [
            'driver' => 'single',
            'path'   => storage_path("logs/sites/$slug/laravel.log"),
            'level'  => env('LOG_LEVEL', 'debug'),
        ]]);

        return $next($request);
    }

    /**
     * Determina o slug do site:
     *  1. constante SITE_SLUG (definida pelo proxy index.php em produção);
     *  2. primeiro segmento da URL, se corresponder a um site configurado;
     *  3. site padrão configurado (único site dinâmico atual).
     */
    private function detectSiteSlug(Request $request): string
    {
        if (defined('SITE_SLUG')) {
            return SITE_SLUG;
        }

        $segment = $request->segment(1);
        if ($segment && is_array(config("sites.$segment"))) {
            return $segment;
        }

        return env('DEFAULT_SITE_SLUG', 'empreendevitoria');
    }
}
