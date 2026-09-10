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
require '/var/www/ol-value-navigator/lib/bootstrap.php';
require_stage(4);
require_post();
verify_csrf();
$id = post_id();
find_comparison($id);
$calculateAfterSave = ($_POST['next'] ?? '') === 'calculate';
// A crafted Lab 4 POST must not bypass the calculation stage gate.
if ($calculateAfterSave) { require_stage(5); }
$submittedLines = $_POST['lines'] ?? null;
if (!is_array($submittedLines) || $submittedLines === []) {
    fail_page('Review not saved', 'No review lines were submitted.');
}

$allowedStatuses = ['AI_SUGGESTED', 'CONFIRMED', 'EXCLUDED', 'UNRESOLVED'];
// This ownership query is the server-side defense against submitted line-ID tampering.
$owned = db()->prepare(
    'SELECT l.id, l.line_number, i.input_side FROM comparison_line l JOIN comparison_input i ON i.id = l.comparison_input_id
     WHERE l.id = ? AND i.comparison_id = ?'
);
$update = db()->prepare(
    'UPDATE comparison_line SET sku = ?, description = ?, quantity = ?, annual_unit_price = ?,
     comparison_group = ?, review_status = ?, representative_note = ? WHERE id = ?'
);

$lineLabel = '';
try {
    // Do not retain a partially saved review if any submitted line is invalid.
    db()->beginTransaction();
    foreach ($submittedLines as $lineId => $values) {
        $lineLabel = '';
        if (!is_array($values) || !ctype_digit((string) $lineId)) {
            throw new InvalidArgumentException('A submitted line identifier was invalid.');
        }
        $owned->execute([(int) $lineId, $id]);
        $ownedLine = $owned->fetch();
        if (!$ownedLine) {
            throw new InvalidArgumentException('A submitted line does not belong to this comparison.');
        }
        $lineLabel = ($ownedLine['input_side'] === 'RHEL' ? 'RHEL' : 'Oracle Linux') . ' line ' . $ownedLine['line_number'] . ': ';
        foreach (['sku', 'description', 'quantity', 'price', 'group', 'status', 'note'] as $field) {
            if (isset($values[$field]) && !is_string($values[$field])) {
                throw new InvalidArgumentException('A submitted field has an invalid format.');
            }
        }
        $sku = normalize_text((string) ($values['sku'] ?? ''));
        $description = normalize_text((string) ($values['description'] ?? ''));
        $quantity = trim((string) ($values['quantity'] ?? ''));
        $price = trim((string) ($values['price'] ?? ''));
        $groupText = trim((string) ($values['group'] ?? ''));
        $status = (string) ($values['status'] ?? '');
        $note = normalize_text((string) ($values['note'] ?? ''));

        foreach ([[$sku, 128, 'SKU'], [$description, 500, 'Description'], [$note, 500, 'Representative note']] as [$text, $limit, $label]) {
            if (strlen($text) > $limit) {
                throw new InvalidArgumentException($label . ' exceeds its maximum length of ' . $limit . ' bytes.');
            }
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
            $missing = [];
            foreach (['SKU' => $sku, 'Description' => $description, 'Quantity' => $quantity, 'Annual unit price' => $price, 'Group' => $group] as $label => $value) {
                if ($value === '' || $value === null) { $missing[] = $label; }
            }
            throw new InvalidArgumentException('Complete these fields before confirming: ' . implode(', ', $missing) . '.');
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
} catch (InvalidArgumentException $exception) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    remember_form('review:' . $id, $_POST, ['lines']);
    flash('error', $lineLabel . $exception->getMessage() . ' Nothing was saved. Your entries are restored below.');
    redirect('/review.php?id=' . $id);
} catch (Throwable $exception) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    error_log('OLVN review save failed: ' . get_class($exception));
    remember_form('review:' . $id, $_POST, ['lines']);
    flash('error', 'The review could not be saved. Nothing was saved. Your entries are restored below.');
    redirect('/review.php?id=' . $id);
}

// Review persistence and calculation have separate outcomes. On a calculation
// failure the review is already saved, so never restore it as an unsaved draft.
if ($calculateAfterSave) {
    try {
        save_calculated_results($id);
        flash('success', 'Annual, three-year, and five-year results were calculated and saved.');
        redirect('/results.php?id=' . $id);
    } catch (DomainException $exception) {
        flash('warning', 'Your review was saved. ' . $exception->getMessage());
    } catch (Throwable $exception) {
        error_log('OLVN calculation after review failed: ' . get_class($exception));
        flash('error', 'Your review was saved, but results could not be calculated or saved. Try Save review and calculate again.');
    }
} else {
    flash('success', 'Representative decisions were saved.');
}
redirect('/review.php?id=' . $id);
