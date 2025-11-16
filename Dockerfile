FROM serversideup/php:8.3-fpm-nginx

# Switch to root for installations
USER root

WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libmagickwand-dev \
    && docker-php-ext-install exif \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

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
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Set correct permissions
RUN chown -R webuser:webgroup /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Switch back to webuser
USER webuser

EXPOSE 8080
