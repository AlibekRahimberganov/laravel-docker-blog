#!/usr/bin/env bash
#
# setup.sh — Laravel loyihasini git clone'dan keyin bir buyruq bilan
# local ishga tushirish uchun skript.
#
# Loyiha tuzilishi: SQLite DB, Docker (app + nginx, compose.yaml orqali).
# Migratsiya va key:generate HOST'da ishga tushiriladi (host'da PHP bor deb
# faraz qilinadi), Docker esa asosan php-fpm + nginx uchun ishlatiladi.
#
# Ishlatilishi (loyiha ildizidan, scripts/ papkasi ichida joylashgan holda):
#   git clone <repo> && cd <repo>
#   ./scripts/setup.sh
#
set -euo pipefail

# Skript qayerdan chaqirilishidan qat'iy nazar (masalan ./scripts/setup.sh
# yoki bash scripts/setup.sh), avval loyiha ildiziga o'tamiz — chunki
# composer.json, .env.example va compose.yaml shu yerda joylashgan.
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

log()  { echo -e "${GREEN}[+] $1${NC}"; }
warn() { echo -e "${YELLOW}[!] $1${NC}"; }
err()  { echo -e "${RED}[x] $1${NC}"; }

require_cmd() {
    if ! command -v "$1" &>/dev/null; then
        err "'$1' topilmadi. Iltimos avval uni o'rnating."
        exit 1
    fi
}

log "Kerakli dasturlar tekshirilmoqda (php, composer, npm, docker)..."
require_cmd php
require_cmd composer
require_cmd npm
require_cmd docker

if command -v docker-compose &>/dev/null; then
    DC="docker-compose"
elif docker compose version &>/dev/null; then
    DC="docker compose"
else
    err "docker-compose (yoki docker compose plugin) topilmadi."
    exit 1
fi
log "Docker Compose buyrug'i: '$DC'"

# ---------- 1. .env ----------
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        log ".env fayli .env.example asosida yaratildi."
    else
        err ".env.example topilmadi."
        exit 1
    fi
else
    warn ".env fayli allaqachon mavjud, ustidan yozilmadi."
fi

# ---------- 2. Composer va NPM dependencies (host'da, chunki migratsiya ham host'da) ----------
log "Composer paketlari o'rnatilmoqda..."
composer install --no-interaction --prefer-dist

log "NPM paketlari o'rnatilmoqda..."
npm install

# ---------- 3. APP_KEY ----------
if ! grep -q "^APP_KEY=base64" .env 2>/dev/null; then
    log "APP_KEY generatsiya qilinmoqda..."
    php artisan key:generate
else
    warn "APP_KEY allaqachon mavjud, qayta generatsiya qilinmadi."
fi

# ---------- 4. SQLite fayli ----------
if [ ! -f database/database.sqlite ]; then
    log "database/database.sqlite fayli yaratilmoqda..."
    mkdir -p database
    touch database/database.sqlite
else
    warn "database.sqlite allaqachon mavjud."
fi

# ---------- 5. Migratsiya va seed (host'da) ----------
log "Migratsiyalar ishga tushirilmoqda..."
php artisan migrate --force

log "Seederlar ishga tushirilmoqda..."
php artisan db:seed --force

# ---------- 6. Frontend build (Vite) ----------
log "Frontend build qilinmoqda (npm run build)..."
npm run build

# ---------- 7. Docker konteynerlarni ko'tarish (app + nginx) ----------
log "Docker image build qilinmoqda va konteynerlar ko'tarilmoqda..."
$DC up -d --build

# ---------- 8. Ruxsatlar ----------
log "storage/bootstrap papkalariga yozish huquqi berilmoqda..."
chmod -R 775 storage bootstrap/cache database 2>/dev/null || true

log "Hammasi tayyor! Loyiha quyidagi manzilda ishlayapti:"
echo -e "    ${YELLOW}http://localhost:9001${NC}"
echo ""
log "Loglarni ko'rish uchun: $DC logs -f app"
