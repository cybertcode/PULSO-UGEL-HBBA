@php
    $configData = Helper::appClasses();
    $breadcrumbs = [['link' => route('dashboard'), 'name' => 'Inicio'], ['name' => 'Slider del Landing']];

@endphp

@extends('layouts/contentNavbarLayout')
@section('title', 'Slider del Landing')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">
                <i class="icon-base ti tabler-slideshow me-2 text-primary"></i>
                Slider del Landing Público
            </h4>
            <p class="text-muted mb-0 small">
                Administra los slides que aparecen en la pantalla principal. Los cambios se reflejan de inmediato.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('landing') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="icon-base ti tabler-external-link me-1"></i>Ver Landing
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">
                <i class="icon-base ti tabler-plus me-1"></i> Nuevo Slide
            </button>
        </div>
    </div>

    {{-- ── TABLA DE SLIDES ── --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:50px;" class="text-center">#</th>
                            <th style="width:80px;">Imagen</th>
                            <th>Título</th>
                            <th style="width:110px;">Tipo</th>
                            <th style="width:80px;" class="text-center">Orden</th>
                            <th style="width:90px;" class="text-center">Estado</th>
                            <th style="width:110px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($slides as $slide)
                            <tr>
                                <td class="text-center text-muted small">{{ $slide->id }}</td>
                                <td>
                                    @if ($slide->imagen_url)
                                        <img src="{{ $slide->imagen_url }}" alt="" class="rounded"
                                            style="width:64px;height:40px;object-fit:cover;">
                                    @else
                                        <div class="rounded d-flex align-items-center justify-content-center"
                                            style="width:64px;height:40px;background:{{ $slide->color_gradiente ?? 'linear-gradient(135deg,#1a1a4e,#7367f0)' }};">
                                            <i class="icon-base ti tabler-photo"
                                                style="color:rgba(255,255,255,.4);font-size:.9rem;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ Str::limit($slide->titulo, 55) }}</div>
                                    @if ($slide->etiqueta)
                                        <small class="text-muted">{{ $slide->etiqueta }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="badge
                @if ($slide->tipo === 'evento') bg-label-success
                @elseif($slide->tipo === 'normativa') bg-label-warning
                @else bg-label-primary @endif">
                                        {{ ucfirst($slide->tipo) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-secondary">{{ $slide->orden }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-flex justify-content-center mb-0">
                                        <input class="form-check-input toggle-activo" type="checkbox"
                                            data-id="{{ $slide->id }}" {{ $slide->activo ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-icon btn-sm btn-label-primary me-1 btn-editar"
                                        data-id="{{ $slide->id }}" data-tipo="{{ $slide->tipo }}"
                                        data-titulo="{{ e($slide->titulo) }}"
                                        data-descripcion="{{ e($slide->descripcion) }}"
                                        data-etiqueta="{{ e($slide->etiqueta) }}"
                                        data-color="{{ e($slide->color_gradiente) }}"
                                        data-imagen="{{ $slide->imagen_url }}" data-url="{{ $slide->url_accion }}"
                                        data-texto="{{ e($slide->texto_accion) }}" data-orden="{{ $slide->orden }}"
                                        data-activo="{{ $slide->activo ? '1' : '0' }}" title="Editar">
                                        <i class="icon-base ti tabler-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('slider-landing.destroy', $slide) }}"
                                        class="d-inline form-eliminar">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-sm btn-label-danger"
                                            title="Eliminar">
                                            <i class="icon-base ti tabler-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="icon-base ti tabler-photo-off d-block mb-2"
                                        style="font-size:2rem;opacity:.4;"></i>
                                    No hay slides configurados. Crea el primero con el botón <strong>Nuevo Slide</strong>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p class="mt-2 small text-muted">
        <i class="icon-base ti tabler-info-circle me-1"></i>
        Los slides se muestran en orden ascendente. Solo los activos son visibles en el landing.
    </p>


    {{-- ════════════ MODAL CREAR ════════════ --}}
    <div class="modal fade" id="modalCrear" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <form method="POST" action="{{ route('slider-landing.store') }}" enctype="multipart/form-data"
                class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="icon-base ti tabler-plus me-2 text-primary"></i>Nuevo Slide
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('content.slider-landing._form', ['slide' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar Slide
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ════════════ MODAL EDITAR ════════════ --}}
    {{-- El modal de edición se construye dinámicamente para cada slide --}}
    @foreach ($slides as $slide)
        <div class="modal fade" id="modalEditar{{ $slide->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <form method="POST" action="{{ route('slider-landing.update', $slide) }}" enctype="multipart/form-data"
                    class="modal-content">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="icon-base ti tabler-pencil me-2 text-warning"></i>Editar Slide
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @include('content.slider-landing._form', ['slide' => $slide])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning text-white">
                            <i class="icon-base ti tabler-device-floppy me-1"></i> Actualizar Slide
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Botón editar → abrir el modal correspondiente al slide
            document.querySelectorAll('.btn-editar').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const modal = document.getElementById('modalEditar' + id);
                    if (modal) new bootstrap.Modal(modal).show();
                });
            });

            // Confirmar eliminación
            document.querySelectorAll('.form-eliminar').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm(
                            '¿Eliminar este slide del landing? Esta acción no se puede deshacer.'
                        )) {
                        this.submit();
                    }
                });
            });

            // Toggle activo via AJAX
            document.querySelectorAll('.toggle-activo').forEach(chk => {
                chk.addEventListener('change', function() {
                    const checkbox = this;
                    fetch(`/slider-landing/${this.dataset.id}/toggle`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')
                                .content,
                            'Accept': 'application/json'
                        }
                    }).catch(() => {
                        checkbox.checked = !checkbox.checked; // revertir si falla
                    });
                });
            });

        });
    </script>
@endsection
