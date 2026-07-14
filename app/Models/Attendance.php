<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory, HasUuids;

    /**
     * Campos que podem ser preenchidos em massa.
     * Incluímos o 'scheduled_at' para o agendamento do atendimento.
     */
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_cpf',
        'customer_cnpj',
        'customer_phone',
        'service_type',
        'description',
        'scheduled_at',
        'status',
    ];

    /**
     * Casts de atributos.
     * Transformar 'scheduled_at' em um objeto Carbon permite usar
     * métodos como ->format('d/m/Y H:i') diretamente na View.
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: O Atendente (User) que realizou o registro.
     * Essencial para auditoria em Vitória de Santo Antão.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper para verificar se o atendimento está atrasado ou é para hoje.
     * Útil para estilização no Index de Atendimentos.
     */
    public function isToday(): bool
    {
        return $this->scheduled_at?->isToday() ?? false;
    }

    /**
     * CPF do cidadão formatado (000.000.000-00). Retorna null quando ausente.
     */
    public function getCustomerCpfFormattedAttribute(): ?string
    {
        $cpf = preg_replace('/\D/', '', (string) $this->customer_cpf);

        return strlen($cpf) === 11
            ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf)
            : ($this->customer_cpf ?: null);
    }

    /**
     * CNPJ do cidadão formatado (00.000.000/0000-00). Retorna null quando ausente.
     */
    public function getCustomerCnpjFormattedAttribute(): ?string
    {
        $cnpj = preg_replace('/\D/', '', (string) $this->customer_cnpj);

        return strlen($cnpj) === 14
            ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj)
            : ($this->customer_cnpj ?: null);
    }

    /**
     * Documento principal para exibição: prioriza o CPF; se não houver,
     * cai para o CNPJ. Retorna null quando não há nenhum documento.
     */
    public function getCustomerDocumentFormattedAttribute(): ?string
    {
        return $this->customer_cpf_formatted ?: $this->customer_cnpj_formatted;
    }
}
