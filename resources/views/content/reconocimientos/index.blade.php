@php
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Reconocimientos - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
       'resources/assets/vendor/libs/select2/select2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
       'resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item active">Reconocimientos</li>
  </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <h4 class="mb-1"><i class="ti tabler-trophy me-2 text-warning"></i>Reconocimientos</h4>
    <p class="mb-0 text-muted">Celebramos el compromiso de quienes impulsan la integridad institucional en la UGEL Huacaybamba.</p>
  </div>
  <div class="d-flex gap-2 align-items-center flex-wrap">
    {{-- Filtros de período --}}
    <form method="GET" class="d-flex gap-2 align-items-end flex-wrap">
      <select name="anio" class="form-select form-select-sm" style="width:100px" onchange="this.form.submit()">
        @foreach($anios as $a)
        <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
        @endforeach
      </select>
      <select name="mes" class="form-select form-select-sm" style="width:130px" onchange="this.form.submit()">
        <option value="">Año completo</option>
        @foreach($meses as $m => $nm)
        <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>{{ $nm }}</option>
        @endforeach
      </select>
      <select name="categoria" class="form-select form-select-sm" style="width:170px" onchange="this.form.submit()">
        <option value="">Todas las categorías</option>
        @foreach($categorias as $cat)
        <option value="{{ $cat }}" {{ $categoria == $cat ? 'selected' : '' }}>{{ $cat }}</option>
        @endforeach
      </select>
    </form>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoReconocimiento">
      <i class="ti tabler-plus me-1"></i>Nuevo Reconocimiento
    </button>
    <a href="{{ route('rep-reportes', ['tipo'=>'reconocimientos','anio'=>$anio]) }}" class="btn btn-label-secondary btn-sm">
      <i class="ti tabler-download me-1"></i>Exportar
    </a>
  </div>
</div>

{{-- Stats --}}
<div class="row g-4 mb-6">
  <div class="col-6 col-md-3">
    <div class="card h-100">
      <div class="card-body text-center">
        <div class="badge rounded bg-label-warning p-3 mb-2 d-inline-flex">
          <i class="ti tabler-user-star icon-28px"></i>
        </div>
        <h3 class="text-warning mb-1">{{ $stats['total_reconocidos'] }}</h3>
        <p class="mb-0 fw-medium small">Reconocimientos Entregados</p>
        <small class="text-muted">Año {{ $anio }}</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card h-100">
      <div class="card-body text-center">
        <div class="badge rounded bg-label-primary p-3 mb-2 d-inline-flex">
          <i class="ti tabler-building-community icon-28px"></i>
        </div>
        <h3 class="text-primary mb-1">{{ $stats['unidades_destacadas'] }}</h3>
        <p class="mb-0 fw-medium small">Unidades Destacadas</p>
        <small class="text-muted">Con reconocimiento</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card h-100">
      <div class="card-body text-center">
        <div class="badge rounded bg-label-success p-3 mb-2 d-inline-flex">
          <i class="ti tabler-chart-line icon-28px"></i>
        </div>
        <h3 class="text-success mb-1">{{ $stats['promedio_puntaje'] }}%</h3>
        <p class="mb-0 fw-medium small">Promedio de Cumplimiento</p>
        <small class="text-muted">General institucional</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card h-100">
      <div class="card-body text-center">
        <div class="badge rounded bg-label-info p-3 mb-2 d-inline-flex">
          <i class="ti tabler-calendar-event icon-28px"></i>
        </div>
        <h3 class="text-info mb-1" style="font-size:1.4rem">{{ $stats['proxima_ceremonia'] }}</h3>
        <p class="mb-0 fw-medium small">Próxima Ceremonia</p>
        <small class="text-muted">Reconocimiento a la Gestión Íntegra</small>
      </div>
    </div>
  </div>
</div>

{{-- Podio de reconocidos --}}
@if($top3->isNotEmpty())
<div class="card mb-6">
  <div class="card-header">
    <h5 class="mb-1"><i class="ti tabler-award me-2 text-warning"></i>Reconocimientos Destacados del Período</h5>
    <p class="card-subtitle">{{ $mes ? ($meses[$mes] ?? '') . ' ' . $anio : 'Año ' . $anio }}</p>
  </div>
  <div class="card-body">
    <div class="row g-4 justify-content-center">
      @foreach($top3 as $idx => $t)
      @php
        $medallas = ['🥇 Primer Lugar', '🥈 Segundo Lugar', '🥉 Tercer Lugar'];
        $medBg    = ['warning', 'secondary', 'danger'];
        $med      = $medallas[$idx] ?? ('N°' . ($idx+1));
        $bg       = $medBg[$idx] ?? 'primary';
      @endphp
      <div class="col-md-4">
        <div class="card h-100 border-{{ $bg }} border-2">
          <div class="card-body text-center p-4">
            <div class="badge bg-label-{{ $bg }} rounded-pill mb-3 px-3 py-2">{{ $med }}</div>
            <div class="avatar avatar-xl mb-3 mx-auto">
              <img src="{{ $t->foto_url }}" alt="{{ $t->nombre }}" class="rounded-circle" style="width:80px;height:80px;object-fit:cover">
            </div>
            <h5 class="mb-1">{{ $t->nombre }}</h5>
            <p class="text-muted mb-1 small">{{ $t->cargo }}</p>
            <span class="badge bg-label-secondary mb-3">{{ $t->unidadOrganica->sigla ?? $t->unidadOrganica->nombre ?? '—' }}</span>
            @if($t->numero_resolucion)
            <div class="mb-3">
              <span class="badge bg-label-info"><i class="ti tabler-file-certificate me-1"></i>{{ $t->numero_resolucion }}</span>
            </div>
            @endif
            <div class="row g-2 mb-3">
              <div class="col-6">
                <div class="p-2 bg-body-secondary rounded text-center">
                  <div class="fw-bold text-success">{{ $t->puntaje_cumplimiento }}%</div>
                  <small class="text-muted">Cumplimiento</small>
                </div>
              </div>
              <div class="col-6">
                <div class="p-2 bg-body-secondary rounded text-center">
                  <div class="fw-bold text-primary">{{ $t->puntaje_puntualidad }}%</div>
                  <small class="text-muted">Puntualidad</small>
                </div>
              </div>
              <div class="col-6">
                <div class="p-2 bg-body-secondary rounded text-center">
                  <div class="fw-bold text-warning">{{ $t->puntaje_participacion }}%</div>
                  <small class="text-muted">Participación</small>
                </div>
              </div>
              <div class="col-6">
                <div class="p-2 bg-body-secondary rounded text-center">
                  <div class="fw-bold text-info">{{ $t->puntaje_responsabilidad }}%</div>
                  <small class="text-muted">Responsabilidad</small>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-center align-items-center gap-2">
              <span class="display-6 fw-bold text-{{ $t->nivel_color }}">{{ number_format($t->puntaje_total, 1) }}%</span>
              <span class="badge bg-label-{{ $t->nivel_color }}">{{ $t->nivel }}</span>
            </div>
            @if($t->motivo)
            <p class="text-muted mt-3 mb-0 small fst-italic">{{ $t->motivo }}</p>
            @endif
          </div>
          <div class="card-footer d-flex justify-content-end gap-1">
            <a href="{{ route('rep-reconocimientos.show', $t) }}" class="btn btn-sm btn-label-primary">
              <i class="ti tabler-eye me-1"></i>Ver detalle
            </a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endif

{{-- Tabla general de reconocidos --}}
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Reconocimientos Recientes</h5>
    <span class="text-muted small">{{ $trabajadores->count() }} reconocidos en el período</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Servidor/a</th>
            <th>Unidad</th>
            <th>Categoría</th>
            <th>Cumplimiento</th>
            <th>Puntualidad</th>
            <th>Participación</th>
            <th>Responsabilidad</th>
            <th>Puntaje Total</th>
            <th>Nivel</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($trabajadores as $idx => $t)
          <tr>
            <td><strong class="text-muted">{{ $idx + 1 }}</strong></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm">
                  <img src="{{ $t->foto_url }}" class="rounded-circle" alt="{{ $t->nombre }}" style="width:36px;height:36px;object-fit:cover">
                </div>
                <div>
                  <div class="fw-medium" style="font-size:13px">{{ $t->nombre }}</div>
                  <small class="text-muted">{{ $t->cargo }}</small>
                </div>
              </div>
            </td>
            <td><span class="badge bg-label-secondary">{{ $t->unidadOrganica->sigla ?? '—' }}</span></td>
            <td><small>{{ $t->categoria ?? '—' }}</small></td>
            <td>
              <div class="d-flex align-items-center gap-1">
                <div class="progress flex-grow-1" style="height:5px;min-width:50px">
                  <div class="progress-bar bg-success" style="width:{{ $t->puntaje_cumplimiento }}%"></div>
                </div>
                <small class="fw-bold">{{ $t->puntaje_cumplimiento }}%</small>
              </div>
            </td>
            <td><small class="fw-bold text-primary">{{ $t->puntaje_puntualidad }}%</small></td>
            <td><small class="fw-bold text-warning">{{ $t->puntaje_participacion }}%</small></td>
            <td><small class="fw-bold text-info">{{ $t->puntaje_responsabilidad }}%</small></td>
            <td>
              <span class="badge bg-label-{{ $t->nivel_color }} fs-6">{{ number_format($t->puntaje_total, 1) }}%</span>
            </td>
            <td><span class="badge bg-{{ $t->nivel_color }}">{{ $t->nivel }}</span></td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('rep-reconocimientos.show', $t) }}" class="btn btn-icon btn-sm btn-label-info" title="Ver detalle">
                  <i class="ti tabler-eye"></i>
                </a>
                <button class="btn btn-icon btn-sm btn-label-primary btn-editar-reconocimiento"
                  data-id="{{ $t->id }}"
                  data-nombre="{{ $t->nombre }}"
                  data-cargo="{{ $t->cargo }}"
                  data-unidad="{{ $t->unidad_organica_id }}"
                  data-dni="{{ $t->dni }}"
                  data-correo="{{ $t->correo }}"
                  data-cumplimiento="{{ $t->puntaje_cumplimiento }}"
                  data-puntualidad="{{ $t->puntaje_puntualidad }}"
                  data-participacion="{{ $t->puntaje_participacion }}"
                  data-responsabilidad="{{ $t->puntaje_responsabilidad }}"
                  data-categoria="{{ $t->categoria }}"
                  data-motivo="{{ $t->motivo }}"
                  data-resolucion="{{ $t->numero_resolucion }}"
                  title="Editar">
                  <i class="ti tabler-edit"></i>
                </button>
                <form method="POST" action="{{ route('rep-reconocimientos.destroy', $t) }}" class="form-eliminar-rec d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-icon btn-sm btn-label-danger" title="Eliminar">
                    <i class="ti tabler-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="11" class="text-center text-muted py-5">
            <i class="ti tabler-trophy-off icon-32px d-block mb-2"></i>
            No hay reconocimientos para el período seleccionado.
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Modal Nuevo Reconocimiento --}}
<div class="modal fade" id="modalNuevoReconocimiento" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="{{ route('rep-reconocimientos.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-trophy me-2 text-warning"></i>Propuesta de Reconocimiento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" required placeholder="Nombres y apellidos">
            </div>
            <div class="col-md-4">
              <label class="form-label">DNI</label>
              <input type="text" name="dni" class="form-control" maxlength="8" placeholder="12345678">
            </div>
            <div class="col-md-6">
              <label class="form-label">Cargo</label>
              <input type="text" name="cargo" class="form-control" placeholder="Especialista en...">
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo institucional</label>
              <input type="email" name="correo" class="form-control" placeholder="servidor@ugel.gob.pe">
            </div>
            <div class="col-md-6">
              <label class="form-label">Unidad Orgánica</label>
              <select name="unidad_organica_id" class="form-select select2-rec">
                <option value="">Seleccionar unidad</option>
                @foreach(\App\Models\UnidadOrganica::where('activo',true)->orderBy('nombre')->get() as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Año <span class="text-danger">*</span></label>
              <select name="anio" class="form-select">
                @foreach($anios as $a)
                <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Mes</label>
              <select name="mes" class="form-select">
                <option value="">Anual</option>
                @foreach($meses as $m => $nm)
                <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>{{ $nm }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Categoría</label>
              <select name="categoria" class="form-select">
                <option value="">Sin categoría</option>
                @foreach($categorias as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">N° Resolución Directoral</label>
              <input type="text" name="numero_resolucion" class="form-control" placeholder="RD N° 1457-2025">
            </div>
            <hr class="my-1">
            <div class="col-12"><h6 class="text-muted mb-0"><i class="ti tabler-chart-bar me-1"></i>Indicadores de Evaluación (0-100)</h6></div>
            <div class="col-md-3">
              <label class="form-label">Cumplimiento <span class="text-danger">*</span></label>
              <input type="number" name="puntaje_cumplimiento" class="form-control" min="0" max="100" value="0" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Puntualidad <span class="text-danger">*</span></label>
              <input type="number" name="puntaje_puntualidad" class="form-control" min="0" max="100" value="0" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Participación <span class="text-danger">*</span></label>
              <input type="number" name="puntaje_participacion" class="form-control" min="0" max="100" value="0" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Responsabilidad <span class="text-danger">*</span></label>
              <input type="number" name="puntaje_responsabilidad" class="form-control" min="0" max="100" value="0" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Foto del servidor/a</label>
              <input type="file" name="foto" class="form-control" accept="image/*">
              <div class="form-text">Imagen JPG/PNG. Máx. 2MB.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Resolución Directoral (PDF)</label>
              <input type="file" name="resolucion_archivo" class="form-control" accept=".pdf">
              <div class="form-text">PDF. Máx. 5MB.</div>
            </div>
            <div class="col-12">
              <label class="form-label">Motivo / Justificación</label>
              <textarea name="motivo" class="form-control" rows="3" placeholder="Descripción de los logros y contribuciones..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning"><i class="ti tabler-trophy me-1"></i>Registrar Reconocimiento</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Editar Reconocimiento --}}
<div class="modal fade" id="modalEditarReconocimiento" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" id="formEditarRec" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Reconocimiento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
              <input type="text" name="nombre" id="rec_nombre" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">DNI</label>
              <input type="text" name="dni" id="rec_dni" class="form-control" maxlength="8">
            </div>
            <div class="col-md-6">
              <label class="form-label">Cargo</label>
              <input type="text" name="cargo" id="rec_cargo" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo</label>
              <input type="email" name="correo" id="rec_correo" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Categoría</label>
              <select name="categoria" id="rec_categoria" class="form-select">
                <option value="">Sin categoría</option>
                @foreach($categorias as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">N° Resolución</label>
              <input type="text" name="numero_resolucion" id="rec_resolucion" class="form-control">
            </div>
            <hr class="my-1">
            <div class="col-md-3">
              <label class="form-label">Cumplimiento</label>
              <input type="number" name="puntaje_cumplimiento" id="rec_cumplimiento" class="form-control" min="0" max="100">
            </div>
            <div class="col-md-3">
              <label class="form-label">Puntualidad</label>
              <input type="number" name="puntaje_puntualidad" id="rec_puntualidad" class="form-control" min="0" max="100">
            </div>
            <div class="col-md-3">
              <label class="form-label">Participación</label>
              <input type="number" name="puntaje_participacion" id="rec_participacion" class="form-control" min="0" max="100">
            </div>
            <div class="col-md-3">
              <label class="form-label">Responsabilidad</label>
              <input type="number" name="puntaje_responsabilidad" id="rec_responsabilidad" class="form-control" min="0" max="100">
            </div>
            <div class="col-12">
              <label class="form-label">Nueva foto (opcional)</label>
              <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label">Motivo</label>
              <textarea name="motivo" id="rec_motivo" class="form-control" rows="3"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.select2-rec').forEach(el =>
    $(el).select2({ dropdownParent: el.closest('.modal'), width: '100%' })
  );

  document.querySelectorAll('.btn-editar-reconocimiento').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = document.getElementById('formEditarRec');
      form.action = '/reconocimientos/' + this.dataset.id;
      document.getElementById('rec_nombre').value          = this.dataset.nombre;
      document.getElementById('rec_cargo').value           = this.dataset.cargo || '';
      document.getElementById('rec_dni').value             = this.dataset.dni || '';
      document.getElementById('rec_correo').value          = this.dataset.correo || '';
      document.getElementById('rec_cumplimiento').value    = this.dataset.cumplimiento;
      document.getElementById('rec_puntualidad').value     = this.dataset.puntualidad;
      document.getElementById('rec_participacion').value   = this.dataset.participacion;
      document.getElementById('rec_responsabilidad').value = this.dataset.responsabilidad;
      document.getElementById('rec_motivo').value          = this.dataset.motivo || '';
      document.getElementById('rec_resolucion').value      = this.dataset.resolucion || '';
      const catEl = document.getElementById('rec_categoria');
      if (catEl) { catEl.value = this.dataset.categoria || ''; }
      new bootstrap.Modal(document.getElementById('modalEditarReconocimiento')).show();
    });
  });

  document.querySelectorAll('.form-eliminar-rec').forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      Swal.fire({
        title: '¿Eliminar reconocimiento?', icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ea5455'
      }).then(r => { if (r.isConfirmed) form.submit(); });
    });
  });
});
</script>
@endsection
