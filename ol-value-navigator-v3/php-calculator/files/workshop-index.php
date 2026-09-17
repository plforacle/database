<?php
declare(strict_types=1);

// Only the local Apache listener and SSH tunnel may reach this rehearsal entry point.
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Private workshop access only.');
}
ini_set('display_errors', '0');
set_time_limit(300);
try {
    require dirname(__DIR__) . '/vendor/autoload.php';
    require dirname(__DIR__) . '/workshop/workshop-db.php';
    $settings = [
        'environment' => 'loopback',
        'demoRepresentativeSubject' => 'v3-workshop-demo',
        'heatWaveModelIdentifier' => 'mistral-7b-instruct-v3',
        'databaseDsn' => WorkshopDb::dsn('runtime'),
        'databaseUserSecretPath' => WorkshopDb::directory('runtime') . '/database.json',
    ];
    App\Bootstrap::app($settings, WorkshopDb::factory()->runtime())->run();
} catch (Slim\Exception\HttpNotFoundException $error) {
    http_response_code(404);
    echo 'Not found.';
} catch (Throwable $error) {
    error_log('V3 workshop request failed: ' . get_class($error));
    http_response_code(500);
    echo 'V3 workshop configuration or database check failed. Run the Lab 3 checks.';
}
