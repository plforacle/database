<?php
declare(strict_types=1);

use App\Infrastructure\Console\PackageFileReader;

/** Isolated workshop adapter. Upstream source files are not modified. */
final class WorkshopDb
{
    public static function directory(string $role): string
    {
        return match ($role) {
            'runtime' => '/etc/olvn-v3/runtime',
            default => throw new RuntimeException('Unknown workshop role.'),
        };
    }

    public static function config(string $role): array
    {
        $bytes = (new PackageFileReader())->read(self::directory($role) . '/connection.json', 4096, true);
        $config = json_decode($bytes, true, 8, JSON_THROW_ON_ERROR);
        if (!is_array($config) || !is_string($config['host'] ?? null)
            || filter_var($config['host'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false
            || !str_starts_with($config['host'], '10.0.1.')) {
            throw new RuntimeException('Expected the Lab 1 private database subnet.');
        }
        return $config;
    }

    public static function dsn(string $role): string
    {
        return 'mysql:host=' . self::config($role)['host'] . ';port=3306;dbname=olvn_v3;charset=utf8mb4';
    }

    public static function connect(string $dsn, string $user, string $password): PDO
    {
        // Match the encrypted, non-identity-verified Lab 2 rehearsal connection.
        // Do not use this adapter as a production verified-TLS configuration.
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_SSL_CIPHER => 'DEFAULT',
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        ]);
        $cipher = $pdo->query("SHOW SESSION STATUS LIKE 'Ssl_cipher'")->fetch(PDO::FETCH_NUM);
        if (!is_array($cipher) || !is_string($cipher[1]) || $cipher[1] === '') {
            throw new RuntimeException('An encrypted database connection is required.');
        }
        return $pdo;
    }

    public static function environment(string $name): string
    {
        return match ($name) {
            'OLVN_DATABASE_DSN' => self::dsn('runtime'),
            'OLVN_DATABASE_USER_SECRET_PATH' => self::directory('runtime') . '/database.json',
            default => '',
        };
    }

    public static function factory(): App\Infrastructure\Console\PackageConnectionFactory
    {
        return new App\Infrastructure\Console\PackageConnectionFactory(
            new PackageFileReader(), self::environment(...), self::connect(...),
        );
    }
}
