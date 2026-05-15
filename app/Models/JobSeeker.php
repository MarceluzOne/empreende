<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class JobSeeker extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'name',
        'cpf',
        'job_function',
        'experience',
        'phone',
        'email',
        'interest_area',
        'curriculo_path',
        'status',
    ];

    protected $appends = ['formatted_cpf', 'formatted_phone', 'curriculo_url', 'status_label'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedCpfAttribute(): string
    {
        $cpf = preg_replace('/[^0-9]/', '', $this->cpf ?? '');
        if (strlen($cpf) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
        }
        return $this->cpf ?? '';
    }

    public function getFormattedPhoneAttribute(): ?string
    {
        if (!$this->phone) return null;
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        if (strlen($phone) === 11) {
            return preg_replace('/(\d{2})(\d{1})(\d{4})(\d{4})/', '($1)$2 $3-$4', $phone);
        }
        if (strlen($phone) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
        }
        return $this->phone;
    }

    public function getCurriculoUrlAttribute(): ?string
    {
        return $this->curriculo_path
            ? Storage::disk('public')->url($this->curriculo_path)
            : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
            default    => $this->status,
        };
    }
}
