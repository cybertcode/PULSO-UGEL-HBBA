@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Reconocimientos - PULSO UGEL')

@section('content')

<div class="mb-4">
  <h4 class="mb-1">Reconocimientos</h4>
  <p class="mb-0 text-muted">Ranking de personal destacado en cumplimiento del Control Interno</p>
</div>

<!-- Top 3 podio -->
<div class="row g-4 mb-5 justify-content-center">
  <!-- 2do lugar -->
  <div class="col-md-3 order-md-1">
    <div class="card text-center border-secondary border-opacity-50 h-100">
      <div class="card-body pt-4">
        <div class="position-relative d-inline-block mb-3">
          <div class="avatar avatar-xl mx-auto">
            <img src="{{ asset('assets/img/avatars/2.png') }}" alt class="rounded-circle">
          </div>
          <span class="position-absolute bottom-0 end-0 badge rounded-circle bg-secondary p-2 fs-5">2</span>
        </div>
        <h6 class="mb-1">Luis Ramírez</h6>
        <small class="text-muted d-block mb-2">Área de Planificación</small>
        <h4 class="text-secondary mb-0">92 pts</h4>
      </div>
    </div>
  </div>
  <!-- 1er lugar -->
  <div class="col-md-4 order-md-2">
    <div class="card text-center border-warning border-2 h-100">
      <div class="card-body pt-4">
        <div class="position-relative d-inline-block mb-3">
          <i class="ti tabler-crown text-warning position-absolute top-0 start-50 translate-middle icon-22px"></i>
          <div class="avatar avatar-xl mx-auto mt-2">
            <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="rounded-circle">
          </div>
          <span class="position-absolute bottom-0 end-0 badge rounded-circle bg-warning p-2 fs-5">1</span>
        </div>
        <h6 class="mb-1">María García</h6>
        <small class="text-muted d-block mb-2">Recursos Humanos</small>
        <h4 class="text-warning mb-0">98 pts</h4>
      </div>
    </div>
  </div>
  <!-- 3er lugar -->
  <div class="col-md-3 order-md-3">
    <div class="card text-center border-danger border-opacity-50 h-100">
      <div class="card-body pt-4">
        <div class="position-relative d-inline-block mb-3">
          <div class="avatar avatar-xl mx-auto">
            <img src="{{ asset('assets/img/avatars/3.png') }}" alt class="rounded-circle">
          </div>
          <span class="position-absolute bottom-0 end-0 badge rounded-circle bg-danger p-2 fs-5">3</span>
        </div>
        <h6 class="mb-1">Ana Torres</h6>
        <small class="text-muted d-block mb-2">Administración</small>
        <h4 class="text-danger mb-0">87 pts</h4>
      </div>
    </div>
  </div>
</div>

<!-- Ranking completo -->
<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">Ranking General de Personal</h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th class="ps-4">#</th>
            <th>Colaborador</th>
            <th>Área</th>
            <th class="text-center">Actividades</th>
            <th class="text-center">Evidencias</th>
            <th class="text-center">Puntaje</th>
            <th class="text-center">Reconocimiento</th>
          </tr>
        </thead>
        <tbody>
          @php
          $ranking = [
            [1, 'María García', 'Recursos Humanos', 15, 14, 98, 'Excelente'],
            [2, 'Luis Ramírez', 'Planificación', 14, 13, 92, 'Muy Bueno'],
            [3, 'Ana Torres', 'Administración', 12, 11, 87, 'Muy Bueno'],
            [4, 'Carlos López', 'Dirección', 11, 9, 78, 'Bueno'],
            [5, 'Rosa Mendez', 'Logística', 10, 8, 70, 'Bueno'],
          ];
          $badges = ['Excelente' => 'success', 'Muy Bueno' => 'primary', 'Bueno' => 'info'];
          @endphp
          @foreach($ranking as [$pos, $nombre, $area, $acts, $evid, $pts, $rec])
          <tr>
            <td class="ps-4 fw-bold {{ $pos <= 3 ? 'text-warning' : '' }}">{{ $pos }}</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm">
                  <img src="{{ asset('assets/img/avatars/'.$pos.'.png') }}" alt class="rounded-circle">
                </div>
                {{ $nombre }}
              </div>
            </td>
            <td>{{ $area }}</td>
            <td class="text-center">{{ $acts }}</td>
            <td class="text-center">{{ $evid }}</td>
            <td class="text-center fw-bold">{{ $pts }}</td>
            <td class="text-center">
              <span class="badge bg-label-{{ $badges[$rec] ?? 'secondary' }}">{{ $rec }}</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
