<?php
declare(strict_types=1);

/**
 * Dependency-free checks for the application's highest-risk logic and contracts.
 *
 * These tests replace database-dependent helpers with small in-memory stubs, then
 * load the production libraries directly. They cover fixed-point arithmetic,
 * fail-closed review rules, the strict GenAI response contract, exact deletion
 * confirmation, authentication controls, route guards, and ownership query
 * contracts. A nonzero exit status makes the script suitable for deployment
 * checks and simple continuous-integration jobs.
 */

/**
 * Supply only the configuration value required by GenAI parser tests.
 *
 * @param string $key Requested configuration key.
 * @param mixed $default Fallback for keys not defined by the test.
 * @return mixed Test value or caller-provided default.
 */
function app_config(string $key, mixed $default = null): mixed
{
    return $key === 'max_lines_per_input' ? 100 : $default;
}

$testLines = [];
/**
 * Replace the repository lookup with the current in-memory test fixture.
 *
 * @param int $comparisonId Unused interface-compatible comparison identifier.
 * @return list<array<string,mixed>> Lines assigned by the current test section.
 */
function comparison_lines(int $comparisonId): array
{
    global $testLines;
    return $testLines;
}

require dirname(__DIR__) . '/lib/money.php';
require dirname(__DIR__) . '/lib/genai.php';
require dirname(__DIR__) . '/lib/deletion.php';

$failures = [];
/**
 * Accumulate an assertion failure so all checks can run in one invocation.
 *
 * @param bool $condition Expected truth value.
 * @param string $message Diagnostic printed when the expectation fails.
 * @return void
 */
function check(bool $condition, string $message): void
{
    global $failures;
    if (!$condition) {
        $failures[] = $message;
    }
}

// Fixed-point conversion, multiplication, display, and confirmation semantics.
check(decimal_to_scaled_int('10.25', 2) === 1025, 'Decimal scaling failed.');
check(scaled_int_to_decimal(-1250) === '-12.50', 'Signed decimal rendering failed.');
check(multiply_price_by_quantity('10.99', '1.25') === 1374, 'Half-up line rounding failed.');
check(multiply_price_by_quantity('1200.00', '10') === 1200000, 'Whole quantity multiplication failed.');
check(money('16800.00') === '$16,800.00', 'Exact money rendering failed.');
check(money('-5600.00') === '-$5,600.00', 'Signed money rendering failed.');
check(comparison_name_matches('Lab 3 saved-input test', 'Lab 3 saved-input test'), 'Exact deletion confirmation failed.');
check(!comparison_name_matches('Lab 3 saved-input test', 'lab 3 saved-input test'), 'Case-changing deletion confirmation was accepted.');
check(!comparison_name_matches('Lab 3 saved-input test', 'Lab 3 saved-input test '), 'Whitespace-changing deletion confirmation was accepted.');
check(!comparison_name_matches('Lab 3 saved-input test', ''), 'Empty deletion confirmation was accepted.');

// Source-contract checks cover authentication primitives and every protected route.
$applicationRoot = dirname(__DIR__);
$bootstrapSource = file_get_contents($applicationRoot . '/lib/bootstrap.php');
$repositorySource = file_get_contents($applicationRoot . '/lib/repository.php');
$schemaSource = file_get_contents($applicationRoot . '/database/schema.sql');
$loginSource = file_get_contents($applicationRoot . '/public/login.php');
$registerSource = file_get_contents($applicationRoot . '/public/register.php');
check(is_string($bootstrapSource), 'The authentication bootstrap could not be read.');
check(is_string($repositorySource), 'The repository could not be read.');
check(is_string($schemaSource), 'The database schema could not be read.');
check(is_string($loginSource), 'The login controller could not be read.');
check(is_string($registerSource), 'The registration controller could not be read.');

if (is_string($bootstrapSource)) {
    foreach ([
        "'cookie_httponly' => true",
        "'cookie_samesite' => 'Lax'",
        "'cookie_secure' => request_is_https()",
        "'use_only_cookies' => true",
        "'use_strict_mode' => true",
        '($now - $lastActivityAt) > 1800',
        '($now - $authenticatedAt) > 28800',
        '($now - $lastRegeneratedAt) > 900',
        'session_regenerate_id(true)',
        'session_destroy()',
    ] as $requiredFragment) {
        check(str_contains($bootstrapSource, $requiredFragment), "Missing session control: {$requiredFragment}");
    }
}

if (is_string($loginSource)) {
    foreach (['password_verify(', 'password_needs_rehash(', 'record_failed_login(', 'authenticate_user('] as $requiredFragment) {
        check(str_contains($loginSource, $requiredFragment), "Missing login control: {$requiredFragment}");
    }
}
if (is_string($registerSource)) {
    foreach (['password_hash(', 'validate_new_password(', 'hash_equals(', 'authenticate_user('] as $requiredFragment) {
        check(str_contains($registerSource, $requiredFragment), "Missing registration control: {$requiredFragment}");
    }
}

$protectedControllers = [
    'add-line.php', 'calculate.php', 'comparison.php', 'create.php',
    'delete.php', 'duplicate.php', 'export.php', 'format.php', 'help.php',
    'index.php', 'logout.php', 'results.php', 'review.php', 'revise.php',
    'save-review.php',
];
foreach ($protectedControllers as $controller) {
    $source = file_get_contents($applicationRoot . '/public/' . $controller);
    check(is_string($source) && str_contains($source, 'require_login();'), "{$controller} is missing require_login().");
}

$comparisonControllers = [
    'add-line.php', 'calculate.php', 'comparison.php', 'delete.php',
    'duplicate.php', 'export.php', 'format.php', 'results.php', 'review.php',
    'revise.php', 'save-review.php',
];
foreach ($comparisonControllers as $controller) {
    $source = file_get_contents($applicationRoot . '/public/' . $controller);
    check(is_string($source) && str_contains($source, 'find_comparison('), "{$controller} is missing the owner-scoped comparison lookup.");
}
$indexSource = file_get_contents($applicationRoot . '/public/index.php');
$createSource = file_get_contents($applicationRoot . '/public/create.php');
$deletionSource = file_get_contents($applicationRoot . '/lib/deletion.php');
check(is_string($indexSource) && str_contains($indexSource, 'WHERE c.owner_user_id = ?'), 'The comparison list is not owner scoped.');
check(is_string($createSource) && str_contains($createSource, 'INSERT INTO comparison (owner_user_id,'), 'Comparison creation does not assign an owner.');
check(is_string($deletionSource) && str_contains($deletionSource, 'DELETE FROM comparison WHERE id = ? AND owner_user_id = ?'), 'Comparison deletion is not owner scoped.');

if (is_string($repositorySource)) {
    foreach ([
        'WHERE c.id = ? AND c.owner_user_id = ?',
        'WHERE i.comparison_id = ? AND c.owner_user_id = ?',
        'WHERE r.comparison_id = ? AND c.owner_user_id = ?',
    ] as $requiredFragment) {
        check(str_contains($repositorySource, $requiredFragment), "Missing owner-scoped repository query: {$requiredFragment}");
    }
}
if (is_string($schemaSource)) {
    foreach ([
        'CREATE TABLE IF NOT EXISTS user_account',
        'owner_user_id BIGINT UNSIGNED NOT NULL',
        'CONSTRAINT fk_comparison_owner',
        'CONSTRAINT fk_deletion_audit_owner',
    ] as $requiredFragment) {
        check(str_contains($schemaSource, $requiredFragment), "Missing authentication or ownership schema: {$requiredFragment}");
    }
}

// A complete two-sided fixture proves deterministic totals across paired groups.
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

// Fail-closed checks prove that unresolved decisions and unmatched groups block totals.
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

// The parser accepts the ML_GENERATE envelope only when its inner contract is exact.
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

// Unsupported root fields must be rejected instead of ignored.
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

echo "All Oracle Linux Value Navigator unit, authentication, route-guard, and ownership contract checks passed.\n";
