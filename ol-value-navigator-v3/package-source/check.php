<?php
declare(strict_types=1);
require __DIR__ . '/runtime.php';
$stage = 'configuration';
try {
    $c = V3Runtime::settings();
    $pdo = V3Runtime::database($c);
    echo "PASS: application database login and encryption (certificate identity not verified).\n";
    $stage = 'catalog';
    $resolver = V3Runtime::catalogs($c, $pdo);
    foreach (['legacy-coverage', 'scenario-1.3.1'] as $profile) {
        $snapshot = $resolver->resolve($profile);
        echo "PASS: $profile catalog " . $snapshot->releaseId() . " (" . $c['catalogMode'] . ").\n";
    }
    if (($argv[1] ?? '') === '--genai') {
        $stage = 'GenAI';
        echo "Testing HeatWave generation; allow several minutes...\n";
        $raw = $pdo->query("SELECT sys.ML_GENERATE('Return the word READY.', JSON_OBJECT('task','generation','model_id','mistral-7b-instruct-v3'))")->fetchColumn();
        $result = json_decode((string)$raw, true, 32, JSON_THROW_ON_ERROR);
        if (!is_string($result['text'] ?? null) || !str_contains(strtoupper($result['text']), 'READY')) {
            throw new RuntimeException('Unexpected GenAI response.');
        }
        echo "PASS: application-account GenAI returned READY.\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, "FAIL [$stage]: " . V3Runtime::error($e) . "\n");
    exit(1);
}
