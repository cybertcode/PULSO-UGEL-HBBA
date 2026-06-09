<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    protected $fillable = ['nombre', 'activo'];

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'cargo_id');
    }
}
