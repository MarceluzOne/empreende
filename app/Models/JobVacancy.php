<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'cnpj',
        'company_name',
        'position',
        'quantity',
        'remuneration',
        'requirements',
        'benefits',
        'min_experience',
        'interest_area',
        'status',
    ];

    protected $casts = [
        'benefits' => 'array',
    ];

    protected $appends = ['formatted_cnpj', 'status_label', 'formatted_remuneration'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function getFormattedCnpjAttribute(): string
    {
        $cnpj = preg_replace('/[^0-9]/', '', $this->cnpj);
        if (strlen($cnpj) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
        }
        return $this->cnpj;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'Ativa',
            'inactive' => 'Inativa',
            'filled'   => 'Preenchida',
            default    => $this->status,
        };
    }

    /**
     * Remuneração formatada em Real (ex.: "R$ 5.000,00"). Retorna null quando
     * não há valor (exibir "A combinar"). Baseia-se no decimal armazenado.
     */
    public function getFormattedRemunerationAttribute(): ?string
    {
        if ($this->remuneration === null || $this->remuneration === '') {
            return null;
        }

        $value = (float) $this->remuneration;
        if ($value <= 0) {
            return null;
        }

        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}
