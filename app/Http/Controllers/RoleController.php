<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    // Tags focadas em Salas e Agendamentos
    private $tags = [
        'rooms', 'rooms_view', 'rooms_create', 'rooms_update', 'rooms_delete',
        'bookings', 'bookings_view', 'bookings_create', 'bookings_update', 'bookings_delete',
        'users', 'users_view', 'users_create', 'users_update', 'users_delete'
    ];

    public function scriptConfiguration()
    {
        try {
            DB::beginTransaction();

            // 1. Criar as Roles do sistema
            $admin = Role::updateOrCreate(['name' => 'admin'], ['name' => 'admin', 'label' => 'Administrador']);
            $staff = Role::updateOrCreate(['name' => 'employee'], ['name' => 'employee', 'label' => 'Funcionário']);

            // 2. Processar as Tags de Reserva (Pai e Filho)
            foreach ($this->tags as $tag) {
                if (!Str::contains($tag, '_')) {
                    // Cria o módulo Pai (ex: rooms)
                    Permission::updateOrCreate(
                        ['name' => $tag],
                        ['name' => $tag, 'label' => ucfirst($tag)]
                    );
                } else {
                    // Cria a ação Filho (ex: rooms_create) vinculada ao Pai
                    $parentName = explode('_', $tag)[0];
                    $parent = Permission::where('name', $parentName)->first();

                    Permission::updateOrCreate(
                        ['name' => $tag],
                        [
                            'name' => $tag,
                            'label' => str_replace('_', ' ', ucfirst($tag)),
                            'permission_id' => $parent ? $parent->id : null
                        ]
                    );
                }
            }

            // 3. Atribuição de Poderes
            // Admin: Pega absolutamente todas as tags
            $admin->permissions()->sync(Permission::all()->pluck('id'));
            
            // Funcionário: Pode ver salas e gerenciar apenas os próprios agendamentos
            $staffPermissions = Permission::whereIn('name', [
                'rooms_view', 
                'bookings_view', 
                'bookings_create'
            ])->pluck('id');
            $staff->permissions()->sync($staffPermissions);

            // 4. Criar o Usuário Admin Inicial
            $user = User::updateOrCreate(
                ['email' => 'arruda16.marcelo@gmail.com'],
                [
                    'name' => 'Admin Sistema', 
                    'password' => Hash::make('12345678')
                ]
            );
            $user->roles()->syncWithoutDetaching([$admin->id]);

            DB::commit();
            return "Sistema de Reservas configurado! Admin: admin@reserva.com | Senha: 12345678";

        } catch (\Throwable $th) {
            DB::rollBack();
            return "Erro: " . $th->getMessage();
        }
    }
}