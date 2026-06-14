<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Valida que o valor é um CPF (11 dígitos) OU um CNPJ (14 dígitos) válido,
 * conferindo os dígitos verificadores. Ignora máscara (pontos, traços, barra).
 */
class CpfOrCnpj implements Rule
{
    public function passes($attribute, $value): bool
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        if (strlen($digits) === 11) {
            return $this->isValidCpf($digits);
        }

        if (strlen($digits) === 14) {
            return $this->isValidCnpj($digits);
        }

        return false;
    }

    public function message(): string
    {
        return 'Informe um CPF ou CNPJ válido.';
    }

    private function isValidCpf(string $cpf): bool
    {
        // Rejeita sequências repetidas (000..., 111..., etc.)
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($i = 0; $i < $t; $i++) {
                $sum += (int) $cpf[$i] * (($t + 1) - $i);
            }
            $digit = ((10 * $sum) % 11) % 10;
            if ((int) $cpf[$t] !== $digit) {
                return false;
            }
        }

        return true;
    }

    private function isValidCnpj(string $cnpj): bool
    {
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
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
}
