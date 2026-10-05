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

    /*
    |--------------------------------------------------------------------------
    | Ativação de conta no atendimento
    |--------------------------------------------------------------------------
    | Recusas da criação de conta para quem foi cadastrado no balcão. Moram
    | aqui porque terminam no mesmo lugar: um aviso no painel "Acesso ao
    | portal", pela mesma via das recusas da redefinição.
    */

    public static function accountAlreadyExists(): self
    {
        return new self(
            'Esta pessoa já tem conta no portal. Em vez de criar outra, gere o link de redefinição de senha.'
        );
    }

    public static function noName(): self
    {
        return new self(
            'Este cadastro está sem nome, e o nome é obrigatório na conta. Preencha os dados do cidadão antes de ativar.'
        );
    }

    public static function noEmail(): self
    {
        return new self(
            'Não há e-mail em nenhum registro deste CPF, e o e-mail é o login da conta. '
            .'Pergunte o e-mail à pessoa e informe no campo da tela.'
        );
    }

    public static function emailTaken(string $email): self
    {
        return new self(
            "O e-mail {$email} já é o login de outra conta do portal. Informe um endereço diferente: reaproveitá-lo "
            .'daria a esta pessoa o acesso da outra.'
        );
    }
}
