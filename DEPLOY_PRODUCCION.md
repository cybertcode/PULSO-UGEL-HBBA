# Guía de Despliegue - PULSO UGEL en Producción

**Servidor:** InMotionHosting  
**Subdominio:** https://pulso.ugelhuacaybamba.edu.pe  
**Raíz del servidor:** /home/n763605/pulso.ugelhuacaybamba.edu.pe

---

## PASO 1 — Preparar en local antes de subir

```bash
# Construir los assets de Vite (ya hecho si public/build existe)
npm run build

# Confirmar cambios en Git
git add .
git commit -m "chore: preparar proyecto para producción"
git push origin main
```

---

## PASO 2 — Primera vez en el servidor (via SSH)

Conectar por SSH al servidor de InMotionHosting:

```bash
ssh n763605@pulso.ugelhuacaybamba.edu.pe
```

### 2.1 Clonar el repositorio

```bash
cd /home/n763605
git clone https://github.com/TU_USUARIO/TU_REPO.git pulso.ugelhuacaybamba.edu.pe
cd pulso.ugelhuacaybamba.edu.pe
```

> Reemplaza la URL de Git con la URL real de tu repositorio.

### 2.2 Configurar el .env de producción

```bash
cp .env.production .env
```

### 2.3 Instalar dependencias PHP

```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

### 2.4 Generar APP_KEY (si es primera instalación)

```bash
# Solo si APP_KEY está vacío en .env, sino saltar este paso
php artisan key:generate
```

### 2.5 Ejecutar migraciones y seeders

```bash
php artisan migrate --force
php artisan db:seed --force
```

### 2.6 Crear enlace simbólico de storage

```bash
php artisan storage:link
```

### 2.7 Optimizar para producción

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 2.8 Permisos de directorios

```bash
chmod -R 775 storage bootstrap/cache
```

---

## PASO 3 — Verificar estructura de carpetas en el servidor

La raíz del subdominio debe apuntar a `/home/n763605/pulso.ugelhuacaybamba.edu.pe`  
(NO a la carpeta public — el `.htaccess` raíz redirige automáticamente a `public/`).

En cPanel → Subdominios → Verificar que el Document Root sea:
```
/home/n763605/pulso.ugelhuacaybamba.edu.pe
```

---

## PASO 4 — Para actualizaciones futuras

```bash
# Desde el servidor via SSH:
cd /home/n763605/pulso.ugelhuacaybamba.edu.pe
bash deploy.sh
```

O manualmente:
```bash
php artisan down
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

---

## Credenciales de acceso al sistema

- **Admin:** admin@admin.com / Admin123
- **Usuarios:** según UsuariosSeeder.php / Ugel@2024

---

## Solución de problemas comunes

| Problema | Solución |
|----------|----------|
| Error 500 | Revisar `storage/logs/laravel.log` |
| Página en blanco | Verificar permisos: `chmod -R 775 storage bootstrap/cache` |
| Error de BD | Verificar datos en `.env` con credenciales del hosting |
| Assets no cargan | Verificar que `public/build` se subió al repo |
| Storage no funciona | Ejecutar `php artisan storage:link` |
