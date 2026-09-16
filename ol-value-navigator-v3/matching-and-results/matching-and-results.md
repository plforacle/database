# Lab 5: Calculate, Save, and Export Comparisons

## Introduction

> **Version 3 authoring draft.** This lab is not ready for deployment or a learner run. Read the prerequisites and pending checkpoints before executing anything. Version 1 remains unchanged.

Validate cost results against independently reviewed expectations. Preserve the saved result and its price basis when producing an export.

Estimated Time: Pending calculation and export rehearsal.

### Objectives

* Distinguish implemented term support from the target release.
* Check arithmetic and incomplete-input behavior.
* Reopen saved comparisons and inspect exported files.

## Task 1: Confirm supported periods

1. Inspect `app/Domain/Coverage/ScenarioTerm.php`. The newer scenario currently accepts 12 or 36 months.

2. Compare that behavior with the Version 3 requirement for annual, three-year, and five-year results. The older analysis calculator's five-year behavior does not implement it in the newer scenario.

3. Implement and test the agreed 12/36/60-month policy across validation, price assumptions, stored results, screens, and presentation exports before teaching it.

4. Do not extend a fixed 36-month contract to five years without an explicit, reviewed assumption.

    **Stop condition:** Five-year scenario output remains a release blocker, not an available button or a display-only change.

## Task 2: Check an independent example

1. Use invented annual totals of USD 1,200 for RHEL and USD 800 for Oracle Linux. These are arithmetic inputs, not product prices or a complete catalog scenario.

2. Under fixed quantities and rates, calculate the following expected totals independently.

    | Period | RHEL | Oracle Linux | Difference |
    | --- | --- | --- | --- |
    | Annual | 1,200.00 | 800.00 | 400.00 |
    | Three years | 3,600.00 | 2,400.00 | 1,200.00 |
    | Five years | 6,000.00 | 4,000.00 | 2,000.00 |

3. Prepare a complete synthetic scenario with independently checked coverage units and adopted prices before comparing it with the application.

4. Test zero and negative differences, cent rounding, exclusions, missing prices, and unsupported periods. Require a correct result or a clear blocker.

## Task 3: Save and export

1. Confirm the reviewed scenario, then reopen the saved revision. Verify that inputs, customer context, results, and catalog identity remain consistent.

2. Test concurrent edits. A stale page must not silently overwrite a newer revision.

3. Download the PowerPoint from the accepted candidate and open it in PowerPoint. Check for repair warnings, clipped content, missing assumptions, and incorrect totals.

4. Verify CSV behavior after the agreed adaptation. Check formula safety and agreement with the saved result.

5. Repeat access checks with a second user. Neither an export URL nor a saved revision identifier may bypass ownership.

    **Pending checkpoint:** Owner-filtered listing, CSV parity, and the final simplified buttons need candidate-level validation.

## Acknowledgements

* **Authors** - Perside Foster, Mark Atkinson, and Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
