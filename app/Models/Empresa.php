<?php

namespace App\Models;

use App\Support\Phone;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    public function getFormattedTelefoneAttribute(): ?string
    {
        return Phone::format($this->telefone);
    }

    protected $fillable = [
        'user_id',
        'razao_social',
        'cnpj',
        'telefone',
        'descricao',
        'cidade',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
