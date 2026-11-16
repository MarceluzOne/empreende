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
     * outros gates nascem de `permissions`, que em produção só muda por SQL à
     * mão; um `can:` apontando para permissão nova responderia 403 para todos
     * até alguém rodar esse SQL — um deploy que trancaria a equipe fora da
     * tela. Assim o gate vale no primeiro request depois do upload.
     *
     * Gerar o link é, na prática, poder entrar na conta de um cidadão. Antes
     * disso bastava ser `type = funcionario`, sem recurso nenhum para revogar.
     *
     * Quem atende no balcão é funcionário com o papel `employee`, e é ele que
     * recebe o pedido de senha — então o papel entra no gate, ao lado de admin.
     * Fica de fora a conta `type = funcionario` sem papel nenhum, que não
     * deveria estar atendendo.
     *
     * Checa o PAPEL, e não uma permissão nomeada, porque permissão fina não é
     * administrável neste sistema: `permissions` é escrita apenas pelo array
     * chumbado em `Database\Seeders\RolesAndPermissionsSeeder` (só local; em
     * produção, por SQL à mão). Não há tela para ligar permissão a papel — a de
     * Equipe só troca o papel da pessoa. Um ramo `hasPermission('senha-cidadao')`
     * seria código morto: a seed daria essa permissão só ao `admin`, que já
     * passa por `isAdmin()`.
     *
     * Consequência a registrar: só existem dois papéis, e os dois passam. Logo
     * não há como ter um funcionário atendendo e impedido de gerar link, a não
     * ser deixando-o sem papel — o que também lhe tira o resto. Controle por
     * pessoa exigiria uma tela de papel × permissão, que não existe.
     */
    private function definePasswordLinkGate(): void
    {
        Gate::define('gerar-link-senha', function ($user) {
            if (! $user->isFuncionario()) {
                return false;
            }

            return $user->isAdmin() || $user->hasRole('employee');
        });
    }
}