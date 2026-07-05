# Dockerfile untuk WebGIS Bajo (Laravel 13 / PHP 8.4)
# Ditujukan untuk platform yang butuh image Docker (mis. Render.com),
# karena Render tidak punya buildpack/Nixpacks native seperti Railway.

FROM php:8.4-cli

# --- System dependencies + PHP extensions ---
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip zip ca-certificates curl \
        libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
        libonig-dev libxml2-dev libcurl4-openssl-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j"$(nproc)" \
        pdo pdo_mysql pdo_pgsql mbstring zip gd bcmath \
        xml dom simplexml xmlwriter xmlreader curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# --- Install dependencies & build front-end assets (Vite) ---
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && npm ci \
    && npm run build \
    && npm cache clean --force \
    && rm -rf node_modules

# --- Entrypoint (jalankan migrasi & start server saat container boot) ---
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Render menyuntikkan variabel PORT secara otomatis saat runtime.
ENV PORT=10000
EXPOSE 10000

ENTRYPOINT ["docker-entrypoint.sh"]
