<?php

namespace App\Services;

use App\Exceptions\AssistedResetException;
use App\Models\User;
use App\Support\Document;
use App\Support\Email;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Redefinição de senha conduzida pelo atendimento, presencial ou por WhatsApp.
 *
 * Existe porque o envio de e-mail nunca foi ligado: sem ele, quem perde a senha
 * do portal não tem caminho de volta. Aqui o funcionário gera o link e repassa.
 *
 * Reaproveita o broker do Laravel (`password_reset_tokens`) apenas para criar e
 * validar o token, sem disparar notificação — daí `createToken()` em vez de
 * `sendResetLink()`. Com isso o hash do token, a expiração e o uso único vêm
 * prontos do framework.
 *
 * O que sustenta o fluxo:
 *  - **o link não identifica ninguém**: quem abrir precisa saber o CPF da conta,
 *    então ele sozinho não serve a quem o interceptar;
 *  - **uso único e com prazo**, de graça pelo broker;
 *  - **quem confirma a identidade é o atendente**, conferindo o CPF e os dados
 *    do cadastro na tela, e **toda geração vai para a auditoria** com autor, IP
 *    e horário.
 *
 * Telefone não é pré-requisito, de propósito. Ele chegou a ser exigido, mas não
 * protegia: o link nunca é enviado por aqui — é devolvido para o atendente
 * repassar —, então exigir um número cadastrado só travava o atendimento de quem
 * nunca informou telefone. Os números seguem no retorno como apoio à conferência
 * e ao envio.
 */
class AssistedPasswordResetService
{
    public function __construct(
        private CitizenService $citizens,
        private UserService $users
    ) {
    }

    /**
     * Link para o cidadão identificado pelo UUID da tela de Cidadãos.
     *
     * @return array{url: string, expires_at: string, phones: array, name: string, email: ?string}
     */
    public function linkForCitizen(string $uuid): array
    {
        return $this->linkFor($this->cpfForCitizen($uuid));
    }

    /**
     * CPF do cidadão a partir do UUID da listagem, já validado.
     */
    private function cpfForCitizen(string $uuid): string
    {
        $cpf = $this->citizens->cpfForUuid($uuid);

        if ($cpf === null) {
            throw AssistedResetException::noAccount();
        }

        $digits = Document::digits($cpf);

        if (strlen($digits) !== 11) {
            throw AssistedResetException::invalidCpf();
        }

        return $digits;
    }

    /**
     * O retorno é achatado de propósito: vai para a sessão como flash, e ali
     * não convém guardar model nem objeto — só o que a tela precisa exibir.
     *
     * @return array{url: string, expires_at: string, phones: array, name: string, email: ?string}
     */
    public function linkFor(string $cpf): array
    {
        $digits = Document::digits($cpf);

        if (strlen($digits) !== 11) {
            throw AssistedResetException::invalidCpf();
        }

        $account = $this->requireAccount($digits);

        $token = Password::broker()->createToken($account);

        AuditService::log('updated', $account, null, 'Gerou link de redefinição de senha para '.$account->name);

        return [
            'url'        => route('password.assisted', ['token' => $token]),
            'expires_at' => Carbon::now()->addMinutes((int) config('auth.passwords.users.expire'))->format('d/m/Y H:i'),
            'phones'     => $this->citizens->phonesFor($digits)->all(),
            'name'       => $account->name,
            'email'      => Email::mask($account->email),
        ];
    }

    /**
     * Redefine a senha. Devolve false em qualquer recusa — CPF que não bate,
     * token vencido, token já usado — porque a tela é pública e distinguir os
     * motivos transformaria a página num verificador de CPFs cadastrados.
     */
    public function reset(string $token, string $cpf, string $password): bool
    {
        if ($this->excedeuTentativas($token)) {
            return false;
        }

        $account = $this->accountForReset($cpf);

        if (!$account) {
            return false;
        }

        $status = Password::reset([
            'email'                 => $account->email,
            'password'              => $password,
            'password_confirmation' => $password,
            'token'                 => $token,
        ], fn ($user, $plain) => $this->users->changePassword($user, $plain));

        if ($status !== Password::PASSWORD_RESET) {
            return false;
        }

        // Sem isto a auditoria mostra "gerou link" e nunca diz se alguem usou.
        // Auth::id() e nulo aqui -- a tela e publica -- e audit_logs aceita.
        AuditService::log(
            'updated',
            $account,
            null,
            'Senha definida pelo proprio cidadao, pelo link do atendimento'
        );

        return true;
    }

    /**
     * Limite de tentativas POR TOKEN, somado ao throttle da rota.
     *
     * O `throttle:5,1` da rota conta por IP, e o CPF e o unico segredo que falta
     * a quem tem o link na mao: distribuindo as tentativas entre IPs, daria para
     * varrer CPFs sem esbarrar em nada. Aqui a conta e do token, entao a janela
     * total e 10 tentativas, independentemente de onde venham.
     */
    private function excedeuTentativas(string $token): bool
    {
        $chave = 'senha-assistida:'.sha1($token);

        if (RateLimiter::tooManyAttempts($chave, 10)) {
            return true;
        }

        RateLimiter::hit($chave, 3600);

        return false;
    }

    private function requireAccount(string $digits): User
    {
        $account = $this->citizens->accountFor($digits);

        if (!$account) {
            throw AssistedResetException::noAccount();
        }

        if ($account->cpf === null) {
            throw AssistedResetException::accountWithoutCpf();
        }

        return $account;
    }

    /**
     * Na redefinição a busca é só por `users.cpf`. O desvio pelo currículo que
     * a tela do funcionário usa aceitaria uma conta com o campo nulo — e aqui
     * o CPF é justamente o que confirma a identidade de quem abriu o link.
     */
    private function accountForReset(string $cpf): ?User
    {
        $variants = Document::cpfVariants($cpf);

        return $variants === []
            ? null
            : User::whereIn('cpf', $variants)->where('type', 'usuario')->first();
    }
}
