<?php
declare(strict_types=1);
/** Offline contract and escaped-rendering tests. No live database is contacted. */
require_once __DIR__ . '/../lib/context.php';
function h(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function app_url(string $path): string { return '/ol-value-navigator' . $path; }
$checks = 0;
function context_check(bool $condition, string $message): void
{
    global $checks;
    ++$checks;
    if (!$condition) { throw new RuntimeException($message); }
}
context_check(customer_context_values([]) === array_fill_keys(array_keys(customer_context_fields()), ''), 'Old comparisons default to blank.');
context_check(customer_context_values(['customer_objective' => "  First\r\nSecond\rThird  "])['customer_objective'] === "First\nSecond\nThird", 'Normalize newlines without losing full text.');
foreach (customer_context_fields() as $key => [$label, $limit]) {
    $text = str_repeat('é', $limit);
    context_check(customer_context_values([$key => $text])[$key] === $text, 'Unicode limit accepted: ' . $key);
    foreach ([$text . 'x', ['nested'], 123, "bad\x00text", "bad\xC3\x28"] as $invalid) {
        try {
            customer_context_values([$key => $invalid]);
            throw new RuntimeException('Invalid context accepted: ' . $key);
        } catch (InvalidArgumentException) { context_check(true, 'Invalid context rejected.'); }
    }
}
$inert = '<script>alert("test")</script>';
context_check(customer_context_values(['customer_name' => $inert])['customer_name'] === $inert, 'Keep text, escape at output.');
ob_start(); render_customer_context(['id' => 7, 'customer_name' => $inert]); $html = ob_get_clean();
context_check(str_contains($html, '&lt;script&gt;') && !str_contains($html, '<script>'), 'HTML is escaped.');
context_check(substr_count($html, 'Not provided') === 3, 'Missing optional values are explicit.');
context_check(str_contains($html, '/context.php?id=7'), 'Edit link preserves comparison ID.');
ob_start(); render_customer_context(['id' => 7, 'customer_name' => $inert], false); $resultsContext = ob_get_clean();
context_check(!str_contains($resultsContext, '/context.php'), 'Results can group the edit action outside the context card.');
context_check(str_contains($resultsContext, '&lt;script&gt;'), 'Results context remains escaped.');
ob_start(); render_customer_context_fields(['customer_objective' => '</textarea><script>x</script>']); $form = ob_get_clean();
context_check(!str_contains($form, '<script>') && substr_count($form, '<textarea') === 4, 'Form cannot be escaped by user input.');
context_check(!str_contains($form, ' required'), 'All four fields remain optional.');
echo "Customer-context tests passed: {$checks} checks.\n";
