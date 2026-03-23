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
until php artisan migrate --force --no-interaction; do
    echo "Aguardando banco de dados..."
    sleep 2
done

# Pega o contador de forma limpa (tinker às vezes retorna lixo/novas linhas)
USUARIOS_COUNT=$(php artisan tinker --execute="echo \DB::table('usuario')->count();" | grep -o '[0-9]*' | tail -1)

if [ "$USUARIOS_COUNT" = "0" ] || [ -z "$USUARIOS_COUNT" ]; then
    echo "Banco vazio detectado. Semeando dados iniciais..."
    php artisan db:seed --force --no-interaction
fi

exec "$@"
