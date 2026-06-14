<?php

namespace App\Support;

/**
 * Formatação de telefone no padrão brasileiro.
 *  - 11 dígitos (celular): (XX)X XXXX-XXXX
 *  - 10 dígitos (fixo):    (XX) XXXX-XXXX
 *  - outros: retorna o valor original (ou null se vazio).
 */
class Phone
{
    public static function format(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) === 11) {
            return preg_replace('/(\d{2})(\d{1})(\d{4})(\d{4})/', '($1)$2 $3-$4', $digits);
        }

        if (strlen($digits) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $digits);
        }

        return $value;
    }
}
