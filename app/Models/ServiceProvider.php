<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos em massa.
     * * @var array
     */
    protected $fillable = [
        'name',
        'provider_type',
        'service_title',
        'email', 
        'instagram',
        'whatsapp',
        'optional_info',
        'status'
    ];

    protected $casts = [
        'provider_type' => 'string',
    ];


    public function getTypeLabelAttribute()
    {
        return $this->provider_type === 'company' ? 'Produto' : 'Serviço';
    }
    public function getStatusLabelAttribute()
{
    return [
        'active'   => 'Ativo',
        'inactive' => 'Inativo',
        'pending'  => 'Pendente',
    ][$this->status] ?? 'Desconhecido';
}
}