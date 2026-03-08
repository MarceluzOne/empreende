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

            $admin = Role::updateOrCreate(['name' => 'admin'], ['name' => 'admin', 'label' => 'Administrador']);
            $staff = Role::updateOrCreate(['name' => 'employee'], ['name' => 'employee', 'label' => 'Funcionário']);

            foreach ($this->tags as $tag) {
                if (!Str::contains($tag, '_')) {
                    Permission::updateOrCreate(
                        ['name' => $tag],
                        ['name' => $tag, 'label' => ucfirst($tag)]
                    );
                } else {
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

            $admin->permissions()->sync(Permission::all()->pluck('id'));
            
            $staffPermissions = Permission::whereIn('name', [
                'rooms_view', 
                'bookings_view', 
                'bookings_create'
            ])->pluck('id');
            $staff->permissions()->sync($staffPermissions);

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