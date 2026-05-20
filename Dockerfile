# syntax=docker/dockerfile:1.7

# =============================================================================
# Stage 1 — Build frontend assets with Vite (Tailwind + Leaflet + Chart.js)
# =============================================================================
FROM node:24-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build

# =============================================================================
# Stage 2 — Runtime: FrankenPHP (Caddy + PHP in a single binary)
# =============================================================================
FROM dunglas/frankenphp:1-php8.3-alpine AS runtime

# Install the PHP extensions Laravel + pgsql needs.
# Build deps are installed as a virtual package and removed after compilation
# to keep the final image small.
RUN apk add --no-cache \
        libpq \
        icu-libs \
        libzip \
        oniguruma \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        libpq-dev \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql \
        pgsql \
        intl \
        bcmath \
        zip \
        opcache \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/*

# Production-tuned php.ini.
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini

# Pull composer binary from the official image.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install PHP dependencies first (better layer caching when only app code changes).
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction \
        --no-progress

# Copy application source.
COPY . .

# Drop in the production-built Vite assets from the frontend stage.
COPY --from=frontend /app/public/build ./public/build

# Finalise autoload + run Laravel's post-install hooks.
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# FrankenPHP runs as www-data by default — make Laravel's writable dirs writable.
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

# Caddyfile tells FrankenPHP to serve the Laravel front controller.
COPY docker/Caddyfile /etc/caddy/Caddyfile

# Startup script — runs migrations, primes caches, then execs the server.
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Render injects the runtime port via $PORT. Default to 8080 locally.
ENV PORT=8080
EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
