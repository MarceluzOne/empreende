<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Valida um CNPJ (14 dígitos) conferindo os dígitos verificadores.
 * Ignora máscara (pontos, barra e traço).
 */
class Cnpj implements Rule
{
    public function passes($attribute, $value): bool
    {
        $cnpj = preg_replace('/\D/', '', (string) $value);

        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        foreach ([$weights1, $weights2] as $pos => $weights) {
            $sum = 0;
            foreach ($weights as $i => $weight) {
                $sum += (int) $cnpj[$i] * $weight;
            }
            $mod = $sum % 11;
            $digit = $mod < 2 ? 0 : 11 - $mod;
            if ((int) $cnpj[12 + $pos] !== $digit) {
                return false;
            }
        }

        return true;
    }

    public function message(): string
    {
        return 'Informe um CNPJ válido.';
    }
}
