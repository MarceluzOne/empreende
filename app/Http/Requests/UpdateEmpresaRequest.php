<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Dados cadastrais da empresa editados pela própria empresa no portal.
 * O CNPJ fica de fora: é a identidade da conta e o vínculo com a vitrine.
 */
class UpdateEmpresaRequest extends FormRequest
{
    /**
     * Bag próprio: a tela do portal tem outros formulários e só o modal de
     * dados cadastrais deve reagir a estes erros.
     */
    protected $errorBag = 'empresa';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'razao_social' => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email,'.$this->user()->id,
            'telefone'     => 'nullable|string|max:20',
            'cidade'       => 'nullable|string|max:255',
            'descricao'    => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'razao_social.required' => 'Informe a razão social.',
            'email.required'        => 'Informe o e-mail de acesso.',
            'email.email'           => 'Informe um e-mail válido.',
            'email.unique'          => 'Este e-mail já está em uso por outra conta.',
            'descricao.max'         => 'A descrição deve ter no máximo 2000 caracteres.',
        ];
    }
}
