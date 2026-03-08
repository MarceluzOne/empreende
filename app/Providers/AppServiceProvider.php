<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Super Admin Bypass (Opcional, mas recomendado para o seu e-mail)
        // Isso garante que você nunca fique trancado fora do sistema
        Gate::before(function ($user, $ability) {
            if ($user->email === 'arruda16.marcelo@gmail.com') {
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
    }
}