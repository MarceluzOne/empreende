<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Papéis, permissões e o admin inicial — só para o ambiente LOCAL.
 *
 * Substitui a antiga rota pública GET /setup-inicial, que fazia o mesmo pela
 * web e sem autenticação. Em produção isto já existe; permissão nova entra lá
 * por script SQL em database/ (ver CLAUDE.md, "Banco de dados").
 *
 * Os gates do Laravel nascem de `permissions` (AppServiceProvider::boot), então
 * este array continua sendo a única definição de papel × permissão no código.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    private array $tags = [
        'admin-only',
        'rooms', 'rooms_view', 'rooms_create', 'rooms_update', 'rooms_delete',
        'bookings', 'bookings_view', 'bookings_create', 'bookings_update', 'bookings_delete',
        'users', 'users_view', 'users_create', 'users_update', 'users_delete',
    ];

    private array $employeePermissions = ['rooms_view', 'bookings_view', 'bookings_create'];

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->error('RolesAndPermissionsSeeder não roda em produção.');
            return;
        }

        DB::transaction(function () {
            $admin = Role::updateOrCreate(['name' => 'admin'], ['label' => 'Administrador']);
            $staff = Role::updateOrCreate(['name' => 'employee'], ['label' => 'Funcionário']);

            foreach ($this->tags as $tag) {
                $this->seedPermission($tag);
            }

            $admin->permissions()->sync(Permission::pluck('id'));
            $staff->permissions()->sync(Permission::whereIn('name', $this->employeePermissions)->pluck('id'));

            $this->seedAdmin($admin);
        });
    }

    /**
     * Tag com '_' (e sem '-') é filha da permissão do prefixo: 'rooms_view' → 'rooms'.
     */
    private function seedPermission(string $tag): void
    {
        $isParent = !Str::contains($tag, '_') || Str::contains($tag, '-');

        if ($isParent) {
            Permission::updateOrCreate(['name' => $tag], ['label' => ucfirst(str_replace('-', ' ', $tag))]);
            return;
        }

        $parent = Permission::where('name', explode('_', $tag)[0])->first();

        Permission::updateOrCreate(['name' => $tag], [
            'label'         => str_replace('_', ' ', ucfirst($tag)),
            'permission_id' => optional($parent)->id,
        ]);
    }

    /**
     * Cria o admin a partir do .env só se ele ainda não existir: rodar o seed
     * de novo não pode redefinir a senha de ninguém.
     */
    private function seedAdmin(Role $admin): void
    {
        $email    = config('app.super_admin_email');
        $password = config('app.super_admin_password');

        if (empty($email) || empty($password)) {
            $this->command->warn('SUPER_ADMIN_EMAIL/SUPER_ADMIN_PASSWORD vazios no .env: admin não criado.');
            return;
        }

        $user = User::firstOrCreate(['email' => $email], [
            'name'     => 'Admin Sistema',
            'password' => Hash::make($password),
            'type'     => 'funcionario',
        ]);

        $user->roles()->syncWithoutDetaching([$admin->id]);
    }
}
