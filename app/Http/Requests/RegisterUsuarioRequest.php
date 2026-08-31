<?php

namespace App\Http\Requests;

use App\Rules\Cpf;
use App\Services\UserRegistrationService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUsuarioRequest extends FormRequest
{
    /**
     * Conta existente detectada na validação, repassada à view para montar os
     * atalhos de login e recuperação de senha.
     *
     * @var array<string, mixed>|null
     */
    private ?array $conflict = null;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza o CPF para apenas dígitos antes de validar, para que a checagem
     * de conta existente compare com o valor efetivamente gravado (sem máscara).
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('cpf')) {
            $this->merge(['cpf' => preg_replace('/\D/', '', (string) $this->cpf)]);
        }
    }

    /**
     * Sem 'unique' em cpf e email: a duplicidade vira uma mensagem só, montada
     * em withValidator(), em vez de dois erros de campo sem saída. O índice
     * único de users.cpf e users.email continua sendo a garantia final.
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'cpf'      => ['required', 'string', new Cpf],
            'email'    => 'required|email|max:255',
            'password' => 'required|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Informe seu nome completo.',
            'cpf.required'       => 'Informe o CPF.',
            'email.required'     => 'Informe um e-mail.',
            'email.email'        => 'Informe um e-mail válido.',
            'password.required'  => 'Informe uma senha.',
            'password.min'       => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            // CPF ou e-mail malformados: o erro de formato já basta.
            if ($v->errors()->hasAny(['cpf', 'email'])) {
                return;
            }

            $conflict = app(UserRegistrationService::class)
                ->accountConflict($this->input('cpf'), $this->input('email'));

            if ($conflict === null) {
                return;
            }

            $this->conflict = $this->describeConflict($conflict);

            $v->errors()->add('conta_existente', $this->conflict['message']);
        });
    }

    /**
     * Guarda o conflito na sessão para a view desenhar a caixa com os atalhos.
     */
    protected function failedValidation(Validator $validator): void
    {
        if ($this->conflict !== null) {
            session()->flash('conta_existente', $this->conflict);
        }

        parent::failedValidation($validator);
    }

    /**
     * @param array<string, mixed> $conflict
     * @return array<string, mixed>
     */
    private function describeConflict(array $conflict): array
    {
        $isUsuario = $conflict['type'] === 'usuario';
        $sameEmail = in_array($conflict['matched'], ['email', 'both'], true);

        return [
            'message'       => $this->conflictMessage($conflict, $isUsuario),
            'type'          => $conflict['type'],
            // Só pré-preenche o login quando o e-mail digitado é o da conta:
            // no conflito por CPF o endereço da conta é outro, e é mascarado.
            'prefill_email' => $sameEmail ? $this->input('email') : null,
            'can_reset'     => $isUsuario,
        ];
    }

    /**
     * @param array<string, mixed> $conflict
     */
    private function conflictMessage(array $conflict, bool $isUsuario): string
    {
        if ($conflict['matched'] !== 'email') {
            return 'Já existe uma conta cadastrada com este CPF, no e-mail '.$conflict['email_masked'].'.';
        }

        if ($isUsuario) {
            return 'Este e-mail já está em uso por uma conta de usuário.';
        }

        $tipo = $conflict['type'] === 'empresa' ? 'empresa' : 'funcionário';

        return 'Este e-mail pertence a uma conta de '.$tipo.'. Use outro e-mail ou acesse pelo portal correspondente.';
    }
}
