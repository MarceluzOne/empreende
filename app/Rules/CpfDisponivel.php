<?php

namespace App\Rules;

use App\Models\JobSeeker;
use App\Models\User;
use App\Support\Document;
use Illuminate\Contracts\Validation\Rule;

/**
 * Garante que o CPF informado não é de OUTRA pessoa.
 *
 * Currículo e conta de acesso localizam alguém pelo documento, então dois
 * cadastros com o mesmo CPF fariam as telas apontar para a pessoa errada.
 * Reencontrar o próprio registro é o caso normal — por isso a comparação é
 * sempre contra o CPF que a pessoa editada tem hoje.
 *
 * A mensagem não diz de quem é o cadastro conflitante: quem digitou o CPF não
 * precisa saber a quem ele pertence para corrigir o próprio erro de digitação.
 */
class CpfDisponivel implements Rule
{
    /**
     * @param string|null $cpfAtual CPF que a pessoa tem hoje, em qualquer grafia.
     */
    public function __construct(private ?string $cpfAtual) {}

    public function passes($attribute, $value): bool
    {
        $variants = Document::cpfVariants($value);

        // Documento fora do formato é assunto da regra Cpf, não desta.
        if ($variants === []) {
            return true;
        }

        $atual  = Document::digits($this->cpfAtual);
        $seeker = JobSeeker::whereIn('cpf', $variants)->first();

        if ($seeker !== null && Document::digits($seeker->cpf) !== $atual) {
            return false;
        }

        $user = User::whereIn('cpf', $variants)->first();

        return $user === null || Document::digits($user->cpf) === $atual;
    }

    public function message(): string
    {
        return 'Este CPF já está cadastrado para outra pessoa.';
    }
}
