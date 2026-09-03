# Lab 5: Calculate, Save, and Manage the Complete Comparison

## Introduction

In this lab, you enable the complete Version 1 application. PHP verifies that every included line is confirmed and that each comparison group contains both sides. It then performs deterministic annual, three-year, and five-year calculations and saves the result snapshot in the MySQL HeatWave DB System.

You also verify the workbook operations required by Version 1: reopen, revise, duplicate, and CSV export.

Estimated Time: 75 minutes

### Objectives

In this lab, you will:

* Enable the complete Lab 5 application stage.
* Verify fixed-precision quantity and money calculations.
* Enforce complete representative review and two-sided alignment.
* Save and display annual, three-year, and five-year results.
* Verify fail-closed behavior for unresolved and unmatched lines.
* Reopen, revise, duplicate, and export a comparison.

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

1. Deploy stage `5`.

    ```bash
    <copy>cd ~/ol-value-navigator-application
    sudo bash deploy.sh 5</copy>
    ```

2. Confirm the installed stage.

    ```bash
    <copy>sudo cat /var/www/ol-value-navigator/stage</copy>
    ```

    The output must be `5`.

3. Run the application unit checks.

    ```bash
    <copy>php ~/ol-value-navigator-application/tests/unit.php</copy>
    ```

    Confirm that the output is:

    ```text
    All Oracle Linux Value Navigator unit checks passed.
    ```

    These checks cover decimal scaling, exact money rendering, signed values, line rounding, whole-quantity multiplication, complete totals, fail-closed calculation, valid GenAI response parsing, and rejection of unsupported GenAI response fields.

## Task 3: Calculate and reconcile the demonstration

1. Open the application in your browser. Replace `PUBLIC_IP_ADDRESS` with the compute instance public IP address.

    ```text
    <copy>http://PUBLIC_IP_ADDRESS/ol-value-navigator/</copy>
    ```

2. In **Saved comparisons**, locate **Lab 3 saved-input test**. Confirm that its status is `CONFIRMED` and its line count is `4`, and then select **Open** for that comparison.

    Do not open **Lab 4 manual fallback test**. That separate boundary-test comparison intentionally remains in `NEEDS_REVIEW` status.

3. On the **Comparison: Lab 3 saved-input test** page, select **Review lines**.

4. Locate each line by its SKU and confirm these reviewed values and alignments.

    | Side | Group | Quantity | Annual unit price | Annual line amount |
    | --- | ---: | ---: | ---: | ---: |
    | RHEL standard | 1 | 10 | $1,200.00 | $12,000.00 |
    | RHEL premium | 2 | 2 | $2,400.00 | $4,800.00 |
    | Oracle Linux basic | 1 | 10 | $800.00 | $8,000.00 |
    | Oracle Linux premier | 2 | 2 | $1,600.00 | $3,200.00 |

5. Confirm that each of the four lines has **Confirmed** selected as its decision. Correct any value or group that does not match the table, and then select **Save representative review**.

6. Confirm that the application reports `Representative decisions were saved.`

7. Select **Calculate confirmed results**.

8. Confirm that the results page opens, and reconcile the displayed totals with this table.

    | Period | RHEL | Oracle Linux | Difference |
    | --- | ---: | ---: | ---: |
    | Annual | $16,800.00 | $11,200.00 | $5,600.00 |
    | Three years | $50,400.00 | $33,600.00 | $16,800.00 |
    | Five years | $84,000.00 | $56,000.00 | $28,000.00 |

9. Confirm that the results page identifies `workshop-v1`, shows every reviewed line and decision, and states when the snapshot was calculated.

    > **Checkpoint:** PHP calculated and saved the annual, three-year, and five-year values from representative-confirmed decimal inputs.

## Task 4: Verify fail-closed alignment behavior

1. Select **Review lines** from the results page.

2. Change one confirmed line to **Unresolved**, enter `Demonstration of a missing representative decision` as its note, and save the review.

3. Select **Calculate confirmed results**.

4. Confirm that the application withholds the results and displays a message requiring every line to be resolved, confirmed, or excluded.

5. Restore the line to **Confirmed**, save the review, and calculate again.

6. Change one confirmed line to a group number that has no confirmed line from the other side, save, and calculate.

7. Confirm that the application identifies the unmatched group and withholds totals.

8. Restore the correct group, save, and calculate again. Confirm that the expected totals return.

## Task 5: Reopen, duplicate, and revise

1. Select **Comparison**, then **All comparisons**, and reopen the calculated demonstration.

2. Confirm that the two original inputs, status, reviewed-line counts, rule version, and result link remain available after reopening.

3. Select **Duplicate comparison**.

4. Confirm that the copy contains the original inputs, suggestions, representative-reviewed fields, and decisions, but does not copy the result snapshot. This forces the representative to review and calculate the copy.

5. On the copy, select **Revise original inputs**.

6. Change the comparison name or demonstration input, then select **Save revision and clear derived data**.

7. Confirm that the copy returns to `DRAFT` and its formatted lines and result are cleared. The application performs this reset because derived values must not survive a source-input revision.

8. Return to the original comparison and confirm that it remains unchanged.

## Task 6: Export the saved workbook

1. Open the original calculated comparison and select **Export CSV workbook**.

2. Open the downloaded CSV file in a text editor or spreadsheet application.

3. Confirm that it contains:

    * Comparison identifier, name, status, and rule version.
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

    > **Checkpoint:** The complete Version 1 application can reopen, revise, duplicate, calculate, save, and export a representative-confirmed comparison.

## Conclusion

You have built the complete application. In the final lab, you will run deployment checks, test core scenarios, and rehearse the end-to-end demonstration.

## Learn More

* [PHP integer division](https://www.php.net/manual/en/function.intdiv.php)
* [MySQL fixed-point data types](https://dev.mysql.com/doc/refman/8.4/en/fixed-point-types.html)
* [PHP CSV output](https://www.php.net/manual/en/function.fputcsv.php)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
