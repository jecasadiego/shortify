# Etapa 1: dependencias PHP con Composer (sin scripts)
FROM composer:2 AS build
WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

# Instalar vendors sin ejecutar scripts (evita artisan en build)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --no-scripts --optimize-autoloader

# Copiamos el resto del proyecto y aseguramos el autoload
COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative

# Etapa 2: runtime PHP + Apache
FROM php:8.2-apache

# Extensiones + rewrite
RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

WORKDIR /var/www/html

# Copia de la app desde la etapa build (como ya lo tienes)
COPY --from=build /app ./

# DocumentRoot = public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
# Ajustar VirtualHost y habilitar .htaccess de Laravel
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf \
    && sed -ri -e 's/AllowOverride\s+None/AllowOverride All/i' /etc/apache2/apache2.conf

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Healthcheck Laravel 11 (/up)
RUN apt-get update && apt-get install -y --no-install-recommends curl && rm -rf /var/lib/apt/lists/*
HEALTHCHECK --interval=30s --timeout=5s CMD curl -fsS http://localhost/up || exit 1

# Arranque: ahora sí ejecutamos artisan y descubrimos paquetes
CMD bash -lc '\
    php artisan package:discover --ansi && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    apache2-foreground'
