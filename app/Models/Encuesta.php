<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Encuesta extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'titulo', 'descripcion', 'modulo', 'estado',
        'fecha_inicio', 'fecha_fin', 'creado_por', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin'    => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(EncuestaPregunta::class)->orderBy('orden');
    }

    public function destinatarios(): HasMany
    {
        return $this->hasMany(EncuestaDestinatario::class);
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(EncuestaRespuesta::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function resolverDestinatarios(): Collection
    {
        $userIds = collect();

        foreach ($this->destinatarios as $dest) {
            switch ($dest->tipo) {
                case 'todos':
                    $ids = User::where('estado', 'activo')->pluck('id');
                    $userIds = $userIds->merge($ids);
                    break;

                case 'unidad_organica':
                    $ids = User::where('unidad_organica_id', $dest->referencia_id)
                        ->where('estado', 'activo')->pluck('id');
                    $userIds = $userIds->merge($ids);
                    break;

                case 'rol':
                    $role = \Spatie\Permission\Models\Role::find($dest->referencia_id);
                    if ($role) {
                        $ids = User::role($role->name)->where('estado', 'activo')->pluck('id');
                        $userIds = $userIds->merge($ids);
                    }
                    break;

                case 'usuario':
                    $userIds->push($dest->referencia_id);
                    break;
            }
        }

        return $userIds->unique()->values();
    }

    public function getModuloLabelAttribute(): string
    {
        return match($this->modulo) {
            'sci'        => 'SCI',
            'integridad' => 'Integridad',
            'ambos'      => 'SCI + Integridad',
            default      => $this->modulo,
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'borrador'   => 'secondary',
            'publicada'  => 'success',
            'cerrada'    => 'warning',
            'archivada'  => 'danger',
            default      => 'secondary',
        };
    }
}
