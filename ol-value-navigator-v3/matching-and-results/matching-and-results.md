# Lab 5: Calculate, Save, and Manage the Complete Comparison

## Introduction

In this lab, you enable the complete Version 1 application. PHP verifies that every included line is confirmed and that each comparison group contains both sides. It then performs deterministic annual, three-year, and five-year calculations and saves the result snapshot in the MySQL HeatWave DB System.

You also verify the workbook operations required by Version 1: reopen, revise, duplicate, CSV export, and confirmed deletion.

Estimated Time: 90 minutes

### Objectives

In this lab, you will:

* Enable the complete Lab 5 application stage.
* Verify fixed-precision quantity and money calculations.
* Enforce complete representative review and two-sided alignment.
* Save and display annual, three-year, and five-year results.
* Verify fail-closed behavior for unresolved and unmatched lines.
* Open the built-in Help and verify its complete workflow guidance.
* Reopen, revise, duplicate, export, and securely delete a comparison.

### Prerequisites

This lab assumes you have:

* The application running at stage 4.
* A comparison with reviewed demonstration lines from Lab 4.
* At least one confirmed RHEL and Oracle Linux line assigned to each comparison group.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Review the deterministic calculation module

1. Change to the application source directory and open the money module.

    ```bash
    <copy>cd ~/ol-value-navigator-application
    less lib/money.php</copy>
    ```

    Press `q` to exit.

2. Confirm that `decimal_to_scaled_int` converts decimal strings into scaled integers. Prices become cents, and quantities become hundredths.

3. Confirm that `multiply_price_by_quantity` multiplies the two integers and rounds each line amount to the nearest cent. It does not use binary floating point for money calculation.

4. Confirm that `calculate_comparison_totals` blocks calculation when any of these conditions exists:

    * No formatted or manual lines exist.
    * A line remains `AI_SUGGESTED` or `UNRESOLVED`.
    * A confirmed line lacks SKU, description, quantity, annual unit price, or comparison group.
    * Either input side has no confirmed line.
    * A comparison group lacks a confirmed line from either side.

5. Review the deterministic formulas.

    ```text
    line annual amount = reviewed quantity x reviewed annual unit price
    side annual total = sum of confirmed line annual amounts
    three-year total = side annual total x 3
    five-year total = side annual total x 5
    difference = RHEL total - Oracle Linux total
    ```

    Excluded lines remain visible but do not participate. Unresolved and unreviewed lines prevent every comparative total from being calculated.

## Task 2: Enable the complete application

If you are updating an already deployed Version 1 application, first complete **Lab 2, Task 1** with the updated package. Do not recreate the database account or repeat the initial database setup. Then complete this task and Tasks 9 through 11 below. Existing comparisons remain in place. If you already applied and verified the customer-context upgrade, start at step 2; this usability update adds no database columns.

1. Apply the additive customer-context database upgrade before deploying the updated PHP files. Replace `HEATWAVE_PRIVATE_IP` with your DB System private IP and enter the administrator password when prompted.

    ```bash
    <copy>mysql --host=HEATWAVE_PRIVATE_IP --user=olvnadmin --password --ssl-mode=REQUIRED &lt; ~/ol-value-navigator-application/database/upgrade-customer-context.sql</copy>
    ```

    The upgrade adds only missing optional columns. It does not delete or recalculate comparisons and is safe to repeat, including on the fresh Lab 2 schema. Confirm that the final output lists `customer_name` with length `120`, and `customer_objective`, `comparison_scope`, and `recommended_next_step` with length `300`. Stop if the command reports an error.

2. Install the PHP ZIP extension required for PowerPoint export, restart PHP-FPM to load it, and deploy stage `5`. The line-ending command also corrects packages previously created on Windows. Existing configuration and saved comparisons are preserved. On an existing installation, deployment checks the four database columns before replacing any application files.

    ```bash
    <copy>cd ~/ol-value-navigator-application
    sed -i 's/\r$//' deploy.sh tests/verify-installation.sh
    sudo dnf install -y php-pecl-zip
    sudo systemctl restart php-fpm
    sudo bash deploy.sh 5</copy>
    ```

3. Confirm the installed stage.

    ```bash
    <copy>sudo cat /var/www/ol-value-navigator/stage</copy>
    ```

    The output must be `5`.

4. Run the application unit checks.

    ```bash
    <copy>php ~/ol-value-navigator-application/tests/unit.php</copy>
    ```

    Confirm that the output is:

    ```text
    All Oracle Linux Value Navigator unit checks passed.
    ```

    These checks cover decimal scaling, exact money rendering, signed values, line rounding, whole-quantity multiplication, complete totals, fail-closed calculation, valid GenAI response parsing, and rejection of unsupported GenAI response fields.

5. Run the customer-context, PowerPoint, form-recovery, and workflow checks.

    ```bash
    <copy>php ~/ol-value-navigator-application/tests/context.php
    php ~/ol-value-navigator-application/tests/presentation.php
    php ~/ol-value-navigator-application/tests/forms.php
    php ~/ol-value-navigator-application/tests/workflow.php</copy>
    ```

    Confirm that all four scripts report their checks passed. These tests do not change saved comparisons.

## Task 3: Calculate and reconcile the demonstration

1. Open the application in your browser. Replace `PUBLIC_IP_ADDRESS` with the compute instance public IP address.

    ```text
    <copy>http://PUBLIC_IP_ADDRESS/ol-value-navigator/</copy>
    ```

2. In **Saved comparisons**, locate **Lab 3 saved-input test**. Confirm that its status is `CONFIRMED` and its line count is `4`, and then select **Open** for that comparison.

    Do not open **Lab 4 manual fallback test**. That separate boundary-test comparison intentionally remains in `NEEDS_REVIEW` status.

3. Confirm that **Review and align: Lab 3 saved-input test** opens directly. If you previously calculated this comparison, **Open** takes you to Results instead; select **Review lines** there.

4. Locate each line by its SKU and confirm these reviewed values and alignments.

    | Side | Group | Quantity | Annual unit price | Annual line amount |
    | --- | ---: | ---: | ---: | ---: |
    | RHEL standard | 1 | 10 | $1,200.00 | $12,000.00 |
    | RHEL premium | 2 | 2 | $2,400.00 | $4,800.00 |
    | Oracle Linux basic | 1 | 10 | $800.00 | $8,000.00 |
    | Oracle Linux premier | 2 | 2 | $1,600.00 | $3,200.00 |

5. Confirm that each of the four lines has **Confirmed** selected as its decision. Correct any value or group that does not match the table, and then select **Save review and calculate**. This submits the current edits and calculates only after they are saved and validated.

6. Confirm that the application reports `Annual, three-year, and five-year results were calculated and saved.`

7. Confirm that the Results page opens automatically for **Lab 3 saved-input test**. No separate calculation button is required.

8. Confirm that the results page opens, and reconcile the displayed totals with this table.

    | Period | RHEL | Oracle Linux | Difference |
    | --- | ---: | ---: | ---: |
    | Annual | $16,800.00 | $11,200.00 | $5,600.00 |
    | Three years | $50,400.00 | $33,600.00 | $16,800.00 |
    | Five years | $84,000.00 | $56,000.00 | $28,000.00 |

9. Expand **Traceable reviewed lines** to inspect every line and decision. Confirm that the page identifies `workshop-v1` and states when the snapshot was calculated.

    > **Checkpoint:** PHP calculated and saved the annual, three-year, and five-year values from representative-confirmed decimal inputs.

## Task 4: Verify fail-closed alignment behavior

1. Select **Review lines** from the results page.

2. Change one confirmed line to **Unresolved** and enter `Demonstration of a missing representative decision` as its note.

3. Select **Save review and calculate**.

4. Confirm that you remain on Review. The message says the review was saved, but requires every line to be resolved, confirmed, or excluded before calculation.

5. Restore the line to **Confirmed** and select **Save review and calculate**.

6. From Results, select **Review lines**. Change one confirmed line to a group number that has no confirmed line from the other side, then select **Save review and calculate**.

7. Confirm that the application identifies the unmatched group and withholds totals.

8. Restore the correct group and select **Save review and calculate** again. Confirm that the expected totals return.

## Task 5: Reopen, duplicate, and revise

1. Select **Home** in the header and reopen the calculated demonstration.

2. Confirm that Results opens directly. Select **Back to comparison details**, then expand **Saved inputs, customer details, and status**. Confirm that the two original inputs, status, reviewed-line counts, and rule version remain available.

3. Expand **More actions**, then select **Duplicate comparison**.

4. On the copy, expand **Saved inputs, customer details, and status** to inspect the inputs and status. Select **Review lines** to inspect the copied suggestions, representative-reviewed fields, and decisions, then select **Back to comparison details**. Confirm that no result snapshot was copied. This forces the representative to review and calculate the copy.

5. On the copy, expand **More actions**, then select **Revise original inputs**.

6. Change the comparison name or demonstration input, then select **Save revision and clear derived data**.

7. Expand **Saved inputs, customer details, and status**. Confirm that the copy returns to `DRAFT` and its formatted lines and result are cleared. The application performs this reset because derived values must not survive a source-input revision.

8. Return to the original comparison and confirm that it remains unchanged.

## Task 6: Export the saved workbook and PowerPoint

1. Open the original calculated comparison and select **Download CSV**.

2. Open the downloaded CSV file in a text editor or spreadsheet application.

3. Confirm that it contains:

    * Comparison identifier, name, status, and rule version.
    * Customer name, objective, comparison scope, and recommended next step, blank if not supplied.
    * Both complete original inputs.
    * Original AI suggestions and representative-reviewed values.
    * Comparison groups, decisions, and notes.
    * The saved annual, three-year, and five-year result snapshot.

4. Confirm that fields beginning with spreadsheet formula characters are prefixed as text. This prevents untrusted pasted text from becoming a spreadsheet formula when the CSV is opened.

5. Verify the calculation and workbook events in the database.

    ```bash
    <copy>mysql --host=HEATWAVE_PRIVATE_IP --user=olvn_app --password --ssl-mode=REQUIRED ol_value_navigator</copy>
    ```

    ```sql
    <copy>SELECT c.id, c.name, c.status, r.calculated_at
    FROM comparison c
    LEFT JOIN comparison_result r ON r.comparison_id = c.id
    ORDER BY c.id DESC;

    SELECT comparison_id, event_type, actor_type, outcome, created_at
    FROM application_event
    WHERE event_type IN (
      'CALCULATION_COMPLETED',
      'COMPARISON_DUPLICATED',
      'INPUTS_REVISED',
      'COMPARISON_EXPORTED'
    )
    ORDER BY id DESC;</copy>
    ```

6. Exit the MySQL client.

    ```sql
    <copy>EXIT;</copy>
    ```

7. In the browser, select **Home** and open the original calculated comparison. Results opens directly. Select **Download PowerPoint**.

8. Open the downloaded `olvn-comparison-ID.pptx` file in Microsoft PowerPoint. Confirm that it opens without a repair warning and contains four slides: comparison overview, subscription-cost results, representative review, and assumptions. Confirm the white background, red table headers, alternating shaded rows, and the use of your comparison and customer names. Slide 4 places the assumptions table beside the recommended next step.

9. Compare the annual, three-year, and five-year amounts on slide 2 with the Results page. Confirm that all nine amounts match, including the difference signs. Amounts are right aligned. Positive differences appear bold green, negative differences appear bold red, and zero remains neutral. The sign and explanation remain visible so color is not the only indicator. Select a table cell in PowerPoint and verify that its text is editable.

10. Confirm that the presentation identifies the comparison, calculation rule, and calculation time. Keep the CSV workbook with the slides because the presentation summarizes the results rather than including every source line.

11. Run the PowerPoint regression checks on the compute instance.

    ```bash
    <copy>php ~/ol-value-navigator-application/tests/presentation.php</copy>
    ```

    Confirm that the output reports `PowerPoint tests passed`. The exporter uses PHP ZipArchive to produce a PowerPoint-compatible ZIP package. It does not call GenAI again or change calculation rules. A PowerPoint repair warning is a failed test even if slides remain visible; stop and report the warning.

    > **Checkpoint:** The complete Version 1 application can reopen, revise, duplicate, calculate, save, and export a representative-confirmed comparison as CSV and PowerPoint.

## Task 7: Delete a comparison with exact-name confirmation

Use the duplicate created in Task 5. Do not delete the original calculated comparison.

1. Select **Home** in the header and locate the duplicate created in Task 5.

2. Record the duplicate's name, select **Open** for that duplicate, and record its ID from the browser address.

    The numeric ID appears at the end of the browser URL after `id=`.

3. If Review opened, select **Back to comparison details**. Expand **More actions**, scroll to **Delete comparison**, and review the warning. The operation deletes the comparison's inputs, formatting runs, reviewed lines, results, and workflow events. It retains only a minimal deletion audit record.

4. Enter a name that does not exactly match the displayed comparison name, then select **Delete comparison and associated data**.

5. Confirm that the application reports `The comparison name did not match. Nothing was deleted.` and that the duplicate remains available.

6. Expand **More actions** again after the failed confirmation. Enter the complete comparison name exactly as displayed, including capitalization and spaces, then select **Delete comparison and associated data** once.

7. Confirm that the application returns to **Home**, reports that the comparison and its associated data were deleted, and no longer lists the duplicate.

8. Connect as the DB System administrator.

    ```bash
    <copy>mysql --host=HEATWAVE_PRIVATE_IP --user=olvnadmin --password --ssl-mode=REQUIRED ol_value_navigator</copy>
    ```

9. Replace `DELETED_COMPARISON_ID` and verify the retained audit record and deleted parent data.

    ```sql
    <copy>SELECT deleted_comparison_id, comparison_name, deleted_at
    FROM comparison_deletion_audit
    WHERE deleted_comparison_id = DELETED_COMPARISON_ID;

    SELECT COUNT(*) AS remaining_comparisons
    FROM comparison
    WHERE id = DELETED_COMPARISON_ID;

    SELECT COUNT(*) AS remaining_inputs
    FROM comparison_input
    WHERE comparison_id = DELETED_COMPARISON_ID;

    SELECT COUNT(*) AS remaining_results
    FROM comparison_result
    WHERE comparison_id = DELETED_COMPARISON_ID;

    SELECT COUNT(*) AS remaining_events
    FROM application_event
    WHERE comparison_id = DELETED_COMPARISON_ID;</copy>
    ```

    Confirm that the audit query returns one row and every remaining count is `0`.

10. Exit the MySQL client.

    ```sql
    <copy>EXIT;</copy>
    ```

    > **Checkpoint:** Exact-name confirmation protects the delete operation, all associated comparison data is removed, and the minimal audit record remains.

## Task 8: Verify the built-in Help

1. Return to the application in the browser and select **Help** in the header.

2. Confirm that **Help and Quick Start** opens without leaving the application.

3. Confirm that the page contains all of these help topics:

    * Quick Start
    * Review decisions
    * Saved comparison actions
    * Troubleshooting
    * Version 1 boundaries

4. Review the Quick Start and confirm that it covers creating, formatting, reviewing, calculating, and exporting a comparison.

5. Review **Saved comparison actions** and confirm that it explains reopening, exporting, duplicating, revising, and deleting a comparison.

6. Select **Home** and confirm that the application returns to the saved-comparisons page.

    > **Checkpoint:** Built-in Help provides the complete Version 1 workflow and returns the user to the application.

## Task 9: Add customer context without changing calculated results

1. Select **Home** in the header. Under **Saved comparisons**, open the calculated demonstration you used in Task 3. Results opens directly. Record its comparison name, numeric ID from the browser address after `id=`, nine displayed amounts, and **Calculated at** timestamp. In this task, this is the **original**. Do not use a copy with status `NEEDS_REVIEW`. An older comparison shows **Not provided** for the four customer-context fields.

2. Select **Edit customer details** above the results table. Enter these demonstration values. This exercise verifies that you can add presentation details to an existing result without recalculating it; customer details remain optional in normal use.

    | Field | Value |
    | --- | --- |
    | Customer name | Demo Meridian |
    | Objective | Compare subscription costs for the demonstration Linux estate. |
    | Comparison scope | Two reviewed support groups. Subscription costs only, excluding migration and hardware. |
    | Recommended next step | Review the confirmed quantities and assumptions with the team. |

    All fields are optional. Customer name allows 120 characters; each other field allows 300. These fields are not sent to GenAI and do not supply pricing or calculation rules.

3. Select **Save customer details**. Confirm that the application reports **Customer details saved. Reviewed lines and calculated results are unchanged.** On the Comparison page, expand **Saved inputs, customer details, and status** and confirm that it still shows `CALCULATED` and the existing reviewed-line counts.

4. Select **View results**. Confirm that all four details match your entries and that all nine amounts and the calculation timestamp match step 1. Do not reformat or recalculate just to change customer details.

5. Select **Download PowerPoint** and open the new download. Confirm that PowerPoint opens without repair. Slide 1 must contain the customer name, objective, and scope. Slide 4 must contain your recommended next step. Slide 2 must still contain the same nine amounts. Full saved context remains in the speaker notes when long text is shortened on slides.

6. Return to the browser tab showing **Results** for the original comparison. Select **Download CSV** above the results table. Open the new CSV and confirm that it contains all four complete context values and the original results. Files downloaded before the edit remain unchanged.

7. Select **Back to comparison details**, expand **More actions**, and select **Duplicate comparison**. Record the copy's different name and numeric ID. Expand **Saved inputs, customer details, and status** and confirm that the copy contains the same four context values. In that section, select **Edit customer details**, change Customer name to `Demo Meridian copy`, and select **Save customer details**. Select **Home**, open the original by the name recorded in step 1, and confirm that it still says `Demo Meridian`.

8. Select **Home** and open the copy by the name recorded in step 7. Review opens directly. Select **Back to comparison details**, expand **Saved inputs, customer details, and status**, then select **Edit customer details**, clear all four fields, and select **Save customer details**. Expand **Saved inputs, customer details, and status** and confirm that all four display **Not provided**. The original comparison must remain unchanged. A copy has no calculated result until you review and calculate it; do not use its ID to verify the original result.

9. Connect to the database as the application account. Replace `HEATWAVE_PRIVATE_IP` with your DB System private IP and enter the application password when prompted.

    ```bash
    <copy>mysql --host=HEATWAVE_PRIVATE_IP --user=olvn_app --password --ssl-mode=REQUIRED ol_value_navigator</copy>
    ```

10. Run these read-only queries without substitutions. They list calculated comparisons with the demonstration customer name. In each output, locate the original name and ID recorded in step 1. If multiple demonstrations match, use that recorded ID to identify your original; do not use the copy from steps 7 and 8.

    ```sql
    <copy>SELECT c.id, c.name, c.customer_name, c.customer_objective,
           c.comparison_scope, c.recommended_next_step,
           c.status, r.calculated_at
    FROM comparison c
    LEFT JOIN comparison_result r ON r.comparison_id = c.id
    WHERE c.customer_name = 'Demo Meridian'
      AND c.status = 'CALCULATED'
    ORDER BY c.id;

    SELECT c.id, c.name, e.event_type, e.outcome, e.created_at
    FROM application_event e
    JOIN comparison c ON c.id = e.comparison_id
    WHERE c.customer_name = 'Demo Meridian'
      AND c.status = 'CALCULATED'
      AND e.event_type = 'CUSTOMER_CONTEXT_UPDATED'
    ORDER BY e.id DESC;
    EXIT;</copy>
    ```

    Confirm that the original context matches step 2, its status remains `CALCULATED`, its calculation timestamp matches step 1, and a `COMPLETED` context-update event exists.

    > **Checkpoint:** Customer context persists, copies independently, appears in both exports, and can be edited or cleared without changing calculated results.

## Task 10: Verify navigation and form recovery

1. Select **Home** in the header. Confirm that it opens the page with **Create a comparison** and **Saved comparisons**. Select **Help**, then **Home**, and confirm that the same page opens.

2. Open the original calculated comparison recorded in Task 9, step 1. Confirm that Results opens directly. Confirm that the cost table appears first, followed by customer context and the expandable **Traceable reviewed lines** section. Confirm that **Download PowerPoint**, **Download CSV**, and **Edit customer details** appear together above the results.

3. Select **Back to comparison details**. Expand **Saved inputs, customer details, and status**. Confirm that the page shows the same comparison name and its saved inputs, rather than the list of all comparisons.

4. Select **Home** and open the copy recorded in Task 9, step 7. Verify its name and ID before continuing. This test must use the copy, not the original calculated comparison.

5. Confirm that Review opens directly. In the first RHEL line, record the current **Annual unit price** and representative note. Replace the price with `not-a-price` and enter `Retained validation test` in **Representative note or exclusion reason**.

6. Select **Save review and calculate**. Confirm that the error identifies the RHEL line and annual unit price. The price must still show `not-a-price` and the note must still show `Retained validation test`. The page explains that these entries are not saved. No calculation occurs. The combined button must validate and save corrected entries before it can calculate.

7. Restore the price and note recorded in step 5. Select **Save review for later**. Confirm the successful-save message and that you remain on Review without calculating.

8. Select **Home**, open the original recorded in Task 9, step 1. Results opens directly. Confirm that its nine amounts and calculation timestamp remain unchanged.

    > **Checkpoint:** Home consistently returns to the application start page, result actions are easy to find, and a failed review preserves entered values without changing saved data.

## Task 11: Verify the simplified complete workflow

1. Select **Home**. Enter `Lab 5 simplified workflow test` as the comparison name.

2. Paste this complete demonstration text into **RHEL SKU information**.

    ```text
    <copy>DEMO-RHEL-STD | Demonstration RHEL standard support | Quantity 10 | Annual unit price USD 1200.00
    DEMO-RHEL-PREM | Demonstration RHEL premium support | Quantity 2 | Annual unit price USD 2400.00</copy>
    ```

3. Paste this complete demonstration text into **Oracle Linux SKU information**.

    ```text
    <copy>DEMO-OL-BASIC | Demonstration Oracle Linux basic support | Quantity 10 | Annual unit price USD 800.00
    DEMO-OL-PREM | Demonstration Oracle Linux premier support | Quantity 2 | Annual unit price USD 1600.00</copy>
    ```

4. Select **Save and format with AI** once. Wait for Review to open automatically; formatting can take a minute or longer. Do not refresh or resubmit. The original inputs are saved before formatting starts. If formatting fails, do not create the comparison again. Review any available suggestions and expand **Manual fallback** to add missing lines using the original inputs.

5. Verify the four lines against the original text. Correct any suggestions. Assign group `1` to `DEMO-RHEL-STD` and `DEMO-OL-BASIC`, and group `2` to `DEMO-RHEL-PREM` and `DEMO-OL-PREM`. Select **Confirmed** for each only after checking it. Exclude any extra non-subscription line with a reason.

6. Select **Save review for later**. Confirm that Review remains open and the successful-save message appears. Select **Home**, then **Open** for this comparison. Confirm that Review opens directly with your saved values and decisions.

7. Select **Save review and calculate**. Confirm that Results opens automatically and shows annual totals of `$16,800.00` for RHEL and `$11,200.00` for Oracle Linux, with a `$5,600.00` difference. Confirm the three-year and five-year values against Task 3.

8. Select **Download PowerPoint**, open the file, and confirm that the saved amounts match without a repair warning. Return to Results and select **Download CSV** to verify the complete supporting data.

9. Record the calculation timestamp. Select **Home**, then **Open** for this calculated comparison. Confirm that Results opens directly and the timestamp has not changed. Opening a comparison must not reformat or recalculate it.

    > **Checkpoint:** A new comparison moves from inputs to review to results with two primary submit actions. Human review remains mandatory, an unfinished review can be saved, and both exports use the saved result.

## Conclusion

You have built the complete application, including built-in Help and confirmed comparison deletion. In the final lab, you will run deployment checks, test core scenarios, and rehearse the end-to-end demonstration.

## Learn More

* [PHP integer division](https://www.php.net/manual/en/function.intdiv.php)
* [MySQL fixed-point data types](https://dev.mysql.com/doc/refman/8.4/en/fixed-point-types.html)
* [PHP CSV output](https://www.php.net/manual/en/function.fputcsv.php)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
