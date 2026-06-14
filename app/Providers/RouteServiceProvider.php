<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/painel';

    public function boot(): void
    {
        $this->configureRateLimiting();

        // As rotas de cada site são registradas pelo App\Providers\SiteServiceProvider
        // a partir de routes/sites/{slug}.php (com prefixo e middleware próprios).
        // routes/web.php fica como ponto de extensão para rotas globais (sem site).
        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
