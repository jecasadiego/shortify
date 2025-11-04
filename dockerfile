# Etapa 1: Composer
FROM composer:2 AS build
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress
COPY . .
RUN php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force || true

# Etapa 2: PHP + Apache
FROM php:8.2-apache

# Extensiones necesarias (ajusta según tu DB)
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar app
WORKDIR /var/www/html
COPY --from=build /app ./

# DocumentRoot = public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Permisos storage/bootstrap
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Healthcheck Laravel 11 (/up)
HEALTHCHECK --interval=30s --timeout=5s CMD curl -fsS http://localhost/up || exit 1

# Start
CMD ["bash", "-lc", "php artisan config:cache && php artisan route:cache && php artisan view:cache && apache2-foreground"]
