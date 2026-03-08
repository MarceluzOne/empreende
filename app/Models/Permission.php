<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['permission_id', 'name', 'label', 'description'];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'permission_id');
    }

    public function parent()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
