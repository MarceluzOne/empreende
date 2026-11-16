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

    /**
     * Máscara 00.000.000/0000-00. Devolve o próprio valor quando não são 14 dígitos.
     */
    public static function maskCnpj(string $digits): string
    {
        if (strlen($digits) !== 14) {
            return $digits;
        }

        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $digits);
    }

    /**
     * As formas em que o CNPJ pode estar gravado — para usar em whereIn.
     *
     * Mesma dualidade do CPF, por outro motivo: `empresas.cnpj` grava **com
     * máscara**, enquanto `attendances.customer_cnpj`, `job_vacancies.cnpj` e
     * `service_providers.cnpj` gravam só dígitos. O script
     * `database/2026_08_31_normaliza_cpf_e_indices.sql` unifica tudo em dígitos,
     * mas enquanto ele não roda em produção qualquer consulta precisa das duas.
     *
     * @return array<int, string>
     */
    public static function cnpjVariants(?string $value): array
    {
        $digits = self::digits($value);

        if (strlen($digits) !== 14) {
            return [];
        }

        return array_values(array_unique([$digits, self::maskCnpj($digits)]));
    }
}
