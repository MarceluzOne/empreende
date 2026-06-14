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
     * Documento do cidadão formatado conforme o tamanho:
     * 11 dígitos => CPF (000.000.000-00), 14 dígitos => CNPJ (00.000.000/0000-00).
     * Retorna null quando não há documento.
     */
    public function getCustomerDocumentFormattedAttribute(): ?string
    {
        $doc = preg_replace('/\D/', '', (string) $this->customer_cpf);

        if (strlen($doc) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
        }

        if (strlen($doc) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
        }

        return $this->customer_cpf ?: null;
    }
}
