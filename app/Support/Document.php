<?php

namespace App\Support;

/**
 * Normalização de CPF.
 *
 * O sistema grava o documento de duas formas conforme a origem: o currículo
 * (job_seekers) guarda com máscara, e conta, inscrição em evento, atendimento
 * e cadastro de prestador guardam só os dígitos. Toda consulta que cruza essas
 * origens precisa considerar as duas formas.
 */
class Document
{
    public static function digits(?string $value): string
    {
        return preg_replace('/\D/', '', (string) $value);
    }

    /**
     * Máscara 000.000.000-00. Devolve o próprio valor quando não são 11 dígitos.
     */
    public static function maskCpf(string $digits): string
    {
        if (strlen($digits) !== 11) {
            return $digits;
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digits);
    }

    /**
     * As formas em que o CPF pode estar gravado — para usar em whereIn.
     * Vazio quando o valor não é um CPF de 11 dígitos.
     *
     * @return array<int, string>
     */
    public static function cpfVariants(?string $value): array
    {
        $digits = self::digits($value);

        if (strlen($digits) !== 11) {
            return [];
        }

        return array_values(array_unique([$digits, self::maskCpf($digits)]));
    }
}
