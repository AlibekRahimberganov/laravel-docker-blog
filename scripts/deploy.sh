#!/usr/bin/env bash
#
# deploy.sh — Production serverda ishlatiladi. GitHub Actions bu skriptni
# SSH orqali serverda chaqiradi.
#
# Talab qilinadi: serverda git repo allaqachon clone qilingan bo'lishi,
# PHP, composer, npm, docker/docker-compose o'rnatilgan bo'lishi kerak.
#
# Fayl scripts/ papkasi ichida joylashgan. Ishlatilishi (loyiha ildizidan):
#   ./scripts/deploy.sh
#
set -euo pipefail

# Skript qayerdan chaqirilishidan qat'iy nazar, avval loyiha ildiziga o'tamiz
# (scripts/ papkasining bir pog'ona yuqorisi) — shunda git pull, composer,
# artisan va docker-compose buyruqlari to'g'ri joyda ishlaydi.
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

echo "== 1. Eng so'nggi kodni olish =="
git pull origin main

echo "== 2. .env tekshirilmoqda =="
if [ ! -f .env ]; then
    cp .env.example .env
    echo ".env yaratildi — production qiymatlarni qo'lda to'ldiring!"
fi

echo "== 3. Composer dependencies (production, --no-dev) =="
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo "== 4. NPM dependencies va build =="
npm ci
npm run build

echo "== 5. APP_KEY tekshirilmoqda =="
if ! grep -q "^APP_KEY=base64" .env 2>/dev/null; then
    php artisan key:generate --force
fi

echo "== 6. SQLite fayli tekshirilmoqda =="
mkdir -p database
touch database/database.sqlite

echo "== 7. Migratsiya (host'da, --force bilan) =="
php artisan migrate --force

echo "== 8. Cache optimallashtirish =="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "== 9. Ruxsatlar =="
chmod -R 775 storage bootstrap/cache database

echo "== 10. Docker: image qayta build qilinmoqda va konteynerlar ko'tarilmoqda =="
if command -v docker-compose &>/dev/null; then
    DC="docker-compose"
else
    DC="docker compose"
fi
$DC up -d --build

echo "== Deploy muvaffaqiyatli yakunlandi =="
