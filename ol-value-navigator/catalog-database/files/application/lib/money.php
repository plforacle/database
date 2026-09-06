<?php
declare(strict_types=1);

/*
 * Deterministic calculation library for reviewed subscription-cost lines.
 * All arithmetic uses scaled integers, while validation prevents incomplete or
 * unpaired representative decisions from reaching a result snapshot.
 */

/**
 * Convert an unsigned decimal string to an integer at the requested scale.
 *
 * Monetary calculations never use binary floating point. For example, 12.34 at
 * scale 2 becomes 1234, while quantity 1.50 becomes 150. Callers can therefore
 * multiply and round using integers with repeatable results.
 *
 * @param string $value Plain decimal text without currency or separators.
 * @param int $scale Number of fractional digits to preserve.
 * @return int Scaled integer representation.
 * @throws InvalidArgumentException When the format or precision is invalid.
 */
function decimal_to_scaled_int(string $value, int $scale): int
{
    // Convert decimal text to an integer scale before arithmetic to avoid binary floating-point drift.
    $value = trim($value);
    if (!preg_match('/^(0|[1-9][0-9]*)(?:\.([0-9]+))?$/', $value, $matches)) {
        throw new InvalidArgumentException('A decimal value has an invalid format.');
    }
    $fraction = $matches[2] ?? '';
    if (strlen($fraction) > $scale) {
        throw new InvalidArgumentException("A decimal value can have at most {$scale} decimal places.");
    }
    $fraction = str_pad($fraction, $scale, '0');
    return ((int) $matches[1] * (10 ** $scale)) + (int) $fraction;
}

/**
 * Convert a scaled integer back to a signed fixed-width decimal string.
 *
 * @param int $value Scaled integer value.
 * @param int $scale Number of digits to place after the decimal point.
 * @return string Decimal text suitable for a DECIMAL database column.
 */
function scaled_int_to_decimal(int $value, int $scale = 2): string
{
    $negative = $value < 0;
    $absolute = abs($value);
    $factor = 10 ** $scale;
    $whole = intdiv($absolute, $factor);
    $fraction = str_pad((string) ($absolute % $factor), $scale, '0', STR_PAD_LEFT);
    return ($negative ? '-' : '') . $whole . '.' . $fraction;
}

/**
 * Multiply a two-decimal unit price by a two-decimal quantity.
 *
 * The intermediate value is measured in ten-thousandths. Adding 50 before
 * integer division applies positive half-up rounding to the nearest cent.
 *
 * @param string $price Annual unit price as nonnegative decimal text.
 * @param string $quantity Positive quantity as decimal text.
 * @return int Extended annual price in cents.
 * @throws InvalidArgumentException When either decimal is malformed.
 */
function multiply_price_by_quantity(string $price, string $quantity): int
{
    $priceCents = decimal_to_scaled_int($price, 2);
    $quantityHundredths = decimal_to_scaled_int($quantity, 2);
    $product = $priceCents * $quantityHundredths;
    return intdiv($product + 50, 100);
}

/**
 * Validate reviewed lines and calculate annual, three-year, and five-year totals.
 *
 * Calculation fails closed when lines are absent, still require a decision, lack
 * required values, or use an unpaired comparison group. Excluded lines remain
 * traceable in the workbook but do not contribute to totals. A positive
 * difference means the confirmed RHEL total exceeds the Oracle Linux total.
 *
 * @param int $comparisonId Comparison primary key.
 * @return array<string,string> Nine fixed-point totals and differences.
 * @throws DomainException When review or alignment is incomplete.
 * @throws InvalidArgumentException When a saved decimal is malformed.
 * @throws PDOException When the comparison-line query fails.
 */
function calculate_comparison_totals(int $comparisonId): array
{
    // Fail closed until every included line is confirmed and every group contains both input sides.
    $lines = comparison_lines($comparisonId);
    if ($lines === []) {
        throw new DomainException('Format or add lines before calculating.');
    }

    $blocking = array_filter(
        $lines,
        static fn(array $line): bool => in_array($line['review_status'], ['AI_SUGGESTED', 'UNRESOLVED'], true)
    );
    if ($blocking !== []) {
        throw new DomainException('Resolve, confirm, or exclude every line before calculating.');
    }

    // Totals stay in cents until the final database-ready result is assembled.
    $totals = ['RHEL' => 0, 'ORACLE_LINUX' => 0];
    $groups = [];
    $confirmedCount = ['RHEL' => 0, 'ORACLE_LINUX' => 0];

    foreach ($lines as $line) {
        if ($line['review_status'] !== 'CONFIRMED') {
            continue;
        }
        if ($line['comparison_group'] === null || (int) $line['comparison_group'] < 1) {
            throw new DomainException('Every confirmed line requires a positive comparison group.');
        }
        foreach (['sku', 'description', 'quantity', 'annual_unit_price'] as $field) {
            if ($line[$field] === null || trim((string) $line[$field]) === '') {
                throw new DomainException('Every confirmed line requires SKU, description, quantity, and annual unit price.');
            }
        }
        $side = $line['input_side'];
        $group = (int) $line['comparison_group'];
        $groups[$group][$side] = true;
        $confirmedCount[$side]++;
        $totals[$side] += multiply_price_by_quantity(
            (string) $line['annual_unit_price'],
            (string) $line['quantity']
        );
    }

    if ($confirmedCount['RHEL'] === 0 || $confirmedCount['ORACLE_LINUX'] === 0) {
        throw new DomainException('At least one confirmed line is required on each side.');
    }
    foreach ($groups as $group => $sides) {
        if (!isset($sides['RHEL'], $sides['ORACLE_LINUX'])) {
            throw new DomainException("Comparison group {$group} must contain a confirmed line from each side.");
        }
    }

    $rhel = $totals['RHEL'];
    $oracle = $totals['ORACLE_LINUX'];
    return [
        'rhel_annual_total' => scaled_int_to_decimal($rhel),
        'oracle_linux_annual_total' => scaled_int_to_decimal($oracle),
        'annual_difference' => scaled_int_to_decimal($rhel - $oracle),
        'rhel_three_year_total' => scaled_int_to_decimal($rhel * 3),
        'oracle_linux_three_year_total' => scaled_int_to_decimal($oracle * 3),
        'three_year_difference' => scaled_int_to_decimal(($rhel - $oracle) * 3),
        'rhel_five_year_total' => scaled_int_to_decimal($rhel * 5),
        'oracle_linux_five_year_total' => scaled_int_to_decimal($oracle * 5),
        'five_year_difference' => scaled_int_to_decimal(($rhel - $oracle) * 5),
    ];
}

/**
 * Format a database decimal as display-only US currency.
 *
 * @param string|int|null $value Fixed-point decimal returned by the database.
 * @return string Currency text such as $1,234.50 or -$12.00.
 * @throws InvalidArgumentException When the value is not a signed money decimal.
 */
function money(string|int|null $value): string
{
    $text = trim((string) $value);
    if (!preg_match('/^(-?)([0-9]+)(?:\.([0-9]{1,2}))?$/', $text, $matches)) {
        throw new InvalidArgumentException('A money value has an invalid format.');
    }
    $whole = preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $matches[2]);
    $fraction = str_pad($matches[3] ?? '', 2, '0');
    return ($matches[1] === '-' ? '-$' : '$') . $whole . '.' . $fraction;
}
