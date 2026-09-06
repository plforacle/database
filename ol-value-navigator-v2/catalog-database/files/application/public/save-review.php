<?php
declare(strict_types=1);
/**
 * POST controller for saving Stage 4 representative review decisions.
 *
 * Each submitted line identifier is joined back to the requested comparison before
 * any update, preventing a modified form from changing another workbook's lines.
 * The route bounds text, validates decimal and group formats, requires complete
 * fields for confirmation, and requires notes for exclusions and unresolved lines.
 * All line edits, result invalidation, derived workbook status, and event logging
 * commit together.
 */
require '/var/www/ol-value-navigator-2/lib/bootstrap.php';
require_stage(4);
require_post();
verify_csrf();
$id = post_id();
find_comparison($id);
$submittedLines = $_POST['lines'] ?? null;
if (!is_array($submittedLines) || $submittedLines === []) {
    fail_page('Review not saved', 'No review lines were submitted.');
}

$allowedStatuses = ['AI_SUGGESTED', 'CONFIRMED', 'EXCLUDED', 'UNRESOLVED'];
// This ownership query is the server-side defense against submitted line-ID tampering.
$owned = db()->prepare(
    'SELECT l.id FROM comparison_line l JOIN comparison_input i ON i.id = l.comparison_input_id
     WHERE l.id = ? AND i.comparison_id = ?'
);
$update = db()->prepare(
    'UPDATE comparison_line SET sku = ?, description = ?, quantity = ?, annual_unit_price = ?,
     comparison_group = ?, review_status = ?, representative_note = ? WHERE id = ?'
);

try {
    // Do not retain a partially saved review if any submitted line is invalid.
    db()->beginTransaction();
    foreach ($submittedLines as $lineId => $values) {
        if (!is_array($values) || !ctype_digit((string) $lineId)) {
            throw new InvalidArgumentException('A submitted line identifier was invalid.');
        }
        $owned->execute([(int) $lineId, $id]);
        if (!$owned->fetchColumn()) {
            throw new InvalidArgumentException('A submitted line does not belong to this comparison.');
        }
        $sku = normalize_text((string) ($values['sku'] ?? ''));
        $description = normalize_text((string) ($values['description'] ?? ''));
        $quantity = trim((string) ($values['quantity'] ?? ''));
        $price = trim((string) ($values['price'] ?? ''));
        $groupText = trim((string) ($values['group'] ?? ''));
        $status = (string) ($values['status'] ?? '');
        $note = normalize_text((string) ($values['note'] ?? ''));

        if (strlen($sku) > 128 || strlen($description) > 500 || strlen($note) > 500) {
            throw new InvalidArgumentException('A reviewed field exceeded its maximum length.');
        }
        if (!in_array($status, $allowedStatuses, true)) {
            throw new InvalidArgumentException('A review decision was invalid.');
        }
        $decimalPattern = '/^(0|[1-9][0-9]*)(?:\.[0-9]{1,2})?$/';
        if ($quantity !== '' && (!preg_match($decimalPattern, $quantity) || (float) $quantity <= 0 || (float) $quantity > 1000000)) {
            throw new InvalidArgumentException('Quantity must be a positive number with no more than two decimal places.');
        }
        if ($price !== '' && (!preg_match($decimalPattern, $price) || (float) $price > 100000000)) {
            throw new InvalidArgumentException('Annual unit price must be a nonnegative number with no more than two decimal places.');
        }
        $group = null;
        if ($groupText !== '') {
            if (!ctype_digit($groupText) || (int) $groupText < 1) {
                throw new InvalidArgumentException('Comparison groups must be positive whole numbers.');
            }
            $group = (int) $groupText;
        }
        if ($status === 'CONFIRMED' && ($sku === '' || $description === '' || $quantity === '' || $price === '' || $group === null)) {
            throw new InvalidArgumentException('A confirmed line requires SKU, description, quantity, annual unit price, and comparison group.');
        }
        if (in_array($status, ['EXCLUDED', 'UNRESOLVED'], true) && $note === '') {
            throw new InvalidArgumentException('Excluded and unresolved lines require a representative note.');
        }
        $update->execute([
            $sku === '' ? null : $sku,
            $description === '' ? null : $description,
            $quantity === '' ? null : $quantity,
            $price === '' ? null : $price,
            $group,
            $status,
            $note === '' ? null : $note,
            (int) $lineId,
        ]);
    }
    // Any edit invalidates the old snapshot, even when the workbook remains confirmed.
    db()->prepare('DELETE FROM comparison_result WHERE comparison_id = ?')->execute([$id]);
    $counts = line_counts($id);
    $status = ($counts['AI_SUGGESTED'] + $counts['UNRESOLVED']) === 0 && $counts['CONFIRMED'] > 0
        ? 'CONFIRMED' : 'NEEDS_REVIEW';
    db()->prepare('UPDATE comparison SET status = ? WHERE id = ?')->execute([$status, $id]);
    // The event outcome records a successful save; details retain the resulting counts.
    record_event($id, 'REPRESENTATIVE_REVIEW_SAVED', 'REPRESENTATIVE', 'CONFIRMED', $counts);
    db()->commit();
    flash('success', 'Representative decisions were saved.');
    redirect('/review.php?id=' . $id);
} catch (InvalidArgumentException $exception) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    fail_page('Review validation failed', $exception->getMessage());
} catch (Throwable $exception) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    error_log('OLVN review save failed: ' . get_class($exception));
    fail_page('Review not saved', 'The review could not be saved. Try again.', 500);
}
