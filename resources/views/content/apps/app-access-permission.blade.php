@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Permisos - PULSO UGEL')

@section('content')

<div class="mb-4">
  <h4 class="mb-1">Permisos del Sistema</h4>
  <p class="mb-0 text-muted">Lista de todos los permisos disponibles agrupados por módulo. Los permisos se asignan a través de los <a href="{{ route('adm-roles') }}">Roles</a>.</p>
</div>

<div class="row g-4">
  @foreach($permisos as $modulo => $listaPermisos)
  @php
    $colorMap = [
      'usuarios'        => 'primary',
      'control-interno' => 'info',
      'integridad'      => 'success',
      'evidencias'      => 'warning',
      'reportes'        => 'secondary',
      'reconocimientos' => 'danger',
      'alertas'         => 'primary',
      'configuracion'   => 'dark',
    ];
    $iconMap = [
      'usuarios'        => 'tabler-users',
      'control-interno' => 'tabler-clipboard-list',
      'integridad'      => 'tabler-shield-check',
      'evidencias'      => 'tabler-file-upload',
      'reportes'        => 'tabler-chart-bar',
      'reconocimientos' => 'tabler-award',
      'alertas'         => 'tabler-bell',
      'configuracion'   => 'tabler-settings',
    ];
    $color = $colorMap[$modulo] ?? 'primary';
    $icon  = $iconMap[$modulo]  ?? 'tabler-lock';
  @endphp
  <div class="col-xl-4 col-md-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center gap-2">
        <div class="avatar avatar-sm">
          <span class="avatar-initial rounded bg-label-{{ $color }}">
            <i class="ti {{ $icon }}"></i>
          </span>
        </div>
        <h6 class="mb-0 text-capitalize">{{ $modulo }}</h6>
        <span class="badge bg-label-{{ $color }} ms-auto">{{ $listaPermisos->count() }}</span>
      </div>
      <div class="card-body">
        <div class="list-group list-group-flush">
          @foreach($listaPermisos as $permiso)
          <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
            <div>
              <div class="fw-medium small">{{ $permiso->name }}</div>
              <small class="text-muted">{{ $permiso->roles_count }} rol(es) lo usan</small>
            </div>
            <span class="badge {{ $permiso->roles_count > 0 ? 'bg-label-success' : 'bg-label-secondary' }}">
              {{ $permiso->roles_count > 0 ? 'En uso' : 'Sin asignar' }}
            </span>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>

<div class="alert alert-info mt-4">
  <i class="ti tabler-info-circle me-2"></i>
  <strong>¿Cómo funciona?</strong> Los permisos no se asignan directamente a usuarios. Se asignan a <strong>Roles</strong>, y los usuarios heredan los permisos del rol que tienen asignado. Para cambiar qué puede hacer un usuario, edita su rol o crea uno nuevo desde <a href="{{ route('adm-roles') }}" class="alert-link">Gestión de Roles</a>.
</div>

@endsection
