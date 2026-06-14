# SATA-QR — Proyecto PULSO (UGEL Huacaybamba)
## Sistema de Control Interno e Integridad Institucional [v2.1.3]

![Version](https://img.shields.io/badge/version-2.1.3-blue.svg)
![Status](https://img.shields.io/badge/status-ready--for--deployment-green.svg)
![Environment](https://img.shields.io/badge/environment-shared--hosting--simulated-orange.svg)

### 🚀 Introducción
SATA-QR (PULSO) es la plataforma oficial de la UGEL Huacaybamba diseñada para la gestión, monitoreo y seguimiento del **Sistema de Control Interno (SCI)** y el **Modelo de Integridad Institucional**. Este sistema permite a las unidades orgánicas registrar actividades, subir evidencias y visualizar el cumplimiento normativo mediante tableros dinámicos y semaforización en tiempo real.

### 🌟 Características Principales (v2.1.3)
- **Buscador Inteligente**: Sistema de búsqueda global sensible a los permisos del usuario.
- **Seguridad Granulada**: Refactorización v2 con más de 60 permisos específicos por módulo y acción.
- **Gestión Administrativa**: Panel Pro para usuarios con reseteo AJAX y cambio de roles fluido.
- **Identidad Institucional**: Interfaz premium personalizada con los colores y branding de la UGEL Huacaybamba.
- **Control de Acceso**: Registro público deshabilitado; acceso exclusivo mediante gestión administrativa.
- **Semaforización de Avance**: Tableros visuales para el seguimiento de metas por unidad orgánica.

### 🛠️ Requisitos del Entorno
- **PHP**: ^8.2 (Entorno Laragon simulando Hosting Compartido)
- **Base de Datos**: MySQL 8.0+
- **Servidor Web**: Apache (.htaccess configurado para subdirectorios)
- **Framework**: Laravel 12 + Vuexy Template (Bootstrap 5)

### 📦 Instalación Local (Simulacro Hosting)
1. **Clonar el repositorio**:
   ```bash
   git clone [URL-DEL-REPO] ugelhuacaybamba
   ```
2. **Configurar el entorno**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. **Migraciones y Datos Maestros**:
   ```bash
   # Importar el esquema base
   # O ejecutar las migraciones (solo en local)
   php artisan migrate --seed
   ```
4. **Compilación de Assets**:
   ```bash
   npm install
   npm run build
   ```

### 🔐 Seguridad y Despliegue (Hosting Compartido)
- **Base de Datos**: No se permite la ejecución de migraciones en producción. Utilizar el script `mysql-schema.sql` y las actualizaciones SQL manuales proporcionadas en cada versión.
- **Rutas**: El sistema está configurado para funcionar en la subcarpeta `ugelhuacaybamba/` mediante el `.htaccess` de la raíz.
- **Assets**: Siempre usar el helper `asset()` para garantizar la compatibilidad de rutas en el hosting.

---
*PULSO UGEL — Promoviendo una gestión íntegra, transparente y eficiente.*
