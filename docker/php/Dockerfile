ARG COMPOSER_VERSION=2
ARG PHP_VERSION=8.2

FROM composer:$COMPOSER_VERSION AS composer
FROM php:${PHP_VERSION}-fpm

RUN apt-get update && apt-get install -y \
    openssl \
    unzip \
    libzip-dev

# UID/GID passati da docker-compose
ARG UID=1000
ARG GID=1000

# Crea gruppo e utente con stessi UID/GID dell'utente host
RUN groupadd -g $GID developer \
    && useradd -m -u $UID -g $GID developer

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Install a pinned Xdebug release that is available for the configured PHP version.
RUN pecl install xdebug-3.5.1  \
&& docker-php-ext-enable xdebug

# Enable opcache only for PHP versions < 8.5 (built-in from 8.5+)
RUN if [ "$(php -r 'echo version_compare(PHP_VERSION, "8.5.0", "<");')" = "1" ]; then \
        docker-php-ext-enable opcache; \
    fi

COPY php-custom.ini /usr/local/etc/php/conf.d/docker-php-custom.ini
COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

ARG COMPOSE_PROJECT_NAME
WORKDIR /var/${COMPOSE_PROJECT_NAME}

USER developer
