<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\JobSeeker;
use App\Models\User;
use App\Support\Document;
use App\Support\Phone;
use Illuminate\Support\Facades\DB;

/**
 * Edição dos dados de um cidadão pela equipe (funcionário ou administrador).
 *
 * "Cidadão" não é uma tabela: é a pessoa que o CitizenService monta cruzando
 * currículo (job_seekers), atendimentos (attendances) e conta de acesso
 * (users) pelo CPF. Salvar, portanto, é espalhar a mesma alteração por essas
 * três origens — sobretudo o próprio CPF, cuja troca precisa alcançar todas
 * elas de uma vez, senão a pessoa se parte em dois cidadãos diferentes.
 *
 * O documento é sempre gravado só com dígitos, a forma para a qual o restante
 * do sistema está convergindo; a leitura continua aceitando as duas grafias.
 */
class CitizenUpdateService
{
    /**
     * Grava nome, CPF, telefone e e-mail nas origens que existirem, dentro de
     * uma transação: falhar no meio deixaria o CPF trocado em parte dos
     * registros, que é justamente o estado que esta tela serve para corrigir.
     *
     * @param  string $cpf  CPF atual do cidadão (qualquer grafia).
     * @param  array{name: string, cpf: string, phone: ?string, email: ?string} $data
     * @return string CPF em dígitos depois da edição — a URL do cidadão deriva dele.
     */
    public function update(string $cpf, array $data): string
    {
        $variants = Document::cpfVariants($cpf);
        $novoCpf  = Document::digits($data['cpf']);
        $phone    = Document::digits($data['phone'] ?? '') ?: null;
        $email    = trim((string) ($data['email'] ?? '')) ?: null;
        $name     = $data['name'];

        DB::transaction(function () use ($variants, $novoCpf, $name, $phone, $email) {
            $this->updateCandidato($variants, $novoCpf, $name, $phone, $email);
            $this->updateAttendances($variants, $novoCpf, $name);
            $this->updateAccount($variants, $novoCpf, $name, $phone, $email);
        });

        return $novoCpf;
    }

    /**
     * Alinha atendimentos e conta quando o CPF muda em OUTRA tela — o cadastro
     * de candidato, onde a equipe corrige o documento junto com o currículo.
     *
     * Sem isso o currículo fica com um documento e o resto do sistema com
     * outro, e a pessoa passa a aparecer como dois cidadãos distintos.
     */
    public function changeCpf(?string $de, ?string $para): void
    {
        $antigo = Document::digits($de);
        $novo   = Document::digits($para);

        if ($antigo === '' || $novo === '' || $antigo === $novo) {
            return;
        }

        $variants = Document::cpfVariants($antigo);

        DB::transaction(function () use ($variants, $novo) {
            Attendance::whereIn('customer_cpf', $variants)->update(['customer_cpf' => $novo]);

            $user = User::whereIn('cpf', $variants)->first();

            if ($user === null) {
                return;
            }

            $user->cpf = $novo;
            $user->save();

            AuditService::log('updated', $user, null, "Atualizou o CPF da conta de {$user->name}");
        });
    }

    /**
     * @param array<int, string> $variants
     */
    private function updateCandidato(array $variants, string $cpf, string $name, ?string $phone, ?string $email): void
    {
        $seeker = $variants === [] ? null : JobSeeker::whereIn('cpf', $variants)->first();

        if ($seeker === null) {
            return;
        }

        $anterior = $seeker->phone;

        $seeker->name = $name;
        $seeker->cpf  = $cpf;

        // Telefone e e-mail em branco significam "não informei", não "apague o
        // que está lá". Um formulário salvo sem preencher o campo não deve
        // destruir um contato que já existia -- a mesma regra que o e-mail da
        // conta sempre seguiu.
        if ($phone !== null) {
            $seeker->phone = $phone;
        }

        if ($email !== null) {
            $seeker->email = $email;
        }

        $seeker->save();

        AuditService::log(
            'updated',
            $seeker,
            $this->phoneChange($anterior, $seeker->phone),
            "Atualizou os dados do cidadão {$name}"
        );
    }

    /**
     * Corrige a IDENTIDADE da pessoa no histórico -- CPF e nome --, que é o que
     * a busca da tela de atendimentos enxerga.
     *
     * O telefone fica de fora de propósito. `customer_phone` é o número que a
     * pessoa informou NAQUELE atendimento; como as pessoas trocam de número,
     * cada linha guarda o da época. Um `update()` em massa achataria todos os
     * atendimentos daquele CPF para um valor só, destruindo o histórico sem
     * caminho de volta.
     *
     * @param array<int, string> $variants
     */
    private function updateAttendances(array $variants, string $cpf, string $name): void
    {
        if ($variants === []) {
            return;
        }

        Attendance::whereIn('customer_cpf', $variants)->update([
            'customer_cpf'  => $cpf,
            'customer_name' => $name,
        ]);
    }

    /**
     * O e-mail da conta é o login: só é sobrescrito quando a equipe informou
     * um, nunca apagado por o formulário ter vindo com o campo em branco.
     *
     * O telefone também é gravado aqui, e esta é a única tela que escreve
     * `users.phone`: é o único lugar onde cabe o telefone de quem só tem conta
     * no portal, sem currículo e sem atendimento.
     *
     * @param array<int, string> $variants
     */
    private function updateAccount(array $variants, string $cpf, string $name, ?string $phone, ?string $email): void
    {
        $user = $variants === [] ? null : User::whereIn('cpf', $variants)->first();

        if ($user === null) {
            return;
        }

        $anterior = $user->phone;

        $user->name = $name;
        $user->cpf  = $cpf;

        if ($email !== null) {
            $user->email = $email;
        }

        if ($phone !== null) {
            $user->phone = $phone;
        }

        $user->save();

        AuditService::log(
            'updated',
            $user,
            $this->phoneChange($anterior, $user->phone),
            "Atualizou a conta de acesso de {$name}"
        );
    }

    /**
     * Registra a troca de telefone com o número anterior e o novo.
     *
     * Pessoas trocam de número, então a troca é legítima e não se bloqueia --
     * mas fica registrada: o `changes` do audit_logs é uma coluna JSON, e com
     * ele o número antigo continua recuperável. Devolve null quando nada mudou,
     * para não poluir a auditoria.
     *
     * @return array{phone_anterior: ?string, phone_novo: ?string}|null
     */
    private function phoneChange(?string $anterior, ?string $novo): ?array
    {
        if ($anterior === $novo) {
            return null;
        }

        return [
            'phone_anterior' => Phone::format($anterior),
            'phone_novo'     => Phone::format($novo),
        ];
    }
}
