# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Dockerfile de PRODUCCIÓN — no confundir con vendor/laravel/sail/.../Dockerfile
# (ese es el de Sail, solo para desarrollo local). Este arma la imagen que
# corre en el servidor real (Oracle Cloud u otro), pensado para arm64/amd64.
# ---------------------------------------------------------------------------

# --- Stage 1: build de assets front-end (Tailwind + JS) con Vite ----------
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources/ resources/
COPY vite.config.js ./

# Necesita alcanzar fonts.bunny.net (plugin laravel-vite-plugin/fonts) —
# requiere acceso a internet en build time. No se puede probar en el
# sandbox de Claude por esa misma razón (ver MEMORIA.md sección 7).
RUN npm run build

# --- Stage 2: dependencias PHP con Composer --------------------------------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
COPY packages/ packages/

# --no-scripts porque todavía no está el código de la app copiado (los
# scripts de post-install de Laravel asumen que artisan ya existe).
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --no-interaction \
    --prefer-dist

COPY . .

RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# --- Stage 3: imagen final ---------------------------------------------------
# Un solo contenedor con nginx + php-fpm (via supervisord), no dos separados:
# para una sola VM chica evita tener que compartir public/build entre
# contenedores distintos por un volumen — más simple de operar y de depurar.
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
        nginx \
        supervisor \
        libpng-dev \
        libzip-dev \
        libxml2-dev \
        icu-dev \
        oniguruma-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        bcmath \
        exif \
        gd \
        intl \
        zip \
        opcache \
    && apk del libpng-dev libzip-dev libxml2-dev icu-dev oniguruma-dev freetype-dev libjpeg-turbo-dev

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache-custom.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

COPY --from=vendor /app /var/www/html
COPY --from=assets /app/public/build /var/www/html/public/build

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
