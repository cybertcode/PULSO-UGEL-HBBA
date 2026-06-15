# Usuarios de Prueba — PULSO UGEL Huacaybamba

> **URL local:** http://localhost/VUEXY-OK/starter-kit/public
> Ejecutar antes: `php artisan migrate:fresh --seed`

---

## Credenciales rápidas para copiar

```
# Super Admin (desarrollo)
admin@admin.com / Admin123

# Administrador
director@ugelhuacaybamba.edu.pe / Ugel@2024

# Coordinador SCI
sci@ugelhuacaybamba.edu.pe / Ugel@2024

# Responsables de Unidad
administracion@ugelhuacaybamba.edu.pe / Ugel@2024
pedagogia@ugelhuacaybamba.edu.pe / Ugel@2024
contabilidad@ugelhuacaybamba.edu.pe / Ugel@2024

# Operadores
logistica@ugelhuacaybamba.edu.pe / Ugel@2024
rrhh@ugelhuacaybamba.edu.pe / Ugel@2024
tesoreria@ugelhuacaybamba.edu.pe / Ugel@2024
infraestructura@ugelhuacaybamba.edu.pe / Ugel@2024
especialista.agi@ugelhuacaybamba.edu.pe / Ugel@2024
especialista.agp@ugelhuacaybamba.edu.pe / Ugel@2024
especialista.inf@ugelhuacaybamba.edu.pe / Ugel@2024
contador@ugelhuacaybamba.edu.pe / Ugel@2024
especialista.inicial@ugelhuacaybamba.edu.pe / Ugel@2024
especialista.primaria@ugelhuacaybamba.edu.pe / Ugel@2024
especialista.secundaria@ugelhuacaybamba.edu.pe / Ugel@2024

# Visualizadores
asesoria@ugelhuacaybamba.edu.pe / Ugel@2024
monitor@ugelhuacaybamba.edu.pe / Ugel@2024
secretaria@ugelhuacaybamba.edu.pe / Ugel@2024
```

---

## Super Admin

| Campo | Valor |
|-------|-------|
| **Email** | `admin@admin.com` |
| **Contraseña** | `Admin123` |
| **Nombre** | Administrador Dev |
| **DNI** | `00000000` |
| **Unidad** | — (sin unidad) |

> **Acceso:** Bypasa TODOS los Gates vía `Gate::before()`. No tiene permisos asignados en BD — simplemente los salta todos.
> **Diferencia con Administrador:** puede gestionar los roles del sistema. Solo para desarrollo/emergencias.

---

## Administrador

| Campo | Valor |
|-------|-------|
| **Email** | `director@ugelhuacaybamba.edu.pe` |
| **Contraseña** | `Ugel@2024` |
| **Nombre** | Mg. Julio Luis Lozano Yllatopa |
| **Cargo** | Director(a) de UGEL |
| **Unidad** | DIR — Dirección |
| **DNI** | `42185634` |

> **Acceso:** Gestión institucional completa — usuarios, configuración, unidades, landing, todos los módulos SCI/Integridad/Encuestas.
> **NO puede:** gestionar roles del sistema (`roles.*` — exclusivo Super Admin).

---

## Coordinador SCI

| Campo | Valor |
|-------|-------|
| **Email** | `sci@ugelhuacaybamba.edu.pe` |
| **Contraseña** | `Ugel@2024` |
| **Nombre** | Carlos Alberto Flores Mendoza |
| **Cargo** | Coordinador(a) de Control Interno |
| **Unidad** | AGI — Gestión Institucional |
| **DNI** | `43297851` |

> **Acceso:** Gestión operativa completa SCI e Integridad en toda la institución. Puede eliminar actividades, validar/eliminar evidencias, exportar reportes, gestionar encuestas completas.
> **NO puede:** gestionar usuarios, configuración institucional, unidades orgánicas, landing.

---

## Responsables de Unidad

Contraseña: **`Ugel@2024`**

| Email | Nombre | Unidad | DNI |
|-------|--------|--------|-----|
| `administracion@ugelhuacaybamba.edu.pe` | Rosa Isabel Vargas Tarazona | OAD | `44512378` |
| `pedagogia@ugelhuacaybamba.edu.pe` | Jorge Luis Ramírez Castillo | AGP | `45623894` |
| `contabilidad@ugelhuacaybamba.edu.pe` | Ana Lucía Torres Espinoza | CONT | `46734521` |

> **Acceso:** Crea y edita actividades de su propia unidad. Puede subir evidencias y crear alertas para su unidad. Puede crear y editar buenas prácticas y recomendaciones. Ve resultados de encuestas.
> **NO puede:** eliminar actividades ni evidencias, validar evidencias, exportar reportes, crear encuestas.

---

## Operadores

Contraseña: **`Ugel@2024`**

| Email | Nombre | Unidad | DNI |
|-------|--------|--------|-----|
| `logistica@ugelhuacaybamba.edu.pe` | Pedro Antonio Huanca Mamani | LOG | `47845632` |
| `rrhh@ugelhuacaybamba.edu.pe` | Lucía Fernández Ríos | RR_HH | `48956743` |
| `tesoreria@ugelhuacaybamba.edu.pe` | Juan Carlos Soto Benites | TESOR | `49067854` |
| `infraestructura@ugelhuacaybamba.edu.pe` | Sandra Milagros León Coronado | INF | `40178965` |
| `especialista.agi@ugelhuacaybamba.edu.pe` | Patricia Soledad Mejía Sánchez | AGI | `47123456` |
| `especialista.agp@ugelhuacaybamba.edu.pe` | Marco Antonio Príncipe López | AGP | `44512367` |
| `especialista.inf@ugelhuacaybamba.edu.pe` | Sofía Alejandra Vega Castillo | INF | `48234567` |
| `contador@ugelhuacaybamba.edu.pe` | Luis Alberto Quispe Mamani | CONT | `43125698` |
| `especialista.inicial@ugelhuacaybamba.edu.pe` | Yolanda Esperanza Condori Huanca | AGP | `44398712` |
| `especialista.primaria@ugelhuacaybamba.edu.pe` | Raúl Ernesto Meza Tucto | AGP | `46587234` |
| `especialista.secundaria@ugelhuacaybamba.edu.pe` | Mirtha Jacqueline Soto Villanueva | AGP | `47891234` |

> **Acceso:** Actualiza avances en actividades asignadas, sube evidencias. Ve reportes y semáforo. Solo responde encuestas.
> **NO puede:** crear actividades, crear alertas, acceder a cumplimiento, gestionar buenas prácticas ni recomendaciones.

---

## Visualizadores

Contraseña: **`Ugel@2024`**

| Email | Nombre | Unidad | DNI |
|-------|--------|--------|-----|
| `asesoria@ugelhuacaybamba.edu.pe` | Roberto Enrique Chávez Palacios | ASESOR | `41289076` |
| `monitor@ugelhuacaybamba.edu.pe` | Karina Beatriz Huanca Quispe | AGI | `46321987` |
| `secretaria@ugelhuacaybamba.edu.pe` | Fernando José Ramos Delgado | DIR | `45789231` |

> **Acceso:** Solo lectura total. Ve cumplimiento y reconocimientos (a diferencia del Operador que no ve cumplimiento). Solo responde encuestas.
> **NO puede:** crear, editar ni eliminar ningún registro.

---

## Diferencia real entre todos los roles

| Permiso | Super Admin | Administrador | Coord. SCI | Resp. Unidad | Operador | Visualizador |
|---------|:-----------:|:-------------:|:----------:|:------------:|:--------:|:------------:|
| **SISTEMA** |
| `roles.*` (gestionar roles) | ✅ bypass | ❌ | ❌ | ❌ | ❌ | ❌ |
| `usuarios.*` | ✅ bypass | ✅ | ❌ | ❌ | ❌ | ❌ |
| `configuracion.*` | ✅ bypass | ✅ | ❌ | ❌ | ❌ | ❌ |
| `unidades.*` / `slider.*` / `instituciones.*` | ✅ bypass | ✅ | ❌ | ❌ | ❌ | ❌ |
| **SCI / INTEGRIDAD** |
| `.eliminar` actividades | ✅ bypass | ✅ | ✅ | ❌ | ❌ | ❌ |
| `.crear` actividades | ✅ bypass | ✅ | ✅ | ✅ | ❌ | ❌ |
| `.editar` actividades (avance) | ✅ bypass | ✅ | ✅ | ✅ | ✅ | ❌ |
| `componentes.*` (estructura) | ✅ bypass | ✅ | ✅ | solo `.ver` | solo `.ver` | solo `.ver` |
| **EVIDENCIAS** |
| `evidencias.validar` | ✅ bypass | ✅ | ✅ | ❌ | ❌ | ❌ |
| `evidencias.eliminar` | ✅ bypass | ✅ | ✅ | ❌ | ❌ | ❌ |
| `evidencias.crear` (subir) | ✅ bypass | ✅ | ✅ | ✅ | ✅ | ❌ |
| **ALERTAS** |
| `alertas.eliminar` | ✅ bypass | ✅ | ✅ | ❌ | ❌ | ❌ |
| `alertas.crear` | ✅ bypass | ✅ | ✅ | ✅ | ❌ | ❌ |
| **REPORTES** |
| `cumplimiento.exportar` / `reportes.exportar` | ✅ bypass | ✅ | ✅ | ❌ | ❌ | ❌ |
| `cumplimiento.ver` | ✅ bypass | ✅ | ✅ | ✅ | ❌ | ✅ |
| `reconocimientos.ver` | ✅ bypass | ✅ | ✅ | ✅ | ❌ | ✅ |
| **BUENAS PRÁCTICAS** |
| `.eliminar` | ✅ bypass | ✅ | ✅ | ❌ | ❌ | ❌ |
| `.crear` / `.editar` | ✅ bypass | ✅ | ✅ | ✅ | ❌ | ❌ |
| **RECOMENDACIONES** |
| `.eliminar` | ✅ bypass | ✅ | ✅ | ❌ | ❌ | ❌ |
| `.crear` / `.editar` | ✅ bypass | ✅ | ✅ | ✅ | ❌ | ❌ |
| **NORMATIVAS** |
| `.crear` / `.editar` / `.eliminar` | ✅ bypass | ✅ | ✅ | ❌ | ❌ | ❌ |
| **ENCUESTAS** |
| `.publicar` / `.exportar` / `.resultados` | ✅ bypass | ✅ | ✅ | solo `.resultados` | ❌ | ❌ |
| `.crear` / `.editar` / `.eliminar` | ✅ bypass | ✅ | ✅ | ❌ | ❌ | ❌ |
| `.responder` | ✅ bypass | ✅ | ✅ | ✅ | ✅ | ✅ |

---

## Usuarios faker adicionales (generados aleatoriamente)

| Rol | Cantidad | Estado |
|-----|----------|--------|
| Operador | 8 | activo |
| Visualizador | 5 | activo |
| Responsable de Unidad | 3 | activo |
| Coordinador SCI | 1 | activo |
| Visualizador | 2 | **sin verificar** (para probar email verification) |
| Operador | 1 | **inactivo** (para probar bloqueo de login) |
| Operador | 1 | **pendiente** (para probar estado pendiente) |

---

## Datos de prueba generados

| Módulo | Datos |
|--------|-------|
| Actividades genéricas por unidad | ~70 (7 tipos × 10 unidades) |
| Actividades SCI 2026 | 29 (una por pregunta SCI) |
| Actividades Integridad 2026 | 23 (una por pregunta Integridad) |
| Buenas Prácticas implementadas | 14 |
| Proyectos del Concurso (todos los estados) | 9 |
| Recomendaciones | 13 (atendidas, en proceso, pendientes) |
| Alertas de prueba | 18 (vencimiento, por vencer, avance bajo, sin evidencia, sistema, resueltas) |
| Reconocimientos históricos | 10 meses (ago 2025 – may 2026) |
| Trabajadores destacados | 6 (ene–mar 2026) |
| Historial ranking | Mayo y Junio 2026 |
