# =========================== Stage 1: builder ==============================
FROM dunglas/frankenphp:1.6-php8.4 AS builder

RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
        git \
        unzip \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    gd \
    pcntl \
    opcache \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    zip \
    bcmath \
    exif \
    intl \
    redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Cache layer cho vendor
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Source ứng dụng (đã bao gồm public/asset/admin/build do build-assets.sh tạo)
COPY . .

# Không mang .env / hot reload / build script của dev vào image
RUN rm -f .env public/hot && rm -rf node_modules
RUN composer install --no-dev --no-scripts --optimize-autoloader --prefer-dist

# ========================= Stage 2: production =============================
FROM dunglas/frankenphp:1.6-php8.4 AS production

ENV SERVER_NAME=":8000" \
    OCTANE_SERVER=frankenphp \
    CONTAINER_ROLE=app

RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
        supervisor \
        curl \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

RUN install-php-extensions \
    gd \
    pcntl \
    opcache \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    zip \
    bcmath \
    exif \
    intl \
    redis

# Timezone VN
RUN ln -sf /usr/share/zoneinfo/Asia/Ho_Chi_Minh /etc/localtime \
    && echo "Asia/Ho_Chi_Minh" > /etc/timezone

RUN groupadd -g 1000 appgroup \
    && useradd -u 1000 -g appgroup -m appuser \
    && mkdir -p /var/log/php \
    && chown -R appuser:appgroup /var/log/php

WORKDIR /var/www/html

COPY --from=builder --chown=appuser:appgroup /var/www/html /var/www/html

# --------------------------- ENV vào container -----------------------------
# Mặc định .env ; production dùng: --build-arg ENV_FILE=.env.production
# Trên k8s có thể override từng biến bằng ConfigMap/Secret (envFrom).
ARG ENV_FILE=.env
COPY --chown=appuser:appgroup ${ENV_FILE} /var/www/html/.env

# ------------------------------ Configs ------------------------------------
COPY --chown=appuser:appgroup ./docker/php.prod.ini /usr/local/etc/php/php.ini
COPY ./docker/supervisor.d/app.conf     /etc/supervisor/conf.d/app.conf
COPY ./docker/supervisor.d/worker.conf  /etc/supervisor/conf.d/worker.conf
COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN chown -R appuser:appgroup storage bootstrap/cache && chmod -R 775 storage bootstrap/cache

# Publish config/octane.php + public/frankenphp-worker.php ngay lúc build
RUN rm -f bootstrap/cache/packages.php bootstrap/cache/services.php \
    && php artisan package:discover --ansi \
    && php artisan octane:install --server=frankenphp --force --no-interaction \
    && php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && chown -R appuser:appgroup /var/www/html/public /var/www/html/config

EXPOSE 8000

HEALTHCHECK --interval=30s --timeout=5s --start-period=15s --retries=3 CMD curl -fsS http://localhost:8000/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
