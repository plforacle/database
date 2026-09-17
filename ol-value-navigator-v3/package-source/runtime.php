<?php
declare(strict_types=1);
require_once __DIR__ . '/app/vendor/autoload.php';

final class V3Runtime
{
    public const CONFIG = '/etc/olvn-v3-package/runtime.json';
    public static function settings(): array
    {
        $bytes = (new App\Infrastructure\Console\PackageFileReader())->read(self::CONFIG, 16384, true);
        $c = json_decode($bytes, true, 16, JSON_THROW_ON_ERROR);
        if (($c['database'] ?? '') !== 'olvn_v3' || ($c['username'] ?? '') !== 'olvn_v3_app'
            || !filter_var($c['host'] ?? '', FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
            || !str_starts_with($c['host'], '10.0.1.')
            || !in_array($c['catalogMode'] ?? '', ['bundled', 'database'], true)) {
            throw new RuntimeException('Invalid V3 runtime configuration.');
        }
        return $c;
    }
    public static function database(array $c): PDO
    {
        $pdo = new PDO('mysql:host=' . $c['host'] . ';port=3306;dbname=olvn_v3;charset=utf8mb4',
            $c['username'], $c['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_SSL_CIPHER => 'DEFAULT',
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ]);
        $row = $pdo->query("SHOW SESSION STATUS LIKE 'Ssl_cipher'")->fetch(PDO::FETCH_NUM);
        if (!is_array($row) || !is_string($row[1]) || $row[1] === '') {
            throw new RuntimeException('Database encryption was not negotiated.');
        }
        return $pdo;
    }
    public static function catalogs(array $c, PDO $pdo): App\Application\Package\CatalogSnapshotResolver
    {
        return $c['catalogMode'] === 'bundled'
            ? new App\Infrastructure\Catalog\BaselineCatalogSnapshotResolver()
            : new App\Infrastructure\Catalog\PdoCatalogSnapshotResolver($pdo);
    }
    public static function error(Throwable $e): string
    {
        if (!$e instanceof PDOException) {
            return 'Application check failed: ' . get_class($e) . '. Check configuration, permissions and installed extensions.';
        }
        $n = (int) ($e->errorInfo[1] ?? $e->getCode());
        $message = "Database error $n.";
        $message .= match ($n) {
            1045 => ' Saved application credentials were rejected.',
            1142, 1370 => ' The application account lacks a required permission.',
            2002, 2003 => ' Check the private DB IP, database state and MySQL network rule.',
            2026 => ' TLS negotiation failed.',
            default => ' Repeat the documented query interactively to obtain the full server error.',
        };
        // Print a bounded routine identifier, never the raw exception or credentials.
        if (preg_match("/for routine '([A-Za-z0-9_.]{1,128})'/", $e->getMessage(), $m)) {
            $message .= ' Routine: ' . $m[1] . '.';
        }
        return $message;
    }
}
