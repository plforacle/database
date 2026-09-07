# Lab 6: Test and Demonstrate the Complete Application

## Introduction

In this lab, you verify the completed Oracle Linux Value Navigator as installed software, a database-backed workflow, and a browser application. You run executable checks, test normal and fail-closed scenarios, inspect saved state, and rehearse the full demonstration.

Estimated Time: 60 minutes

### Objectives

In this lab, you will:

* Run PHP unit and deployed-application checks.
* Verify authentication, ownership, database completeness, and workflow consistency.
* Test confirmed, excluded, unresolved, AI-failure, and manual-entry scenarios.
* Verify safe browser and configuration behavior.
* Rehearse the end-to-end Version 2 baseline demonstration.
* Identify controls deferred beyond this prototype.

### Prerequisites

This lab assumes you have:

* Completed Labs 1 through 5.
* A stage 5 application deployment.
* At least one calculated demonstration comparison.
* Browser and SSH access to the Oracle Linux compute instance.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Run the automated checks

1. Run the source-level unit checks.

    ```bash
    <copy>php ~/ol-value-navigator-2-application/tests/unit.php</copy>
    ```

    Confirm that all checks pass.

2. Run the deployed-application verification as `root` so it can inspect the private files installed for Apache.

    ```bash
    <copy>sudo bash ~/ol-value-navigator-2-application/tests/verify-installation.sh</copy>
    ```

    This script verifies:

    * The private configuration and stage files exist.
    * The deployed stage is `5`.
    * Every deployed PHP file passes `php -l` syntax validation.
    * Apache is active.
    * The Login and Register pages return successful HTTP responses.
    * Anonymous requests to the comparison list, Help, a comparison, and an export redirect to Login.

3. Run the database connection check as Apache.

    ```bash
    <copy>sudo -u apache php /var/www/ol-value-navigator-2/check-database.php</copy>
    ```

    Confirm that it reports the MySQL Server version, `workshop-v2`, the authentication schema, the two-user ownership filter, and the rollback of all temporary test rows.

    > **Checkpoint:** Source logic, authentication controls, route guards, ownership contracts, deployed PHP syntax, Apache, public authentication routes, protected-route redirects, and the private database connection pass executable checks.

## Task 2: Verify database consistency

1. Connect with the application account.

    ```bash
    <copy>mysql --host=HEATWAVE_PRIVATE_IP --user=olvn2_app --password --ssl-mode=REQUIRED ol_value_navigator_2</copy>
    ```

2. Find any comparison that does not have exactly two original inputs.

    ```sql
    <copy>SELECT c.id, c.owner_user_id, c.name, COUNT(i.id) AS input_count
    FROM comparison c
    LEFT JOIN comparison_input i ON i.comparison_id = c.id
    GROUP BY c.id, c.owner_user_id, c.name
    HAVING COUNT(i.id) &lt;&gt; 2;</copy>
    ```

    The query should return an empty result.

3. Find any orphaned line.

    ```sql
    <copy>SELECT l.id
    FROM comparison_line l
    LEFT JOIN comparison_input i ON i.id = l.comparison_input_id
    WHERE i.id IS NULL;</copy>
    ```

    The query should return an empty result.

4. Find any calculated comparison that still contains an unreviewed or unresolved line.

    ```sql
    <copy>SELECT DISTINCT c.id, c.name, l.review_status
    FROM comparison c
    JOIN comparison_input i ON i.comparison_id = c.id
    JOIN comparison_line l ON l.comparison_input_id = i.id
    WHERE c.status = 'CALCULATED'
      AND l.review_status IN ('AI_SUGGESTED', 'UNRESOLVED');</copy>
    ```

    The query should return an empty result.

5. Find any comparison or deletion audit whose owner account is missing.

    ```sql
    <copy>SELECT c.id, c.owner_user_id
    FROM comparison c
    LEFT JOIN user_account u ON u.id = c.owner_user_id
    WHERE u.id IS NULL;

    SELECT a.deleted_comparison_id, a.owner_user_id
    FROM comparison_deletion_audit a
    LEFT JOIN user_account u ON u.id = a.owner_user_id
    WHERE u.id IS NULL;</copy>
    ```

    Both queries should return an empty result.

6. Display each account with its number of active comparisons. This query does not display password hashes.

    ```sql
    <copy>SELECT u.username, u.active, COUNT(c.id) AS comparison_count
    FROM user_account u
    LEFT JOIN comparison c ON c.owner_user_id = u.id
    GROUP BY u.id, u.username, u.active
    ORDER BY u.username;</copy>
    ```

7. Display the complete event sequence for the calculated demonstration. Replace `COMPARISON_ID`.

    ```sql
    <copy>SELECT event_type, actor_type, outcome, details, created_at
    FROM application_event
    WHERE comparison_id = COMPARISON_ID
    ORDER BY id;</copy>
    ```

    Confirm that the sequence includes creation, successful formatting for each side, representative review, calculation, and any export action you performed.

8. Exit the MySQL client.

    ```sql
    <copy>EXIT;</copy>
    ```

## Task 3: Test the browser scenarios

1. Open the application. An anonymous request must redirect to **Sign in**.

    ```text
    http://PUBLIC_IP_ADDRESS/ol-value-navigator-2/
    ```

2. Execute this test matrix with demonstration data.

    | Scenario | Action | Required result |
    | --- | --- | --- |
    | Anonymous access | Open the application, Help, or a saved-comparison URL after logout | The request redirects to Sign in |
    | Registration | Create a unique username and a 12-to-128-character demonstration passphrase | Registration starts a signed-in session without displaying or storing a plaintext password |
    | Login failure | Submit an incorrect password | A generic error appears without confirming whether the username exists |
    | Login and logout | Sign in, then select Logout | Login shows only the account's comparisons; Logout destroys the authenticated session |
    | Two-user list isolation | Sign in as a second account | The first account's comparisons do not appear |
    | Two-user direct access | As the second account, request the first account's comparison, review, result, and export URLs | Each request returns Comparison not found and exposes no workbook data |
    | Confirmed comparison | Confirm complete paired lines and calculate | Annual, three-year, and five-year results appear and persist |
    | Missing value | Leave a quantity or price empty and mark the line unresolved | A note is required and totals are withheld |
    | Excluded line | Exclude a line with a reason | The line remains visible and is absent from totals |
    | Unmatched group | Put a confirmed line in a group with no opposite-side line | Calculation identifies the group and withholds totals |
    | Invalid AI result or timeout | Use a formatting attempt that fails or wait for an unavailable service | A generic failure message appears and manual entry remains available |
    | Manual line | Add and complete a manual line | The same field, alignment, and decision validation applies |
    | Reopen | Return to the list and open a saved comparison | Original inputs, reviewed values, decisions, and result snapshot persist |
    | Duplicate | Duplicate a comparison | Inputs and reviewed lines copy, but calculation must be run again |
    | Revise | Revise the duplicate's original input | Derived lines and results clear while the event history remains |
    | Export | Export the calculated original | CSV contains source, review, alignment, rule, and result sections |
    | Help | Select Help from an application page and use Return to comparisons | The complete workflow guidance opens and returns to the saved-comparisons page |
    | Delete with incorrect name | Duplicate a demonstration, open the duplicate, and enter a confirmation name that is not exact | The application reports that nothing was deleted and the duplicate remains available |
    | Delete with exact name | Enter the duplicate's complete name exactly and delete it | The duplicate and its associated data disappear from the saved-comparisons list |

3. Complete this two-user isolation check with two separate application accounts.

    1. Sign in as the account that owns `Lab 3 saved-input test` and record its numeric comparison ID from the browser URL.
    2. Select **Logout**, then register a second unique workshop account.
    3. Confirm that `Lab 3 saved-input test` is absent from **Your saved comparisons**.
    4. While signed in as the second account, request each path below after replacing `FIRST_USER_COMPARISON_ID`:

        ```text
        http://PUBLIC_IP_ADDRESS/ol-value-navigator-2/comparison.php?id=FIRST_USER_COMPARISON_ID
        http://PUBLIC_IP_ADDRESS/ol-value-navigator-2/review.php?id=FIRST_USER_COMPARISON_ID
        http://PUBLIC_IP_ADDRESS/ol-value-navigator-2/results.php?id=FIRST_USER_COMPARISON_ID
        http://PUBLIC_IP_ADDRESS/ol-value-navigator-2/export.php?id=FIRST_USER_COMPARISON_ID
        ```

    5. Confirm that each request reports **Comparison not found** and does not expose the name, source input, reviewed lines, result, or CSV data.
    6. Confirm that no controls for revising, formatting, calculating, duplicating, exporting, or deleting the first account's comparison are present in the second account.
    7. Log out, sign back in as the first account, and confirm that its comparison and saved state are unchanged.

    The source-level unit suite also verifies that every item action enters through the same owner-scoped lookup and that create, list, update, and delete statements carry the current owner identifier.

4. Manually recalculate the demonstration values and confirm that they match the displayed values:

    ```text
    RHEL annual:        (10 x 1200.00) + (2 x 2400.00) = 16800.00
    Oracle annual:      (10 x 800.00)  + (2 x 1600.00) = 11200.00
    Annual difference:  16800.00 - 11200.00 = 5600.00
    Three-year values:  each annual value x 3
    Five-year values:   each annual value x 5
    ```

## Task 4: Verify safe deployment behavior

1. Confirm that the private configuration is not present under the public web directory.

    ```bash
    <copy>test ! -e /var/www/html/ol-value-navigator-2/config.php &amp;&amp; echo "PASS: no public configuration file"</copy>
    ```

2. Confirm that an HTTP request for a configuration file returns `404`.

    ```bash
    <copy>curl --silent --output /dev/null --write-out '%{http_code}\n' http://localhost/ol-value-navigator-2/config.php</copy>
    ```

3. Confirm the private configuration permissions.

    ```bash
    <copy>sudo stat --format='%U %G %a %n' /var/www/ol-value-navigator-2/config.php</copy>
    ```

    The expected owner, group, and mode are `root apache 640`.

4. Submit an incomplete form in the browser and confirm that the application shows a helpful validation message without displaying the database password, DSN, private IP, SQL statement, stack trace, or pasted source text.

5. Restart Apache and confirm that an anonymous application request redirects to Login.

    ```bash
    <copy>sudo systemctl restart httpd
    systemctl is-active httpd
    test "$(curl --silent --output /dev/null --write-out '%{http_code}' http://localhost/ol-value-navigator-2/)" = "303" &amp;&amp; echo "PASS: protected application route"
    curl --fail --silent http://localhost/ol-value-navigator-2/login.php | grep --quiet "Sign in" &amp;&amp; echo "PASS: login route"</copy>
    ```

    Confirm that Apache is `active` and the route passes.

## Task 5: Rehearse the complete demonstration

1. State the boundary: Oracle Linux Value Navigator compares representative-supplied subscription costs. It is not a quote, licensing determination, or complete TCO analysis.

2. Register or log in and explain that the application uses secure PHP sessions and restricts each comparison to its owner.

3. Create a comparison and paste the complete demonstration RHEL and Oracle Linux text into the two separate inputs.

4. Explain that the MySQL HeatWave DB System provides private database storage and that MySQL HeatWave GenAI formats each input with `sys.ML_GENERATE`.

5. Format both inputs and show the original AI suggestions beside the complete source text.

6. Correct any suggestions, align paired lines, and explain that the representative, not AI, owns the confirmation and exclusion decisions.

7. Demonstrate an unresolved line and show that PHP withholds all comparative totals.

8. Resolve the line, calculate, and reconcile the annual, three-year, and five-year values.

9. Reopen and export the comparison to show user-owned workbook-style persistence.

10. Duplicate the comparison, demonstrate that an incorrect deletion confirmation preserves it, and then enter its exact name to delete it.

11. Log out and confirm that protected pages return to Sign in.

12. State the Version 2 baseline boundaries:

    * No master RHEL or Oracle Linux SKU catalogs.
    * Application-managed accounts only, with no password recovery or multifactor authentication.
    * Each account can access only its own comparisons.
    * Demonstration data only.
    * No production approval of calculation rules.
    * No claim that aligned lines are product equivalents.

## Task 6: Record the next production controls

1. Record these remaining Version 2 and production-readiness work items outside the demonstration application:

    * Password recovery, multifactor authentication, administrator account management, and stronger distributed login-rate controls.
    * Approved calculation-rule governance and change control.
    * Customer-data classification, consent, retention, deletion, and audit policy.
    * HTTPS, managed secrets, certificate verification, private web-tier access, and security monitoring.
    * High availability, automatic backups, deletion protection, recovery testing, and operational contacts.
    * Accessibility, usability, load, concurrency, and failure-recovery testing.

2. Keep customer information, credentials, private keys, private IP addresses, and OCI identifiers out of the repository.

    > **Checkpoint:** The full Version 2 baseline workflow is deployed, tested, traceable, reproducible, and ready for a demonstration using synthetic data.

## Conclusion

You have completed the Oracle Linux Value Navigator workshop.

## Learn More

* [Oracle Linux documentation](https://docs.oracle.com/en/operating-systems/oracle-linux/)
* [OCI Compute documentation](https://docs.oracle.com/en-us/iaas/Content/Compute/home.htm)
* [MySQL HeatWave GenAI](https://dev.mysql.com/doc/heatwave/en/mys-hw-genai-overview.html)
* [PHP security](https://www.php.net/manual/en/security.php)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
