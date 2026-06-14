<?php

namespace App\Models;

use App\Support\Phone;
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
        return Phone::format($this->phone);
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
