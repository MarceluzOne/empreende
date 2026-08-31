<?php

namespace App\Services;

use App\Exceptions\AssistedResetException;
use App\Models\User;
use App\Support\Document;
use App\Support\Email;
use App\Support\Phone;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Password;

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
 * Duas travas sustentam o fluxo:
 *  - o link não identifica ninguém: quem abrir precisa saber o CPF da conta;
 *  - o link só é gerado para quem tem telefone já cadastrado, para o
 *    funcionário enviar a um número que o sistema conhecia de antes. Sem isso,
 *    no atendimento por WhatsApp a pessoa confirmaria na tela um CPF que ela
 *    mesma acabou de ditar — confirmação nenhuma.
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
     * Grava um telefone na conta quando o cadastro não tem nenhum, para
     * desbloquear a geração do link.
     *
     * Vale registrar o limite disto: um número informado durante o próprio
     * atendimento não confirma identidade — quem pediu o link é quem ditou o
     * número. Serve para o caso em que o funcionário já se convenceu de quem
     * é a pessoa por outro meio (documento no balcão, por exemplo). Por isso
     * a ação vai para a auditoria com o número gravado e o autor.
     */
    public function registerPhone(string $uuid, string $phone): void
    {
        $account = $this->requireAccount($this->cpfForCitizen($uuid));

        $this->citizens->storeAccountPhone($account, $phone);

        AuditService::log(
            'updated',
            $account,
            ['phone' => Phone::format($phone)],
            'Cadastrou telefone no atendimento para '.$account->name
        );
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
        $phones  = $this->requirePhones($digits);

        $token = Password::broker()->createToken($account);

        AuditService::log('updated', $account, null, 'Gerou link de redefinição de senha para '.$account->name);

        return [
            'url'        => route('password.assisted', ['token' => $token]),
            'expires_at' => Carbon::now()->addMinutes((int) config('auth.passwords.users.expire'))->format('d/m/Y H:i'),
            'phones'     => $phones->all(),
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

        return $status === Password::PASSWORD_RESET;
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
     * @return Collection<int, array{digits: string, formatted: ?string, sources: array<int, string>}>
     */
    private function requirePhones(string $digits): Collection
    {
        $phones = $this->citizens->phonesFor($digits);

        if ($phones->isEmpty()) {
            throw AssistedResetException::noPhone();
        }

        return $phones;
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
