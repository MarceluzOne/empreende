<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/portal/funcionario/painel';

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

        $this->configureFormLimiters();
    }

    /**
     * Limites dos formulários públicos (login, cadastro, agendamento).
     *
     * O site fica atrás da Cloudflare, e o IP vem do X-Forwarded-For que ela
     * acrescenta (TrustProxies). Quem alcançar a origem sem passar por ela
     * pode forjar esse IP — por isso o login também conta POR E-MAIL, chave
     * que não depende do IP: uma conta não aguenta força bruta distribuída.
     */
    private function configureFormLimiters(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return [
                $this->formLimit(Limit::perMinute(5), 'email')->by('login:'.$email.'|'.$request->ip()),
                $this->formLimit(Limit::perMinute(10), 'email')->by('login-email:'.$email),
                $this->formLimit(Limit::perMinute(20), 'email')->by('login-ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('cadastro', fn (Request $request) => [
            $this->formLimit(Limit::perMinute(5), 'email')->by('cadastro:'.$request->ip()),
            $this->formLimit(Limit::perHour(20), 'email')->by('cadastro-hora:'.$request->ip()),
        ]);

        // Agendamento e inscrição em evento: contadores separados por rota.
        RateLimiter::for('formulario-publico', function (Request $request) {
            $key = optional($request->route())->getName().'|'.$request->ip();

            return [
                $this->formLimit(Limit::perMinute(3), 'throttle')->by('form:'.$key),
                $this->formLimit(Limit::perHour(10), 'throttle')->by('form-hora:'.$key),
            ];
        });
    }

    /**
     * Estourado o limite, volta para o formulário com o aviso na própria tela
     * (as telas são HTML standalone e mostram $errors), em vez da página 429.
     */
    private function formLimit(Limit $limit, string $field): Limit
    {
        return $limit->response(function (Request $request, array $headers) use ($field) {
            $minutes = max(1, (int) ceil(((int) ($headers['Retry-After'] ?? 60)) / 60));

            return back()
                ->withErrors([$field => "Muitas tentativas. Aguarde {$minutes} minuto(s) e tente novamente."])
                ->withInput($request->except('password', 'password_confirmation', 'current_password'));
        });
    }
}
