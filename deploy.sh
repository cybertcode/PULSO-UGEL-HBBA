#!/bin/bash
# Script de despliegue para producción - PULSO UGEL
# Ejecutar desde el servidor: bash deploy.sh
# IMPORTANTE: InMotionHosting usa PHP 8.1 por defecto, usar ruta explícita a PHP 8.3

PHP=/opt/cpanel/ea-php83/root/usr/bin/php
COMPOSER=/opt/cpanel/composer/bin/composer

echo "=== PULSO UGEL - Deploy a Producción ==="

# 1. Ir al directorio del proyecto
cd /home/n763605/pulso.ugelhuacaybamba.edu.pe

# 2. Bajar el sitio a mantenimiento
$PHP artisan down --message="Actualizando sistema..." --retry=60

# 3. Actualizar código desde Git
git pull origin main

# 4. Instalar/actualizar dependencias PHP (sin dev)
$PHP $COMPOSER install --no-dev --optimize-autoloader --no-interaction

# 5. Limpiar y optimizar cachés
$PHP artisan config:clear
$PHP artisan cache:clear
$PHP artisan route:clear
$PHP artisan view:clear

# 6. Ejecutar migraciones
$PHP artisan migrate --force

# 7. Optimizar para producción
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan event:cache

# 8. Crear enlace simbólico storage (solo la primera vez)
$PHP artisan storage:link

# 9. Permisos de directorios
chmod -R 775 storage bootstrap/cache

# 10. Volver a subir el sitio
$PHP artisan up

echo "=== Deploy completado exitosamente ==="
echo "URL: https://pulso.ugelhuacaybamba.edu.pe"
