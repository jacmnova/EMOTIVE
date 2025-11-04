#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

# Asegura permisos mínimos para Laravel
if [ -d storage ]; then
  find storage -type d -exec chmod 775 {} \; || true
  find storage -type f -exec chmod 664 {} \; || true
fi
if [ -d bootstrap/cache ]; then
  chmod -R 775 bootstrap/cache || true
fi

# Instalar dependencias PHP si no existen (normalmente vendor viene del CI)
if [ ! -d vendor ]; then
  composer install --no-dev --optimize-autoloader
fi

# Enlazar storage
php artisan storage:link || true

# Migraciones y seeders
php artisan migrate --force
php artisan db:seed --force

# Nota: Las factories normalmente se usan para generar datos de prueba
# Si necesitas ejecutar factories en producción, descomenta la siguiente línea
# o crea un seeder específico que use las factories
# php artisan tinker --execute="User::factory()->count(10)->create();"

# Optimizaciones
php artisan optimize

# Reinicios de servicios comunes (ajusta si es necesario)
if command -v systemctl >/dev/null 2>&1; then
  # Detectar si es Ubuntu (php8.2-fpm) o Amazon Linux (php-fpm)
  if systemctl list-units --type=service | grep -q php8.2-fpm; then
    sudo systemctl restart php8.2-fpm || true
  else
    sudo systemctl restart php-fpm || true
  fi
  sudo systemctl restart laravel-queue || true
fi

echo "Deploy finalizado con éxito"


