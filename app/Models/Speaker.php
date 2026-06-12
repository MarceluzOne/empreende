<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Speaker extends Model
{
    use HasUuids;
    protected $fillable = ['name', 'bio', 'email', 'phone', 'photo_path'];

    public function photoUrl(): ?string
    {
        return $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null;
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
