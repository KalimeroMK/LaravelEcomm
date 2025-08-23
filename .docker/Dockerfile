# Stage 1: Build dependencies
FROM php:8.4-fpm-alpine AS builder

# Install system dependencies
RUN apk add --no-cache \
    git curl nano unzip bash zip \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libxml2-dev oniguruma-dev libzip-dev \
    icu-dev g++ make autoconf libpng libjpeg freetype libzip \
    redis  # Install Redis server (optional, if needed)

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd pdo_mysql mbstring exif pcntl bcmath zip intl

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Set user permissions (проверка дали `www-data` постои)
RUN id -u www-data &>/dev/null || adduser -D -u 1000 -G www-data www-data

# Stage 2: Final image (Minimal)
FROM php:8.4-fpm-alpine

# Install runtime dependencies (ова е клучно за правилно работење)
RUN apk add --no-cache libpng libjpeg freetype libzip icu redis

# Copy built dependencies from builder
COPY --from=builder /usr/local /usr/local
COPY --from=builder /usr/bin/composer /usr/bin/composer

# Enable Redis PHP extension
RUN docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www

# Set user
USER www-data
