<?php
declare(strict_types=1);
require '/var/www/ol-value-navigator/lib/bootstrap.php';
require_stage(5);
require_post();
verify_csrf();
$id = post_id();
$comparison = find_comparison($id);
$inputs = comparison_inputs($id);
$lines = comparison_lines($id);
$statement = db()->prepare('SELECT * FROM comparison_result WHERE comparison_id = ?');
$statement->execute([$id]);
$result = $statement->fetch();
record_event($id, 'COMPARISON_EXPORTED', 'REPRESENTATIVE', 'COMPLETED', ['format' => 'CSV']);

function csv_text(mixed $value): string
{
    $text = (string) $value;
    return preg_match('/^[=+@-]/', ltrim($text)) ? "'" . $text : $text;
}

function csv_row($output, array $fields): void
{
    fputcsv($output, $fields, ',', '"', '');
}

$safeName = preg_replace('/[^a-z0-9]+/i', '-', strtolower((string) $comparison['name']));
$safeName = trim((string) $safeName, '-') ?: 'comparison';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="olvn-' . $safeName . '-' . $id . '.csv"');
$output = fopen('php://output', 'wb');
if ($output === false) {
    exit;
}
csv_row($output, ['Oracle Linux Value Navigator comparison']);
csv_row($output, ['Comparison ID', $id]);
csv_row($output, ['Name', csv_text($comparison['name'])]);
csv_row($output, ['Status', $comparison['status']]);
csv_row($output, ['Rule version', $comparison['version_label']]);
csv_row($output, []);
foreach (['RHEL' => 'RHEL original input', 'ORACLE_LINUX' => 'Oracle Linux original input'] as $side => $label) {
    csv_row($output, [$label]);
    csv_row($output, [csv_text($inputs[$side]['raw_text'] ?? '')]);
    csv_row($output, []);
}
csv_row($output, ['Reviewed lines']);
csv_row($output, ['Side', 'Line', 'Group', 'Entry method', 'Suggested SKU', 'Suggested description', 'Suggested quantity', 'Suggested annual unit price', 'Confirmed SKU', 'Confirmed description', 'Confirmed quantity', 'Confirmed annual unit price', 'Decision', 'Representative note']);
foreach ($lines as $line) {
    csv_row($output, [
        $line['input_side'], $line['line_number'], $line['comparison_group'], $line['entry_method'],
        csv_text($line['suggested_sku']), csv_text($line['suggested_description']), $line['suggested_quantity'],
        $line['suggested_annual_unit_price'], csv_text($line['sku']), csv_text($line['description']), $line['quantity'],
        $line['annual_unit_price'], $line['review_status'], csv_text($line['representative_note']),
    ]);
}
csv_row($output, []);
csv_row($output, ['Calculated result']);
if ($result) {
    csv_row($output, ['Period', 'RHEL', 'Oracle Linux', 'Difference']);
    csv_row($output, ['Annual', $result['rhel_annual_total'], $result['oracle_linux_annual_total'], $result['annual_difference']]);
    csv_row($output, ['Three years', $result['rhel_three_year_total'], $result['oracle_linux_three_year_total'], $result['three_year_difference']]);
    csv_row($output, ['Five years', $result['rhel_five_year_total'], $result['oracle_linux_five_year_total'], $result['five_year_difference']]);
} else {
    csv_row($output, ['No calculated result snapshot']);
}
fclose($output);
