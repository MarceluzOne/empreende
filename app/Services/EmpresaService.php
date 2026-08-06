<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EmpresaService
{
    /**
     * Atualiza os dados cadastrais da empresa. A razão social é o nome de
     * exibição da conta e o e-mail é o login, então os dois vivem em users e
     * precisam andar junto com a linha de empresas.
     */
    public function updateProfile(User $user, array $data): Empresa
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name'  => $data['razao_social'],
                'email' => $data['email'],
            ]);

            return Empresa::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'razao_social' => $data['razao_social'],
                    'telefone'     => $data['telefone'] ?? null,
                    'cidade'       => $data['cidade'] ?? null,
                    'descricao'    => $data['descricao'] ?? null,
                ]
            );
        });
    }
}
