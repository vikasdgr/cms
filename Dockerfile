FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk update && apk add --no-cache \
   zip unzip git curl libpng-dev libxml2-dev oniguruma-dev libzip-dev \
   bash mysql-client \
   && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Copy composer files first for better layer caching
COPY composer*.json ./

# Install PHP dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --optimize-autoloader

# Copy application files
COPY . .

# Set git safe directory
RUN git config --global --add safe.directory /var/www/html

# Complete composer installation
RUN composer dump-autoload --optimize

# Copy and make initialization script executable
COPY docker-init.sh /usr/local/bin/docker-init.sh
RUN chmod +x /usr/local/bin/docker-init.sh

# Create necessary directories and set permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-init.sh"]
