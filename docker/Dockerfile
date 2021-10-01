FROM php:fpm-alpine3.14

RUN apk add icu-dev musl-dev zip libzip-dev freetype-dev libpng libpng-dev libjpeg-turbo-dev libxml2 libxslt-dev libwebp-dev zlib-dev libxpm-dev --no-cache \
    coreutils \
    php7-fpm \
    php7-apcu \
    php8-ctype \
    php8-curl \
    php8-dom \
    php8-gd \
    php8-iconv \
    php7-imagick \
    openssh-client \
    imagemagick \
    imagemagick-libs \
    imagemagick-dev \
    php8-json \
    php8-intl \
    php7-mcrypt \
    php8-fileinfo\
    php8-mbstring \
    php8-opcache \
    php8-openssl \
    php8-pdo \
    php8-pdo_mysql \
    php8-mysqli \
    php8-xml \
    php8-zlib \
    php8-phar \
    php8-tokenizer \
    php8-session \
    php8-simplexml \
    php8-xdebug \
    php8-zip \
    php8-xmlwriter \
    make \
    curl
RUN apk add --no-cache $PHPIZE_DEPS && pecl install xdebug && docker-php-ext-enable xdebug
RUN apk add --update nodejs npm
RUN apk add --update npm
# Install PHP extensions
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install gd
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip
RUN docker-php-ext-install pcntl
RUN docker-php-ext-install soap

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www