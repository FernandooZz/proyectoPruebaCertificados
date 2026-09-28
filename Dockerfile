# PHP 8.2 + Apache
FROM php:8.2-apache

# Dependencias necesarias
RUN apt-get update && apt-get install -y \
    pkg-config \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-install pdo pdo_mysql gd mbstring \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Habilitar rewrite
RUN a2enmod rewrite

# Permitir .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copiar backend completo
COPY backend/ /var/www/html/

# Apache debe servir backend/src
ENV APACHE_DOCUMENT_ROOT=/var/www/html/src

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

EXPOSE 80