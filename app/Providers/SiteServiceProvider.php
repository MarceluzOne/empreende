<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Registra, no boot da aplicação, as views e rotas de TODOS os sites
 * configurados em config/sites/*.php.
 *
 * O registro acontece uma única vez (não depende do site do request), o que
 * mantém o comportamento compatível com route:cache. A configuração específica
 * de cada request (banco, sessão, URL) é aplicada pelo middleware
 * App\Http\Middleware\ResolveSiteContext.
 */
class SiteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $sitesPath = config_path('sites');
        if (!is_dir($sitesPath)) {
            return;
        }

        foreach (glob($sitesPath . '/*.php') as $file) {
            $slug = basename($file, '.php');
            $config = config("sites.$slug");
            if (!is_array($config)) {
                continue;
            }

            $this->registerViews($slug, $config);
            $this->registerRoutes($slug);
        }
    }

    private function registerViews(string $slug, array $config): void
    {
        $namespace = $config['view_namespace'] ?? $slug;
        $path = resource_path("views/$namespace");

        if (is_dir($path)) {
            $this->loadViewsFrom($path, $namespace);
        }
    }

    private function registerRoutes(string $slug): void
    {
        $webRoutes = base_path("routes/sites/$slug.php");
        if (file_exists($webRoutes)) {
            Route::middleware('web')
                ->prefix($slug)
                ->group($webRoutes);
        }

        // API sob o MESMO prefixo do site ({slug}/api), não em /api/{slug}.
        // No deploy o Laravel roda atrás de um proxy na pasta /www/{slug}/, que
        // só atende caminhos sob /{slug}; um /api/{slug} cairia em /www/api/
        // (inexistente) => 404. Mantendo /{slug}/api a API passa pelo mesmo proxy.
        $apiRoutes = base_path("routes/sites/$slug.api.php");
        if (file_exists($apiRoutes)) {
            Route::middleware('api')
                ->prefix("$slug/api")
                ->group($apiRoutes);
        }
    }
}
