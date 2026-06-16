# Guía de Despliegue - PULSO UGEL en Producción

**Estado:** ✅ Desplegado y funcionando en producción.

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
git clone https://github.com/cybertcode/PULSO-UGEL-HBBA.git pulso.ugelhuacaybamba.edu.pe
cd pulso.ugelhuacaybamba.edu.pe
```

### 2.2 Configurar el .env de producción

Subir `.env.production` por File Manager o SCP (nunca por Git, está en `.gitignore`) y renombrarlo:

```bash
cp .env.production .env
```

### 2.3 Instalar dependencias PHP

> **InMotionHosting usa PHP 8.1 por defecto.** El proyecto requiere PHP 8.2+ (se usa 8.3).
> El servidor tiene PHP 8.3 disponible en `/opt/cpanel/ea-php83/` — usar siempre rutas completas para no afectar otros proyectos del hosting compartido.

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

## PASO 3 — Enrutamiento del subdominio (método usado y confirmado)

El Document Root del subdominio en cPanel quedó apuntando a la **raíz del proyecto**:

```
/home/n763605/pulso.ugelhuacaybamba.edu.pe
```

(no a `public/`). El enrutamiento hacia `public/` lo hace el [.htaccess](.htaccess) raíz del proyecto:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

Confirmado funcionando en producción — no es necesario cambiar el Document Root en cPanel con este enfoque.

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

> Si en algún momento se reescribió el historial de Git (`push --force`), sincronizar el servidor con `git fetch && git reset --hard origin/main` en vez de `git pull` (ver incidencia #4 abajo).

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

---

## Incidencias reales resueltas durante el despliegue (referencia)

### 1. "Index of /" en vez de la web

**Causa:** faltaba el archivo `.htaccess` raíz que redirige las peticiones hacia `public/`.
**Solución:** crear [.htaccess](.htaccess) en la raíz del proyecto con `RewriteRule ^(.*)$ public/$1 [L]`. El Document Root del subdominio se deja apuntando a la raíz del proyecto, no a `public/`.

### 2. `composer install` no encuentra `composer.json`

**Causa:** se ejecutó el comando desde `/home/n763605` en lugar de la carpeta del proyecto.
**Solución:** `cd pulso.ugelhuacaybamba.edu.pe` antes de correr composer.

### 3. `Root composer.json requires php ^8.2 but your php version (8.1.34)`

**Causa:** InMotionHosting usa PHP 8.1 por defecto en la shell SSH; el proyecto requiere 8.2+.
**Solución:** usar la ruta explícita al binario de PHP 8.3 del servidor (`/opt/cpanel/ea-php83/root/usr/bin/php`) y el composer de cPanel (`/opt/cpanel/composer/bin/composer`) en **todos** los comandos `php` y `composer`. No usar alias globales para no afectar otros proyectos del hosting compartido.

### 4. `git pull` falla con "divergent branches"

**Causa:** se hizo `git filter-branch` + `push --force` en local para eliminar `.env.production` del historial de GitHub (ver incidencia #6), por lo que el historial del servidor quedó desincronizado.
**Solución:** en el servidor, `git fetch origin` seguido de `git reset --hard origin/main` (seguro porque `.env` nunca estuvo en Git).

### 5. `Call to undefined function Database\Factories\fake()` al correr el seeder

**Causa:** el helper `fake()` depende de `fakerphp/faker`, que estaba en `require-dev` de `composer.json`. Al instalar con `--no-dev` en producción, el paquete no se instala y la llamada falla.
**Solución:** mover `fakerphp/faker` de `require-dev` a `require` en [composer.json](composer.json) y cambiar `fake()` por `$this->faker` en [UserFactory.php](database/factories/UserFactory.php) (forma correcta dentro de un Factory). Luego `composer update fakerphp/faker` en local para regenerar `composer.lock`, commit, push, y en el servidor `git pull` + `composer install --no-dev` de nuevo.

### 6. `.env.production` quedó subido a GitHub

**Causa:** se había permitido temporalmente en `.gitignore` para facilitar el primer despliegue.
**Solución:** revertir el `.gitignore` (`.env.production` vuelve a estar ignorado) y limpiar el historial con:

```bash
git stash
git filter-branch --force --index-filter "git rm --cached --ignore-unmatch .env.production" --prune-empty --tag-name-filter cat -- --all
git push --force origin main develop
git push --force origin refs/tags/v1.5.3 refs/tags/v1.5.4 refs/tags/v2.1.4-deployed
```

**Importante:** después de un `push --force` que reescribe el historial, cualquier clon existente (incluido el del servidor) debe sincronizarse con `git fetch && git reset --hard origin/main`, no con `git pull`.

### 7. `composer.lock` desactualizado tras mover `fakerphp/faker` a `require`

**Causa:** editar `composer.json` manualmente sin actualizar `composer.lock` causa error de instalación (`lock file is not up to date`).
**Solución:** correr `composer update fakerphp/faker --no-interaction` en local (no `composer update` completo, para no actualizar otras dependencias sin revisión) y subir ambos archivos juntos.
