<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Recusa na geração do link de redefinição assistida.
 *
 * Cada motivo já vem com a mensagem pronta para a tela do funcionário: o
 * controller só repassa. São recusas de negócio, não erros de sistema — daí
 * serem tratadas no controller e nunca chegarem ao Handler.
 */
class AssistedResetException extends RuntimeException
{
    /**
     * O CPF consolidado do cidadão não tem 11 dígitos. Acontece com cadastros
     * antigos gravados truncados: a pessoa pode até ter conta, mas não há como
     * casar os registros nem confirmar a identidade com um documento quebrado.
     */
    public static function invalidCpf(): self
    {
        return new self(
            'O CPF deste cadastro está incompleto ou inválido, então não dá para confirmar a identidade na tela de '
            .'redefinição. Corrija o CPF no cadastro antes de gerar o link.'
        );
    }

    public static function noAccount(): self
    {
        return new self('Esta pessoa não tem conta no portal. O link de senha só existe para quem já se cadastrou.');
    }

    public static function accountWithoutCpf(): self
    {
        return new self(
            'A conta desta pessoa existe, mas está sem CPF gravado — e é o CPF que confirma a identidade na tela de redefinição. '
            .'Rode o backfill de CPF antes de gerar o link.'
        );
    }

    public static function noPhone(): self
    {
        return new self(
            'Não há telefone cadastrado para este CPF em nenhum registro (currículo, atendimento, evento ou prestador). '
            .'Sem um número conhecido não há como confirmar com quem estamos falando: atenda presencialmente.'
        );
    }
}
