<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Normativa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'normativas';

    protected $fillable = [
        'nombre', 'codigo', 'descripcion', 'tipo', 'alcance', 'modulo',
        'archivo_path', 'archivo_nombre_original', 'link_externo',
        'tutorial_url', 'tutorial_tipo',
        'fecha_emision', 'fecha_vigencia', 'vigente', 'entidad_emisora',
        'observacion', 'orden', 'creado_por',
    ];

    protected $casts = [
        'fecha_emision'  => 'date',
        'fecha_vigencia' => 'date',
        'vigente'        => 'boolean',
        'orden'          => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (Auth::check() && !$model->creado_por) {
                $model->creado_por = Auth::id();
            }
        });
    }

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            'ley'          => 'Ley',
            'decreto'      => 'Decreto',
            'resolucion'   => 'Resolución',
            'directiva'    => 'Directiva',
            'manual'       => 'Manual',
            'reglamento'   => 'Reglamento',
            'oficio'       => 'Oficio',
            default        => ucfirst($this->tipo ?? 'Otro'),
        };
    }

    public function getTipoColorAttribute(): string
    {
        return match($this->tipo) {
            'ley'          => 'danger',
            'decreto'      => 'warning',
            'resolucion'   => 'primary',
            'directiva'    => 'info',
            'manual'       => 'success',
            'reglamento'   => 'secondary',
            'oficio'       => 'dark',
            default        => 'secondary',
        };
    }

    public function getTipoIconAttribute(): string
    {
        return match($this->tipo) {
            'ley'          => 'tabler-gavel',
            'decreto'      => 'tabler-file-certificate',
            'resolucion'   => 'tabler-file-check',
            'directiva'    => 'tabler-file-description',
            'manual'       => 'tabler-book',
            'reglamento'   => 'tabler-list-details',
            'oficio'       => 'tabler-mail',
            default        => 'tabler-file',
        };
    }

    public function getModuloLabelAttribute(): string
    {
        return match($this->modulo) {
            'sci'        => 'Control Interno',
            'integridad' => 'Modelo Integridad',
            'general'    => 'General',
            default      => ucfirst($this->modulo ?? 'General'),
        };
    }

    public function getModuloColorAttribute(): string
    {
        return match($this->modulo) {
            'sci'        => 'primary',
            'integridad' => 'warning',
            'general'    => 'secondary',
            default      => 'secondary',
        };
    }

    public function getAlcanceLabelAttribute(): string
    {
        return match($this->alcance) {
            'nacional'      => 'Nacional',
            'regional'      => 'Regional',
            'institucional' => 'Institucional',
            default         => ucfirst($this->alcance ?? ''),
        };
    }

    public function getTieneArchivoAttribute(): bool
    {
        return !empty($this->archivo_path);
    }

    public function getTieneLinkAttribute(): bool
    {
        return !empty($this->link_externo);
    }

    public function getTieneTutorialAttribute(): bool
    {
        return !empty($this->tutorial_url);
    }

    public function getEstaVigenteAttribute(): bool
    {
        if (!$this->vigente) return false;
        if ($this->fecha_vigencia && $this->fecha_vigencia->isPast()) return false;
        return true;
    }

    public function getYoutubeEmbedAttribute(): ?string
    {
        if (!$this->tutorial_url) return null;
        // Extraer ID de YouTube para embed
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->tutorial_url, $matches);
        if (isset($matches[1])) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
        return null;
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeVigentes($q)
    {
        return $q->where('vigente', true)
                 ->where(fn($q) => $q->whereNull('fecha_vigencia')->orWhere('fecha_vigencia', '>=', now()));
    }

    public function scopePorModulo($q, string $modulo)
    {
        return $q->where('modulo', $modulo);
    }

    public function scopePorTipo($q, string $tipo)
    {
        return $q->where('tipo', $tipo);
    }
}
