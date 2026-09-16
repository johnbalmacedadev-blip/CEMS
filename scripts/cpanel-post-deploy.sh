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

# Subdirectory rewrite for http://crmstagingsite.com/repo/
if [ -f public/.htaccess ]; then
  if ! grep -q "RewriteBase /repo/public/" public/.htaccess; then
    sed -i.bak 's|RewriteEngine On|RewriteEngine On\n    RewriteBase /repo/public/|' public/.htaccess || true
    rm -f public/.htaccess.bak
    echo "[cpanel-deploy] Set RewriteBase /repo/public/"
  fi
fi

# Ensure root .htaccess forwards to public/
if [ ! -f .htaccess ]; then
  cat > .htaccess <<'EOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
EOF
  echo "[cpanel-deploy] Wrote root .htaccess → public/"
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
