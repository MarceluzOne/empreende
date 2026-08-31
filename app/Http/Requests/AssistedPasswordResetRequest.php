<?php

namespace App\Http\Requests;

use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class AssistedPasswordResetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza o CPF para dígitos antes de validar — a pessoa digita com
     * máscara, e é só por dígitos que o CPF casa com `users.cpf`.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('cpf')) {
            $this->merge(['cpf' => preg_replace('/\D/', '', (string) $this->cpf)]);
        }
    }

    /**
     * Sem checar aqui se o CPF existe: a tela é pública, e uma mensagem do
     * tipo "CPF não encontrado" a transformaria num verificador de cadastros.
     * A recusa vem depois, genérica, do AssistedPasswordResetService.
     */
    public function rules(): array
    {
        return [
            'token'    => ['required', 'string'],
            'cpf'      => ['required', 'string', new Cpf],
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'     => 'Link inválido. Peça um novo no atendimento.',
            'cpf.required'       => 'Informe o CPF.',
            'password.required'  => 'A nova senha é obrigatória.',
            'password.confirmed' => 'A confirmação da senha não confere.',
        ];
    }
}
