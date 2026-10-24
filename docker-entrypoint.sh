#!/bin/bash
set -e

# Dynamically set Apache port from Render's $PORT (defaults to 80 if not set)
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Ensure upload and storage directories exist with write permissions
mkdir -p /var/www/html/public/images/banners \
         /var/www/html/storage/app/public/modules \
         /var/www/html/storage/app/public/documents \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/images
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/images

# Ensure storage link
php artisan storage:link || true

# Run config and route caches for production speed
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec apache2-foreground
