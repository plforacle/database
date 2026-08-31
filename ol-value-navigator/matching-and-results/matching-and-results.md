# Lab 5: Align Confirmed RHEL and Oracle Linux Lines and Build the Cost Results Page

## Introduction

This lab walks you through reviewing the lines formatted from the complete RHEL and Oracle Linux freeform inputs, aligning representative-confirmed lines, and presenting traceable subscription-cost results.

Estimated Time: 75 minutes

### About Representative Alignment

AI formats each side but does not declare product equivalence. The representative assigns related lines to the same comparison group, confirms their values, and resolves or excludes uncertain lines before calculation.

### Objectives

In this lab, you will:

* Align related RHEL and Oracle Linux lines.
* Record confirmed, excluded, and unresolved decisions.
* Calculate annual, three-year, and five-year totals.
* Withhold totals when included lines remain unresolved.

### Prerequisites

This lab assumes you have:

* A working PHP calculator connected to the workshop database.
* A saved comparison containing the complete RHEL and Oracle Linux source text and the MySQL HeatWave GenAI suggestions created in Lab 4.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Add representative alignment

1. Display the formatted RHEL and Oracle Linux lines side by side with the complete original text from both freeform inputs available for reference.

2. Allow the representative to assign related lines to the same `comparison_group`.

3. Allow the representative to correct SKU, description, quantity, and annual unit price before marking a line `CONFIRMED`.

4. Allow the representative to mark a line as `EXCLUDED` only after entering an exclusion reason.

5. Keep uncertain or incomplete values `UNRESOLVED`. Do not invent missing values or equivalence.

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

2. Display each confirmed RHEL and Oracle Linux line, its comparison group, quantity, price, and decision.

3. Display the annual, three-year, and five-year totals only when every included line is confirmed.

4. Display excluded and unresolved lines with their reasons. Do not include those lines in the comparative savings amount.

## Task 4: Verify the fail-closed behavior

1. Create a comparison containing one confirmed group and one unresolved line.

2. Confirm that the completed group is visible and comparative totals are withheld.

3. Exclude the unresolved line with a reason or correct and confirm it.

4. Confirm the remaining lines and verify the displayed totals manually.

## Learn More

* [PHP arbitrary precision mathematics](https://www.php.net/manual/en/book.bc.php)
* [MySQL HeatWave fixed-point data types](https://dev.mysql.com/doc/refman/8.4/en/fixed-point-types.html)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, August 2026
