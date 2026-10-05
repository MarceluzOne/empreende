<?php

namespace App\Http\Requests;

use App\Rules\CpfOrCnpj;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class AssistedPasswordResetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza o documento para dígitos antes de validar — quem preenche digita
     * com máscara, e é só por dígitos que ele casa com `users.cpf` (cidadão) ou
     * `empresas.cnpj` (empresa).
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('cpf')) {
            $this->merge(['cpf' => preg_replace('/\D/', '', (string) $this->cpf)]);
        }
    }

    /**
     * Aceita CPF (cidadão) e CNPJ (empresa). Um campo só, sem bifurcar a tela:
     * o link não diz de quem é, e perguntar "você é pessoa ou empresa?" já
     * entregaria parte da resposta.
     *
     * Sem checar aqui se o documento existe: a tela é pública, e uma mensagem
     * do tipo "não encontrado" a transformaria num verificador de cadastros.
     * A recusa vem depois, genérica, do AssistedPasswordResetService.
     */
    public function rules(): array
    {
        return [
            'token'    => ['required', 'string'],
            'cpf'      => ['required', 'string', new CpfOrCnpj],
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'     => 'Link inválido. Peça um novo no atendimento.',
            'cpf.required'       => 'Informe o CPF ou o CNPJ do cadastro.',
            'password.required'  => 'A nova senha é obrigatória.',
            'password.confirmed' => 'A confirmação da senha não confere.',
        ];
    }
}
