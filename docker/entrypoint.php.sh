#!/bin/bash

# Read all enviroment variables or use entrypoint.php.sh.dev
set -o allexport; source .env; set +o allexport

mkdir storage/app/ssl
openssl genrsa -aes256 -passout pass:"$JWT_PASSPHRASE" -out storage/app/ssl/jwt-private.key 2048
openssl rsa -passin pass:"$JWT_PASSPHRASE" -in storage/app/ssl/jwt-private.key -pubout -out storage/app/ssl/jwt-public.key

composer install

cp -n docker/.env .

chown -R www-data:www-data .

php artisan key:generate --ansi
php artisan config:cache
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan storage:link

# Wait for MySQL
while ! mysqladmin ping -h"$DB_HOST"; do sleep 1; done

php artisan migrate:fresh --seed

set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php-fpm "$@"
fi

exec "$@"
