# syntax=docker/dockerfile:1
# ============================================================================
# SIMCO - Sistema de PQRSF, Clínica de Occidente
# Imagen para desplegar en Render (runtime: Docker)
#
# La base de datos NO se crea aquí: ya existe y la administra otro equipo.
# Este contenedor solo la consulta. Ver OPERACIONES.md y CLAUDE.md.
# ============================================================================

# ----------------------------------------------------------------------------
# Etapa 1: compilar el CSS de TailwindCSS
# ----------------------------------------------------------------------------
FROM node:20-alpine AS css

WORKDIR /build
COPY package.json ./
RUN npm install --no-audit --no-fund

# Tailwind escanea estas rutas (ver tailwind.config.js) para decidir qué
# clases conserva, así que deben estar presentes al compilar.
COPY tailwind.config.js ./
COPY resources ./resources
COPY app/Views ./app/Views
COPY public_html ./public_html

RUN npx @tailwindcss/cli \
        -i ./resources/css/input.css \
        -o /tmp/output.css \
        --minify \
    && test -s /tmp/output.css

# ----------------------------------------------------------------------------
# Etapa 2: dependencias PHP de producción
# ----------------------------------------------------------------------------
FROM composer:2 AS vendor

WORKDIR /build
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction \
        --no-scripts \
        --prefer-dist \
    && test -f vendor/autoload.php

# ----------------------------------------------------------------------------
# Etapa 3: imagen final
# ----------------------------------------------------------------------------
FROM php:8.2-apache

# mbstring, curl y xml ya vienen compiladas en la imagen oficial de PHP.
# Aquí se añaden las que faltan: PostgreSQL, gd y zip.
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libpq-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" pdo_pgsql pgsql gd zip; \
    apt-get purge -y --auto-remove; \
    rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers expires deflate

COPY docker/php-production.ini /usr/local/etc/php/conf.d/zz-simco.ini
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf.tpl
COPY docker/entrypoint.sh     /usr/local/bin/entrypoint.sh
COPY docker/generate-env.php  /usr/local/bin/generate-env.php
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

# Código de la aplicación (.dockerignore excluye .env, Anexos, vendor y build)
COPY app       ./app
COPY config    ./config
COPY resources ./resources
COPY routes    ./routes
COPY public_html ./public_html
COPY composer.json composer.lock .env.example ./

COPY --from=vendor /build/vendor ./vendor
COPY --from=css    /tmp/output.css ./public_html/assets/css/output.css

# FileUploadService guarda en la ruta RELATIVA 'public_html/Anexos', y bajo
# Apache el directorio de trabajo es el del script (public_html/), no la raíz
# del proyecto. Este enlace hace que 'public_html/Anexos' resuelva al
# directorio correcto sin tocar el código ni cambiar la ruta que se guarda en
# la columna `ruta` de formulario_pqr_anexos.
RUN mkdir -p public_html/Anexos \
    && ln -s /var/www/html/public_html /var/www/html/public_html/public_html

RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} + \
    && find /var/www/html -type f -exec chmod 644 {} + \
    && chmod -R 775 /var/www/html/public_html/Anexos

# Render inyecta PORT; 10000 es su valor por defecto.
ENV PORT=10000
EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
