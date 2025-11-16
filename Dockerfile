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
RUN composer install --prefer-dist --no-scripts --no-autoloader --ignore-platform-reqs

# Copy application
COPY . .

# Generate optimized autoload
RUN composer dump-autoload --optimize --no-scripts

# Create ALL necessary directories
RUN mkdir -p storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Create entrypoint script to fix permissions on container start
RUN printf '#!/bin/bash\n\
set -e\n\
\n\
# Fix permissions every time container starts\n\
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache\n\
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache\n\
\n\
# Execute the main command\n\
exec "$@"\n' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
