<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos em massa.
     * Incluímos o 'scheduled_at' para o agendamento do atendimento.
     */
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_cpf',
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
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
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
}