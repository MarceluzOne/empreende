<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'label', 'description'];

    // Relacionamento com Permissões (N:N)
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    // Relacionamento com Usuários (N:N)
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function hasPermission($permissionName)
{
    return $this->roles()->whereHas('permissions', function ($query) use ($permissionName) {
        $query->where('name', $permissionName);
    })->exists();
}
}