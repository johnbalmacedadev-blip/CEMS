<?php
/**
 * FTP/FTPS helpers for CEMS deploy (avoids PowerShell escaping issues).
 *
 * Usage:
 *   php scripts/ftp_deploy.php test
 *   php scripts/ftp_deploy.php list [remotePath]
 *   php scripts/ftp_deploy.php upload <localDir> [remotePath]
 *   php scripts/ftp_deploy.php put-env <localEnvFile>
 */

$root = dirname(__DIR__);
$envFile = $root . DIRECTORY_SEPARATOR . '.env.deploy';
if (! is_file($envFile)) {
    fwrite(STDERR, "Missing .env.deploy — copy from .env.deploy.example\n");
    exit(1);
}

$cfg = parseEnvFile($envFile);
$host = $cfg['FTP_HOST'] ?? '';
$port = (int) ($cfg['FTP_PORT'] ?? 21);
$user = $cfg['FTP_USER'] ?? '';
$pass = $cfg['FTP_PASSWORD'] ?? '';
$useSsl = (($cfg['FTP_SSL'] ?? '1') === '1');
$remoteBase = trim((string) ($cfg['REMOTE_PATH'] ?? '.'), "/\\");
if ($remoteBase === '') {
    $remoteBase = '.';
}

if ($host === '' || $user === '' || $pass === '') {
    fwrite(STDERR, "FTP_HOST / FTP_USER / FTP_PASSWORD required in .env.deploy\n");
    exit(1);
}

$cmd = $argv[1] ?? 'test';
$conn = ftpConnect($host, $port, $user, $pass, $useSsl);

try {
    switch ($cmd) {
        case 'test':
            echo "OK connected to {$host}:{$port}\n";
            echo 'PWD=' . ftp_pwd($conn) . "\n";
            $list = ftp_nlist($conn, '.');
            if ($list === false) {
                echo "LIST failed (often PASV/firewall). Control channel login works.\n";
                exit(2);
            }
            echo "Entries (" . count($list) . "):\n";
            foreach (array_slice($list, 0, 50) as $item) {
                echo "  {$item}\n";
            }
            break;

        case 'list':
            $path = $argv[2] ?? $remoteBase;
            ensureRemoteDir($conn, $path);
            $list = ftp_nlist($conn, $path === '.' ? '.' : $path);
            if ($list === false) {
                fwrite(STDERR, "LIST failed for {$path}\n");
                exit(2);
            }
            foreach ($list as $item) {
                echo $item . "\n";
            }
            break;

        case 'upload':
            $local = $argv[2] ?? '';
            $remote = $argv[3] ?? $remoteBase;
            if ($local === '' || ! is_dir($local)) {
                fwrite(STDERR, "Usage: php scripts/ftp_deploy.php upload <localDir> [remotePath]\n");
                exit(1);
            }
            echo "Uploading {$local} -> {$remote}\n";
            uploadTree($conn, $local, $remote, [
                '.env',
                '.env.backup',
                '.env.deploy',
                '.git',
            ]);
            echo "Upload finished.\n";
            break;

        case 'put-env':
            $localEnv = $argv[2] ?? '';
            if ($localEnv === '' || ! is_file($localEnv)) {
                fwrite(STDERR, "Usage: php scripts/ftp_deploy.php put-env <localEnvFile>\n");
                exit(1);
            }
            $remoteEnv = ($remoteBase === '.' ? '.env' : rtrim($remoteBase, '/') . '/.env');
            $exists = @ftp_size($conn, $remoteEnv);
            if ($exists > 0) {
                echo "Remote .env already exists — not overwritten.\n";
                break;
            }
            ensureRemoteDir($conn, dirname($remoteEnv) === '\\' ? '.' : dirname($remoteEnv));
            if (! ftp_put($conn, $remoteEnv, $localEnv, FTP_BINARY)) {
                fwrite(STDERR, "Failed to upload .env\n");
                exit(1);
            }
            echo "Uploaded first-run .env to {$remoteEnv}\n";
            break;

        default:
            fwrite(STDERR, "Unknown command: {$cmd}\n");
            exit(1);
    }
} finally {
    ftp_close($conn);
}

function parseEnvFile(string $path): array
{
    $out = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
            continue;
        }
        [$k, $v] = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v);
        if (
            (str_starts_with($v, '"') && str_ends_with($v, '"'))
            || (str_starts_with($v, "'") && str_ends_with($v, "'"))
        ) {
            $v = substr($v, 1, -1);
        }
        $out[$k] = $v;
    }

    return $out;
}

function ftpConnect(string $host, int $port, string $user, string $pass, bool $useSsl)
{
    $conn = $useSsl ? @ftp_ssl_connect($host, $port, 30) : @ftp_connect($host, $port, 30);
    if (! $conn && $useSsl) {
        echo "FTPS connect failed, retrying plain FTP...\n";
        $conn = @ftp_connect($host, $port, 30);
    }
    if (! $conn) {
        fwrite(STDERR, "Cannot connect to {$host}:{$port}\n");
        exit(1);
    }
    if (! @ftp_login($conn, $user, $pass)) {
        fwrite(STDERR, "FTP login failed for {$user}\n");
        exit(1);
    }
    // Required for most cPanel hosts / NATs
    ftp_pasv($conn, true);
    @ftp_set_option($conn, FTP_USEPASVADDRESS, false);

    return $conn;
}

function ensureRemoteDir($conn, string $path): void
{
    $path = str_replace('\\', '/', $path);
    if ($path === '' || $path === '.' || $path === '/') {
        return;
    }
    $parts = explode('/', trim($path, '/'));
    $cur = '';
    foreach ($parts as $part) {
        if ($part === '' || $part === '.') {
            continue;
        }
        $cur .= '/' . $part;
        if (@ftp_chdir($conn, $cur)) {
            ftp_chdir($conn, '/');
            // restore relative: chdir to absolute may fail on jailed accounts
            continue;
        }
        // Try from current home using relative segments
    }

    // Relative mkdir walk from home
    @ftp_chdir($conn, '/');
    $pwd = ftp_pwd($conn) ?: '.';
    $walk = $pwd === '/' ? [] : [];
    // Always walk from login directory
    @ftp_chdir($conn, $pwd);
    foreach ($parts as $part) {
        if ($part === '' || $part === '.') {
            continue;
        }
        if (! @ftp_chdir($conn, $part)) {
            if (! @ftp_mkdir($conn, $part)) {
                // may already exist as file race; try chdir again
            }
            if (! @ftp_chdir($conn, $part)) {
                fwrite(STDERR, "Cannot create/enter remote dir segment: {$part}\n");
                exit(1);
            }
        }
    }
    // return to login home
    @ftp_chdir($conn, $pwd);
}

function uploadTree($conn, string $localDir, string $remoteDir, array $skipNames): void
{
    $localDir = rtrim($localDir, "\\/");
    $remoteDir = trim(str_replace('\\', '/', $remoteDir), '/');
    if ($remoteDir === '') {
        $remoteDir = '.';
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($localDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $count = 0;
    foreach ($iterator as $fileInfo) {
        /** @var SplFileInfo $fileInfo */
        $rel = substr($fileInfo->getPathname(), strlen($localDir) + 1);
        $rel = str_replace('\\', '/', $rel);
        $top = explode('/', $rel)[0];
        if (in_array($top, $skipNames, true) || in_array(basename($rel), $skipNames, true)) {
            continue;
        }
        // Never upload local secrets / VCS
        if (str_contains($rel, '/.git/') || str_starts_with($rel, '.git/')) {
            continue;
        }

        $remotePath = ($remoteDir === '.' ? $rel : $remoteDir . '/' . $rel);
        if ($fileInfo->isDir()) {
            ensureRemoteDir($conn, $remotePath);
            continue;
        }

        ensureRemoteDir($conn, dirname($remotePath));
        $ok = @ftp_put($conn, $remotePath, $fileInfo->getPathname(), FTP_BINARY);
        if (! $ok) {
            // retry once
            $ok = @ftp_put($conn, $remotePath, $fileInfo->getPathname(), FTP_BINARY);
        }
        if (! $ok) {
            fwrite(STDERR, "FAIL {$rel}\n");
            exit(1);
        }
        $count++;
        if ($count % 50 === 0) {
            echo "  uploaded {$count} files...\n";
        }
    }
    echo "  uploaded {$count} files total.\n";
}
