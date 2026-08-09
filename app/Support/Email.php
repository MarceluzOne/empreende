<?php

namespace App\Support;

/**
 * Mascaramento de e-mail para telas públicas.
 *
 * Serve para confirmar a alguém que já existe um cadastro sem entregar o
 * endereço: quem digita o CPF de outra pessoa não descobre o e-mail dela.
 */
class Email
{
    /**
     * ma***@email.com — mantém as duas primeiras letras e o domínio.
     */
    public static function mask(?string $email): ?string
    {
        if ($email === null || strpos($email, '@') === false) {
            return null;
        }

        [$local, $domain] = explode('@', $email, 2);

        $visible = mb_substr($local, 0, 2);
        $hidden  = str_repeat('*', max(mb_strlen($local) - 2, 1));

        return $visible.$hidden.'@'.$domain;
    }
}
