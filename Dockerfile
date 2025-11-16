FROM serversideup/php:8.3-fpm-nginx

WORKDIR /var/www/html

# Install additional PHP extensions
RUN install-php-extensions exif gd imagick

# Copy composer files
COPY composer.json composer.lock* ./

# Install dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# Copy application
COPY . .

# Generate optimized autoload
RUN composer dump-autoload --optimize

# Create ALL necessary directories
RUN mkdir -p storage/app/public \
    && mkdir -p storage/framework/cache/data \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# Set correct permissions
RUN chown -R webuser:webgroup /var/www/html \
    && chmod -R 775 storage \
    && chmod -R 775 bootstrap/cache

EXPOSE 8080
