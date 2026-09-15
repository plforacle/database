<?php
declare(strict_types=1);
/** Dependency-free regression tests for the V1 PowerPoint exporter. */
require_once __DIR__ . '/../lib/presentation.php';
$checks = 0;
function ppt_check(bool $condition, string $message): void
{
    global $checks;
    ++$checks;
    if (!$condition) { throw new RuntimeException($message); }
}
/**
 * Check the local and central ZIP version fields, not just parseable XML.
 * The previous PharData package had version-needed 0 and passed XML tests,
 * but Microsoft's package reader rejected it before reaching the slide XML.
 */
function ppt_zip_headers(string $bytes): bool
{
    $end = strrpos($bytes, "PK\x05\x06");
    if ($end === false || strlen($bytes) < $end + 22) { return false; }
    $count = unpack('v', substr($bytes, $end + 10, 2))[1];
    $offset = unpack('V', substr($bytes, $end + 16, 4))[1];
    if ($count < 1) { return false; }
    for ($index = 0; $index < $count; ++$index) {
        if (substr($bytes, $offset, 4) !== "PK\x01\x02" || strlen($bytes) < $offset + 46) { return false; }
        $version = unpack('v', substr($bytes, $offset + 6, 2))[1];
        $method = unpack('v', substr($bytes, $offset + 10, 2))[1];
        $local = unpack('V', substr($bytes, $offset + 42, 4))[1];
        if (substr($bytes, $local, 4) !== "PK\x03\x04" || strlen($bytes) < $local + 30) { return false; }
        $localVersion = unpack('v', substr($bytes, $local + 4, 2))[1];
        if ($version !== 20 || $localVersion !== 20 || $method !== 8) { return false; }
        $lengths = unpack('vname/vextra/vcomment', substr($bytes, $offset + 28, 6));
        $offset += 46 + array_sum($lengths);
    }
    return $offset === $end;
}
$comparison = ['id' => 1, 'status' => 'CALCULATED', 'name' => 'Demo & <review> "test"', 'version_label' => 'workshop-v1'];
$result = ['comparison_id' => 1, 'calculated_at' => '2026-09-09 12:00:00'];
foreach (['annual' => ['16800.00', '11200.00', '5600.00'], 'three_year' => ['50400.00', '33600.00', '16800.00'], 'five_year' => ['84000.00', '56000.00', '28000.00']] as $period => $values) {
    $result['rhel_' . $period . '_total'] = $values[0];
    $result['oracle_linux_' . $period . '_total'] = $values[1];
    $result[$period . '_difference'] = $values[2];
}
$lines = [
    ['input_side' => 'RHEL', 'comparison_group' => 1, 'review_status' => 'CONFIRMED'],
    ['input_side' => 'ORACLE_LINUX', 'comparison_group' => 1, 'review_status' => 'CONFIRMED'],
    ['input_side' => 'RHEL', 'comparison_group' => null, 'review_status' => 'EXCLUDED'],
];
$presentation = new ResultsPresentation();
$bytes = $presentation->export($comparison, $result, $lines);
ppt_check(str_starts_with($bytes, 'PK'), 'Must be a real ZIP/PPTX, not HTML.');
ppt_check(ppt_zip_headers($bytes), 'Every entry must have Microsoft-compatible ZIP 2.0 headers.');
$badHeader = substr_replace($bytes, "\x00\x00", 4, 2);
ppt_check(!ppt_zip_headers($badHeader), 'Regression: version-needed 0 must be rejected.');
$directory = sys_get_temp_dir() . '/olvn-ppt-test-' . bin2hex(random_bytes(8));
mkdir($directory, 0700);
$path = $directory . '/test.zip';
try {
    file_put_contents($path, $bytes);
    $archive = new PharData($path);
    $xmlParts = [];
    foreach (new RecursiveIteratorIterator($archive) as $entry) {
        $xml = $entry->getContent();
        if (class_exists('DOMDocument')) {
            $doc = new DOMDocument();
            ppt_check($doc->loadXML($xml, LIBXML_NONET), 'Every XML part must parse.');
        }
        $xmlParts[] = $xml;
    }
    $slides = $archive['ppt/presentation.xml']->getContent();
    ppt_check(substr_count($slides, '<p:sldId ') === 4, 'Exactly four slides.');
    // Schema-valid shared themes still trigger repair in desktop PowerPoint.
    $slideTheme = $archive['ppt/slideMasters/_rels/slideMaster1.xml.rels']->getContent();
    $notesTheme = $archive['ppt/notesMasters/_rels/notesMaster1.xml.rels']->getContent();
    ppt_check(str_contains($slideTheme, 'Target="../theme/theme1.xml"'), 'Slide master uses slide theme.');
    ppt_check(str_contains($notesTheme, 'Target="../theme/theme2.xml"') && !str_contains($notesTheme, 'theme1.xml'), 'Notes master must use a separate theme part.');
    ppt_check(isset($archive['ppt/theme/theme2.xml']), 'Notes theme part exists.');
    ppt_check(str_contains($archive['[Content_Types].xml']->getContent(), 'PartName="/ppt/theme/theme2.xml"'), 'Notes theme content type is declared.');
    $costs = $archive['ppt/slides/slide2.xml']->getContent();
    foreach (['16,800.00', '11,200.00', '5,600.00', '50,400.00', '33,600.00', '84,000.00', '56,000.00', '28,000.00'] as $amount) {
        ppt_check(str_contains($costs, $amount), 'Saved amount preserved: ' . $amount);
    }
    ppt_check(str_contains($costs, '<a:tbl>'), 'Results remain an editable table.');
    preg_match_all('/<a:tr\b[^>]*>(.*?)<\/a:tr>/s', $costs, $resultRows);
    ppt_check(count($resultRows[1]) === 4, 'Header plus three periods, with no misleading grand total.');
    ppt_check(str_contains($resultRows[1][0], 'val="C74634"'), 'Result headers use Oracle red.');
    ppt_check(str_contains($resultRows[1][1], 'val="EED1CE"') && str_contains($resultRows[1][2], 'val="F8EEEC"'), 'Rows alternate light red shading.');
    foreach (array_slice($resultRows[1], 1) as $rowNumber => $rowXml) {
        preg_match_all('/<a:tc>(.*?)<\/a:tc>/s', $rowXml, $cells);
        ppt_check(count($cells[1]) === 4, 'Each period retains its four columns.');
        foreach ([1, 2, 3] as $column) {
            ppt_check(str_contains($cells[1][$column], 'algn="r"'), 'Amounts are right aligned.');
        }
        ppt_check(str_contains($cells[1][3], 'val="006B3C"') && str_contains($cells[1][3], 'b="1"'), 'Positive saved differences are emphasized in dark green.');
    }
    $assumptions = $archive['ppt/slides/slide4.xml']->getContent();
    ppt_check(str_contains($assumptions, '<a:tbl>') && str_contains($assumptions, 'Recommended next step'), 'Assumptions table and next step remain editable content.');
    foreach ($xmlParts as $xml) {
        ppt_check(!preg_match('/Capital Group|Broadcom|VMware|11,330,380|48,651,580/', $xml), 'No names, prices or claims imported from the design reference.');
    }
    ppt_check(str_contains($archive['ppt/slides/slide1.xml']->getContent(), 'Demo &amp; &lt;review&gt;'), 'User text is escaped.');
    foreach ($xmlParts as $xml) { ppt_check(!str_contains($xml, 'TargetMode="External"'), 'No external relationships.'); }
    unset($archive);
} finally {
    unset($archive);
    unlink($path);
    rmdir($directory);
}
foreach (['DRAFT', 'CONFIRMED', 'NEEDS_REVIEW'] as $status) {
    try {
        $presentation->export(array_replace($comparison, ['status' => $status]), $result, $lines);
        throw new RuntimeException('Uncalculated state accepted.');
    } catch (DomainException) { ppt_check(true, 'Uncalculated state rejected.'); }
}
foreach ([array_replace($result, ['annual_difference' => 'not-money']), array_replace($result, ['comparison_id' => 2])] as $invalid) {
    try { $presentation->export($comparison, $invalid, $lines); throw new RuntimeException('Invalid result accepted.'); }
    catch (DomainException) { ppt_check(true, 'Invalid result rejected.'); }
}
foreach ([[], [$lines[0]], [array_replace($lines[0], ['review_status' => 'NEEDS_REVIEW']), $lines[1]], [$lines[0], array_replace($lines[1], ['comparison_group' => 2])]] as $invalid) {
    try { $presentation->export($comparison, $result, $invalid); throw new RuntimeException('Invalid review accepted.'); }
    catch (DomainException) { ppt_check(true, 'Invalid review rejected.'); }
}
// Edge cases must export without losing sign, Unicode, or maximum DECIMAL cents.
$edge = array_replace($result, ['annual_difference' => '-5600.00', 'rhel_five_year_total' => '99999999999999.99']);
ppt_check(str_starts_with($presentation->export(array_replace($comparison, ['name' => str_repeat('Long Unicode café 中文 ', 20) . "\x01"]), $edge, $lines), 'PK'), 'Long Unicode and signed totals export.');
if (isset($argv[1])) { file_put_contents($argv[1], $bytes); }
// Optional customer context changes narrative only. Old comparisons above still
// export with no fields; this fixture exercises full and deliberately long text.
$contextComparison = array_replace($comparison, [
    'name' => 'Demonstration subscription comparison',
    'customer_name' => 'Demo Meridian & Partners',
    'customer_objective' => 'Compare annual subscription costs for our demonstration Linux estate.',
    'comparison_scope' => 'Two reviewed support groups. Subscription costs only. No migration or hardware costs.',
    'recommended_next_step' => 'Review the confirmed quantities and assumptions with the team before sharing the comparison.',
]);
$contextBytes = $presentation->export($contextComparison, $result, $lines);
$contextDirectory = sys_get_temp_dir() . '/olvn-context-ppt-' . bin2hex(random_bytes(8));
mkdir($contextDirectory, 0700);
$contextPath = $contextDirectory . '/context.zip';
try {
    file_put_contents($contextPath, $contextBytes);
    $zip = new ZipArchive(); $zip->open($contextPath);
    $first = $zip->getFromName('ppt/slides/slide1.xml');
    $last = $zip->getFromName('ppt/slides/slide4.xml');
    ppt_check(str_contains($first, 'Demo Meridian &amp; Partners'), 'Customer appears as escaped slide text.');
    ppt_check(str_contains($first, $contextComparison['customer_objective']), 'Objective appears on overview.');
    ppt_check(str_contains($first, $contextComparison['comparison_scope']), 'Scope appears on overview.');
    ppt_check(str_contains($last, $contextComparison['recommended_next_step']), 'Human next step appears on final slide.');
    ppt_check(str_contains($zip->getFromName('ppt/slides/slide2.xml'), '16,800.00'), 'Context leaves saved totals unchanged.');
    $zip->close();
    $dense = array_replace($contextComparison, [
        'customer_name' => str_repeat('W', 120),
        'customer_objective' => str_repeat("Long context & <text> é\n", 12),
        'comparison_scope' => str_repeat('W', 300),
        'recommended_next_step' => str_repeat('W', 300),
    ]);
    $denseBytes = $presentation->export($dense, $result, $lines);
    file_put_contents($contextPath, $denseBytes); $zip->open($contextPath);
    $notes = $zip->getFromName('ppt/notesSlides/notesSlide1.xml');
    foreach (['customer_name','customer_objective','comparison_scope','recommended_next_step'] as $key) {
        foreach (explode("\n", $dense[$key]) as $paragraph) {
            ppt_check(str_contains($notes, htmlspecialchars($paragraph, ENT_XML1 | ENT_QUOTES, 'UTF-8')), 'Full context retained in notes: ' . $key);
        }
    }
    ppt_check(str_contains($zip->getFromName('ppt/slides/slide1.xml'), '...'), 'Long slide narrative is visibly shortened.');
    ppt_check(ppt_zip_headers($denseBytes), 'Dense export preserves compatible ZIP headers.');
    $zip->close();
    // All styles depend on saved value signs, including zero and negative zero.
    $signedResult = array_replace($result, ['annual_difference' => '-5600.00', 'three_year_difference' => '0.00', 'five_year_difference' => '-0.00']);
    $signedBytes = $presentation->export($contextComparison, $signedResult, $lines);
    file_put_contents($contextPath, $signedBytes); $zip->open($contextPath);
    $signedXml = $zip->getFromName('ppt/slides/slide2.xml');
    preg_match_all('/<a:tr\b[^>]*>(.*?)<\/a:tr>/s', $signedXml, $signedRows);
    foreach ([1 => ['-5,600.00', 'A4262C'], 2 => ['0.00', '312D2A'], 3 => ['-0.00', '312D2A']] as $rowIndex => [$amount, $color]) {
        preg_match_all('/<a:tc>(.*?)<\/a:tc>/s', $signedRows[1][$rowIndex], $cells);
        ppt_check(str_contains($cells[1][3], '>' . $amount . '</a:t>'), 'Preserve signed saved difference exactly.');
        ppt_check(str_contains($cells[1][3], 'val="' . $color . '"'), 'Negative is red and zero remains neutral.');
        ppt_check(!str_contains($cells[1][3], 'val="006B3C"'), 'Never present a negative or zero difference as positive.');
    }
    $zip->close();
    if (isset($argv[4])) { file_put_contents($argv[4], $signedBytes); }
    if (isset($argv[2])) { file_put_contents($argv[2], $contextBytes); }
    if (isset($argv[3])) { file_put_contents($argv[3], $denseBytes); }
} finally {
    if (isset($zip)) { unset($zip); }
    unlink($contextPath); rmdir($contextDirectory);
}
// Display shortening avoids chopped words while retaining a hard layout bound.
$shorten = new ReflectionMethod(ResultsPresentation::class, 'shorten');
ppt_check($shorten->invoke($presentation, str_repeat('Alpha beta ', 8), 21) === 'Alpha beta Alpha...', 'Shorten at a nearby word boundary.');
ppt_check($shorten->invoke($presentation, str_repeat('W', 100), 40) === str_repeat('W', 37) . '...', 'Unbroken strings keep a safe display limit.');
ppt_check($shorten->invoke($presentation, 'Short name', 40) === 'Short name', 'Short text remains unchanged.');
echo "PowerPoint tests passed: {$checks} checks.\n";
if (!class_exists('DOMDocument')) { echo "XML parser checks skipped: optional PHP DOM extension is not installed.\n"; }
