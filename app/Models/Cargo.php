<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $fillable = ['nombre', 'activo'];

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function usuarios()
    {
        return $this->hasMany(User::class, 'cargo', 'nombre');
    }
}
