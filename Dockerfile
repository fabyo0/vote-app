FROM serversideup/php:8.3-fpm-nginx

USER root

WORKDIR /var/www/html

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    libmagickwand-dev \
    && docker-php-ext-install exif \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy composer files
COPY composer.json composer.lock* ./

# Install dependencies
RUN composer install --prefer-dist --no-scripts --no-autoloader --ignore-platform-reqs

# Copy application
COPY . .

# Generate optimized autoload
RUN composer dump-autoload --optimize --no-scripts

# Create directories
RUN mkdir -p storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 777 storage bootstrap/cache

# Run Laravel optimizations during build
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

USER www-data

EXPOSE 8080
