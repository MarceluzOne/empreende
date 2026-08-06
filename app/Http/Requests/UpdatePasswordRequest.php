<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Bag próprio: o portal do usuário tem outros formulários na mesma tela e
     * só o modal de senha deve reagir a estes erros.
     */
    protected $errorBag = 'senha';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', 'different:current_password', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'         => 'Informe sua senha atual.',
            'current_password.current_password' => 'A senha atual está incorreta.',
            'password.required'                 => 'Informe a nova senha.',
            'password.confirmed'                => 'A confirmação da nova senha não confere.',
            'password.different'                => 'A nova senha deve ser diferente da atual.',
            'password.min'                      => 'A nova senha deve ter pelo menos 8 caracteres.',
        ];
    }
}
