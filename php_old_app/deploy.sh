#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

echo "🚀 Iniciando despliegue..."

# Asegura permisos mínimos para Laravel
if [ -d storage ]; then
  find storage -type d -exec chmod 775 {} \; || true
  find storage -type f -exec chmod 664 {} \; || true
fi
if [ -d bootstrap/cache ]; then
  chmod -R 775 bootstrap/cache || true
fi

# Instalar dependencias PHP
echo "📦 Instalando dependencias PHP..."
composer install --no-dev --optimize-autoloader

# Instalar dependencias Node.js y compilar assets
echo "📦 Instalando dependencias Node.js..."
if [ -f package.json ]; then
  npm ci
  npm run build
fi

# Enlazar storage
echo "🔗 Enlazando storage..."
php artisan storage:link || true

# Detectar si es el primer despliegue
# Verificamos si existe la tabla migrations en la base de datos
FIRST_DEPLOY=false
if ! php artisan migrate:status &>/dev/null; then
  FIRST_DEPLOY=true
  echo "✨ Detectado primer despliegue"
fi

# Migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

# Seeders
echo "🌱 Ejecutando seeders..."
php artisan db:seed --force

# Ejecutar factories solo en el primer despliegue
if [ "$FIRST_DEPLOY" = true ]; then
  echo "🏭 Ejecutando factories (primer despliegue)..."
  
  # Verificar si existe un seeder específico para factories
  if [ -f database/seeders/DatabaseSeeder.php ]; then
    # Si el DatabaseSeeder ya ejecuta factories, no hacer nada más
    echo "✅ Factories ejecutadas a través de seeders"
  else
    # Ejecutar factories manualmente si es necesario
    # Descomenta y ajusta según tus necesidades:
    # php artisan tinker --execute="
    #   \App\Models\User::factory()->count(10)->create();
    #   // Agrega más factories aquí según necesites
    # "
    echo "ℹ️  Si necesitas ejecutar factories, edita este script o crea un seeder"
  fi
fi

# Optimizaciones
echo "⚡ Optimizando aplicación..."
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reinicios de servicios comunes (ajusta si es necesario)
if command -v systemctl >/dev/null 2>&1; then
  echo "🔄 Reiniciando servicios..."
  # Detectar si es Ubuntu (php8.2-fpm) o Amazon Linux (php-fpm)
  if systemctl list-units --type=service | grep -q php8.2-fpm; then
    sudo systemctl restart php8.2-fpm || true
  else
    sudo systemctl restart php-fpm || true
  fi
  # Reiniciar queue worker si existe
  if systemctl list-units --type=service | grep -q laravel-queue; then
    sudo systemctl restart laravel-queue || true
  fi
fi

echo "✅ Despliegue finalizado con éxito"


