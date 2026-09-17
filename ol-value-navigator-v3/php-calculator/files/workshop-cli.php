<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/workshop-db.php';

try {
    $command = $argv[1] ?? '';
    if ($command === 'configure') {
        $role = $argv[2] ?? '';
        $host = $argv[3] ?? '';
        if (count($argv) !== 4 || filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false
            || !str_starts_with($host, '10.0.1.')) {
            throw new RuntimeException('Usage: configure runtime V3_PRIVATE_IP');
        }
        $directory = WorkshopDb::directory($role);
        if (!is_dir($directory) || is_link($directory) || fileowner($directory) !== posix_geteuid()
            || (fileperms($directory) & 0077) !== 0) {
            throw new RuntimeException('Create the private role directory with mode 0700 first.');
        }
        $password = stream_get_contents(STDIN, 4097);
        if (!is_string($password) || $password === '' || strlen($password) > 4096
            || str_contains($password, "\0") || str_contains($password, "\n") || str_contains($password, "\r")) {
            throw new RuntimeException('Supply the password on standard input without a newline.');
        }
        $files = ['connection.json' => json_encode(['host' => $host], JSON_THROW_ON_ERROR)];
        $files['database.json'] = json_encode(['username' => 'olvn_v3_app', 'password' => $password], JSON_THROW_ON_ERROR);
        foreach ($files as $name => $_bytes) {
            if (file_exists($directory . '/' . $name) || is_link($directory . '/' . $name)) {
                throw new RuntimeException('Configuration already exists; nothing was overwritten.');
            }
        }
        umask(0077);
        foreach ($files as $name => $bytes) {
            $handle = fopen($directory . '/' . $name, 'xb');
            if ($handle === false || fwrite($handle, $bytes) !== strlen($bytes)) {
                throw new RuntimeException('Configuration write failed; inspect partial files before retrying.');
            }
            fclose($handle);
        }
        unset($password, $files);
        echo "CONFIGURED\n";
        exit(0);
    }
    if ($command === 'check') {
        $pdo = WorkshopDb::factory()->runtime();
        echo json_encode([
            'database' => $pdo->query('SELECT DATABASE()')->fetchColumn(),
            'account' => $pdo->query('SELECT CURRENT_USER()')->fetchColumn(),
            'tls' => 'encrypted; server identity not verified',
        ], JSON_THROW_ON_ERROR) . PHP_EOL;
        exit(0);
    }
    if ($command === 'genai') {
        $pdo = WorkshopDb::factory()->runtime();
        $result = $pdo->query("SELECT sys.ML_GENERATE('Return the word READY.', JSON_OBJECT('task','generation','model_id','mistral-7b-instruct-v3'))")->fetchColumn();
        echo (string) $result . PHP_EOL;
        exit(0);
    }
    if ($command !== 'package:status') {
        throw new RuntimeException('Unsupported rehearsal command.');
    }
    $files = new App\Infrastructure\Console\PackageFileReader();
    exit((new App\Infrastructure\Console\PackageCommand(
        WorkshopDb::factory(), $files, new App\Infrastructure\Console\PackageBaselineWriter(),
    ))->run($command, array_slice($argv, 2)));
} catch (Throwable $error) {
    // Do not print connection exceptions, credentials, or caller payloads.
    fwrite(STDERR, "Workshop command failed (" . get_class($error) . "). Check the documented inputs and file permissions.\n");
    exit(1);
}
