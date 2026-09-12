#!/bin/sh
set -e

cd /var/www/html

# ---------------------------------------------------------------------------
# CONTAINER_ROLE quyết định process chạy trong pod:
#   app       -> Octane / FrankenPHP (HTTP :8000)          [mặc định]
#   worker    -> queue:work + schedule:work
#   scheduler -> chỉ schedule:work
#   *         -> chạy lệnh truyền vào (vd: php artisan migrate)
# ---------------------------------------------------------------------------

ROLE="${CONTAINER_ROLE:-app}"

mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache
chown -R appuser:appgroup storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# storage:link cho FILESYSTEM_DISK=public
if [ ! -e public/storage ]; then
    php artisan storage:link --quiet || true
fi

php artisan route:clear --quiet || true
php artisan view:clear  --quiet || true
php artisan config:clear --quiet || true

if [ "${CACHE_CONFIG:-false}" = "true" ]; then
    php artisan config:cache
fi

php artisan route:cache || true
php artisan view:cache  || true

case "$ROLE" in
    app)
        echo "[entrypoint] role=app -> FrankenPHP/Octane :8000"
        exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/app.conf
        ;;
    worker)
        echo "[entrypoint] role=worker -> queue:work + schedule:work"
        exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/worker.conf
        ;;
    scheduler)
        echo "[entrypoint] role=scheduler -> schedule:work"
        exec php artisan schedule:work --no-interaction
        ;;
    *)
        echo "[entrypoint] chạy lệnh tuỳ ý: $*"
        exec "$@"
        ;;
esac
