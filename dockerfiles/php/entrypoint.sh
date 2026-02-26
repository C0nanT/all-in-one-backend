#!/bin/sh
set -e
# Ownership for PHP-FPM; then permissive so host user can edit/delete (e.g. laravel.log)
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 777 /var/www/storage /var/www/bootstrap/cache
exec "$@"
