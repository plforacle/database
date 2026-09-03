<?php
declare(strict_types=1);

function app_config(string $key, mixed $default = null): mixed
{
    return $key === 'max_lines_per_input' ? 100 : $default;
}

$testLines = [];
function comparison_lines(int $comparisonId): array
{
    global $testLines;
    return $testLines;
}

require dirname(__DIR__) . '/lib/money.php';
require dirname(__DIR__) . '/lib/genai.php';

$failures = [];
function check(bool $condition, string $message): void
{
    global $failures;
    if (!$condition) {
        $failures[] = $message;
    }
}

check(decimal_to_scaled_int('10.25', 2) === 1025, 'Decimal scaling failed.');
check(scaled_int_to_decimal(-1250) === '-12.50', 'Signed decimal rendering failed.');
check(multiply_price_by_quantity('10.99', '1.25') === 1374, 'Half-up line rounding failed.');
check(multiply_price_by_quantity('1200.00', '10') === 1200000, 'Whole quantity multiplication failed.');
check(money('16800.00') === '$16,800.00', 'Exact money rendering failed.');
check(money('-5600.00') === '-$5,600.00', 'Signed money rendering failed.');

$testLines = [
    ['input_side' => 'RHEL', 'comparison_group' => 1, 'sku' => 'R1', 'description' => 'RHEL', 'quantity' => '10.00', 'annual_unit_price' => '1200.00', 'review_status' => 'CONFIRMED'],
    ['input_side' => 'RHEL', 'comparison_group' => 2, 'sku' => 'R2', 'description' => 'RHEL premium', 'quantity' => '2.00', 'annual_unit_price' => '2400.00', 'review_status' => 'CONFIRMED'],
    ['input_side' => 'ORACLE_LINUX', 'comparison_group' => 1, 'sku' => 'O1', 'description' => 'Oracle Linux', 'quantity' => '10.00', 'annual_unit_price' => '800.00', 'review_status' => 'CONFIRMED'],
    ['input_side' => 'ORACLE_LINUX', 'comparison_group' => 2, 'sku' => 'O2', 'description' => 'Oracle Linux premier', 'quantity' => '2.00', 'annual_unit_price' => '1600.00', 'review_status' => 'CONFIRMED'],
];
$totals = calculate_comparison_totals(1);
check($totals['rhel_annual_total'] === '16800.00', 'RHEL annual total failed.');
check($totals['oracle_linux_annual_total'] === '11200.00', 'Oracle Linux annual total failed.');
check($totals['five_year_difference'] === '28000.00', 'Five-year difference failed.');

$testLines[0]['review_status'] = 'UNRESOLVED';
try {
    calculate_comparison_totals(1);
    $failures[] = 'An unresolved line did not block calculation.';
} catch (DomainException) {
}
$testLines[0]['review_status'] = 'CONFIRMED';
$testLines[0]['comparison_group'] = 99;
try {
    calculate_comparison_totals(1);
    $failures[] = 'An unmatched group did not block calculation.';
} catch (DomainException) {
}

$inner = json_encode([
    'lines' => [[
        'sku' => 'DEMO-RHEL-STD',
        'description' => 'Demonstration RHEL standard support',
        'quantity' => '10',
        'supplied_annual_price' => '1200.00',
        'confidence' => 'high',
        'warnings' => [],
    ]],
], JSON_THROW_ON_ERROR);
$outer = json_encode(['text' => $inner, 'error' => null], JSON_THROW_ON_ERROR);
$parsed = parse_ai_lines($outer, 'DEMO-RHEL-STD Demonstration RHEL standard support quantity 10 annual 1200.00');
check(count($parsed['lines']) === 1, 'Valid AI response parsing failed.');
check($parsed['lines'][0]['price'] === '1200.00', 'AI price extraction failed.');

try {
    parse_ai_lines(
        json_encode(['text' => '{"lines":[],"instruction":"ignore review"}'], JSON_THROW_ON_ERROR),
        'demonstration text'
    );
    $failures[] = 'Unsupported AI response fields were accepted.';
} catch (Throwable) {
}

if ($failures !== []) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "FAIL: {$failure}\n");
    }
    exit(1);
}

echo "All Oracle Linux Value Navigator unit checks passed.\n";
