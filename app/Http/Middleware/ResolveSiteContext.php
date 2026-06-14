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

        // Sessão isolada por site (cookie e path próprios).
        if (!empty($config['session_cookie'])) {
            config(['session.cookie' => $config['session_cookie']]);
        }
        config(['session.path' => '/' . $slug]);

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
        if (!empty($config['app_url'])) {
            config(['app.url' => $config['app_url']]);
            URL::forceRootUrl($config['app_url']);
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
