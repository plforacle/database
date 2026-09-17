<?php
declare(strict_types=1);
require __DIR__ . '/app/vendor/autoload.php';
function check(bool $ok, string $label): void {
    if (!$ok) { throw new RuntimeException($label); }
    echo "PASS: $label\n";
}
try {
    check(PHP_VERSION_ID >= 80300, 'PHP 8.3 or newer');
    foreach (['pdo_mysql','curl','mbstring','zip','gd','dom','fileinfo','iconv','simplexml','xmlreader','xmlwriter','openssl'] as $ext) {
        check(extension_loaded($ext), "PHP extension $ext");
    }
    $resolver = new App\Infrastructure\Catalog\BaselineCatalogSnapshotResolver();
    $legacy = $resolver->resolve('legacy-coverage');
    $scenario = $resolver->resolve('scenario-1.3.1');
    check(count($legacy->entries()) === 103 && count($scenario->entries()) === 106, 'both complete bundled catalog profiles load');
    $input = App\Domain\Coverage\CoverageScenario::example();
    $input['groups'][0]['offering'] = 'SYN-BASIC';
    $input['groups'][0]['olamRequired'] = false;
    $input = App\Domain\Coverage\CoverageInput::normalize($input);
    $r = (new App\Domain\Coverage\CoverageEvaluator())->evaluate($input, $legacy);
    // Independently calculated: 10 x invented source $150, 10 x invented target $60.
    foreach (['sourceAnnual'=>'1500.00','oracleAnnual'=>'600.00','savingsAnnual'=>'900.00',
        'sourceThreeYear'=>'4500.00','oracleThreeYear'=>'1800.00','sourceFiveYear'=>'7500.00',
        'oracleFiveYear'=>'3000.00','savingsPercent'=>'60.00'] as $key=>$value) {
        check(($r[$key] ?? null) === $value, "synthetic arithmetic $key");
    }
    check($r['ready'] === true, 'complete synthetic legacy comparison');
    $input['lines'][0]['sku'] = 'NOT-A-REAL-SKU';
    $blocked = (new App\Domain\Coverage\CoverageEvaluator())->evaluate($input, $legacy);
    check($blocked['ready'] === false, 'unknown SKU does not produce a ready comparison');
    echo "OFFLINE CHECKS PASSED. Database, GenAI and browser checks are separate.\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'FAIL: ' . $e->getMessage() . "\n");
    exit(1);
}
