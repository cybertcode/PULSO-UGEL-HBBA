@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Matriz de Riesgos — PULSO UGEL')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-style1">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
      <li class="breadcrumb-item active">Matriz de Riesgos</li>
    </ol>
  </nav>

  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
      <h4 class="mb-1">Matriz de Riesgos</h4>
      <p class="text-muted mb-0">Identificación y tratamiento de riesgos por componente COSO — {{ $anio_actual }}</p>
    </div>
    @can('riesgos.crear')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoRiesgo">
      <i class="ti tabler-plus me-1"></i> Nuevo Riesgo
    </button>
    @endcan
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible mb-4"><{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger alert-dismissible mb-4">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif

  {{-- Estadísticas --}}
  <div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2 bg-label-primary rounded-circle"><i class="ti tabler-shield-exclamation fs-4"></i></div>
          <h3 class="mb-0">{{ $stats['total'] }}</h3>
          <small class="text-muted">Total Riesgos</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2 bg-label-danger rounded-circle"><i class="ti tabler-alert-triangle fs-4"></i></div>
          <h3 class="mb-0 text-danger">{{ $stats['criticos'] }}</h3>
          <small class="text-muted">Críticos</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2 bg-label-warning rounded-circle"><i class="ti tabler-alert-circle fs-4"></i></div>
          <h3 class="mb-0 text-warning">{{ $stats['altos'] }}</h3>
          <small class="text-muted">Altos</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2 bg-label-info rounded-circle"><i class="ti tabler-activity fs-4"></i></div>
          <h3 class="mb-0">{{ $stats['activos'] }}</h3>
          <small class="text-muted">Activos</small>
        </div>
      </div>
    </div>
  </div>

  {{-- Mapa de calor de riesgos (5x5) --}}
  <div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">Mapa de Riesgos (Probabilidad × Impacto)</h5></div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered text-center mb-0" style="min-width:400px">
          <thead>
            <tr>
              <th class="bg-light" style="width:120px">Prob. / Impacto</th>
              @for($i=1;$i<=5;$i++)
              <th class="bg-light">{{ $i }}</th>
              @endfor
            </tr>
          </thead>
          <tbody>
            @for($p=5;$p>=1;$p--)
            <tr>
              <th class="bg-light">{{ $p }}</th>
              @for($imp=1;$imp<=5;$imp++)
              @php
                $nivel = $p * $imp;
                $cls   = $nivel >= 15 ? 'danger' : ($nivel >= 8 ? 'warning' : ($nivel >= 4 ? 'info' : 'success'));
                $count = $riesgos->getCollection()->where('probabilidad',$p)->where('impacto',$imp)->count();
              @endphp
              <td class="bg-{{ $cls }} bg-opacity-25 fw-semibold">
                {{ $nivel }}
                @if($count > 0)<br><span class="badge bg-{{ $cls }}">{{ $count }}</span>@endif
              </td>
              @endfor
            </tr>
            @endfor
          </tbody>
        </table>
      </div>
      <div class="d-flex gap-3 mt-2 flex-wrap">
        <span class="badge bg-success bg-opacity-75">Bajo (1–3)</span>
        <span class="badge bg-info bg-opacity-75">Moderado (4–7)</span>
        <span class="badge bg-warning bg-opacity-75">Alto (8–14)</span>
        <span class="badge bg-danger bg-opacity-75">Crítico (15–25)</span>
      </div>
    </div>
  </div>

  {{-- Tabla --}}
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h5 class="mb-0">Registro de Riesgos</h5>
      <form method="GET" class="d-flex gap-2 flex-wrap">
        <select name="clasificacion" class="form-select form-select-sm" style="width:140px" onchange="this.form.submit()">
          <option value="">Clasificación</option>
          <option value="critico" {{ request('clasificacion')=='critico' ? 'selected':'' }}>Crítico</option>
          <option value="alto"    {{ request('clasificacion')=='alto' ? 'selected':'' }}>Alto</option>
          <option value="moderado"{{ request('clasificacion')=='moderado' ? 'selected':'' }}>Moderado</option>
          <option value="bajo"    {{ request('clasificacion')=='bajo' ? 'selected':'' }}>Bajo</option>
        </select>
        <select name="estado" class="form-select form-select-sm" style="width:130px" onchange="this.form.submit()">
          <option value="">Estado</option>
          <option value="activo"   {{ request('estado')=='activo' ? 'selected':'' }}>Activo</option>
          <option value="mitigado" {{ request('estado')=='mitigado' ? 'selected':'' }}>Mitigado</option>
          <option value="aceptado" {{ request('estado')=='aceptado' ? 'selected':'' }}>Aceptado</option>
          <option value="cerrado"  {{ request('estado')=='cerrado' ? 'selected':'' }}>Cerrado</option>
        </select>
        <select name="componente" class="form-select form-select-sm" style="width:180px" onchange="this.form.submit()">
          <option value="">Componente</option>
          @foreach($componentes as $c)
          <option value="{{ $c->id }}" {{ request('componente')==$c->id ? 'selected':'' }}>{{ $c->nombre }}</option>
          @endforeach
        </select>
      </form>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Código</th>
            <th>Riesgo</th>
            <th>Tipo</th>
            <th>Componente / Unidad</th>
            <th>P×I</th>
            <th>Nivel</th>
            <th>Tratamiento</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($riesgos as $r)
          <tr>
            <td><span class="badge bg-label-secondary">{{ $r->codigo ?? 'R-'.$r->id }}</span></td>
            <td>
              <div class="fw-semibold">{{ $r->nombre }}</div>
              @if($r->descripcion)
              <small class="text-muted">{{ Str::limit($r->descripcion,60) }}</small>
              @endif
            </td>
            <td><span class="badge bg-label-primary">{{ ucfirst($r->tipo) }}</span></td>
            <td>
              <div>{{ $r->componente?->nombre ?? '—' }}</div>
              @if($r->unidadOrganica)
              <small class="text-muted">{{ $r->unidadOrganica->nombre }}</small>
              @endif
            </td>
            <td class="text-center fw-bold">{{ $r->probabilidad }} × {{ $r->impacto }} = <span class="text-{{ $r->color_clasificacion }}">{{ $r->probabilidad * $r->impacto }}</span></td>
            <td><span class="badge bg-{{ $r->color_clasificacion }}">{{ ucfirst($r->clasificacion) }}</span></td>
            <td><span class="badge bg-label-info">{{ ucfirst($r->tipo_tratamiento) }}</span></td>
            <td>
              @php $sc = match($r->estado){ 'activo'=>'warning','mitigado'=>'info','aceptado'=>'secondary','cerrado'=>'success',default=>'secondary' }; @endphp
              <span class="badge bg-{{ $sc }}">{{ ucfirst($r->estado) }}</span>
            </td>
            <td>
              <div class="d-flex gap-1">
                @can('riesgos.editar')
                <button class="btn btn-sm btn-icon btn-outline-primary btn-editar-riesgo"
                  data-riesgo="{{ json_encode($r->only(['id','nombre','codigo','descripcion','componente_id','unidad_organica_id','tipo','probabilidad','impacto','controles_existentes','acciones_tratamiento','tipo_tratamiento','responsable_id','estado','observaciones','anio'])) }}"
                  title="Editar"><i class="ti tabler-edit"></i></button>
                @endcan
                @can('riesgos.eliminar')
                <form method="POST" action="{{ route('matriz-riesgos.destroy', $r) }}" class="form-eliminar d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Eliminar"><i class="ti tabler-trash"></i></button>
                </form>
                @endcan
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="9" class="text-center py-4 text-muted">No hay riesgos registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $riesgos->links() }}</div>
  </div>
</div>

{{-- Modal Nuevo Riesgo --}}
@can('riesgos.crear')
<div class="modal fade" id="modalNuevoRiesgo" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <form method="POST" action="{{ route('matriz-riesgos.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Registrar Nuevo Riesgo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-2">
              <label class="form-label">Código</label>
              <input type="text" name="codigo" class="form-control" placeholder="R-001">
            </div>
            <div class="col-md-7">
              <label class="form-label">Nombre del Riesgo <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Año</label>
              <input type="number" name="anio" class="form-control" value="{{ $anio_actual }}" min="2020" max="2050">
            </div>
            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label">Tipo <span class="text-danger">*</span></label>
              <select name="tipo" class="form-select" required>
                <option value="estrategico">Estratégico</option>
                <option value="operativo" selected>Operativo</option>
                <option value="cumplimiento">Cumplimiento</option>
                <option value="reporte">Reporte</option>
                <option value="tecnologico">Tecnológico</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Componente SCI</label>
              <select name="componente_id" class="form-select select2">
                <option value="">— Sin componente —</option>
                @foreach($componentes as $c)
                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Unidad Orgánica</label>
              <select name="unidad_organica_id" class="form-select select2">
                <option value="">— Sin unidad —</option>
                @foreach($unidades as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Probabilidad (1-5) <span class="text-danger">*</span></label>
              <input type="number" name="probabilidad" class="form-control" min="1" max="5" value="1" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Impacto (1-5) <span class="text-danger">*</span></label>
              <input type="number" name="impacto" class="form-control" min="1" max="5" value="1" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Tratamiento <span class="text-danger">*</span></label>
              <select name="tipo_tratamiento" class="form-select" required>
                <option value="mitigar">Mitigar</option>
                <option value="aceptar">Aceptar</option>
                <option value="transferir">Transferir</option>
                <option value="evitar">Evitar</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Estado <span class="text-danger">*</span></label>
              <select name="estado" class="form-select" required>
                <option value="activo" selected>Activo</option>
                <option value="mitigado">Mitigado</option>
                <option value="aceptado">Aceptado</option>
                <option value="cerrado">Cerrado</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Controles existentes</label>
              <textarea name="controles_existentes" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Acciones de tratamiento</label>
              <textarea name="acciones_tratamiento" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Responsable</label>
              <select name="responsable_id" class="form-select select2">
                <option value="">— Sin asignar —</option>
                @foreach($usuarios as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de Revisión</label>
              <input type="date" name="fecha_revision" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Registrar Riesgo</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endcan
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection
@section('page-script')
<script>
  document.querySelectorAll('.select2').forEach(el => {
    if (window.jQuery) $(el).select2({ dropdownParent: $(el).closest('.modal') });
  });
  document.querySelectorAll('.form-eliminar').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      if (typeof Swal !== 'undefined') {
        Swal.fire({ title:'¿Eliminar riesgo?', icon:'warning', showCancelButton:true,
          confirmButtonText:'Sí', cancelButtonText:'Cancelar', confirmButtonColor:'#d33'
        }).then(r => { if(r.isConfirmed) form.submit(); });
      } else form.submit();
    });
  });
</script>
@endsection
