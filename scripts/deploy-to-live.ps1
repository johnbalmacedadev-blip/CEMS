# Deploy CEMS to live cPanel via FTPS, then run migrations through /deploy/run.
# Usage (from project root):
#   .\scripts\deploy-to-live.ps1
#   .\scripts\deploy-to-live.ps1 -SkipPrepare
#   .\scripts\deploy-to-live.ps1 -SkipUpload

param(
    [switch]$SkipPrepare,
    [switch]$SkipUpload,
    [switch]$SkipMigrate
)

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $Root

$envDeployPath = Join-Path $Root ".env.deploy"
if (-not (Test-Path $envDeployPath)) {
    throw "Missing .env.deploy — copy .env.deploy.example and fill FTP/DB values."
}

function Read-DotEnv([string]$Path) {
    $map = @{}
    Get-Content $Path | ForEach-Object {
        $line = $_.Trim()
        if ($line -eq "" -or $line.StartsWith("#") -or -not $line.Contains("=")) { return }
        $parts = $line.Split("=", 2)
        $key = $parts[0].Trim()
        $val = $parts[1].Trim()
        if (($val.StartsWith('"') -and $val.EndsWith('"')) -or ($val.StartsWith("'") -and $val.EndsWith("'"))) {
            $val = $val.Substring(1, $val.Length - 2)
        }
        $map[$key] = $val
    }
    return $map
}

$cfg = Read-DotEnv $envDeployPath
$liveUrl = ($cfg["LIVE_URL"] ?? "").TrimEnd("/")
$basePath = ($cfg["LIVE_BASE_PATH"] ?? "/db-system").TrimEnd("/")
if (-not $basePath.StartsWith("/")) { $basePath = "/" + $basePath }
$deployToken = $cfg["DEPLOY_TOKEN"] ?? ""
$remotePath = if ($cfg["REMOTE_PATH"]) { $cfg["REMOTE_PATH"] } else { "." }

$DeployDir = Join-Path (Split-Path -Parent $Root) "deploy"
$stage = Join-Path $DeployDir "_stage_live"

Write-Host "`n=== CEMS deploy to live ===" -ForegroundColor Cyan
Write-Host "Target: $liveUrl" -ForegroundColor DarkGray
Write-Host "FTP host: $($cfg['FTP_HOST'])  remote: $remotePath`n" -ForegroundColor DarkGray

if (-not $SkipPrepare) {
    Write-Host "[1/4] Preparing production package (keep stage)..." -ForegroundColor Yellow
    # Build archive + leave a fresh stage for upload by re-running prepare steps with KeepStage.
    & powershell -NoProfile -ExecutionPolicy Bypass -File (Join-Path $Root "scripts\deploy-prepare.ps1") -KeepStage -RewriteBase $basePath
    if ($LASTEXITCODE -ne 0) { throw "deploy-prepare.ps1 failed" }
} else {
    Write-Host "[1/4] Skip prepare" -ForegroundColor DarkYellow
}

if (-not (Test-Path $stage)) {
    # Fallback: extract newest archive if stage missing
    $archive = Get-ChildItem $DeployDir -Filter "CEMS-deploy-*.tar.gz" | Sort-Object LastWriteTime -Descending | Select-Object -First 1
    if (-not $archive) { throw "No deploy stage or archive found in $DeployDir" }
    Write-Host "Extracting $($archive.Name) to stage..." -ForegroundColor Yellow
    if (Test-Path $stage) { Remove-Item $stage -Recurse -Force }
    New-Item -ItemType Directory -Force -Path $stage | Out-Null
    Push-Location $stage
    try { tar -xzf $archive.FullName } finally { Pop-Location }
}

# Inject RewriteBase into staged .htaccess if missing
$htaccess = Join-Path $stage ".htaccess"
if (Test-Path $htaccess) {
    $ht = Get-Content $htaccess -Raw
    if ($ht -notmatch "RewriteBase") {
        $ht = $ht -replace "RewriteEngine On", "RewriteEngine On`r`n    RewriteBase $basePath/"
        Set-Content -Path $htaccess -Value $ht -NoNewline
    }
}

# Build first-run production .env (uploaded only if remote lacks one)
$prodEnvPath = Join-Path $DeployDir "_production.env"
$appKey = $cfg["APP_KEY"]
if (-not $appKey) {
    $appKey = ("base64:" + [Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Maximum 256 }) -as [byte[]]))
}
@"
APP_NAME="Car Empire Management System"
APP_ENV=production
APP_KEY=$appKey
APP_DEBUG=false
APP_URL=$liveUrl
DEPLOY_TOKEN=$deployToken
LOG_CHANNEL=stack
LOG_LEVEL=error
DB_CONNECTION=mysql
DB_HOST=$($cfg['DB_HOST'])
DB_PORT=$($cfg['DB_PORT'])
DB_DATABASE=$($cfg['DB_DATABASE'])
DB_USERNAME=$($cfg['DB_USERNAME'])
DB_PASSWORD=$($cfg['DB_PASSWORD'])
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=240
SESSION_DOMAIN=
SESSION_SECURE_COOKIE=true
"@ | Set-Content -Path $prodEnvPath -Encoding utf8

if (-not $SkipUpload) {
    Write-Host "[2/4] Connecting for upload..." -ForegroundColor Yellow
    $useSftp = $false
    if ($cfg.ContainsKey("SFTP_USER") -and $cfg["SFTP_USER"]) {
        $useSftp = $true
    } elseif ($cfg.ContainsKey("SFTP_HOST") -and $cfg["SFTP_HOST"]) {
        $useSftp = $true
    }

    if ($useSftp) {
        python (Join-Path $Root "scripts\sftp_deploy.py") test
        if ($LASTEXITCODE -ne 0) { throw "SFTP test failed" }
        Write-Host "[3/4] Uploading staged files via SFTP..." -ForegroundColor Yellow
        python (Join-Path $Root "scripts\sftp_deploy.py") upload $stage $remotePath
        if ($LASTEXITCODE -ne 0) { throw "SFTP upload failed" }
        python (Join-Path $Root "scripts\sftp_deploy.py") put-env $prodEnvPath
    } else {
        php (Join-Path $Root "scripts\ftp_deploy.php") test
        $ftpTest = $LASTEXITCODE
        Write-Host "[3/4] Uploading staged files via FTPS..." -ForegroundColor Yellow
        if ($ftpTest -eq 2) {
            Write-Host "FTP login works but LIST/PUT data channel failed (PASV ports blocked)." -ForegroundColor Red
            Write-Host "Fix one of these, then re-run with -SkipPrepare:" -ForegroundColor Yellow
            Write-Host "  1) Add cPanel SSH/SFTP main account to .env.deploy (SFTP_USER / SFTP_PASSWORD)" -ForegroundColor Yellow
            Write-Host "  2) Ask host to open FTP passive port range for your IP" -ForegroundColor Yellow
            Write-Host "  3) Upload FileZilla package from a network that allows PASV" -ForegroundColor Yellow
            throw "FTP data channel unavailable from this machine"
        }
        php (Join-Path $Root "scripts\ftp_deploy.php") upload $stage $remotePath
        if ($LASTEXITCODE -ne 0) { throw "FTP upload failed" }
        php (Join-Path $Root "scripts\ftp_deploy.php") put-env $prodEnvPath
    }
} else {
    Write-Host "[2-3/4] Skip upload" -ForegroundColor DarkYellow
}

if (-not $SkipMigrate) {
    if (-not $deployToken) { throw "DEPLOY_TOKEN missing in .env.deploy" }
    $migrateUrl = "$liveUrl/deploy/run?token=$deployToken"
    Write-Host "[4/4] Running migrations via $liveUrl/deploy/run ..." -ForegroundColor Yellow
    try {
        $resp = Invoke-RestMethod -Uri $migrateUrl -Method GET -TimeoutSec 180
        $resp | ConvertTo-Json -Depth 6
        if (-not $resp.ok) { throw "Deploy endpoint reported errors" }
    } catch {
        Write-Host "Post-deploy call failed: $($_.Exception.Message)" -ForegroundColor Red
        Write-Host "Open manually after upload: $migrateUrl" -ForegroundColor Yellow
        throw
    }
} else {
    Write-Host "[4/4] Skip migrate" -ForegroundColor DarkYellow
}

Write-Host "`n=== Live deploy finished ===" -ForegroundColor Green
Write-Host "Site: $liveUrl/login"
