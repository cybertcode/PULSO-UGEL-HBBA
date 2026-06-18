<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cargo extends Model
{
    protected $fillable = ['nombre', 'activo'];

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'cargo_user')->withTimestamps();
    }
}
