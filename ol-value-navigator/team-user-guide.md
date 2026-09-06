# Oracle Linux Value Navigator Team User Guide

## Purpose

Oracle Linux Value Navigator is a workshop prototype for organizing supplied RHEL and Oracle Linux subscription information into a reviewable comparison. MySQL HeatWave GenAI formats the supplied text into candidate fields. A representative reviews every candidate and controls every correction, alignment, exclusion, and confirmation. PHP calculates and saves the annual, three-year, and five-year results.

This application is not a quote, licensing determination, product-equivalence determination, or complete total cost of ownership analysis.

## Before you begin

1. Obtain the application URL from the presenter.

2. Use demonstration information only. Do not enter customer, confidential, personal, production, or contract information.

3. Remember that Version 1 has no login or comparison ownership. Everyone using the prototype can see and work with the same saved comparisons.

4. Give your comparison a unique name using this format:

    ```text
    Team demonstration - YOUR_NAME - YYYY-MM-DD
    ```

## Open the application Help

1. Select **Help** in the application header from any page.

2. Use **Quick Start** for the shortest complete workflow.

3. Use the remaining Help topics for review decisions, saved comparison actions, troubleshooting, and Version 1 boundaries.

4. Select **Return to comparisons** when you are ready to begin.

## Create a comparison

1. Open the application URL in a browser.

2. Confirm that the **Comparisons** page contains:

    * **Comparison name**
    * **RHEL SKU information**
    * **Oracle Linux SKU information**
    * **Save original inputs**
    * **Saved comparisons**

3. In **Comparison name**, enter your unique demonstration name.

4. Paste the complete supplied RHEL text into **RHEL SKU information**. For a demonstration, use:

    ```text
    DEMO-RHEL-STD | Demonstration RHEL standard support | Quantity 10 | Annual unit price USD 1200.00
    DEMO-RHEL-PREM | Demonstration RHEL premium support | Quantity 2 | Annual unit price USD 2400.00
    Note: synthetic demonstration data only.
    ```

5. Paste the complete supplied Oracle Linux text into **Oracle Linux SKU information**. For a demonstration, use:

    ```text
    DEMO-OL-BASIC | Demonstration Oracle Linux basic support | Quantity 10 | Annual unit price USD 800.00
    DEMO-OL-PREM | Demonstration Oracle Linux premier support | Quantity 2 | Annual unit price USD 1600.00
    Note: synthetic demonstration data only.
    ```

6. Select **Save original inputs** once.

7. Confirm that the application reports `The complete original inputs were saved.`

8. Confirm that the comparison page shows:

    * Your comparison name
    * Status `DRAFT`
    * Rule version `workshop-v1`
    * Both complete original inputs

## Format the inputs with GenAI

1. On the comparison page, select **Format with GenAI** once.

2. Wait for the request to finish. Formatting both inputs can take several seconds. Do not refresh the page or select the button again while the request is running.

3. Read the completion message. The application reports the RHEL and Oracle Linux results independently.

4. Confirm that the **Review and align** page opens.

5. If one side reports that formatting could not be completed, use the manual fallback procedure in this guide. Do not invent missing subscription information.

## Review every line

1. Expand **Show complete original input** under each input side.

2. Compare every candidate line with the corresponding original input.

3. Review these fields on every line:

    * SKU
    * Description
    * Quantity
    * Annual unit price
    * Group
    * Decision
    * Representative note or exclusion reason

4. Correct a field only when the correct value appears in the supplied original input.

5. Do not treat the displayed AI confidence as approval. It is part of the preserved AI suggestion.

6. Use the following expected values for the demonstration:

    | Input side | SKU | Quantity | Annual unit price | Group |
    | --- | --- | ---: | ---: | ---: |
    | RHEL | `DEMO-RHEL-STD` | `10` | `1200.00` | `1` |
    | Oracle Linux | `DEMO-OL-BASIC` | `10` | `800.00` | `1` |
    | RHEL | `DEMO-RHEL-PREM` | `2` | `2400.00` | `2` |
    | Oracle Linux | `DEMO-OL-PREM` | `2` | `1600.00` | `2` |

7. Assign related RHEL and Oracle Linux lines the same positive group number. A group records the representative's chosen alignment. It does not claim that the products are equivalent.

8. Select one decision for every line:

    | Decision | When to use it |
    | --- | --- |
    | **Confirmed** | You reviewed all required fields and accept the line for calculation. |
    | **Excluded** | The supplied line must not participate in the calculation. Enter an exclusion reason. |
    | **Unresolved** | A supplied value or decision remains uncertain. Enter a representative note. |
    | **Needs review** | Review is not complete. This decision blocks calculation. |

9. For the four demonstration lines, select **Confirmed** after checking their values and groups.

10. If GenAI created a line that is not a subscription item, select **Excluded** and enter a concise reason based on the original input.

11. Select **Save representative review**.

12. Confirm that the application reports `Representative decisions were saved.`

13. Confirm that the comparison is ready for calculation. Calculation remains blocked while any line is marked **Needs review** or **Unresolved**, or when a confirmed group does not contain confirmed lines from both input sides.

## Add a line manually

Use manual fallback only when a supplied subscription item was not extracted or GenAI formatting was unavailable.

1. On the **Review and align** page, scroll to **Manual fallback**.

2. Select **Add RHEL line** or **Add Oracle Linux line** for the side containing the supplied item.

3. Confirm that the application reports `A manual line was added. Complete its fields and save the review.`

4. Locate the new line marked `MANUAL`.

5. Complete its fields using only values from the corresponding original input.

6. Assign a positive comparison group.

7. Select the appropriate decision.

8. Enter a representative note when the decision is **Excluded** or **Unresolved**.

9. Select **Save representative review**.

## Calculate and review the results

1. Confirm that every line has a final **Confirmed** or **Excluded** decision.

2. Confirm that each confirmed group contains at least one confirmed RHEL line and at least one confirmed Oracle Linux line.

3. Select **Calculate confirmed results**.

4. Confirm that the application reports `Annual, three-year, and five-year results were calculated and saved.`

5. Review the **Confirmed subscription-cost comparison** table.

6. For the demonstration values, confirm these totals:

    | Period | RHEL | Oracle Linux | Difference |
    | --- | ---: | ---: | ---: |
    | Annual | `$16,800.00` | `$11,200.00` | `$5,600.00` |
    | Three years | `$50,400.00` | `$33,600.00` | `$16,800.00` |
    | Five years | `$84,000.00` | `$56,000.00` | `$28,000.00` |

7. Review the **Traceable reviewed lines** table. Confirm that it shows the side, group, SKU, description, quantity, annual unit price, decision, and representative note for every line.

8. Confirm that the page identifies the calculation rule version and calculation timestamp.

9. Interpret a positive difference as the confirmed RHEL total minus the confirmed Oracle Linux total. Do not interpret the difference as a quote or guaranteed savings.

## Reopen a saved comparison

1. From the results or review page, select **Comparison**.

2. Select **All comparisons**.

3. Locate your comparison under **Saved comparisons**.

4. Review its status, line count, and updated time.

5. Select **Open**.

6. Select **Review lines** to inspect the reviewed data or **View results** when the comparison has a calculated result.

## Export the workbook

1. Open the required comparison.

2. Under **Workbook actions**, select **Export CSV workbook**.

3. Confirm that the browser downloads a CSV file whose name begins with `olvn-`.

4. Open the file with a spreadsheet application.

5. Confirm that it contains:

    * Comparison identifier, name, status, and rule version
    * Both complete original inputs
    * AI suggestions and representative-reviewed values
    * Decisions and representative notes
    * Calculated results when a result snapshot exists

## Duplicate a comparison

1. Open the comparison you want to copy.

2. Under **Workbook actions**, select **Duplicate comparison** once.

3. Confirm that the application reports `The comparison was duplicated without a result snapshot. Review and calculate the copy.`

4. Confirm that the duplicate name ends with `copy` and its status is `NEEDS_REVIEW`.

5. Select **Review lines**, verify every copied line and decision, and select **Save representative review**.

6. Select **Calculate confirmed results** to create a result snapshot for the duplicate.

## Revise original inputs

Revising original inputs clears all formatted lines and the calculated result for that comparison. The workflow event history remains.

1. Open the comparison you want to revise.

2. Under **Workbook actions**, select **Revise original inputs**.

3. Review the warning before continuing.

4. Update the comparison name or either complete original input.

5. Select **Save revision and clear derived data**.

6. Confirm that the application reports that the original inputs were revised and that previous lines and results were cleared.

7. Select **Format with GenAI** and repeat the complete review, alignment, decision, and calculation process.

## Delete a comparison

Deleting a comparison is permanent. The application deletes its original inputs, formatting runs, reviewed lines, calculated result, and workflow events. It retains only the comparison ID, name, and deletion time in a separate audit record.

1. Open the comparison you intend to delete.

2. Scroll to **Delete comparison**.

3. Confirm that the displayed comparison name is the one you intend to delete.

4. Type the complete comparison name exactly as displayed. The confirmation is case-sensitive and must not contain extra spaces.

5. Select **Delete comparison and associated data** once.

6. If the name does not match, confirm that the application reports `The comparison name did not match. Nothing was deleted.`

7. After entering the exact name, confirm that the application returns to **Comparisons** and reports that the comparison and its associated data were deleted.

8. Confirm that the deleted comparison no longer appears under **Saved comparisons**.

## Status reference

| Status | Meaning |
| --- | --- |
| `DRAFT` | Original inputs are saved, but no current formatted lines are ready for review. |
| `NEEDS_REVIEW` | At least one line needs a final representative decision or the comparison has not been recalculated. |
| `CONFIRMED` | Every line has a final decision and at least one line is confirmed. |
| `CALCULATED` | The application saved the annual, three-year, and five-year result snapshot. |

## Current Version 1 boundaries

* No login or multi-user ownership
* No master RHEL or Oracle Linux SKU catalogs
* Demonstration data only
* No production approval of calculation rules
* No claim that aligned lines are product equivalents
