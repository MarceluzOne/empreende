<?php

namespace App\Services;

use App\Exceptions\AssistedResetException;
use App\Models\User;
use App\Support\Document;
use Illuminate\Support\Str;

/**
 * Criação de conta do portal durante o atendimento.
 *
 * Existe por causa de quem foi cadastrado no balcão: a pessoa tem currículo,
 * tem atendimentos, aparece em Cidadãos — e não tem conta nenhuma. O cadastro
 * de candidato não cria uma (`JobSeekerService::store()` grava `user_id` com o
 * id do próprio atendente), então essas pessoas nunca tiveram caminho de
 * entrada no portal. Não é "redefinir senha": é não haver senha.
 *
 * A conta nasce com senha aleatória que ninguém conhece — nem a equipe, nem a
 * pessoa — e inutilizável de propósito. Quem define a primeira senha é a
 * própria pessoa, pelo mesmo link assistido da redefinição. Com isso não há
 * momento nenhum em que um funcionário saiba a senha de um cidadão.
 *
 * Criar a conta é responsabilidade distinta de redefinir senha, daí a classe
 * separada; o link final sai do AssistedPasswordResetService, sem token nem
 * tela novos.
 */
class CitizenAccountActivationService
{
    public function __construct(
        private CitizenService $citizens,
        private UserRegistrationService $registration,
        private AssistedPasswordResetService $reset
    ) {
    }

    /**
     * Cria a conta do cidadão e devolve o link para ele definir a senha.
     *
     * @param  string|null $email  Informado pelo atendente quando o cadastro
     *                             não tem e-mail em nenhuma origem.
     * @return array{url: string, expires_at: string, phones: array, name: string, email: ?string}
     */
    public function activate(string $uuid, ?string $email = null): array
    {
        $cpf     = $this->requireCpf($uuid);
        $citizen = $this->citizens->detail($uuid);

        if ($citizen['account'] !== null) {
            throw AssistedResetException::accountAlreadyExists();
        }

        $conta = $this->registration->create([
            'name'     => $this->requireName($citizen),
            'email'    => $this->requireEmail($cpf, $email),
            'cpf'      => $cpf,
            // Nunca exibida nem enviada: a conta só passa a ser utilizável
            // depois que a pessoa define a senha pelo link.
            'password' => Str::random(40),
        ]);

        // O autocadastro pelo site não deixa rastro em audit_logs, mas conta
        // criada por terceiro deixa: aqui há um funcionário responsável.
        AuditService::log(
            'created',
            $conta,
            ['email' => $conta->email],
            "Criou conta no atendimento para {$conta->name}"
        );

        return $this->reset->linkFor($cpf);
    }

    private function requireCpf(string $uuid): string
    {
        $cpf = Document::digits($this->citizens->cpfForUuid($uuid));

        if (strlen($cpf) !== 11) {
            throw AssistedResetException::invalidCpf();
        }

        return $cpf;
    }

    /**
     * @param array<string, mixed> $citizen
     */
    private function requireName(array $citizen): string
    {
        $name = trim((string) ($citizen['name'] ?? ''));

        if ($name === '') {
            throw AssistedResetException::noName();
        }

        return $name;
    }

    /**
     * O e-mail é o login, e `users.email` é NOT NULL UNIQUE — sem um não há
     * conta. Vale o que o atendente informou; na falta dele, o primeiro já
     * conhecido nas outras origens deste CPF.
     *
     * Um e-mail que já é login de outra conta é recusado em vez de reaproveitado:
     * duas pessoas com o mesmo endereço de cadastro acontece (casal, familiar),
     * e silenciosamente ligar a conta nova ao login alheio daria à segunda
     * pessoa o acesso da primeira.
     */
    private function requireEmail(string $cpf, ?string $informado): string
    {
        $email = trim((string) $informado) ?: ($this->citizens->emailsFor($cpf)[0] ?? '');

        if ($email === '') {
            throw AssistedResetException::noEmail();
        }

        if (User::where('email', $email)->exists()) {
            throw AssistedResetException::emailTaken($email);
        }

        return $email;
    }
}
