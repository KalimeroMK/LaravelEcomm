# Use FrankenPHP base image
FROM dunglas/frankenphp:latest

# Install PHP extensions using the built-in installer
RUN install-php-extensions \
    pdo_mysql \
    gd \
    mbstring \
    exif \
    pcntl \
    bcmath \
    zip \
    intl \
    opcache \
    redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Create www-data user if not exists
RUN set -x \
    && addgroup -g 1000 -S www-data 2>/dev/null || true \
    && adduser -u 1000 -D -S -G www-data www-data 2>/dev/null || true

# Fix permissions for www-data
RUN chown -R www-data:www-data /var/www

# Create data directory for Caddy with proper permissions
RUN mkdir -p /data/caddy && chmod -R 777 /data/caddy || true

# Note: FrankenPHP/Caddy needs to run as root to bind to port 80
# The PHP processes will still run as www-data for security

# Expose port 80 for FrankenPHP
EXPOSE 80

# Start FrankenPHP with Caddy configuration
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
