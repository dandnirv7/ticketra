#!/bin/sh
# docker/entrypoint.sh
# Bersihkan cache Filament/Laravel, jalankan migrasi (opt-in), lalu supervisord.

set -e

cd /var/www/html

echo "[entrypoint] Clearing Laravel & Filament caches..."
php artisan optimize:clear || true

# Hapus cache Filament secara eksplisit (jaga-jaga jika optimize:clear belum mencakup)
rm -f bootstrap/cache/filament/panels/*.php 2>/dev/null || true

# Pastikan permission storage & bootstrap cache aman untuk www-data
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Auto-migrate (opt-in): hanya jalan jika RUN_MIGRATIONS=true
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "[entrypoint] RUN_MIGRATIONS=true → running migrations..."
    php artisan migrate --force --no-interaction
    echo "[entrypoint] Migrations finished."
else
    echo "[entrypoint] RUN_MIGRATIONS not set → skipping migrations."
fi

# Warmup cache produksi (opsional, mempercepat boot)
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache
# php artisan filament:cache-components

echo "[entrypoint] Cache cleared. Starting supervisord..."

exec "$@"
