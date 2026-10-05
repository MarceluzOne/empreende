<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\Cpf;
use App\Rules\CpfDisponivel;
use App\Services\CitizenService;
use App\Support\Document;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Edição dos dados de um cidadão pela equipe.
 *
 * As checagens de duplicidade comparam sempre contra o CPF ATUAL do cidadão:
 * reencontrar o próprio currículo ou a própria conta é o caso normal (o CPF
 * não mudou), e só um registro de outra pessoa caracteriza conflito.
 */
class UpdateCitizenRequest extends FormRequest
{
    private ?string $cpfAtual = null;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'cpf'   => ['required', 'string', 'max:14', new Cpf, new CpfDisponivel($this->cpfAtual())],
            'phone' => 'nullable|string|max:20',
            'email' => ['nullable', 'email', 'max:255', $this->emailDisponivel()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'O nome é obrigatório.',
            'cpf.required'   => 'O CPF é obrigatório.',
            'email.email'    => 'Informe um e-mail válido.',
        ];
    }

    /**
     * CPF que o cidadão tem hoje, em dígitos. Vazio quando o UUID da URL não
     * corresponde a ninguém — o controller devolve 404 nesse caso.
     */
    private function cpfAtual(): string
    {
        if ($this->cpfAtual === null) {
            $this->cpfAtual = (string) app(CitizenService::class)->cpfForUuid((string) $this->route('uuid'));
        }

        return $this->cpfAtual;
    }

    /**
     * O e-mail vira login da conta do cidadão, então não pode colidir com o de
     * outra conta.
     */
    private function emailDisponivel(): Closure
    {
        return function ($attribute, $value, $fail) {
            $user = User::where('email', $value)->first();

            if ($user === null) {
                return;
            }

            if (Document::digits($user->cpf) !== $this->cpfAtual()) {
                $fail('Este e-mail já está em uso por outra conta.');
            }
        };
    }
}
