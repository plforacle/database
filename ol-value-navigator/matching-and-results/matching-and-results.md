# Lab 4: Match SKUs and Calculate Results

## Introduction

This lab walks you through matching confirmed RHEL lines to governed Oracle Linux options and presenting traceable subscription-cost results.

Estimated Time: 75 minutes

### About Governed Matching

A mapping stored in MySQL may suggest an eligible Oracle Linux option. The representative still reviews the choice. Unknown SKUs remain unresolved, and excluded lines require an explanation.

### Objectives

In this lab, you will:

* Retrieve governed SKU mappings.
* Record confirmed, excluded, and unresolved decisions.
* Calculate annual, three-year, and five-year totals.
* Withhold totals when included lines remain unresolved.

### Prerequisites

This lab assumes you have:

* A working PHP calculator connected to the workshop database.
* Demonstration catalog and mapping rows.
* A saved comparison containing at least one RHEL line.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Add governed matching

1. Create a PHP function that retrieves mappings from `sku_mapping` by RHEL SKU.

2. Display each matched Oracle Linux option and its rationale beside the RHEL line.

3. Allow the representative to mark a valid mapped line as `CONFIRMED`.

4. Allow the representative to mark a line as `EXCLUDED` only after entering an exclusion reason.

5. Keep an unknown SKU as `UNRESOLVED`. Do not invent a mapping.

## Task 2: Add the calculation rules

1. Calculate the confirmed RHEL annual amount.

    ```text
    RHEL annual amount = quantity x RHEL annual unit price
    ```

2. Calculate the selected Oracle Linux annual amount.

    ```text
    Oracle annual amount = quantity x Oracle annual unit price
    ```

3. Calculate the period totals.

    ```text
    three-year total = annual total x 3
    five-year total = annual total x 5
    ```

4. Use fixed-precision decimal values for money. Do not use binary floating point values for monetary calculations.

## Task 3: Create the results page

1. Create `results.php` and load one saved comparison by its identifier.

2. Display each RHEL SKU, quantity, decision, selected Oracle Linux option, and mapping rationale.

3. Display the annual, three-year, and five-year totals only when every included line is confirmed.

4. Display excluded and unresolved lines with their reasons. Do not include those lines in the comparative savings amount.

## Task 4: Verify the fail-closed behavior

1. Create a comparison containing one known SKU and one unknown SKU.

2. Confirm that the known line is visible and the comparative totals are withheld.

3. Exclude the unknown line with a reason or correct it to a known demonstration SKU.

4. Confirm the remaining lines and verify the displayed totals manually.

## Learn More

* [PHP arbitrary precision mathematics](https://www.php.net/manual/en/book.bc.php)
* [MySQL fixed-point data types](https://dev.mysql.com/doc/refman/8.4/en/fixed-point-types.html)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, August 2026
