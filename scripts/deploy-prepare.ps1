# CEMS - prepare files for server deployment (run from project root)
# Usage: .\scripts\deploy-prepare.ps1
#        .\scripts\deploy-prepare.ps1 -SkipZip
# Archives and staging go to ..\deploy (sibling of this project folder).

param(
    [switch]$SkipZip
)

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $Root
$DeployDir = Join-Path (Split-Path -Parent $Root) "deploy"

Write-Host "`n=== CEMS Deployment Prepare ===" -ForegroundColor Cyan
Write-Host "Project: $Root`n"

$scribeConfig = Join-Path $Root "config\scribe.php"
$scribeBackup = Join-Path $Root "config\scribe.php.deploy-bak"
# Local XAMPP often lacks ext-gd; production usually has it. Ignore for packaging only.
$composerIgnore = @("--ignore-platform-req=ext-gd", "--ignore-platform-req=ext-zip")

function Invoke-Composer {
    param([Parameter(Mandatory = $true)][string[]]$Args)
    & composer @Args @composerIgnore
    if ($LASTEXITCODE -ne 0) {
        throw "Composer failed: composer $($Args -join ' ') (exit $LASTEXITCODE)"
    }
}

Write-Host "[1/7] Composer install (with dev, for Scribe)..." -ForegroundColor Yellow
Invoke-Composer @("install", "--no-interaction", "--prefer-dist")

Write-Host "[2/7] Generate API docs (Scribe)..." -ForegroundColor Yellow
php artisan scribe:generate --force 2>&1 | Out-Host
if ($LASTEXITCODE -ne 0) {
    Write-Host "Warning: Scribe generation failed." -ForegroundColor DarkYellow
}

Write-Host "[3/7] Build feature docs into public/documentation..." -ForegroundColor Yellow
if (-not (Test-Path "documentation")) {
    if (Test-Path "public\documentation\index.html") {
        Write-Host "  documentation/ source missing; using existing public/documentation build." -ForegroundColor DarkYellow
    } else {
        Write-Host "  documentation/ source missing and no public/documentation build; skipping feature docs." -ForegroundColor DarkYellow
    }
} else {
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
}

Write-Host "[4/7] Backup/remove Scribe config, then composer install --no-dev..." -ForegroundColor Yellow
if (Test-Path $scribeConfig) {
    Copy-Item $scribeConfig $scribeBackup -Force
    Remove-Item $scribeConfig -Force
    Write-Host "  Removed config/scribe.php before --no-dev (avoids class-not-found during package:discover)" -ForegroundColor DarkGray
}
foreach ($cacheFile in @("bootstrap\cache\packages.php", "bootstrap\cache\services.php")) {
    if (Test-Path $cacheFile) {
        Remove-Item $cacheFile -Force
    }
}
Invoke-Composer @("install", "--no-dev", "--optimize-autoloader", "--no-interaction", "--prefer-dist")
# Drop stale package discovery again and rediscover without Scribe.
foreach ($cacheFile in @("bootstrap\cache\packages.php", "bootstrap\cache\services.php")) {
    if (Test-Path $cacheFile) {
        Remove-Item $cacheFile -Force
    }
}
php artisan package:discover --ansi | Out-Host
if (Test-Path "vendor\knuckleswtf\scribe") {
    throw "Scribe is still present under vendor after --no-dev. Aborting deploy package."
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
    } elseif ($c.Path -in @("public\documentation\index.html", "public\vendor\scribe")) {
        Write-Host "  SKIP  $($c.Label) ($($c.Path))" -ForegroundColor DarkYellow
    } else {
        Write-Host "  MISSING  $($c.Label) ($($c.Path))" -ForegroundColor Red
        throw "Missing required deploy artifact: $($c.Path)"
    }
}

if (-not $SkipZip) {
    $stamp = Get-Date -Format "yyyy-MM-dd"
    $archiveName = "CEMS-deploy-$stamp.tar.gz"
    $archivePath = Join-Path $DeployDir $archiveName
    $stage = Join-Path $DeployDir "_stage"
    New-Item -ItemType Directory -Force -Path $DeployDir | Out-Null
    if (Test-Path $archivePath) { Remove-Item $archivePath -Force }
    if (Test-Path $stage) { Remove-Item $stage -Recurse -Force }
    New-Item -ItemType Directory -Force -Path $stage | Out-Null

    Write-Host "`n[7/8] Staging cPanel root layout (public files at site root)..." -ForegroundColor Yellow
    # Copy project into stage (exclude heavy/local-only paths).
    $robolog = Join-Path $DeployDir "_robocopy.log"
    $rcArgs = @(
        $Root, $stage, "/E", "/NFL", "/NDL", "/NJH", "/NJS", "/NC", "/NS", "/NP",
        "/XD", "node_modules", ".git", "deploy", "documentation\node_modules", "documentation\build", "documentation\export", "storage\logs", "storage\framework\cache", "storage\framework\sessions", "storage\framework\views",
        "/XF", ".env", ".env.backup", "config\scribe.php", "config\scribe.php.deploy-bak"
    )
    & robocopy @rcArgs | Out-Null
    # robocopy exit codes 0-7 are success-ish
    if ($LASTEXITCODE -ge 8) {
        throw "robocopy staging failed with exit code $LASTEXITCODE (see $robolog)"
    }

    # Flatten public web assets into stage root so extract works when docroot = project root.
    # Keep stage\public\ for Laravel public_path() / storage:link.
    # Never replace Composer vendor/ with public/vendor — merge public/vendor/* into vendor/.
    $publicDir = Join-Path $stage "public"
    Get-ChildItem -Force $publicDir | ForEach-Object {
        if ($_.Name -in @("index.php", ".htaccess", "storage")) { return }
        $dest = Join-Path $stage $_.Name
        if ($_.Name -eq "vendor" -and $_.PSIsContainer) {
            Get-ChildItem -Force $_.FullName | ForEach-Object {
                $vendorDest = Join-Path $dest $_.Name
                if ($_.PSIsContainer) {
                    if (Test-Path $vendorDest) { Remove-Item $vendorDest -Recurse -Force }
                    Copy-Item $_.FullName $vendorDest -Recurse -Force
                } else {
                    Copy-Item $_.FullName $vendorDest -Force
                }
            }
            return
        }
        if ($_.PSIsContainer) {
            if (Test-Path $dest) { Remove-Item $dest -Recurse -Force }
            Copy-Item $_.FullName $dest -Recurse -Force
        } else {
            Copy-Item $_.FullName $dest -Force
        }
    }

    # Root front controller (paths relative to site root, not public/)
    $rootIndex = @'
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
'@
    $utf8NoBom = New-Object System.Text.UTF8Encoding $false
    [System.IO.File]::WriteAllText((Join-Path $stage "index.php"), $rootIndex.TrimStart() + "`n", $utf8NoBom)

    # Root .htaccess: rewrite + map /storage/* to public/storage (avoids clash with Laravel storage/)
    $rootHtaccess = @'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Uploaded files live in public/storage (not Laravel storage/ app dir)
    RewriteRule ^storage/(.*)$ public/storage/$1 [L]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
'@
    [System.IO.File]::WriteAllText((Join-Path $stage ".htaccess"), $rootHtaccess.TrimStart() + "`n", $utf8NoBom)

    # Guard: UTF-8 BOM in index.php prevents Set-Cookie and breaks login sessions
    $indexBytes = [System.IO.File]::ReadAllBytes((Join-Path $stage "index.php"))
    if ($indexBytes.Length -ge 3 -and $indexBytes[0] -eq 0xEF -and $indexBytes[1] -eq 0xBB -and $indexBytes[2] -eq 0xBF) {
        throw "Root index.php still has a UTF-8 BOM. Aborting - this breaks login sessions."
    }

    if (-not (Test-Path (Join-Path $stage "index.php"))) {
        throw "Staging root index.php was not created."
    }
    if (-not (Test-Path (Join-Path $stage "documentation\index.html"))) {
        Write-Host "  Note: staging has no documentation/ (feature docs skipped)." -ForegroundColor DarkYellow
    }
    if (-not (Test-Path (Join-Path $stage "vendor\autoload.php"))) {
        throw "Staging vendor/autoload.php missing - public/vendor flatten may have wiped Composer vendor."
    }
    if (-not (Test-Path (Join-Path $stage "vendor\scribe"))) {
        Write-Host "  Note: staging has no vendor/scribe (API docs assets skipped)." -ForegroundColor DarkYellow
    }

    Write-Host "[8/8] Creating deployment archive from staged root layout..." -ForegroundColor Yellow
    Push-Location $stage
    try {
        tar -czf $archivePath .
    } finally {
        Pop-Location
    }
    Remove-Item $stage -Recurse -Force -ErrorAction SilentlyContinue

    $sizeMb = [math]::Round((Get-Item $archivePath).Length / 1MB, 1)
    Write-Host ("Archive created: {0} ({1} MB)" -f $archivePath, $sizeMb) -ForegroundColor Green
    Write-Host "  Layout: public web files are at archive ROOT (index.php, .htaccess, documentation/, images/, vendor/scribe/)." -ForegroundColor DarkGray
    Write-Host "  public/ is still included for Laravel storage:link / public_path()." -ForegroundColor DarkGray
} else {
    Write-Host "`n[7/8] Skipped ZIP (-SkipZip). Upload project folder via FTP/Git." -ForegroundColor DarkYellow
}

Write-Host "[restore] Restoring local dev environment..." -ForegroundColor Yellow
if (Test-Path $scribeBackup) {
    Copy-Item $scribeBackup $scribeConfig -Force
    Remove-Item $scribeBackup -Force
}
Invoke-Composer @("install", "--no-interaction", "--prefer-dist")
php artisan package:discover --ansi | Out-Null
php artisan config:clear | Out-Null

Write-Host "`n=== Done ===" -ForegroundColor Cyan
Write-Host @"

UPLOAD TO SERVER (cPanel / document root = site folder):
  - Extract ..\deploy\CEMS-deploy-*.tar.gz into /home/.../crmstagingsite.com/
  - Keep the existing live .env (do not overwrite)
  - Root already includes index.php, .htaccess, documentation/, images/, vendor/scribe/
  - No need to move public/ contents manually

ON SERVER:
  php artisan migrate --force
  php artisan storage:link
  chmod -R 775 storage bootstrap/cache
  php artisan optimize:clear
  php artisan config:cache && php artisan route:cache && php artisan view:cache

"@
