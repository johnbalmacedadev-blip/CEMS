#!/usr/bin/env bash
# CEMS — prepare files for server deployment (run from project root)
# Usage: bash scripts/deploy-prepare.sh
#        SKIP_ARCHIVE=1 bash scripts/deploy-prepare.sh

set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

SCRIBE_CONFIG="$ROOT/config/scribe.php"
SCRIBE_BACKUP="$ROOT/config/scribe.php.deploy-bak"

echo ""
echo "=== CEMS Deployment Prepare ==="
echo "Project: $ROOT"
echo ""

echo "[1/7] Composer install (with dev, for Scribe)..."
composer install --no-interaction --prefer-dist

echo "[2/7] Generate API docs (Scribe)..."
php artisan scribe:generate --force || echo "Warning: Scribe failed"

echo "[3/7] Build feature docs into public/documentation..."
if [ ! -d documentation/node_modules ]; then
  (cd documentation && npm ci)
fi
export DOCUSAURUS_BASE_URL="/documentation/"
export DOCUSAURUS_URL="https://your-domain.com"
(cd documentation && npm run build:laravel)

echo "[4/7] Backup Scribe config, then composer install --no-dev..."
if [ -f "$SCRIBE_CONFIG" ]; then
  cp "$SCRIBE_CONFIG" "$SCRIBE_BACKUP"
fi
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
if [ -f "$SCRIBE_CONFIG" ]; then
  rm -f "$SCRIBE_CONFIG"
  echo "  Removed config/scribe.php (production uses pre-built /docs view)"
fi

echo "[5/7] Clear caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "[6/7] Verify build artifacts..."
for path in public/documentation/index.html public/vendor/scribe resources/views/scribe/index.blade.php vendor/autoload.php; do
  if [ -e "$path" ]; then echo "  OK  $path"; else echo "  MISSING  $path"; fi
done

if [ "${SKIP_ARCHIVE:-}" != "1" ]; then
  STAMP=$(date +%Y-%m-%d)
  ZIP="deploy/CEMS-deploy-${STAMP}.tar.gz"
  mkdir -p deploy
  tar --exclude='node_modules' --exclude='.git' --exclude='documentation/node_modules' \
      --exclude='documentation/build' --exclude='documentation/export' --exclude='deploy' \
      --exclude='.env' --exclude='.env.backup' --exclude='config/scribe.php' \
      -czf "$ZIP" .
  echo ""
  echo "Archive created: $ZIP"
else
  echo ""
  echo "Skipped archive (SKIP_ARCHIVE=1). Upload project folder via FTP/Git."
fi

echo "[restore] Restoring local dev environment..."
if [ -f "$SCRIBE_BACKUP" ]; then
  mv "$SCRIBE_BACKUP" "$SCRIBE_CONFIG"
fi
composer install --no-interaction --prefer-dist
php artisan config:clear >/dev/null

echo ""
echo "=== Done ==="
echo "See DEPLOYMENT_CHECKLIST.md for server steps."
