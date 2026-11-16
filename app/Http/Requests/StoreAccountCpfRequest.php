<?php

namespace App\Http\Requests;

use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;

/**
 * CPF informado para uma conta que ainda não tem um — pelo próprio usuário,
 * no login, ou pelo atendente. A checagem de "já pertence a outra conta" fica
 * no AccountCpfService, que precisa saber de qual conta se trata.
 */
class StoreAccountCpfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('cpf')) {
            $this->merge(['cpf' => preg_replace('/\D/', '', (string) $this->cpf)]);
        }
    }

    public function rules(): array
    {
        return [
            'cpf' => ['required', 'string', new Cpf],
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.required' => 'Informe o CPF.',
        ];
    }
}
