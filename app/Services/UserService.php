<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Troca a senha do usuário. O hash é feito aqui porque o cast 'hashed'
     * declarado no model não tem efeito no Laravel 9 — casts() como método só
     * passou a ser lido a partir do Laravel 11.
     */
    public function changePassword(User $user, string $password): void
    {
        $user->password = Hash::make($password);
        $user->save();
    }
}
