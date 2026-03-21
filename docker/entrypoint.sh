#!/bin/sh

if [ ! -f ".env" ]; then
    cp .env.example .env
fi

if [ ! -d "vendor" ]; then
    composer install --no-interaction --optimize-autoloader
fi

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    php artisan key:generate --no-interaction
fi

php artisan jwt:secret --force --no-interaction

echo "Esperando o banco de dados iniciar..."
sleep 5

php artisan migrate --force --no-interaction

USUARIOS_COUNT=$(php artisan tinker --execute="echo \App\Models\Usuario::count()")

if [ "$USUARIOS_COUNT" = "0" ]; then
    echo "Banco vazio détectado. Semeando dados iniciais..."
    php artisan db:seed --force --no-interaction
fi

exec "$@"
