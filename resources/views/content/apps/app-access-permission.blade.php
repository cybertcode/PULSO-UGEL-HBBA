@php
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Permisos - PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('content')

<div class="mb-6">
  <h4 class="mb-1">Permisos del Sistema</h4>
  <p class="mb-0 text-muted">
    Los permisos se asignan a <a href="{{ route('adm-roles') }}" class="text-primary">Roles</a>.
    Los usuarios heredan automáticamente los permisos del rol asignado.
  </p>
</div>

<div class="card">
  <div class="card-datatable table-responsive">
    <table class="datatables-permisos table border-top">
      <thead>
        <tr>
          <th>Permiso</th>
          <th>Módulo</th>
          <th>Roles que lo usan</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
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
        @foreach($listaPermisos as $permiso)
        <tr>
          <td>
            <div class="d-flex align-items-center gap-3">
              <div class="avatar avatar-sm">
                <span class="avatar-initial rounded bg-label-{{ $color }}">
                  <i class="icon-base ti {{ $icon }}"></i>
                </span>
              </div>
              <span class="fw-medium text-heading">{{ $permiso->name }}</span>
            </div>
          </td>
          <td>
            <span class="badge bg-label-{{ $color }} text-capitalize">{{ $modulo }}</span>
          </td>
          <td>
            <div class="d-flex flex-wrap gap-1">
              @forelse($permiso->roles as $rol)
              <span class="badge bg-label-secondary">{{ $rol->name }}</span>
              @empty
              <span class="text-muted small">Sin asignar</span>
              @endforelse
            </div>
          </td>
          <td>
            @if($permiso->roles_count > 0)
            <span class="badge bg-label-success">
              <i class="icon-base ti tabler-check icon-14px me-1"></i>En uso
            </span>
            @else
            <span class="badge bg-label-secondary">Sin asignar</span>
            @endif
          </td>
        </tr>
        @endforeach
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="alert alert-primary d-flex align-items-center gap-3 mt-6" role="alert">
  <i class="icon-base ti tabler-info-circle icon-22px flex-shrink-0"></i>
  <div>
    <strong>¿Cómo funciona el sistema de permisos?</strong>
    Los permisos no se asignan directamente a usuarios — se agrupan en
    <a href="{{ route('adm-roles') }}" class="alert-link">Roles</a>.
    Cada usuario tiene un rol y hereda automáticamente todos sus permisos.
    El rol <strong>Super Admin</strong> tiene acceso total sin necesitar permisos explícitos.
  </div>
</div>

@endsection

@section('page-script')
<script>
'use strict';
document.addEventListener('DOMContentLoaded', function () {
  const table = document.querySelector('.datatables-permisos');
  if (!table) return;

  new DataTable(table, {
    responsive: true,
    order: [[0, 'asc']],
    pageLength: 10,
    layout: {
      topStart: {
        rowClass: 'row m-3 my-0 justify-content-between',
        features: [{ pageLength: { menu: [10, 25, 50], text: '_MENU_' } }]
      },
      topEnd: {
        features: [{ search: { placeholder: 'Buscar permiso...', text: '_INPUT_' } }]
      },
      bottomStart: { rowClass: 'row mx-3 justify-content-between', features: ['info'] },
      bottomEnd: 'paging'
    },
    language: {
      paginate: {
        next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>'
      }
    }
  });

  setTimeout(() => {
    [
      { sel: '.dt-search .form-control', rm: 'form-control-sm' },
      { sel: '.dt-length .form-select',  rm: 'form-select-sm' },
      { sel: '.dt-length',               add: 'mb-md-6 mb-0' },
      { sel: '.dt-layout-table',         rm: 'row mt-2' },
      { sel: '.dt-layout-full',          rm: 'col-md col-12', add: 'table-responsive' },
    ].forEach(({ sel, rm, add }) => {
      document.querySelectorAll(sel).forEach(el => {
        rm  && rm.split(' ').forEach(c => el.classList.remove(c));
        add && add.split(' ').forEach(c => el.classList.add(c));
      });
    });
  }, 100);
});
</script>
@endsection
