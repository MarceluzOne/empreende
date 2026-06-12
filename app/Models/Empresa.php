<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = [
        'user_id',
        'razao_social',
        'cnpj',
        'telefone',
        'descricao',
        'cidade',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
