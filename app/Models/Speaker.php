<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speaker extends Model
{
    use HasUuids;
    protected $fillable = ['name', 'bio', 'email', 'phone'];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
