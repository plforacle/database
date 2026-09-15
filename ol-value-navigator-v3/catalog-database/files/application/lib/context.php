<?php
declare(strict_types=1);

/**
 * Optional, representative-written presentation context, never a calculation input.
 * One field contract drives validation, forms, display and exports. Existing rows
 * and older test fixtures without these fields behave as blank context.
 */
function customer_context_fields(): array
{
    return [
        'customer_name' => ['Customer name', 120],
        'customer_objective' => ['Objective', 300],
        'comparison_scope' => ['Comparison scope', 300],
        'recommended_next_step' => ['Recommended next step', 300],
    ];
}

/** Validate UTF-8 text on the server; reject arrays, controls and oversized input. */
function customer_context_values(array $input): array
{
    $values = [];
    foreach (customer_context_fields() as $key => [$label, $limit]) {
        $value = $input[$key] ?? '';
        if (!is_string($value) || !preg_match('//u', $value)
            || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value)) {
            throw new InvalidArgumentException($label . ' must contain plain text.');
        }
        $value = trim(str_replace(["\r\n", "\r"], "\n", $value));
        if (preg_match_all('/./us', $value) > $limit) {
            throw new InvalidArgumentException($label . ' must contain at most ' . $limit . ' characters.');
        }
        $values[$key] = $value;
    }
    return $values;
}

/** Shared escaped fields used during creation and independent context editing. */
function render_customer_context_fields(array $values = []): void
{
    foreach (customer_context_fields() as $key => [$label, $limit]) {
        echo '<label for="' . $key . '">' . h($label) . ' (optional)</label>';
        echo '<textarea class="context-input" id="' . $key . '" name="' . $key
            . '" maxlength="' . $limit . '" rows="2">' . h($values[$key] ?? '') . '</textarea>';
        echo '<small>Maximum ' . $limit . ' characters.</small>';
    }
}

/** Show complete context as inert text, keeping blank fields explicit. */
function render_customer_context(array $comparison, bool $showEdit = true): void
{
    echo '<section class="card"><h2>Customer context</h2><dl class="customer-context">';
    foreach (customer_context_fields() as $key => [$label]) {
        $value = (string) ($comparison[$key] ?? '');
        echo '<dt>' . h($label) . '</dt><dd>' . nl2br(h($value === '' ? 'Not provided' : $value)) . '</dd>';
    }
    echo '</dl>';
    if ($showEdit) {
        echo '<a class="button secondary" href="' . h(app_url('/context.php?id=' . (int) $comparison['id']))
            . '">Edit customer details</a>';
    }
    echo '</section>';
}
