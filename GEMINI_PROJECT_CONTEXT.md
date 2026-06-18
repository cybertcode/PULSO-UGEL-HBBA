# Historial de Versiones - Proyecto PULSO (SATA-QR - UGEL Huacaybamba)

## [v2.2.0] - 2026-06-17 (Sesión Actual)
### Seguridad, Multi-Roles y Gestión Estructural de Cargos
- **Sistema Multi-rol Avanzado**:
    - Migración de lógica de rol único a múltiple mediante **Select2**, permitiendo a los usuarios poseer diversos perfiles de acceso simultáneos.
    - Implementación de una interfaz interactiva de "Toggle Rol" en la gestión de acceso para agregar o quitar permisos individuales con un solo clic y confirmación visual.
    - Actualización de iconografía dinámica para el nuevo rol **"Coordinador SCI"** y visualización de múltiples badges en todas las tablas del sistema.
- **Soporte Multi-cargo Institucional**:
    - Reestructuración masiva de la base de datos: eliminación del campo `cargo_id` en `users` y creación de la tabla pivot `cargo_user`.
    - Esta mejora permite que un funcionario desempeñe múltiples cargos técnicos o administrativos dentro de la UGEL, reflejando fielmente la realidad operativa.
    - Sincronización automática de cargos existentes durante la migración para garantizar la integridad de los datos históricos.
- **Validación de Identidad y Seguridad**:
    - Implementación de restricción de **DNI Único** a nivel de base de datos y validaciones de backend, previniendo la duplicidad de registros de personal.
    - Refactorización de los controladores de `UserList` y `Perfil` para soportar la sincronización asíncrona de múltiples entidades (roles y cargos).
- **Mejoras de UI/UX y Correcciones**:
    - Corrección de la longitud de campo para URLs en Instituciones Vinculadas (cambio a tipo `TEXT`), eliminando errores de truncado en enlaces extensos.
    - Optimización de las vistas de configuración, cumplimiento y modelo de integridad para consumir la nueva estructura relacional de cargos.
    - Limpieza profunda de caché de rutas y actualización de seeders maestros.

## [v2.1.5] - 2026-06-15
### Infraestructura, Despliegue y Refactorización
- **Optimización para Hosting Compartido (InMotionHosting)**:
    - Actualización de `DEPLOY_PRODUCCION.md` para forzar el uso de PHP 8.3 mediante rutas absolutas (`/opt/cpanel/ea-php83/root/usr/bin/php`), garantizando la compatibilidad del proyecto en el servidor de producción.
    - Rediseño del script `deploy.sh` con variables para los binarios de PHP y Composer, simplificando el mantenimiento y mejorando la robustez del flujo de despliegue.
    - Adición de guías visuales en la documentación para la configuración de PHP 8.3 desde el MultiPHP Manager de cPanel.
- **Mejores Prácticas de Desarrollo**:
    - Refactorización de `UserFactory.php` migrando del helper global `fake()` a `$this->faker`, siguiendo los estándares actuales de Laravel para una mejor integración con herramientas de análisis estático.

## [v2.1.4] - 2026-06-15
### Refactorización AJAX y Utilidades Globales de Notificación
- **Buenas Prácticas (Flujo Asíncrono)**:
    - Implementación de un flujo AJAX completo para la gestión de estados del concurso (Recepcionar, Declarar Elegible, No Elegible, Ganador UGEL y Resultados Externos).
    - Eliminación de recargas de página mediante el uso de `FormData` y peticiones asíncronas, mejorando la fluidez del panel de gestión.
    - Nueva funcionalidad para **Presentar Prácticas Institucionales al Concurso**, permitiendo a los gestores convertir una práctica de oficina en un proyecto concursable con un solo clic.
- **Recomendaciones (Refinamiento y Seguridad)**:
    - Sincronización de la visibilidad de campos (Unidad Orgánica, Responsable) basada en permisos granulados para evitar la edición no autorizada por parte de usuarios operativos.
    - Integración de la lógica de guardado y actualización con el sistema de notificaciones global.
- **Utilidades Globales de UI (Pulso-UI)**:
    - **pulsoToast**: Implementación de un sistema de notificaciones toast ligero y estéticamente alineado con la identidad visual (soporte para success, error, warning e info).
    - **pulsoConfirm**: Wrapper global sobre SweetAlert2 para estandarizar los diálogos de confirmación en todo el sistema, con fallback automático a diálogos nativos.
- **Seguridad y Permisos**:
    - Actualización de `RolesPermisosSeeder.php` para otorgar permisos de creación en buenas prácticas a roles operativos, permitiendo la presentación de propuestas al concurso.
- **Estructura Técnica**:
    - Controladores: Refactorización de `BuenasPracticasController.php` y `RecomendacionesController.php` para retornar respuestas JSON.
    - Vistas: Actualización de `index.blade.php` en ambos módulos e integración de scripts globales en `scripts.blade.php`.
    - Rutas: Nueva ruta POST para la presentación de prácticas al concurso.

## [v2.1.3] - 2026-06-14
### Buscador Inteligente y Layout Dinámico (Protección de Interfaz)
- **Buscador Global Sensible a Permisos**:
    - Implementación de `SearchController.php` para generar dinámicamente las sugerencias de búsqueda basadas en las capacidades reales del usuario logueado.
    - Modificación de `main.js` para consumir la API de búsqueda en lugar de archivos JSON estáticos, garantizando que un usuario nunca vea accesos a módulos restringidos en la búsqueda.
- **Limpieza de Interfaz (Navbar & Shortcuts)**:
    - Aplicación de directivas `@can` en `navbar-partial.blade.php` para ocultar dinámicamente el dropdown de notificaciones, accesos directos (Shortcuts) y enlaces del perfil según el rol del usuario.
    - Sincronización de la visibilidad de elementos en el menú lateral y navbar con el nuevo esquema de permisos granulados.
- **Estructura Técnica**:
    - Nuevo Controlador: `SearchController.php`.
    - Assets: Actualización de lógica en `resources/assets/js/main.js`.
    - Rutas: Nueva ruta `/search/data` protegida por autenticación.

### Refactorización de Seguridad de Alto Impacto (Permisos Granulados v2)
- **Granularidad Extrema de Accesos**:
    - Evolución del sistema de permisos de un esquema genérico (`configuracion.ver/editar`) a uno **específico por módulo** (`slider.*`, `instituciones.*`, `unidades.*`, `roles.*`, `componentes.*`, `normativas.*`, etc.).
    - Implementación de acciones CRUD completas (`ver`, `crear`, `editar`, `eliminar`) para cada entidad del sistema, permitiendo una delegación de funciones 100% precisa.
    - Refactorización masiva de `routes/web.php` para desvincular las rutas de los permisos globales de configuración y asociarlas a sus nuevos permisos específicos.
- **Optimización de Roles Institucionales**:
    - Actualización de los roles maestros (`Administrador`, `Coordinador SCI`, `Responsable de Unidad`, `Operador`, `Visualizador`) para heredar el nuevo esquema de permisos granulados.
    - Los permisos ahora siguen un estándar industrial: `[modulo].[accion]`, facilitando la auditoría y el mantenimiento.
- **Refinamiento de UI/UX Administrativa (Autenticación y Registro)**:
    - **Seguridad de Acceso**: Desactivación del registro público (`Fortify::registration`) para centralizar la creación de usuarios en la administración institucional.
    - **Rediseño Premium**: Actualización de las vistas de `Registro` y `Verificación 2FA` con estética institucional (doble panel, branding UGEL).
    - Actualización de las vistas de administración para ocultar/mostrar botones de acción (Editar, Eliminar, Crear) basados estrictamente en los nuevos permisos granulados mediante directivas `@can`.
- **Estructura Técnica**:
    - Migración: `2026_06_12_112045_refactorizar_permisos_granulados_v2.php`.
    - Seeder: `RolesPermisosSeeder.php` totalmente reescrito con el nuevo mapa de permisos.
    - Rutas: Limpieza y re-protección de más de 60 rutas del sistema e inhabilitación de `/register`.

### Administración Pro y Refinamiento UI/UX (Módulo de Usuarios)
- **Gestión Avanzada de Usuarios**:
    - Implementación de **Reseteo de Contraseña Administrativo** vía AJAX, permitiendo a los administradores actualizar credenciales con validación de complejidad en tiempo real.
    - Sistema de **Cambio de Rol Rápido** directamente desde la tabla de roles, utilizando peticiones asíncronas para una gestión de permisos más fluida.
    - Protección de seguridad: bloqueo de cambios de rol y eliminación de la propia cuenta para el usuario autenticado.
- **Ingeniería de Reciclaje Premium (UI/UX)**:
    - Integración de **Toasts persistentes** mediante `sessionStorage`, permitiendo mostrar notificaciones de éxito tras recargas de página provocadas por acciones AJAX.
    *   Refinamiento estético de las tablas de administración con badges de estado dinámicos, tooltips informativos e iconografía Tabler optimizada.
    *   Mejora del layout `contentNavbarLayout` para soportar notificaciones globales del sistema.
- **Refactorización de Seguridad y Limpieza de Base de Datos**:
- **Sistema de Permisos y Roles**:
    - Refactorización profunda de `routes/web.php` para implementar una granularidad de permisos más estricta (`ver`, `crear`, `editar`, `eliminar`) en todos los módulos (Slider, Instituciones, SCI, Integridad, Normativas, Encuestas).
    - Normalización de los nombres de permisos para asegurar consistencia con el `RolesPermisosSeeder`.
    - Protección de rutas API con los mismos middleware de permisos que las vistas principales.
- **Mantenimiento de Base de Datos**:
    - Implementación de migración de limpieza para eliminar permisos huérfanos de módulos legacy (`paci`, `riesgos`, `actas`, etc.).
    - Eliminación automática de roles de prueba sin usuarios asignados (`testeando`).
- **Estructura Técnica**:
    - Migración: `2026_06_12_103715_limpiar_permisos_huerfanos_y_roles_prueba.php`.
    - Seeder actualizado: `RolesPermisosSeeder.php`.
    - Rutas optimizadas: `routes/web.php`.

### Refinamiento de Autenticación y Páginas Especiales
- **Vistas de Autenticación**:
    - Personalización integral de `login`, `register`, `forgot-password`, `reset-password`, `verify-email` y `two-factor` con la identidad institucional (logo y nombre dinámico).
    - Optimización de los layouts de autenticación para una experiencia de usuario más profesional y coherente con la marca UGEL Huacaybamba.
- **Páginas de Error y Estado (Misc)**:
    - Implementación de controladores y vistas para estados especiales del sistema: `Error 404`, `No Autorizado`, `En Mantenimiento` y `Próximamente`.
    - Integración de rutas dedicadas para la gestión de estas páginas bajo el prefijo `/pages/misc-`.
- **Sistema de Alertas e Identidad Dinámica**:
    - Implementación de `AlertaObserver` para la automatización de notificaciones institucionales.
    - Soporte para logotipos dinámicos diferenciados para emails y portal público mediante nuevas columnas en configuración.
    - Actualización de `AlertaService` para gestionar el flujo de alertas por vencimiento próximo.
- **Estructura Técnica**:
    - Nuevos controladores: `MiscComingSoon`, `MiscNotAuthorized`, `MiscUnderMaintenance`.
    - Migraciones: `add_vencimiento_proximo_to_alertas_tipo_enum`, `add_logo_url_email_to_configuracion_institucional`.
    - Observer: `AlertaObserver.php`.
    - Esquema: Actualización integral de `mysql-schema.sql`.

## [v2.1.2] - 2026-06-11
### Módulo de Cumplimiento SCI y Gestión de Evidencias
- **Módulo de Cumplimiento (SCI)**:
    - Rediseño y optimización del Panel SCI para un seguimiento más intuitivo del cumplimiento normativo.
    - Implementación de nuevas vistas para la gestión de Responsables y seguimiento de Actividades Sin Evidencia.
- **Navegación Dinámica**: Actualización del menú vertical para integrar los nuevos accesos directos a los reportes de cumplimiento y paneles de control.
- **Refinamiento UI/UX**: Mejora en la presentación de tablas de cumplimiento y filtros de responsables en el panel administrativo.

### Estructura Técnica
- **Controladores**: Actualización de `CumplimientoController` con nueva lógica de filtrado y métricas de avance.
- **Vistas**: Implementación de `panel-sci.blade.php`, `responsables.blade.php` y `sin-evidencia.blade.php` en el módulo de cumplimiento.
- **Configuración de Menú**: Actualización de `verticalMenu.json` con la nueva jerarquía de accesos.

## [v2.1.1] - 2026-06-11
### Módulo de Recomendaciones, Contacto SCI y Ayuda
- **Módulo de Recomendaciones**:
    - Implementación de un sistema de gestión de recomendaciones institucionales con categorización por módulo.
    - Actualización de la estructura de base de datos para soportar la trazabilidad de recomendaciones por áreas.
- **Contacto SCI y Configuración**:
    - Adición de campos de contacto específicos para el Sistema de Control Interno (SCI) en la configuración institucional.
    - Mejora en la interfaz de configuración para gestionar estos nuevos canales de comunicación.
- **Portal de Ayuda**: Rediseño y actualización de la sección de Ayuda para facilitar la navegación del usuario.
- **Optimización de Vistas**: Refinamiento en el Landing Page y vistas de administración para integrar los nuevos componentes de contacto y recomendaciones.

### Estructura Técnica
- **Modelos y Controladores**: `Recomendacion`, `RecomendacionesController`.
- **Migraciones**: Adición de campos `modulo` en recomendaciones y campos de contacto SCI en configuración.
- **UI**: Nuevas interfaces en `resources/views/content/recomendaciones/` y actualización de `ayuda/index.blade.php`.

## [v2.1.0] - 2026-06-11
### Módulo de Normativas, Refactorización de Buenas Prácticas y Alertas
- **Módulo de Normativas**: Implementación completa del sistema de gestión de normativas legales e institucionales, incluyendo CRUD administrativo y vistas públicas.
- **Refactorización de Buenas Prácticas**: 
    - Evolución del módulo a un esquema de dos niveles para mejor organización.
    - Soporte para adjuntar archivos técnicos y evidencias en formato PDF/documentos.
    - Sistema de estados para concursos de buenas prácticas.
- **Mejoras en Configuración Institucional**: 
    - Integración de autoridades mediante relaciones de clave foránea para mayor integridad.
    - Implementación de configuración granular para niveles de notificación del sistema.
- **Sistema de Alertas por Email**: Creación de Jobs asíncronos para el envío automatizado de alertas, optimizando el rendimiento del servidor.
- **Optimización de Ranking y Reportes**: 
    - Adición de la dimensión de "módulo" en el historial de ranking para trazabilidad por áreas.
    - Nuevos componentes visuales (`_ranking.blade.php`, `_tabla.blade.php`) para reportes ejecutivos.
- **Refinamiento UI/UX Global**: Actualización masiva de controladores y vistas (Dashboard, Avance, Reconocimientos) para integrar las nuevas métricas y funcionalidades.

### Estructura Técnica
- **Nuevos Modelos y Controladores**: `Normativa`, `NormativasController`.
- **Migraciones**: Implementación de 11 nuevas migraciones para normativas, archivos en buenas prácticas, autoridades en configuración y trazabilidad de ranking.
- **Seeders**: `NormativasSeeder` para inicialización de base legal.
- **UI**: Nuevas carpetas de vistas en `resources/views/content/normativas/` y parciales de reportes.

## [v2.0.0] - 2026-06-10
### Refactorización Estructural Mayor, Módulo de Encuestas y Seguimiento Visual
- **Nueva Estructura SCI e Integridad**: Evolución de los módulos legacy (`PACI`, `Matriz de Riesgos`, `Actas de Comité`) hacia un modelo dinámico y jerárquico basado en Ejes, Etapas, Componentes y Preguntas.
- **Módulo de Encuestas (PULSO-Polls)**: Implementación integral de un motor de encuestas institucional con:
    - Gestión de preguntas (múltiples tipos) y opciones.
    - Sistema de destinatarios y control de respuestas.
    - Exportación de resultados a Excel y PDF (DomPDF).
- **Sistema de Semáforo y Avance**: Implementación de tableros visuales de seguimiento para el avance de unidades orgánicas y cumplimiento de metas mediante semaforización dinámica.
- **Refactorización v2 de Actividades y Alertas**: Optimización profunda del esquema de base de datos para mejorar la trazabilidad, rendimiento y consistencia relacional.
- **Soporte PWA (Progressive Web App)**: El sistema ahora es instalable y ofrece capacidades offline básicas mediante Service Worker y Manifiesto de Aplicación.
- **Notificaciones Dinámicas**: Sistema de notificaciones en tiempo real para la revisión de evidencias y alertas críticas.
- **Limpieza de Código (Cleanup)**: Depuración de controladores, modelos y migraciones legacy para mantener la arquitectura limpia y alineada con los nuevos estándares.

### Estructura Técnica
- **Nuevos Modelos**: `SciEje`, `SciComponente`, `SciPregunta`, `IntegridadEtapa`, `IntegridadComponente`, `IntegridadPregunta`, `Encuesta`, `EncuestaPregunta`, `EncuestaRespuesta`, `EncuestaOpcion`.
- **Controladores**: `SciEstructuraController`, `IntegridadEstructuraController`, `EncuestaController`, `EncuestaResultadoController`, `EncuestaRespuestaController`, `SemaforoController`, `AvanceUnidadesController`, `ModeloIntegridadController`.
- **Migraciones**: Refactorización completa mediante migraciones v2 (`refactor_actividades_v2`, `refactor_alertas_v2`), `add_tipos_to_encuesta_preguntas` y `add_encuestas_to_alertas_modulo_enum`.
- **UI/UX**: Nuevas vistas administrativas para la gestión de estructuras y encuestas, manteniendo la fidelidad visual a la plantilla Vuexy.

## [v1.5.4] - 2026-06-10
### Automatización y Portal de Publicaciones
- **Portal de Todas las Publicaciones**: Creación de la vista `publicaciones.blade.php` y su ruta correspondiente, permitiendo a los ciudadanos explorar el historial completo de noticias, eventos y normativas con filtrado dinámico.
- **Automatización de Autoría**: El sistema ahora detecta automáticamente al usuario logueado en el panel administrativo y lo asigna como autor por defecto de las nuevas publicaciones, manteniendo la trazabilidad.
- **Selector de Color Dinámico**: Implementación de un campo de selección de color en el formulario de Sliders que genera automáticamente degradados CSS profesionales (`linear-gradient`) para las tarjetas del landing.
- **Mejoras de UX en Panel**: Refinamiento de los controladores para mejorar la retroalimentación al usuario (mensajes de éxito personalizados) y optimización de las relaciones de modelos.
- **Respaldo de Estructura**: Generación de `mysql-schema.sql` para facilitar la replicación del entorno de base de datos en el hosting compartido.

## [v1.5.3] - 2026-06-10
### Sistema de Contenido Enriquecido y Detalle de Noticias
- **Editor Quill Integrado**: Implementación de un editor de texto enriquecido (WYSIWYG) en el panel administrativo para la gestión de contenidos con formato profesional.
- **Rediseño de Página de Detalle**: Transformación de `noticia.blade.php` en un portal de lectura inmersivo con:
    - **Hero Header**: Gradientes dinámicos y meta-información estilizada (autor, fecha traducida, tiempo estimado de lectura).
    - **Cuerpo del Artículo**: Tipografía optimizada para lectura, soporte para HTML enriquecido y bloques de resumen destacados.
    - **Sidebar Ejecutivo**: Acceso rápido al sistema PULSO y carrusel vertical de publicaciones relacionadas.
- **Gestión Multimedia**: Soporte para imágenes de portada (`imagen_portada_url`) independientes del slider y sistema de previsualización en tiempo real durante la carga.
- **Estructura de Datos**: Extensión de la tabla `slider_landing` mediante migración para soportar los nuevos campos de contenido y metadatos.

## [v1.5.2] - 2026-06-09
### Rediseño Integral de UI/UX (Landing Page Premium)
- **Paleta de Colores Gubernamental**: Transición a una gama cromática ejecutiva (Azul Confianza `#1E3A8A`, Rojo Crimson `#E31B54`, Ámbar `#F59E0B`) con contrastes accesibles y fondos Slate.
- **Estructura Dinámica**:
  - Sustitución del listado estático por un **Components Grid** (Bento Grid optimizado) que extrae los módulos oficiales (`\App\Models\Componente`) directamente de la base de datos con iconos Tabler dinámicos.
  - El **Footer** ahora se alimenta directamente de `ConfiguracionInstitucional` y de las entidades guardadas en `InstitucionVinculada`, mostrando datos 100% reales.
- **Micro-interacciones y Estética (Apple/Vercel Style)**:
  - Implementación de **Glassmorphism** extremo (filtros de desenfoque de fondo en el Nav y estadísticas superpuestas).
  - Sombras multinivel orgánicas (`Soft Shadows`) en todos los elementos interactivos.
  - Botones principales con efecto interno de reflejo de luz.
  - La sección *Acerca del Sistema* ahora exhibe un **Mock UI estilo ventana macOS**.
  - La *Normativa Legal* se presenta como un **Timeline** interactivo conectado por un hilo visual.
  - Tarjetas de *Instituciones Vinculadas* con carga de logotipos en escala de grises y revelado de color en hover.

## [v1.5.1] - 2026-06-09
### Refinamiento de Layout y Estabilidad
- **Ajuste de Proporciones**: Rediseño del ancho global del portal al 90% de la ventana con contenedores internos al 95%, logrando un diseño centrado y moderno.
- **Corrección de Estadísticas**: Reparación de error SQL en `LandingController` mediante la sincronización de campos con la tabla `paci` (cambio de `periodo` a `anio`) y adición de fallbacks de seguridad.
- **Optimización UI**: Remoción de componentes no solicitados y forzado de estilos críticos para evitar problemas de caché en el despliegue.

## [v1.5.0] - 2026-06-09
### Portal Institucional y Landing Page de Alto Impacto
- **Landing Page Pública**: Implementación de una interfaz moderna y atractiva para el portal principal, integrando secciones de noticias, banners dinámicos e indicadores de impacto.
- **Gestión de Sliders**: Nuevo módulo administrativo para el control total de los banners del landing, permitiendo subida de imágenes, gestión de títulos, descripciones y estados de activación.
- **Instituciones Vinculadas**: Sistema de gestión para enlaces institucionales y aliados estratégicos, con soporte para logos dinámicos y categorización.
- **Arquitectura de Layouts**: Creación de `layoutLanding.blade.php` optimizado para SEO y carga rápida, separando la lógica del portal público del panel administrativo.
- **Vite & Assets**: Integración de bundles específicos (`landing-institucional.css/js`) para optimizar el rendimiento y aislamiento de estilos.

### Estructura Técnica
- **Modelos**: `SliderLanding`, `InstitucionVinculada`.
- **Controladores**: `LandingController`, `SliderLandingController`, `InstitucionVinculadaController`.
- **Migraciones**: `create_slider_landing_table`, `create_instituciones_vinculadas_table`.
- **Rutas**: Definición de rutas públicas para el portal y rutas protegidas para la administración del landing en `web.php`.

## [v1.4.4] - 2026-06-09
### Lógica Funcional y Persistencia Institucional
- **Sembrado de Datos Reales**: Implementación de `NuevosModulosSeeder` que integra datos oficiales del PACI 2026, Matriz de Riesgos, Actas de Comité y Autoevaluaciones, permitiendo dashboards 100% operativos.
- **Refactorización de Controladores**: Optimización de `ActasComiteController`, `UserList.php` y `PerfilController` para soportar las nuevas micro-interacciones y filtrado dinámico.
- **Sincronización de Maestros**: Actualización de los seeders de Unidades Orgánicas, Usuarios y Configuración Institucional para reflejar fielmente la estructura jerárquica de la UGEL Huacaybamba.
- **Mejoras en Perfil y Reportes**: Refinamiento estético de la vista de perfil y optimización del motor de exportación PDF para reportes de cumplimiento.

## [v1.4.3] - 2026-06-09
### Consolidación Documental y Respaldo de Evidencias
- **Integración de Informe 054**: Incorporación del Informe Nº 054-2026-GRH-GRDS-DRE-UGEL HUACAYBAMBA/UGA-RCAV (MD y PDF) al repositorio como base técnica del sistema.
- **Evidencias de Implementación**: Inclusión de screenshots de verificación (`verify_*.png`, `ci_*.png`) que validan la fidelidad de la interfaz actual con respecto a los prototipos propuestos.
- **Documentación de Referencia**: Adición de `documentation.html` al esquema de documentos oficiales del proyecto.

## [v1.4.2] - 2026-06-09
### Rediseño UI/UX y Optimización de Dashboards
- **Dashboards de Impacto**: Rediseño completo del Panel SCI y Modelo de Integridad utilizando un sistema de tarjetas (cards) ejecutivas, semaforización avanzada y micro-interacciones.
- **Gestión de Usuarios Pro**: Actualización estética masiva de la lista de usuarios y catálogo de cargos, implementando un diseño compacto de alta densidad, avatares estilizados y badges de estado con indicadores de pulso.
- **Optimización de Reportes**: Refinamiento en los cálculos de avance y días de retraso en exportaciones PDF y vistas de cumplimiento.
- **Interconectividad**: Mejora en la navegación entre módulos mediante el uso de parámetros de filtrado inteligentes en las rutas.

## [v1.4.1] - 2026-06-09
### Normalización Relacional en Unidades Orgánicas
- **Integración de Cargos**: Actualización de `UnidadesOrganicasController` para utilizar la relación `cargo` (FK) en lugar del campo de texto legacy, asegurando la consistencia con el Catálogo Maestro de Cargos.
- **Refinamiento de UI**: Ajuste en la vista de Unidades Orgánicas para renderizar correctamente los nombres de los cargos desde la relación, mejorando la visualización de responsables.
- **Optimización de Consultas**: Implementación de Eager Loading (`with('cargo')`) para optimizar el rendimiento al listar responsables y sus cargos.

## [v1.4.0] - 2026-06-09
### Refinamiento y UX de Evidencias
- **Preselección Inteligente**: Ajuste en `EvidenciasController` para condicionar la preselección de actividades al parámetro `?nueva=1`, optimizando la experiencia de usuario al navegar desde otros módulos.
- **Optimización de Componentes**: Mejora en la inicialización de Select2 en la vista de evidencias, corrigiendo errores de envoltura (wrapper) y asegurando el auto-submit mediante eventos nativos de la librería.
- **Persistencia de Filtros**: Refactorización del manejo de eventos en filtros para garantizar una respuesta inmediata y precisa del sistema de búsqueda.

## [v1.3.9] - 2026-06-08
### Refactorización de Evidencias y Cargos
- **Evidencias (URL-based)**: Migración del sistema de evidencias de carga local a almacenamiento basado en URLs, mejorando la compatibilidad con hosting compartido y reduciendo el consumo de almacenamiento local.
- **Normalización de Cargos**: Refactorización del campo `cargo` en la tabla de usuarios de un simple texto a una relación formal de clave foránea (`cargo_id`), integrándose plenamente con el Catálogo Maestro de Cargos.
- **Listado de Usuarios Pro**: Implementación de paginación y búsqueda del lado del servidor (Server-side) en Datatables para optimizar la carga de grandes volúmenes de usuarios.
- **Validación y Seguridad**: Inclusión de reglas de validación más estrictas para DNI y control de permisos granulares para el registro de evidencias basado en asignaciones.

## [v1.3.8] - 2026-06-08
### Gestión Avanzada y Auditoría SCI
- **Asignación Multirrol**: Implementación de un sistema flexible de responsabilidades donde se pueden asignar múltiples usuarios a una actividad, cada uno con un rol distinto (Principal, Colaborador, Supervisor).
- **Builder de Responsables**: Nueva interfaz interactiva en los modales de creación y edición que permite gestionar la lista de responsables de forma dinámica y visual.
- **Auditoría Estética**: Rediseño del historial de cambios con una línea de tiempo estilizada, iconografía semántica por campo y visualización clara de los responsables involucrados.
- **UI Premium de Gestión**: Actualización masiva de la vista de Control Interno con KPIs de alto impacto, filtros mejorados y modales con identidad visual institucional reforzada (degradados y tipografía).

## [v1.3.7] - 2026-06-08
### Panel de Actividades de Alto Impacto
- **Filtros Avanzados**: Implementación de un sistema de filtrado dinámico en "Mis Actividades" que incluye búsqueda por código/nombre (debounce), rango de fechas (Flatpickr), estado, prioridad y rango de avance (noUiSlider).
- **UI/UX Reimagined**: Rediseño completo de la interfaz de actividades utilizando un sistema de tarjetas ejecutivas con indicadores visuales de estado (semaforización), prioridades y roles.
- **KPIs y Métricas**: Adición de un panel de indicadores rápidos (Total, Completadas, Vencidas, Pendientes) para un seguimiento inmediato del rendimiento individual.
- **Historial Detallado**: Mejora del modal de historial con una línea de tiempo visual, iconografía por tipo de campo y comparativa clara entre valores anteriores y nuevos.
- **Interacción Fluida**: Integración de actualizaciones de avance mediante AJAX con notificaciones de SweetAlert2 personalizadas.

## [v1.3.6] - 2026-06-08
### Reportes y Exportación Premium
- **Customización de DataTables**: Implementación de funciones `customize` para PDF, Excel e Impresión, añadiendo cabeceras oficiales de la UGEL Huánuco y estilos corporativos.
- **Formatos Profesionales**: Los reportes PDF ahora incluyen orientación horizontal, numeración de páginas, footer institucional y líneas de marca personalizadas.
- **Excel Corporativo**: Optimización de anchos de columna y estilos de cabecera en las exportaciones a Excel para un acabado ejecutivo.
- **Identidad en Reportes**: Integración de la identidad visual de la institución en todos los documentos generados por el sistema.

## [v1.3.5] - 2026-06-08
### Integración AJAX y Datos Institucionales
- **Gestión de Usuarios (AJAX)**: Refactorización de `UserList.php` para soportar operaciones CRUD mediante peticiones asíncronas, eliminando recargas de página innecesarias.
- **Configuración Real**: Actualización de `ConfiguracionInstitucionalSeeder` con los datos oficiales de la institución (nombres, direcciones, autoridades).
- **Carga de Datos Maestros**: Creación de `CargosSeeder` e integración en el flujo principal de `DatabaseSeeder` para asegurar un entorno de trabajo listo para producción.
- **Mejoras de UI**: Ajustes finales en el listado de usuarios y modales de permisos para una mejor experiencia de usuario.

## [v1.3.4] - 2026-06-08
### Normalización Estructural y Relacional
- **Unidades Orgánicas**: Migración del campo `responsable` (texto) a `responsable_id` (FK), vinculando formalmente cada unidad con un usuario del sistema para una gestión de auditoría y flujos de trabajo más robusta.
- **Componentes del Sistema**: Corrección técnica de las columnas `tipo` e `icono` en la tabla de componentes para asegurar la consistencia en el renderizado de menús dinámicos.
- **Refactorización de Seeders**: Actualización de `UnidadesOrganicasSeeder` para reflejar el nuevo esquema relacional.
- **Optimización de Vistas**: Ajustes en las interfaces de Administración de Componentes y Unidades Orgánicas para soportar la selección de responsables desde la base de datos de usuarios.

## [v1.3.3] - 2026-06-08
### Catálogo Maestro de Cargos
- **Gestión de Cargos**: Implementación de un CRUD completo para el catálogo de cargos institucionales, permitiendo estandarizar las denominaciones en todo el sistema.
- **Selección Dinámica (UI)**: Integración de Select2 con soporte para "tags" en los formularios de Usuarios y Reconocimientos, permitiendo seleccionar cargos existentes o crear nuevos instantáneamente.
- **Componentes de Administración**: Se ha añadido una nueva sección de gestión de cargos dentro de la lista de usuarios para facilitar la administración centralizada.
- **Estructura Técnica**: 
    - Modelo `Cargo` y controlador `CargosController`.
    - Migración `create_cargos_table` para el almacenamiento persistente.
    - Definición de rutas API-like para la carga dinámica de datos.

## [v1.3.2] - 2026-06-08
### Optimización de Perfil Institucional
- **Interfaz de Perfil**: Rediseño de la vista de perfil para mostrar de forma clara y profesional la Unidad Orgánica, el Rol asignado y el Estado de cuenta del usuario.
- **Componentes Jetstream**: Actualización de `update-profile-information-form.blade.php` para integrar campos informativos (solo lectura) que reflejan la jerarquía institucional.
- **Visualización de Estados**: Implementación de badges dinámicos para el estado de cuenta (Activo, Pendiente, Inactivo) siguiendo el sistema de colores de la plantilla.

## [v1.3.1] - 2026-06-08
### Refinamiento UI/UX y Configuración
- **Identidad Visual**: Implementación de soporte para favicon dinámico y ajustes en la configuración institucional.
- **Layouts y Estilos**: Optimización de `commonMaster` y `contentNavbarLayout` para una mejor integración visual. Actualización de `pulso-ugel.css` con efectos de interactividad mejorados.
- **Vistas de Módulos**: Refinamiento estético de las interfaces de PACI, Matriz de Riesgos, Actas de Comité y Autoevaluación.
- **Perfil de Usuario**: Actualización de la vista de perfil para mantener la consistencia con la línea gráfica institucional.
- **Estructura Técnica**: Nueva migración para la gestión de favicons en la tabla de configuración.

## [v1.3.0] - 2026-06-08 (Sesión Actual)
### Módulo de Control Interno (SCI) e Integridad
- **PACI (Programa Anual de Control Interno)**: Implementación de la gestión del Programa Anual de Control Interno.
- **Matriz de Riesgos**: Sistema para la identificación, evaluación y respuesta a riesgos institucionales.
- **Actas de Comité**: Registro y seguimiento de actas del comité de control interno.
- **Autoevaluación SCI**: Módulo interactivo para la autoevaluación del Sistema de Control Interno.
- **Modelo de Integridad**: Estructura para el seguimiento de compromisos y pilares de integridad.
- **Seguridad y Accesos**: Configuración de roles y permisos específicos para los nuevos módulos en `RolesPermisosSeeder.php`.

### Estructura Técnica
- **Modelos**: `Paci`, `MatrizRiesgo`, `ActaComite`, `Autoevaluacion`, `AutoevaluacionRespuesta`, `IntegridadCompromiso`.
- **Controladores**: `PaciController`, `MatrizRiesgosController`, `ActasComiteController`, `AutoevaluacionController`.
- **Migraciones**: Tablas de `paci`, `matriz_riesgos`, `actas_comite`, `autoevaluacion_sci` e `integridad_pilares`.
- **Rutas**: Definición de rutas seguras para la administración de los nuevos componentes en `web.php`.

## [v1.2.0] - 2026-06-07
### Implementación Integral de SATA-QR
- **Dashboard Ejecutivo**: Panel de control con indicadores de gestión en tiempo real y KPIs institucionales.
- **Control Interno & Integridad**: Módulos para el seguimiento de actividades, evidencias y cumplimiento del Modelo de Integridad.
- **Sistema de Alertas**: Notificaciones automáticas por correo electrónico y panel de gestión de alertas institucionales.
- **Reconocimientos**: Gestión de trabajadores destacados y resoluciones de felicitación.
- **Ranking de Unidades**: Sistema de puntuación y semaforización para las unidades orgánicas.
- **Administración de Componentes**: Panel para la gestión de la estructura del sistema.
- **Capa de Servicios**: Implementación de `ImageService` para la gestión profesional de archivos y fotos de perfil.
- **Identidad Institucional**: Personalización de Login, Navbar, Menú y Perfiles con la imagen de la UGEL Huacaybamba.
- **Localización**: Soporte completo para español y configuración de datos geográficos (Ubigeo).

### Estructura Técnica
- **Modelos**: Actividad, Alerta, HistorialRanking, TrabajadorDestacado, UnidadOrganica, ConfiguracionInstitucional.
- **Servicios**: `app/Services/ImageService.php`.
- **UI/UX**: Estilos personalizados en `resources/assets/css/pulso-ugel.css` y componentes Blade optimizados.

---
*Punto de control: Todos los cambios han sido confirmados en el repositorio local (starter-kit).*
