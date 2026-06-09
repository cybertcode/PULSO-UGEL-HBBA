# Historial de Versiones - Proyecto SATA-QR (UGEL Huacaybamba)

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
