<?php
declare(strict_types=1);
/**
 * Offline workflow regression checks with an in-memory persistence test double.
 * No credentials, database connection, GenAI request, or extra PHP extension is
 * required. The real orchestration and fixed-point calculation code run below.
 * Run: php tests/workflow.php
 */
require dirname(__DIR__) . '/lib/workflow.php';
require dirname(__DIR__) . '/lib/money.php';
$checks = 0;
function check_workflow(bool $condition, string $message): void
{
    global $checks;
    if (!$condition) { throw new RuntimeException($message); }
    ++$checks;
}

/** Minimal transaction-aware test double for the SQL used by workflow.php. */
final class WorkflowStore
{
    public array $result = [];
    public array $events = [];
    public string $status = 'CONFIRMED';
    public bool $failEvent = false;
    private ?array $snapshot = null;
    public function beginTransaction(): void { $this->snapshot = [$this->result, $this->events, $this->status]; }
    public function inTransaction(): bool { return $this->snapshot !== null; }
    public function commit(): void { $this->snapshot = null; }
    public function rollBack(): void { [$this->result, $this->events, $this->status] = $this->snapshot; $this->snapshot = null; }
    public function prepare(string $sql): object
    {
        return new class($this, $sql) {
            public function __construct(private WorkflowStore $store, private string $sql) {}
            public function execute(array $params): void
            {
                if (str_starts_with($this->sql, 'INSERT INTO comparison_result')) {
                    $this->store->result = $params;
                } elseif (str_starts_with($this->sql, 'DELETE FROM comparison_result')) {
                    $this->store->result = [];
                } elseif (str_contains($this->sql, "status = 'CALCULATED'")) {
                    $this->store->status = 'CALCULATED';
                } elseif (str_contains($this->sql, "status = 'NEEDS_REVIEW'")) {
                    $this->store->status = 'NEEDS_REVIEW';
                } else {
                    throw new RuntimeException('Unexpected workflow SQL.');
                }
            }
        };
    }
}
$store = new WorkflowStore();
function db(): WorkflowStore { global $store; return $store; }
function find_comparison(int $id): array { return ['id' => $id, 'version_label' => 'workshop-v1']; }
function record_event(int $id, string $event, string $actor, string $outcome, ?array $details = null): void
{
    if (db()->failEvent) { throw new RuntimeException('Simulated persistence failure'); }
    db()->events[] = [$id, $event, $actor, $outcome, $details];
}
$lines = [];
function comparison_lines(int $id): array { global $lines; return $lines; }
function confirmed_lines(): array
{
    return [
        ['input_side' => 'RHEL', 'review_status' => 'CONFIRMED', 'sku' => 'DEMO-RHEL', 'description' => 'Demo RHEL', 'quantity' => '10', 'annual_unit_price' => '1200', 'comparison_group' => 1],
        ['input_side' => 'ORACLE_LINUX', 'review_status' => 'CONFIRMED', 'sku' => 'DEMO-OL', 'description' => 'Demo Oracle', 'quantity' => '10', 'annual_unit_price' => '800', 'comparison_group' => 1],
    ];
}

foreach ([3, 4] as $stage) {
    check_workflow(comparison_landing_path(['id' => 4, 'status' => 'CALCULATED', 'line_count' => 4], $stage) === '/comparison.php?id=4', 'Earlier lab landing remains unchanged.');
}
check_workflow(comparison_landing_path(['id' => 4, 'status' => 'CALCULATED', 'line_count' => 4], 5) === '/results.php?id=4', 'Open calculated result directly.');
foreach (['CONFIRMED', 'NEEDS_REVIEW'] as $status) {
    check_workflow(comparison_landing_path(['id' => 4, 'status' => $status, 'line_count' => 2], 5) === '/review.php?id=4', 'Resume existing review without recalculating.');
}
check_workflow(comparison_landing_path(['id' => 4, 'status' => 'DRAFT', 'line_count' => 0], 5) === '/comparison.php?id=4', 'Unformatted draft opens saved input actions.');

$lines = confirmed_lines();
save_calculated_results(4);
check_workflow(db()->result['rhel_annual_total'] === '12000.00', 'Annual RHEL unchanged.');
check_workflow(db()->result['oracle_linux_five_year_total'] === '40000.00', 'Five-year Oracle Linux unchanged.');
check_workflow(db()->result['three_year_difference'] === '12000.00', 'Three-year difference unchanged.');
check_workflow(db()->status === 'CALCULATED' && count(db()->events) === 1, 'Result, status and event saved.');
check_workflow(!db()->inTransaction(), 'Successful transaction completed.');
$lines[0]['annual_unit_price'] = '1250';
save_calculated_results(4);
check_workflow(db()->result['rhel_annual_total'] === '12500.00', 'Recalculation uses current saved review values.');

foreach (['AI_SUGGESTED', 'UNRESOLVED', 'unpaired', 'empty'] as $invalid) {
    $before = [db()->result, db()->events, db()->status];
    $lines = confirmed_lines();
    if ($invalid === 'unpaired') { $lines[0]['comparison_group'] = 2; }
    elseif ($invalid === 'empty') { $lines = []; }
    else { $lines[0]['review_status'] = $invalid; }
    try { save_calculated_results(4); check_workflow(false, 'Invalid review must block calculation.'); }
    catch (DomainException $exception) { check_workflow(true, 'Blocked invalid review.'); }
    check_workflow([db()->result, db()->events, db()->status] === $before, 'Blocked calculation has no persistence side effects.');
}
$lines = confirmed_lines();
$before = [db()->result, db()->events, db()->status];
db()->failEvent = true;
try { save_calculated_results(4); check_workflow(false, 'Persistence failure must surface.'); }
catch (RuntimeException $exception) { check_workflow(true, 'Persistence failure reported.'); }
check_workflow([db()->result, db()->events, db()->status] === $before && !db()->inTransaction(), 'Failed result transaction rolls back completely.');
db()->failEvent = false;

// GenAI is replaced only at its external-call boundary. Test partial failures
// while exercising the actual loop, invalidation and status orchestration.
$inputs = ['RHEL' => ['input_side' => 'RHEL', 'raw_text' => 'Original RHEL'], 'ORACLE_LINUX' => ['input_side' => 'ORACLE_LINUX', 'raw_text' => 'Original Oracle']];
$failSides = [];
$formatted = [];
function comparison_inputs(int $id): array { global $inputs; return $inputs; }
function format_input_with_genai(array $input): int
{
    global $failSides, $formatted;
    if (in_array($input['input_side'], $failSides, true)) { throw new RuntimeException('Simulated model unavailable'); }
    $formatted[] = $input['input_side'];
    return 1;
}
function line_counts(int $id): array { global $formatted; return ['AI_SUGGESTED' => count($formatted)]; }
foreach ([[], ['RHEL'], ['ORACLE_LINUX'], ['RHEL', 'ORACLE_LINUX']] as $failSides) {
    $formatted = [];
    db()->result = ['previous' => 'result'];
    db()->status = 'DRAFT';
    $original = $inputs;
    $messages = format_comparison_inputs(4);
    check_workflow(count($messages) === 2, 'Both sides report an outcome.');
    check_workflow(count($formatted) === 2 - count($failSides), 'Retain every successful side.');
    check_workflow($inputs === $original, 'Formatting does not change saved original input text.');
    check_workflow(db()->result === [], 'Reformatting invalidates prior result.');
    check_workflow(db()->status === ($formatted === [] ? 'DRAFT' : 'NEEDS_REVIEW'), 'Suggestions still require human review.');
}
unset($inputs['ORACLE_LINUX']);
$failSides = []; $formatted = [];
$messages = format_comparison_inputs(4);
check_workflow(str_contains($messages[1], 'original input is missing'), 'Missing side reported explicitly.');
echo "Workflow tests passed: $checks checks.\n";
