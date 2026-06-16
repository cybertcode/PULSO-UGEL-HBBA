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

> **InMotionHosting usa PHP 8.1 por defecto.** El proyecto requiere PHP 8.3.  
> El servidor tiene PHP 8.3 disponible en `/opt/cpanel/ea-php83/` — usar siempre rutas completas para no afectar otros proyectos del hosting.

```bash
/opt/cpanel/ea-php83/root/usr/bin/php /opt/cpanel/composer/bin/composer install --no-dev --optimize-autoloader --no-interaction
```

También configurar PHP 8.3 en cPanel para el subdominio:  
**cPanel → MultiPHP Manager → pulso.ugelhuacaybamba.edu.pe → PHP 8.3**

### 2.4 Generar APP_KEY (si es primera instalación)

```bash
# Solo si APP_KEY está vacío en .env, sino saltar este paso
/opt/cpanel/ea-php83/root/usr/bin/php artisan key:generate
```

### 2.5 Ejecutar migraciones y seeders

```bash
/opt/cpanel/ea-php83/root/usr/bin/php artisan migrate --force
/opt/cpanel/ea-php83/root/usr/bin/php artisan db:seed --force
```

### 2.6 Crear enlace simbólico de storage

```bash
/opt/cpanel/ea-php83/root/usr/bin/php artisan storage:link
```

### 2.7 Optimizar para producción

```bash
/opt/cpanel/ea-php83/root/usr/bin/php artisan config:cache
/opt/cpanel/ea-php83/root/usr/bin/php artisan route:cache
/opt/cpanel/ea-php83/root/usr/bin/php artisan view:cache
/opt/cpanel/ea-php83/root/usr/bin/php artisan event:cache
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
/opt/cpanel/ea-php83/root/usr/bin/php artisan down
git pull origin main
/opt/cpanel/ea-php83/root/usr/bin/php /opt/cpanel/composer/bin/composer install --no-dev --optimize-autoloader
/opt/cpanel/ea-php83/root/usr/bin/php artisan migrate --force
/opt/cpanel/ea-php83/root/usr/bin/php artisan config:cache
/opt/cpanel/ea-php83/root/usr/bin/php artisan route:cache
/opt/cpanel/ea-php83/root/usr/bin/php artisan view:cache
/opt/cpanel/ea-php83/root/usr/bin/php artisan up
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
