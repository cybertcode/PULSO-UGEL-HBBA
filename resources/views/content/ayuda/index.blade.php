@php
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Ayuda — PULSO UGEL')

@section('content')

<div class="mb-4">
  <h4 class="mb-1 fw-bold">
    <i class="ti tabler-help-circle me-2 text-info"></i>Centro de Ayuda
  </h4>
  <p class="text-muted mb-0">Guías, tutoriales y soporte para el uso del sistema PULSO UGEL.</p>
</div>

{{-- Búsqueda --}}
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%);">
  <div class="card-body text-center py-5">
    <h5 class="text-white fw-bold mb-2">¿En qué podemos ayudarte?</h5>
    <p class="text-white-50 mb-4">Busca en nuestra base de conocimientos</p>
    <div class="mx-auto" style="max-width:500px">
      <div class="input-group">
        <input type="text" class="form-control form-control-lg" placeholder="Buscar guías, tutoriales...">
        <button class="btn btn-warning"><i class="ti tabler-search"></i></button>
      </div>
    </div>
  </div>
</div>

{{-- Categorías de ayuda --}}
<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center py-4">
        <div class="avatar avatar-lg bg-label-primary rounded mb-3 mx-auto">
          <i class="ti tabler-clipboard-list fs-3"></i>
        </div>
        <h6 class="fw-bold">Control Interno</h6>
        <p class="text-muted small mb-3">Cómo registrar y hacer seguimiento de actividades del SCI.</p>
        <a href="#" class="btn btn-outline-primary btn-sm">Ver guías</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center py-4">
        <div class="avatar avatar-lg bg-label-success rounded mb-3 mx-auto">
          <i class="ti tabler-shield-check fs-3"></i>
        </div>
        <h6 class="fw-bold">Modelo de Integridad</h6>
        <p class="text-muted small mb-3">Cómo monitorear los 9 componentes del Modelo de Integridad PCM.</p>
        <a href="#" class="btn btn-outline-success btn-sm">Ver guías</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center py-4">
        <div class="avatar avatar-lg bg-label-warning rounded mb-3 mx-auto">
          <i class="ti tabler-file-upload fs-3"></i>
        </div>
        <h6 class="fw-bold">Evidencias</h6>
        <p class="text-muted small mb-3">Cómo subir, validar y gestionar evidencias del sistema.</p>
        <a href="#" class="btn btn-outline-warning btn-sm">Ver guías</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center py-4">
        <div class="avatar avatar-lg bg-label-danger rounded mb-3 mx-auto">
          <i class="ti tabler-bell fs-3"></i>
        </div>
        <h6 class="fw-bold">Alertas</h6>
        <p class="text-muted small mb-3">Cómo gestionar y responder a las alertas del sistema.</p>
        <a href="#" class="btn btn-outline-danger btn-sm">Ver guías</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center py-4">
        <div class="avatar avatar-lg bg-label-info rounded mb-3 mx-auto">
          <i class="ti tabler-users fs-3"></i>
        </div>
        <h6 class="fw-bold">Usuarios y Roles</h6>
        <p class="text-muted small mb-3">Cómo administrar usuarios, roles y permisos del sistema.</p>
        <a href="#" class="btn btn-outline-info btn-sm">Ver guías</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center py-4">
        <div class="avatar avatar-lg bg-label-secondary rounded mb-3 mx-auto">
          <i class="ti tabler-chart-bar fs-3"></i>
        </div>
        <h6 class="fw-bold">Reportes</h6>
        <p class="text-muted small mb-3">Cómo generar y exportar reportes del sistema.</p>
        <a href="#" class="btn btn-outline-secondary btn-sm">Ver guías</a>
      </div>
    </div>
  </div>
</div>

{{-- Contacto soporte --}}
<div class="card border-0 shadow-sm">
  <div class="card-body d-flex align-items-center gap-4 flex-wrap">
    <div class="avatar avatar-lg bg-label-info rounded">
      <i class="ti tabler-headset fs-3"></i>
    </div>
    <div class="flex-grow-1">
      <h6 class="fw-bold mb-1">¿No encontraste lo que buscabas?</h6>
      <p class="text-muted mb-0 small">Contacta al equipo de soporte técnico de la UGEL.</p>
    </div>
    <a href="mailto:soporte@ugel.gob.pe" class="btn btn-info text-white">
      <i class="ti tabler-mail me-1"></i> Contactar soporte
    </a>
  </div>
</div>

@endsection
