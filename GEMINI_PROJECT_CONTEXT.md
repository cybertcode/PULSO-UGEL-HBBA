# Historial de Versiones - Proyecto PULSO (SATA-QR - UGEL Huacaybamba)

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
