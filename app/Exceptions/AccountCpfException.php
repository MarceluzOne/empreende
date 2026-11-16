<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Recusas ao gravar o CPF de uma conta do portal que ainda não tem um.
 *
 * As mensagens servem às duas telas que chegam aqui — o próprio cidadão, no
 * primeiro login, e o atendente, na tela de contas sem CPF —, por isso nunca
 * dizem de quem é o cadastro conflitante.
 */
class AccountCpfException extends RuntimeException
{
    public static function taken(): self
    {
        return new self(
            'Este CPF já está vinculado a outra conta. Se ele é mesmo seu, procure o atendimento do Empreende Vitória.'
        );
    }

    public static function alreadyFilled(): self
    {
        return new self('Esta conta já tem CPF gravado. Para corrigi-lo, use a edição do cidadão.');
    }

    public static function notPortalAccount(): self
    {
        return new self('Só contas de usuário do portal recebem CPF por aqui.');
    }
}
