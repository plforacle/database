<?php
declare(strict_types=1);
/**
 * Database-free regression checks for failed-form recovery.
 * Run with: php tests/forms.php
 * These checks exercise session data only. They create no comparisons or files.
 */
require dirname(__DIR__) . '/lib/forms.php';
$checks = 0;
function check_form(bool $condition, string $message): void
{
    global $checks;
    if (!$condition) { throw new RuntimeException($message); }
    ++$checks;
}
$_SESSION = [];
check_form(take_form('create') === [], 'Missing draft must be empty.');
remember_form('create', ['name' => 'Demo & <test>', 'rhel_text' => "First\nSecond", 'csrf' => 'private-token'], ['name', 'rhel_text']);
$draft = take_form('create');
check_form($draft['name'] === 'Demo & <test>', 'Preserve source characters for escaped rendering.');
check_form($draft['rhel_text'] === "First\nSecond", 'Preserve line breaks.');
check_form(!isset($draft['csrf']), 'Do not copy fields outside the whitelist.');
check_form(take_form('create') === [], 'Draft must be consumed once.');

$lines = ['7' => ['quantity' => '1.25', 'price' => 'not-a-price', 'note' => 'Keep this note', 'group' => '2', 'status' => 'CONFIRMED']];
remember_form('review:4', ['lines' => $lines], ['lines']);
check_form(take_form('review:8') === [], 'Do not restore a draft on another comparison.');
$draft = take_form('review:4');
check_form($draft['lines'] === $lines, 'Retain invalid values and all other review edits.');
check_form(form_value($draft['lines'][7], 'price') === 'not-a-price', 'Show invalid price for correction.');
check_form(form_value(['name' => ['unexpected']], 'name', 'saved') === 'saved', 'Nested values must not become scalar input values.');
check_form(form_value(['name' => ''], 'name', 'saved') === '', 'An intentionally cleared value stays blank.');

remember_form('create', ['name' => 'Old'], ['name']);
$_SESSION['form_recovery']['create']['at'] = time() - 901;
check_form(take_form('create') === [], 'Expired drafts must not reappear.');
check_form(!isset($_SESSION['form_recovery']), 'Discard expired data.');
remember_form('create', ['name' => 'Old'], ['name']);
remember_form('revise:4', ['name' => 'New'], ['name']);
check_form(take_form('create') === [], 'Keep only the latest failed form.');
check_form(take_form('revise:4')['name'] === 'New', 'Retain the newest failed form.');
remember_form('create', ['name' => str_repeat('x', 50001)], ['name']);
check_form(strlen(take_form('create')['name']) === 50000, 'Bound unusually large submitted fields.');
echo "Form recovery tests passed: $checks checks.\n";
