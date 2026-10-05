<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
        URL::forceScheme('https');
    }
        // 1. Super Admin Bypass (Opcional, mas recomendado para o seu e-mail)
        // Isso garante que você nunca fique trancado fora do sistema
        Gate::before(function ($user, $ability) {
            if ($user->email === config('app.super_admin_email')) {
                return true;
            }
        });

        // 2. Registro Dinâmico de Permissões
        // Verificamos se a tabela existe para evitar erro ao rodar migrations do zero
        if (Schema::hasTable('permissions')) {
            try {
                foreach (Permission::all() as $permission) {
                    Gate::define($permission->name, function ($user) use ($permission) {
                        // Chama o método que vamos criar no Model User
                        return $user->hasPermission($permission->name);
                    });
                }
            } catch (\Exception $e) {
                // Silencia erros se o banco não estiver pronto
            }
        }

        $this->definePasswordLinkGate();
    }

    /**
     * Quem pode gerar link de senha e ativar conta de cidadão.
     *
     * Definido em CÓDIGO, de propósito, e não como permissão na tabela. Os
     * outros gates nascem de `permissions`, semeada por `GET /setup-inicial`;
     * um `can:` apontando para permissão nova responderia 403 para todos até
     * alguém acessar aquela URL em produção — um deploy que trancaria a equipe
     * fora da tela. Assim o gate vale no primeiro request depois do upload.
     *
     * Gerar o link é, na prática, poder entrar na conta de um cidadão. Antes
     * disso bastava ser `type = funcionario`; agora é revogável por papel.
     * 'senha-cidadao' entra no array do RoleController para poder ser atribuída
     * pela tela de Equipe quando a seed rodar.
     */
    private function definePasswordLinkGate(): void
    {
        Gate::define('gerar-link-senha', function ($user) {
            return $user->isFuncionario()
                && ($user->isAdmin() || $user->hasPermission('senha-cidadao'));
        });
    }
}