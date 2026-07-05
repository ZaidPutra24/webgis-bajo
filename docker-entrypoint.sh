#!/bin/sh
set -e

# Render menyediakan semua konfigurasi lewat Environment Variables
# (bukan file .env), jadi ini hanya jaga-jaga kalau file .env belum ada.
if [ ! -f .env ]; then
  cp .env.example .env
fi

echo ">> Membersihkan & membangun ulang cache konfigurasi..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ">> Menjalankan migrasi database..."
php artisan migrate --force

# Set RUN_SEED=true di Environment Variables Render HANYA pada deploy
# pertama, lalu hapus/ubah ke false agar seeder tidak jalan berulang
# setiap kali service restart/redeploy.
if [ "$RUN_SEED" = "true" ]; then
  echo ">> Menjalankan seeder..."
  php artisan db:seed --force
fi

php artisan storage:link || true

echo ">> Menjalankan server di port ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
