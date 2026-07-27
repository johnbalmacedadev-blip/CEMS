# CEMS - prepare files for server deployment (run from project root)
# Usage: .\scripts\deploy-prepare.ps1
#        .\scripts\deploy-prepare.ps1 -SkipZip

param(
    [switch]$SkipZip
)

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $Root

Write-Host "`n=== CEMS Deployment Prepare ===" -ForegroundColor Cyan
Write-Host "Project: $Root`n"

$scribeConfig = Join-Path $Root "config\scribe.php"
$scribeBackup = Join-Path $Root "config\scribe.php.deploy-bak"

Write-Host "[1/7] Composer install (with dev, for Scribe)..." -ForegroundColor Yellow
composer install --no-interaction --prefer-dist

Write-Host "[2/7] Generate API docs (Scribe)..." -ForegroundColor Yellow
php artisan scribe:generate --force 2>&1 | Out-Host
if ($LASTEXITCODE -ne 0) {
    Write-Host "Warning: Scribe generation failed." -ForegroundColor DarkYellow
}

Write-Host "[3/7] Build feature docs into public/documentation..." -ForegroundColor Yellow
if (-not (Test-Path "documentation\node_modules")) {
    Push-Location documentation
    npm ci
    Pop-Location
}
Push-Location documentation
$env:DOCUSAURUS_BASE_URL = "/documentation/"
$env:DOCUSAURUS_URL = "https://your-domain.com"
npm run build:laravel
Pop-Location

Write-Host "[4/7] Backup Scribe config, then composer install --no-dev..." -ForegroundColor Yellow
if (Test-Path $scribeConfig) {
    Copy-Item $scribeConfig $scribeBackup -Force
}
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
if (Test-Path $scribeConfig) {
    Remove-Item $scribeConfig -Force
    Write-Host "  Removed config/scribe.php (production uses pre-built /docs view)" -ForegroundColor DarkGray
}

Write-Host "[5/7] Clear caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

Write-Host "[6/7] Verify build artifacts..." -ForegroundColor Yellow
$checks = @(
    @{ Path = "public\documentation\index.html"; Label = "Feature docs" },
    @{ Path = "public\vendor\scribe"; Label = "Scribe API assets" },
    @{ Path = "resources\views\scribe\index.blade.php"; Label = "Scribe Blade view" },
    @{ Path = "vendor\autoload.php"; Label = "Composer vendor (no-dev)" }
)
foreach ($c in $checks) {
    if (Test-Path $c.Path) {
        Write-Host "  OK  $($c.Label)" -ForegroundColor Green
    } else {
        Write-Host "  MISSING  $($c.Label) ($($c.Path))" -ForegroundColor Red
    }
}

if (-not $SkipZip) {
    $stamp = Get-Date -Format "yyyy-MM-dd"
    $archiveName = "CEMS-deploy-$stamp.tar.gz"
    $archivePath = Join-Path $Root "deploy\$archiveName"
    New-Item -ItemType Directory -Force -Path (Join-Path $Root "deploy") | Out-Null
    if (Test-Path $archivePath) { Remove-Item $archivePath -Force }

    Write-Host "`n[7/7] Creating deployment archive (vendor included, may take several minutes)..." -ForegroundColor Yellow
    tar --exclude='node_modules' --exclude='.git' --exclude='documentation/node_modules' `
        --exclude='documentation/build' --exclude='documentation/export' --exclude='deploy' `
        --exclude='.env' --exclude='.env.backup' --exclude='config/scribe.php' `
        -czf $archivePath .
    $sizeMb = [math]::Round((Get-Item $archivePath).Length / 1MB, 1)
    Write-Host ("Archive created: deploy\{0} ({1} MB)" -f $archiveName, $sizeMb) -ForegroundColor Green
} else {
    Write-Host "`n[7/7] Skipped ZIP (-SkipZip). Upload project folder via FTP/Git." -ForegroundColor DarkYellow
}

Write-Host "[restore] Restoring local dev environment..." -ForegroundColor Yellow
if (Test-Path $scribeBackup) {
    Copy-Item $scribeBackup $scribeConfig -Force
    Remove-Item $scribeBackup -Force
}
composer install --no-interaction --prefer-dist
php artisan config:clear | Out-Null

Write-Host "`n=== Done ===" -ForegroundColor Cyan
Write-Host @"

UPLOAD TO SERVER:
  - Use deploy\CEMS-deploy-*.tar.gz  OR  upload the whole project (exclude .env, node_modules)
  - Production package has NO config/scribe.php (API docs served from pre-built views)

ON SERVER (see DEPLOYMENT_CHECKLIST.md):
  cp .env.production.example .env
  php artisan key:generate
  php artisan migrate --force
  php artisan storage:link
  chmod -R 775 storage bootstrap/cache
  php artisan config:cache && php artisan route:cache && php artisan view:cache

"@
