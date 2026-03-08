<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['permission_id', 'name', 'label', 'description'];

    // Relacionamento com as sub-permissões (Filhas)
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'permission_id');
    }

    // Relacionamento com a permissão "Pai"
    public function parent()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    // Relacionamento com Roles (N:N)
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
