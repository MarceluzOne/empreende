<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Ativação da conta de um cidadão durante o atendimento.
 *
 * O e-mail é opcional aqui: quando o cadastro já tem um em alguma origem
 * (currículo, inscrição em evento, prestador), o atendente não precisa digitar
 * nada. O campo existe para quem veio só de atendimento, que não guarda e-mail
 * em nenhuma coluna.
 *
 * A recusa por falta de e-mail não é desta camada e sim do
 * CitizenAccountActivationService: só ele sabe se alguma origem já tem um, e a
 * mensagem precisa dizer isso ao atendente.
 */
class ActivateCitizenAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge(['email' => trim((string) $this->input('email')) ?: null]);
        }
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email:filter', 'max:255', 'unique:users,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email'  => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já é o login de outra conta do portal. Informe um endereço diferente.',
        ];
    }
}
