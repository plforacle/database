<?php
declare(strict_types=1);
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Private V3 rehearsal only.');
}
ini_set('display_errors', '0');
set_time_limit(300);
try {
    require dirname(__DIR__, 2) . '/runtime.php';
    $c = V3Runtime::settings();
    $pdo = V3Runtime::database($c);
    $settings = [
        'environment' => 'loopback',
        'demoRepresentativeSubject' => 'v3-workshop-demo',
        'heatWaveModelIdentifier' => 'mistral-7b-instruct-v3',
    ];
    App\Bootstrap::app($settings, $pdo, null, V3Runtime::catalogs($c, $pdo))->run();
} catch (Slim\Exception\HttpNotFoundException $e) {
    http_response_code(404);
    echo 'Not found.';
} catch (Throwable $e) {
    error_log(class_exists('V3Runtime') ? V3Runtime::error($e) : get_class($e));
    http_response_code(500);
    echo 'V3 check failed. Run sudo bash test.sh from the installed package directory.';
}
