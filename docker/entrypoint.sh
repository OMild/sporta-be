#!/bin/bash
set -e

# Create log directory
mkdir -p /var/log/supervisor

# Check if Laravel is installed (check vendor/autoload.php for complete install)
if [ ! -f /var/www/vendor/autoload.php ]; then
    echo "Installing Laravel..."
    cd /var/www

    # Remove any existing files that might block installation
    rm -rf /var/www/* /var/www/.[!.]* 2>/dev/null || true

    composer create-project laravel/laravel . --prefer-dist --no-interaction

    # Set proper permissions
    chown -R www-data:www-data /var/www
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache
fi

# Set permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# Wait for MySQL to be ready
echo "Waiting for MySQL..."
while ! php -r "try { new PDO('mysql:host=mysql;port=3306', 'root', '${DB_PASSWORD:-secret}'); echo 'OK'; } catch(Exception \$e) { exit(1); }" 2>/dev/null; do
    sleep 2
done
echo "MySQL is ready!"

# Run migrations if artisan exists
if [ -f /var/www/artisan ]; then
    cd /var/www

    # Generate key if not set
    php artisan key:generate --force --no-interaction 2>/dev/null || true

    # Clear and cache config
    php artisan config:clear
    php artisan cache:clear

    # Run migrations
    php artisan migrate --force --no-interaction 2>/dev/null || true
fi

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
