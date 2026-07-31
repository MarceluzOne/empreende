<?php

namespace App\Models;

use App\Support\Phone;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Speaker extends Model
{
    use HasUuids;
    protected $fillable = ['name', 'bio', 'email', 'phone', 'photo_path', 'cpf', 'signature_path', 'is_director'];

    protected $casts = ['is_director' => 'boolean'];

    /** Diretor do Empreende Vitória — assina todos os certificados. */
    public function scopeDirector($query)
    {
        return $query->where('is_director', true);
    }

    public function getFormattedPhoneAttribute(): ?string
    {
        return Phone::format($this->phone);
    }

    public function getFormattedCpfAttribute(): ?string
    {
        return $this->cpf
            ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf)
            : null;
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null;
    }

    public function signatureUrl(): ?string
    {
        return $this->signature_path ? Storage::disk('public')->url($this->signature_path) : null;
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
