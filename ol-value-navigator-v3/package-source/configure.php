<?php
declare(strict_types=1);
require __DIR__ . '/runtime.php';

// Preflight runs as root before the extracted package is accessible to Apache.
// Require Apache-owned private regular files even when the reader is root.
function setupSecret(string $path, int $limit): string
{
    $stat = lstat($path);
    $apache = posix_getpwnam('apache');
    if ($stat === false || $apache === false || $stat['uid'] !== $apache['uid']
        || ($stat['mode'] & 0077) !== 0) {
        throw new RuntimeException('Unsafe credential ownership or permissions.');
    }
    return (new App\Infrastructure\Console\PackageFileReader())->read($path, $limit);
}

try {
    $preflight = ($argv[1] ?? '') === '--preflight';
    if ($preflight) {
        array_splice($argv, 1, 1);
    }
    if (($argv[1] ?? '') === '--existing') {
        $c = json_decode(setupSecret(V3Runtime::CONFIG, 16384), true, 16, JSON_THROW_ON_ERROR);
        if (($c['database'] ?? '') !== 'olvn_v3' || ($c['username'] ?? '') !== 'olvn_v3_app'
            || ($c['catalogMode'] ?? '') !== 'bundled') {
            throw new RuntimeException('Existing configuration does not match this installer.');
        }
        $host = $c['host'] ?? '';
        $password = $c['password'] ?? '';
        $mode = $c['catalogMode'];
    } elseif (($argv[1] ?? '') === '--reuse') {
        $host = json_decode(setupSecret('/etc/olvn-v3/runtime/connection.json', 4096), true, 16, JSON_THROW_ON_ERROR)['host'];
        $secret = json_decode(setupSecret('/etc/olvn-v3/runtime/database.json', 16384), true, 16, JSON_THROW_ON_ERROR);
        $password = $secret['password'];
        if ($secret['username'] !== 'olvn_v3_app') {
            throw new RuntimeException('Unexpected existing username.');
        }
        $mode = $argv[2] ?? '';
    } else {
        $host = $argv[1] ?? '';
        $mode = $argv[2] ?? '';
        $password = stream_get_contents(STDIN, 4097);
    }
    if (!filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || !str_starts_with($host, '10.0.1.')
        || !in_array($mode, ['bundled', 'database'], true) || !is_string($password)
        || $password === '' || strlen($password) > 4096 || strpbrk($password, "\r\n\0") !== false) {
        throw new RuntimeException('Invalid setup input.');
    }
    if (!$preflight && (file_exists(V3Runtime::CONFIG) || is_link(V3Runtime::CONFIG))) {
        throw new RuntimeException('Configuration exists; refusing to overwrite it.');
    }
    $c = ['host'=>$host, 'database'=>'olvn_v3', 'username'=>'olvn_v3_app', 'password'=>$password, 'catalogMode'=>$mode];
    V3Runtime::database($c); // Validate before saving; never save a mistyped password.
    if ($preflight) {
        echo "PASS: credentials verified; no configuration files changed.\n";
        exit(0);
    }
    umask(0077);
    $bytes = json_encode($c, JSON_THROW_ON_ERROR);
    $temporary = tempnam(dirname(V3Runtime::CONFIG), '.setup-');
    if ($temporary === false) {
        throw new RuntimeException('Cannot stage credentials.');
    }
    try {
        if (file_put_contents($temporary, $bytes, LOCK_EX) !== strlen($bytes)
            || !link($temporary, V3Runtime::CONFIG)) {
            throw new RuntimeException('Configuration write failed or destination already exists.');
        }
    } finally {
        unlink($temporary); // Only this invocation's temporary file, never existing credentials.
    }
    echo "PASS: credentials verified and saved privately.\n";
} catch (Throwable $e) {
    fwrite(STDERR, V3Runtime::error($e) . "\n");
    exit(1);
}
