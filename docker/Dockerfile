FROM composer:2.8.12 AS composer

FROM php:8.4-cli

# Install system dependencies needed for Composer & SQLite
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      git \
      unzip \
      libzip-dev \
      sqlite3 \
      libsqlite3-dev \
      && rm -rf /var/lib/apt/lists/*

# PHP extensions needed for Testbench + Coverage
RUN docker-php-ext-install pdo pdo_sqlite zip && \
    pecl install pcov && docker-php-ext-enable pcov

COPY memory.ini /usr/local/etc/php/conf.d/

# Install Composer (from official image)
COPY --from=composer /usr/bin/composer /usr/bin/composer