#!/bin/bash
# Script de despliegue para producción - PULSO UGEL
# Ejecutar desde el servidor: bash deploy.sh

echo "=== PULSO UGEL - Deploy a Producción ==="

# 1. Ir al directorio del proyecto
cd /home/n763605/pulso.ugelhuacaybamba.edu.pe

# 2. Bajar el sitio a mantenimiento
php artisan down --message="Actualizando sistema..." --retry=60

# 3. Actualizar código desde Git
git pull origin main

# 4. Instalar/actualizar dependencias PHP (sin dev)
composer install --no-dev --optimize-autoloader --no-interaction

# 5. Copiar .env de producción si no existe
if [ ! -f .env ]; then
    cp .env.production .env
    echo "Archivo .env creado desde .env.production"
fi

# 6. Limpiar y optimizar cachés
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 7. Ejecutar migraciones
php artisan migrate --force

# 8. Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 9. Crear enlace simbólico storage (solo la primera vez)
php artisan storage:link

# 10. Permisos de directorios
chmod -R 775 storage bootstrap/cache
chown -R nobody:nobody storage bootstrap/cache

# 11. Volver a subir el sitio
php artisan up

echo "=== Deploy completado exitosamente ==="
echo "URL: https://pulso.ugelhuacaybamba.edu.pe"
