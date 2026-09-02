#!/bin/sh
set -e

# Espera a que MySQL acepte conexiones antes de seguir (sin esto, la
# primera migración puede fallar por una carrera con el arranque de MySQL).
echo "Esperando a MySQL en ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
until php -r "new PDO('mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    sleep 2
done
echo "MySQL disponible."

# APP_KEY: se genera solo si falta (no pisa una ya definida en .env/entorno).
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

php artisan storage:link || true
php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

exec "$@"
