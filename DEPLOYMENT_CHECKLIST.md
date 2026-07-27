# CEMS — Server Deployment Checklist

Use this when deploying Car Empire Management System to cPanel, VPS, or any PHP hosting.

---

## Before upload (on your PC)

Run from the project root:

```powershell
.\scripts\deploy-prepare.ps1
```

Or on Linux/Mac:

```bash
bash scripts/deploy-prepare.sh
```

This script:

1. Installs Composer dependencies
2. Generates **Scribe API docs** → `public/vendor/scribe`
3. Builds **feature docs** → `public/documentation`
4. Re-installs Composer **without dev** packages (production)
5. Removes `config/scribe.php` (avoids missing-class errors; `/docs` uses pre-built views)
6. Clears local caches
7. Creates **`deploy/CEMS-deploy-YYYY-MM-DD.tar.gz`**
8. Restores your local dev environment (Scribe + full Composer)

### Do NOT upload

| File / folder | Reason |
|---------------|--------|
| `.env` | Local secrets — create new `.env` on server |
| `node_modules/` | Not needed on server |
| `documentation/node_modules/` | Build-time only |
| `.git/` | Optional — not required on server |

### MUST be on server after prepare

| Path | Purpose |
|------|---------|
| `vendor/` | PHP dependencies |
| `public/documentation/` | User guides at `/documentation/` |
| `public/vendor/scribe/` | API docs at `/docs` |
| `public/.htaccess` | URL rewriting |

---

## Server requirements

| Requirement | Version |
|-------------|---------|
| PHP | 8.1+ |
| Extensions | mbstring, openssl, pdo, tokenizer, xml, ctype, json, fileinfo, gd or imagick |
| MySQL / MariaDB | 5.7+ / 10.3+ |
| Composer | Recommended (if SSH available) |

---

## Upload & document root

### Option A — Recommended (document root = `public`)

1. Upload the whole project to e.g. `public_html/cems/` or above web root
2. In cPanel → **Domains** → set document root to:
   ```
   public_html/cems/public
   ```

### Option B — cPanel (files inside `public_html`)

If you cannot change document root:

1. Upload Laravel project **outside** `public_html` (e.g. `cems/`)
2. Move **contents** of `cems/public/` into `public_html/`
3. Edit `public_html/index.php` — update paths to `../cems/vendor` and `../cems/bootstrap`

---

## Server setup (SSH or cPanel Terminal)

```bash
cd /path/to/your/project

# 1. Environment
cp .env.production.example .env
nano .env   # set APP_URL, DB_*, APP_DEBUG=false

# 2. App key (if empty)
php artisan key:generate

# 3. Database
php artisan migrate --force

# 4. Storage symlink (vehicle images, uploads)
php artisan storage:link

# 5. Permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache   # Linux; user may vary

# 6. Production cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### If you upload WITHOUT `vendor/` (Composer on server)

```bash
composer install --no-dev --optimize-autoloader --no-interaction
# Do NOT run scribe:generate on production — upload pre-built public/vendor/scribe and resources/views/scribe from prepare script
```

**Note:** Production deploys omit `config/scribe.php`. The app serves pre-generated API docs at `/docs` via a static route.

---

## Production `.env` settings

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-actual-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

FILESYSTEM_DISK=public
```

Copy from `.env.production.example` and fill in real values.

---

## After deploy — verify these URLs

| URL | What |
|-----|------|
| `/login` | Login page |
| `/home` | Home menu (after login) |
| `/vehicles` | Unit Report |
| `/memos` | Memos |
| `/trail-form-list` | Trail Form List |
| `/analytics-report/sales` | Sales Report (monthly sales, fast-selling, PDF export) |
| `/analytics-report/sales-executive` | Sales Executive Report (top performers) |
| `/documentation/docs/intro` | Feature documentation |
| `/docs` | API docs (login required) |

---

## New modules (recent migrations)

Ensure migrations ran on production:

- `memos` — company memos (`company_documents.body`)
- `trail_form_clients` — Trail Form List

```bash
php artisan migrate --force
```

---

## Analytics — Sales Reports

New management reports built on Released-unit data (no migration needed):

- `/analytics-report/sales` — monthly units/sales charts, top makes/models, body-type mix, fastest-selling models, **Export PDF**
- `/analytics-report/sales-executive` — Sales Team / Agents / Executives ranking of top performers

---

## Optional — import released units from Excel

If migrating historical data, the JSON export is generated from the source Excel and read by these commands:

```bash
# 1) Vehicles + status details + expense summary (upsert by plate)
php artisan import:released-units

# 2) Per-category expense line items (Paint/Scanner/Aircon/etc.) with date+text notes
php artisan import:released-expense-items
```

Requires `storage/app/released_units_import.json` to be present. Safe to re-run (upserts / skips existing).

---

## Troubleshooting

### 500 Internal Server Error
- Check `storage/logs/laravel.log`
- Verify `.env` exists and `APP_KEY` is set
- `chmod -R 775 storage bootstrap/cache`

### Method Not Allowed (405)
```bash
php artisan route:clear
php artisan config:clear
```
- Confirm `public/.htaccess` exists
- Document root must point to `public/`

### Images / uploads not showing
```bash
php artisan storage:link
```
- Confirm `public/storage` symlink exists

### Documentation 404
- Re-run prepare script locally and re-upload `public/documentation/`
- Or on server with Node: `cd documentation && npm ci && npm run build:laravel`

### Clear all caches
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Quick reference — one-time deploy

```bash
# On PC
.\scripts\deploy-prepare.ps1

# Upload deploy/CEMS-deploy-*.tar.gz → extract on server

# On server
cp .env.production.example .env
# edit .env
php artisan key:generate
php artisan migrate --force
php artisan storage:link
chmod -R 775 storage bootstrap/cache
php artisan config:cache && php artisan route:cache && php artisan view:cache
```
