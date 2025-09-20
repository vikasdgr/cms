#!/bin/bash
set -e

echo "Starting Laravel initialization..."

# Wait for database to be ready
echo "Waiting for database connection..."
while ! php -r "
try {
    new PDO('mysql:host=db;dbname=cms', 'laravel_user', 'secret');
    echo 'Connected successfully';
    exit(0);
} catch (PDOException \$e) {
    exit(1);
}"; do
    echo "Database not ready, waiting..."
    sleep 2
done

echo "Database connection established!"

# Copy .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# Update .env with Docker database settings
echo "Updating .env with Docker configuration..."
sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sed -i 's/DB_HOST=.*/DB_HOST=db/' .env
sed -i 's/DB_PORT=.*/DB_PORT=3306/' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=cms/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=laravel_user/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=secret/' .env

# Generate application key if it doesn't exist
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Clear any cached configuration
echo "Clearing configuration cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Install Composer dependencies if vendor directory is missing or outdated
if [ ! -d "vendor" ] || [ "composer.json" -nt "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed database if needed (uncomment if you have seeders)
# echo "Seeding database..."
# php artisan db:seed --force

# Create storage link if it doesn't exist
if [ ! -L "public/storage" ]; then
    echo "Creating storage link..."
    php artisan storage:link
fi

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Laravel initialization complete!"

# Start PHP server
echo "Starting PHP development server..."
exec php -S 0.0.0.0:80 -t public