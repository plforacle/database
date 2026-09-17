<?php
declare(strict_types=1);
// Isolated test double. Never shipped in the deployment package.
final class V3Runtime
{
    public const CONFIG = __DIR__ . '/runtime.json';
    public static function database(array $c): void
    {
        if ($c['password'] !== 'invented-test-password') {
            throw new PDOException('Simulated rejected credentials.');
        }
    }
    public static function error(Throwable $e): string
    {
        return 'Test failure: ' . get_class($e);
    }
}
