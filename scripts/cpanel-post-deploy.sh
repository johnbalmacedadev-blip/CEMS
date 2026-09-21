#!/bin/bash
# Runs from the Git repository root after cPanel pulls a new commit.
set -euo pipefail

ROOT="$(pwd)"
cd "$ROOT"

echo "[cpanel-deploy] Starting in $ROOT"

# Required writable dirs (must exist after Git pull)
mkdir -p \
  bootstrap/cache \
  storage/app/public \
  storage/app/imports \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/testing \
  storage/framework/views \
  storage/logs

# Ensure placeholder ignore files exist (keeps empty dirs in git + on disk)
for f in \
  bootstrap/cache/.gitignore \
  storage/app/.gitignore \
  storage/app/public/.gitignore \
  storage/app/imports/.gitignore \
  storage/framework/cache/.gitignore \
  storage/framework/cache/data/.gitignore \
  storage/framework/sessions/.gitignore \
  storage/framework/testing/.gitignore \
  storage/framework/views/.gitignore \
  storage/logs/.gitignore
do
  if [ ! -f "$f" ]; then
    printf '*\n!.gitignore\n' > "$f"
  fi
done

# cPanel PHP user must write here
chmod -R 775 bootstrap/cache storage 2>/dev/null || chmod -R 777 bootstrap/cache storage 2>/dev/null || true

# First deploy only: create .env from production example (never overwrite existing)
if [ ! -f .env ]; then
  if [ -f .env.production.example ]; then
    cp .env.production.example .env
    echo "[cpanel-deploy] Created .env from .env.production.example"
  else
    echo "[cpanel-deploy] ERROR: no .env and no .env.production.example" >&2
    exit 1
  fi
fi

# Scribe is require-dev; remove config on production so artisan never loads missing classes
if [ -f config/scribe.php ]; then
  # Keep file if Scribe is installed; otherwise rename aside
  if [ ! -d vendor/knuckleswtf/scribe ]; then
    mv config/scribe.php config/scribe.php.dev-only 2>/dev/null || rm -f config/scribe.php
    echo "[cpanel-deploy] Set aside config/scribe.php (Scribe not installed)"
  fi
fi

# Root .htaccess must front-controller via root index.php (not rewrite-all to public/),
# otherwise Laravel sees the wrong path and returns 404 for / and /login.
if [ -f .htaccess ]; then
  if ! grep -q "RewriteBase /ce-dbase/" .htaccess; then
    echo "[cpanel-deploy] WARNING: .htaccess missing RewriteBase /ce-dbase/ — pull latest from GitHub"
  fi
fi

COMPOSER_BIN=""
for c in composer composer.phar /usr/local/bin/composer "$HOME/bin/composer"; do
  if command -v "$c" >/dev/null 2>&1; then
    COMPOSER_BIN="$c"
    break
  fi
  if [ -x "$c" ]; then
    COMPOSER_BIN="$c"
    break
  fi
done

PHP_BIN="php"
if command -v php >/dev/null 2>&1; then
  PHP_BIN="$(command -v php)"
fi

if [ -n "$COMPOSER_BIN" ]; then
  echo "[cpanel-deploy] composer install --no-dev"
  $COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction
else
  echo "[cpanel-deploy] WARNING: composer not found — ensure vendor/ exists on server"
fi

if grep -q '^APP_KEY=$' .env 2>/dev/null || ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
  echo "[cpanel-deploy] Generating APP_KEY"
  $PHP_BIN artisan key:generate --force
fi

echo "[cpanel-deploy] artisan migrate + caches"
$PHP_BIN artisan migrate --force
$PHP_BIN artisan storage:link 2>/dev/null || true
$PHP_BIN artisan optimize:clear
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

# Re-apply writable perms after artisan cache writes
chmod -R 775 bootstrap/cache storage 2>/dev/null || true

echo "[cpanel-deploy] Done"
