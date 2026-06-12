<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSeeker extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'name',
        'cpf',
        'job_function',
        'city',
        'state',
        'phone',
        'email',
        'linkedin_url',
        'github_url',
        'summary',
        'skills',
        'experiences',
        'education',
        'languages',
        'certifications',
        'experience',
        'interest_area',
        'status',
    ];

    protected $casts = [
        'experiences'    => 'array',
        'education'      => 'array',
        'languages'      => 'array',
        'certifications' => 'array',
    ];

    protected $appends = ['formatted_phone', 'status_label'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
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

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
            default    => $this->status,
        };
    }
}
