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

{{-- Hero búsqueda --}}
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%);">
  <div class="card-body text-center py-5">
    <h5 class="text-white fw-bold mb-2">¿En qué podemos ayudarte?</h5>
    <p class="text-white-50 mb-4">Busca guías, preguntas frecuentes y tutoriales</p>
    <div class="mx-auto" style="max-width:540px">
      <div class="input-group input-group-lg shadow rounded-3 overflow-hidden">
        <span class="input-group-text bg-white border-0 ps-3"><i class="ti tabler-search" style="color:#6c757d"></i></span>
        <input type="text" id="ayudaSearch" class="form-control border-0 ps-1" placeholder="Buscar guías, tutoriales, preguntas frecuentes..." style="color:#212529; background:#fff;">
        <button class="btn btn-warning px-4 border-0" id="clearSearch" style="display:none" title="Limpiar"><i class="ti tabler-x" style="color:#212529"></i></button>
      </div>
      <div id="searchCounter" class="text-white-50 small mt-2" style="display:none"></div>
    </div>
  </div>
</div>

{{-- Mensaje sin resultados --}}
<div id="noResults" class="alert alert-warning d-none text-center py-4">
  <i class="ti tabler-search-off fs-3 mb-2 d-block"></i>
  <strong>Sin resultados</strong> — Intenta con otras palabras clave.
</div>

{{-- Categorías de ayuda --}}
<h6 class="text-uppercase text-muted fw-semibold small mb-3 ls-1">
  <i class="ti tabler-layout-grid me-1"></i>Módulos del sistema
</h6>
<div class="row g-4 mb-4" id="categoryCards">

  <div class="col-md-4 ayuda-item"
       data-tags="control interno sci actividad actividades plan anual registro seguimiento hacer">
    <div class="card border-0 shadow-sm h-100 ayuda-card">
      <div class="card-body text-center py-4">
        <div class="avatar avatar-lg bg-label-primary rounded mb-3 mx-auto d-flex align-items-center justify-content-center">
          <i class="ti tabler-clipboard-list fs-3"></i>
        </div>
        <h6 class="fw-bold">Control Interno (SCI)</h6>
        <p class="text-muted small mb-3">Registra y hace seguimiento de actividades del SCI.</p>
        <a href="#faq-sci" class="btn btn-outline-primary btn-sm">Ver guías</a>
      </div>
    </div>
  </div>

  <div class="col-md-4 ayuda-item"
       data-tags="integridad modelo pcm componente componentes monitorear 9">
    <div class="card border-0 shadow-sm h-100 ayuda-card">
      <div class="card-body text-center py-4">
        <div class="avatar avatar-lg bg-label-success rounded mb-3 mx-auto d-flex align-items-center justify-content-center">
          <i class="ti tabler-shield-check fs-3"></i>
        </div>
        <h6 class="fw-bold">Modelo de Integridad</h6>
        <p class="text-muted small mb-3">Monitorea los 9 componentes del Modelo de Integridad PCM.</p>
        <a href="#faq-integridad" class="btn btn-outline-success btn-sm">Ver guías</a>
      </div>
    </div>
  </div>

  <div class="col-md-4 ayuda-item"
       data-tags="evidencias subir validar gestionar archivo documento adjuntar">
    <div class="card border-0 shadow-sm h-100 ayuda-card">
      <div class="card-body text-center py-4">
        <div class="avatar avatar-lg bg-label-warning rounded mb-3 mx-auto d-flex align-items-center justify-content-center">
          <i class="ti tabler-file-upload fs-3"></i>
        </div>
        <h6 class="fw-bold">Evidencias</h6>
        <p class="text-muted small mb-3">Sube, valida y gestiona evidencias del sistema.</p>
        <a href="#faq-evidencias" class="btn btn-outline-warning btn-sm">Ver guías</a>
      </div>
    </div>
  </div>

  <div class="col-md-4 ayuda-item"
       data-tags="alertas alerta gestionar responder notificación notificaciones semáforo">
    <div class="card border-0 shadow-sm h-100 ayuda-card">
      <div class="card-body text-center py-4">
        <div class="avatar avatar-lg bg-label-danger rounded mb-3 mx-auto d-flex align-items-center justify-content-center">
          <i class="ti tabler-bell fs-3"></i>
        </div>
        <h6 class="fw-bold">Alertas</h6>
        <p class="text-muted small mb-3">Gestiona y responde a las alertas del sistema.</p>
        <a href="#faq-alertas" class="btn btn-outline-danger btn-sm">Ver guías</a>
      </div>
    </div>
  </div>

  <div class="col-md-4 ayuda-item"
       data-tags="usuarios roles permisos administrar usuario crear eliminar password contraseña">
    <div class="card border-0 shadow-sm h-100 ayuda-card">
      <div class="card-body text-center py-4">
        <div class="avatar avatar-lg bg-label-info rounded mb-3 mx-auto d-flex align-items-center justify-content-center">
          <i class="ti tabler-users fs-3"></i>
        </div>
        <h6 class="fw-bold">Usuarios y Roles</h6>
        <p class="text-muted small mb-3">Administra usuarios, roles y permisos del sistema.</p>
        <a href="#faq-usuarios" class="btn btn-outline-info btn-sm">Ver guías</a>
      </div>
    </div>
  </div>

  <div class="col-md-4 ayuda-item"
       data-tags="reportes reporte exportar generar pdf excel descarga">
    <div class="card border-0 shadow-sm h-100 ayuda-card">
      <div class="card-body text-center py-4">
        <div class="avatar avatar-lg bg-label-secondary rounded mb-3 mx-auto d-flex align-items-center justify-content-center">
          <i class="ti tabler-chart-bar fs-3"></i>
        </div>
        <h6 class="fw-bold">Reportes</h6>
        <p class="text-muted small mb-3">Genera y exporta reportes del sistema.</p>
        <a href="#faq-reportes" class="btn btn-outline-secondary btn-sm">Ver guías</a>
      </div>
    </div>
  </div>

</div>

{{-- FAQ por módulo --}}
<h6 class="text-uppercase text-muted fw-semibold small mb-3 ls-1">
  <i class="ti tabler-help me-1"></i>Preguntas frecuentes
</h6>

@php
$faqs = [
  ['id' => 'sci', 'label' => 'Control Interno', 'icon' => 'tabler-clipboard-list', 'color' => 'primary', 'items' => [
    ['q' => '¿Cómo registro una actividad en el SCI?', 'a' => 'Ve a <strong>Control Interno → Mis Actividades</strong>, haz clic en "Nueva Actividad", completa el formulario con descripción, fecha y componente, y guarda. La actividad aparecerá en el listado con estado Pendiente.'],
    ['q' => '¿Cómo actualizo el avance de una actividad?', 'a' => 'En el listado de actividades, haz clic en el icono de editar (<i class="ti tabler-edit"></i>) de la actividad. Modifica el porcentaje de avance y adjunta evidencias si corresponde.'],
    ['q' => '¿Qué significa el semáforo de colores en SCI?', 'a' => '<strong class="text-success">Verde</strong>: avance ≥ 75%. <strong class="text-warning">Amarillo</strong>: avance entre 40% y 74%. <strong class="text-danger">Rojo</strong>: avance < 40% o actividad vencida.'],
    ['q' => '¿Puedo filtrar actividades por año?', 'a' => 'Sí. En la parte superior del listado encontrarás un selector de año. Cambia el año para ver el plan de trabajo correspondiente.'],
  ]],
  ['id' => 'integridad', 'label' => 'Modelo de Integridad', 'icon' => 'tabler-shield-check', 'color' => 'success', 'items' => [
    ['q' => '¿Qué son los 9 componentes del Modelo de Integridad?', 'a' => 'Son los pilares definidos por la PCM: Compromiso, Integridad, Transparencia, Rendición de Cuentas, Gestión de Riesgos, Capacitación, Supervisión, Participación Ciudadana y Mejora Continua.'],
    ['q' => '¿Cómo registro el avance de un componente?', 'a' => 'Ve a <strong>Modelo de Integridad → Componentes</strong>, selecciona el componente, edita el indicador correspondiente y actualiza el porcentaje de cumplimiento con su evidencia.'],
    ['q' => '¿Cómo veo el semáforo general de integridad?', 'a' => 'En el Dashboard de Modelo de Integridad encontrarás un panel de semáforo general que agrega el puntaje de los 9 componentes y muestra el estado global.'],
  ]],
  ['id' => 'evidencias', 'label' => 'Evidencias', 'icon' => 'tabler-file-upload', 'color' => 'warning', 'items' => [
    ['q' => '¿Qué formatos de archivo puedo subir?', 'a' => 'Se aceptan PDF, Word (.docx), Excel (.xlsx), imágenes (JPG, PNG) y archivos comprimidos (ZIP). El tamaño máximo por archivo es de 10 MB.'],
    ['q' => '¿Cómo adjunto una evidencia a una actividad?', 'a' => 'Al editar una actividad, en la sección "Evidencias" haz clic en "Adjuntar archivo", selecciona el documento desde tu computadora y confirma la subida.'],
    ['q' => '¿Quién puede ver las evidencias que subo?', 'a' => 'Las evidencias son visibles para el responsable de la actividad, el jefe de área y los administradores del sistema. No son públicas.'],
  ]],
  ['id' => 'alertas', 'label' => 'Alertas', 'icon' => 'tabler-bell', 'color' => 'danger', 'items' => [
    ['q' => '¿Cuándo se genera una alerta automática?', 'a' => 'El sistema genera alertas cuando: una actividad está próxima a vencer (7 días), el avance está por debajo del mínimo esperado, o una evidencia fue rechazada por el validador.'],
    ['q' => '¿Cómo respondo a una alerta?', 'a' => 'Ve a <strong>Alertas</strong> en el menú, haz clic sobre la alerta, revisa el detalle y toma la acción indicada (actualizar avance, adjuntar evidencia, etc.). Marca la alerta como atendida al finalizar.'],
    ['q' => '¿Puedo desactivar las notificaciones?', 'a' => 'Las alertas críticas no pueden desactivarse. Las notificaciones informativas pueden configurarse en tu perfil de usuario en la sección Notificaciones.'],
  ]],
  ['id' => 'usuarios', 'label' => 'Usuarios y Roles', 'icon' => 'tabler-users', 'color' => 'info', 'items' => [
    ['q' => '¿Cómo creo un nuevo usuario?', 'a' => 'Ve a <strong>Administración → Usuarios</strong>, haz clic en "Nuevo Usuario", completa los datos y asigna el rol correspondiente. El usuario recibirá sus credenciales por correo.'],
    ['q' => '¿Qué roles existen en el sistema?', 'a' => '<strong>Administrador</strong>: acceso total. <strong>Responsable</strong>: gestiona actividades de su área. <strong>Supervisor</strong>: aprueba y valida evidencias. <strong>Visualizador</strong>: solo lectura.'],
    ['q' => '¿Cómo cambio la contraseña de un usuario?', 'a' => 'El administrador puede restablecer la contraseña desde <strong>Administración → Usuarios → Editar</strong>. El propio usuario puede cambiarla desde su perfil en la esquina superior derecha.'],
  ]],
  ['id' => 'reportes', 'label' => 'Reportes', 'icon' => 'tabler-chart-bar', 'color' => 'secondary', 'items' => [
    ['q' => '¿Cómo exporto el reporte de avance SCI?', 'a' => 'Ve a <strong>Reportes → Avance SCI</strong>, selecciona el año y las áreas que deseas incluir, luego haz clic en "Exportar PDF" o "Exportar Excel".'],
    ['q' => '¿Puedo programar reportes automáticos?', 'a' => 'Actualmente los reportes se generan manualmente. La funcionalidad de reportes programados está en desarrollo para una próxima versión.'],
    ['q' => '¿El reporte incluye gráficos?', 'a' => 'Sí. El reporte PDF incluye gráficos de barras y el semáforo de avance. El Excel incluye los datos tabulares para análisis personalizado.'],
  ]],
];
@endphp

@foreach($faqs as $section)
<div class="card border-0 shadow-sm mb-3 ayuda-faq-section" id="faq-{{ $section['id'] }}">
  <div class="card-header bg-transparent border-bottom py-3">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar avatar-sm bg-label-{{ $section['color'] }} rounded d-flex align-items-center justify-content-center">
        <i class="ti {{ $section['icon'] }} fs-5"></i>
      </div>
      <h6 class="fw-bold mb-0">{{ $section['label'] }}</h6>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="accordion accordion-flush" id="accordion-{{ $section['id'] }}">
      @foreach($section['items'] as $i => $item)
      <div class="accordion-item ayuda-item"
           data-tags="{{ strtolower(strip_tags($item['q'])) }} {{ strtolower(strip_tags($item['a'])) }} {{ $section['id'] }} {{ strtolower($section['label']) }}">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold small" type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#faq-{{ $section['id'] }}-{{ $i }}">
            {{ $item['q'] }}
          </button>
        </h2>
        <div id="faq-{{ $section['id'] }}-{{ $i }}" class="accordion-collapse collapse"
             data-bs-parent="#accordion-{{ $section['id'] }}">
          <div class="accordion-body text-muted small">{!! $item['a'] !!}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endforeach

{{-- Contacto soporte --}}
<div class="card border-0 shadow-sm mt-4 ayuda-item" data-tags="soporte contacto correo email técnico">
  <div class="card-body d-flex align-items-center gap-4 flex-wrap">
    <div class="avatar avatar-lg bg-label-info rounded d-flex align-items-center justify-content-center">
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

@section('page-script')
<script>
(function () {
  const searchInput  = document.getElementById('ayudaSearch');
  const clearBtn     = document.getElementById('clearSearch');
  const counter      = document.getElementById('searchCounter');
  const noResults    = document.getElementById('noResults');
  const allItems     = document.querySelectorAll('.ayuda-item');
  const faqSections  = document.querySelectorAll('.ayuda-faq-section');

  function normalize(str) {
    return str.toLowerCase()
      .normalize('NFD').replace(/[̀-ͯ]/g, '');
  }

  function highlight(text, query) {
    if (!query) return text;
    const re = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(re, '<mark class="bg-warning rounded px-1">$1</mark>');
  }

  function runSearch(raw) {
    const query = normalize(raw.trim());

    // Reset highlights
    document.querySelectorAll('.accordion-button, .accordion-body').forEach(el => {
      el.innerHTML = el.getAttribute('data-original') || el.innerHTML;
    });

    if (!query) {
      allItems.forEach(el => el.classList.remove('d-none'));
      faqSections.forEach(el => el.classList.remove('d-none'));
      noResults.classList.add('d-none');
      counter.style.display = 'none';
      clearBtn.style.display = 'none';
      return;
    }

    clearBtn.style.display = '';
    let visible = 0;

    allItems.forEach(item => {
      const tags  = normalize(item.getAttribute('data-tags') || '');
      const texts = normalize(item.innerText || '');
      const match = tags.includes(query) || texts.includes(query);
      item.classList.toggle('d-none', !match);
      if (match) visible++;
    });

    // Highlight in accordions
    if (query.length >= 2) {
      document.querySelectorAll('.accordion-button, .accordion-body').forEach(el => {
        if (!el.getAttribute('data-original')) {
          el.setAttribute('data-original', el.innerHTML);
        }
        el.innerHTML = highlight(el.getAttribute('data-original'), query);
      });
    }

    // Hide empty FAQ sections
    faqSections.forEach(section => {
      const anyVisible = [...section.querySelectorAll('.ayuda-item')]
        .some(i => !i.classList.contains('d-none'));
      section.classList.toggle('d-none', !anyVisible);
    });

    noResults.classList.toggle('d-none', visible > 0);
    counter.style.display = '';
    counter.textContent = visible === 1
      ? '1 resultado encontrado'
      : `${visible} resultados encontrados`;
  }

  let debounceTimer;
  searchInput.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => runSearch(this.value), 220);
  });

  clearBtn.addEventListener('click', function () {
    searchInput.value = '';
    runSearch('');
    searchInput.focus();
  });

  // Scroll to section when clicking card links
  document.querySelectorAll('a[href^="#faq-"]').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });
})();
</script>
@endsection
