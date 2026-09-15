<?php
declare(strict_types=1);

/**
 * One-use, short-lived form recovery after validation or save failures.
 * Callers whitelist editable fields, so tokens and private configuration are not
 * copied. Values stay in the existing session, never in URLs or log messages.
 * The nested shape supports lines[line-id][field]; deeper submitted structures
 * are not part of any application form. The caller still validates every field
 * before database writes and escapes it when rendering the restored form.
 *
 * @param string $key Route name plus comparison ID where applicable.
 * @param array $input Submitted form values, normally $_POST.
 * @param list<string> $fields Top-level editable field names to retain.
 */
function remember_form(string $key, array $input, array $fields): void
{
    $values = array_intersect_key($input, array_flip($fields));
    // Browser-sized forms fit comfortably. Bound tampered requests before storing.
    $filter = static function (array $items, int $depth) use (&$filter): array {
        $out = [];
        foreach (array_slice($items, 0, 250, true) as $name => $value) {
            if (is_string($value)) { $out[$name] = substr($value, 0, 50000); }
            elseif (is_array($value) && $depth < 2) { $out[$name] = $filter($value, $depth + 1); }
        }
        return $out;
    };
    // Retain only the latest failed form, limiting session growth across comparisons.
    $_SESSION['form_recovery'] = [$key => ['at' => time(), 'values' => $filter($values, 0)]];
}

/**
 * Consume a matching draft once, expiring it after 15 minutes.
 * A different route or comparison cannot consume this draft. Recovery is for the
 * immediate redirect after a failure, not an autosave or a saved comparison.
 * Callers overlay these values on the view only; this function makes no DB writes.
 */
function take_form(string $key): array
{
    $draft = $_SESSION['form_recovery'][$key] ?? null;
    if ($draft === null) { return []; }
    unset($_SESSION['form_recovery']);
    return time() - (int) $draft['at'] <= 900 ? $draft['values'] : [];
}

/** Read only scalar form text; nested, tampered values are never rendered as arrays. */
function form_value(array $values, string $key, string $default = ''): string
{
    return is_string($values[$key] ?? null) ? $values[$key] : $default;
}
