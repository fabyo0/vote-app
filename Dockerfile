FROM serversideup/php:8.3-fpm-nginx

WORKDIR /var/www/html

# Install additional PHP extensions
RUN install-php-extensions exif gd imagick

# Copy composer files
COPY composer.json composer.lock* ./

# Install dependencies (ignore platform requirements temporarily)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs || \
    composer install --no-dev --no-scripts --prefer-dist

# Copy application
COPY . .

# Generate optimized autoload
RUN composer dump-autoload --optimize

# Set correct permissions
RUN chown -R webuser:webgroup /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Laravel optimization
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 8080
