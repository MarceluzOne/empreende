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
        $atual    = Document::digits($cpf);
        $novoCpf  = Document::digits($data['cpf']);
        $phone    = Document::digits($data['phone'] ?? '') ?: null;
        $email    = trim((string) ($data['email'] ?? '')) ?: null;
        $name     = $data['name'];

        DB::transaction(function () use ($atual, $novoCpf, $name, $phone, $email) {
            $account = $this->accountForCitizen($atual);

            $this->updateCandidato($atual, $novoCpf, $name, $phone, $email);
            $this->updateAttendances($atual, $novoCpf, $name);
            $this->updateAccount($account, $novoCpf, $name, $phone, $email);
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

        DB::transaction(function () use ($antigo, $novo) {
            $this->whereCpf(Attendance::query(), 'customer_cpf', $antigo)->update(['customer_cpf' => $novo]);

            $user = $this->whereCpf(User::query(), 'cpf', $antigo)->first();

            if ($user === null) {
                return;
            }

            $user->cpf = $novo;
            $user->save();

            AuditService::log('updated', $user, null, "Atualizou o CPF da conta de {$user->name}");
        });
    }

    /**
     * Conta de acesso do cidadão cujo CPF atual é `$cpf`.
     *
     * Pelo `users.cpf` primeiro. Sem ele, pelo currículo: contas anteriores à
     * coluna `cpf` ficam com o campo nulo, e é justamente nelas que a equipe
     * vem corrigir os dados. O desvio exige `type = 'usuario'` porque currículo
     * de balcão aponta para a conta do ATENDENTE (ver CitizenService::accountFor).
     *
     * Público porque a validação do e-mail precisa saber qual conta é a do
     * próprio cidadão, para não acusar o e-mail dele como "de outra conta".
     */
    public function accountForCitizen(string $cpf): ?User
    {
        $digits = Document::digits($cpf);

        if ($digits === '') {
            return null;
        }

        $byCpf = $this->whereCpf(User::query(), 'cpf', $digits)->first();

        if ($byCpf !== null) {
            return $byCpf;
        }

        $userId = $this->whereCpf(JobSeeker::query(), 'cpf', $digits)->whereNotNull('user_id')->value('user_id');

        return $userId === null ? null : User::where('id', $userId)->where('type', 'usuario')->first();
    }

    /**
     * Filtra pelo CPF em qualquer grafia — inclusive incompleto.
     *
     * Um CPF completo cabe nas duas formas de `cpfVariants()`. Um incompleto
     * (cadastro antigo, "556.122.92") não tem máscara previsível, então é
     * comparado só pelos dígitos. É o caso que mais precisa desta tela: é
     * para corrigir um documento assim que a equipe abre a edição.
     */
    private function whereCpf($query, string $column, string $digits)
    {
        $variants = Document::cpfVariants($digits);

        if ($variants !== []) {
            return $query->whereIn($column, $variants);
        }

        return $query->whereRaw(
            "REPLACE(REPLACE(REPLACE({$column}, '.', ''), '-', ''), ' ', '') = ?",
            [$digits]
        );
    }

    private function updateCandidato(string $atual, string $cpf, string $name, ?string $phone, ?string $email): void
    {
        $seeker = $atual === '' ? null : $this->whereCpf(JobSeeker::query(), 'cpf', $atual)->first();

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
     */
    private function updateAttendances(string $atual, string $cpf, string $name): void
    {
        if ($atual === '') {
            return;
        }

        $this->whereCpf(Attendance::query(), 'customer_cpf', $atual)->update([
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
     * A conta vem resolvida de antes, porque o currículo (por onde ela pode ter
     * sido achada) já está com o CPF novo quando chega a vez dela.
     */
    private function updateAccount(?User $user, string $cpf, string $name, ?string $phone, ?string $email): void
    {
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
