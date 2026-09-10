# Lab 3: Deploy the PHP Foundation and Saved-Input Workflow

## Introduction

In this lab, you deploy the working PHP application foundation. The application saves the complete RHEL and Oracle Linux freeform text in one transaction, lists saved comparisons, and reopens either original input without exposing database credentials.

The supplied source is the application you will continue enabling in Labs 4 and 5. A stage file keeps later features unavailable until the lab that explains and verifies them.

Estimated Time: 60 minutes

### Objectives

In this lab, you will:

* Review the complete application source layout.
* Deploy the application for the Lab 3 stage.
* Configure the private PDO connection.
* Verify the database connection from the Apache service account.
* Create, list, and reopen a saved comparison.
* Verify transactional storage and safe browser output.

### Prerequisites

This lab assumes you have:

* Completed Lab 2.
* The application package extracted under `~/ol-value-navigator-application`.
* The MySQL HeatWave DB System private IP address.
* The private password for `olvn_app`.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Review the application structure

1. Change to the application source directory.

    ```bash
    <copy>cd ~/ol-value-navigator-application</copy>
    ```

2. List the application files.

    ```bash
    <copy>find . -maxdepth 2 -type f | sort</copy>
    ```

3. Use this compact source map to understand each supplied file.

    ```text
    deploy.sh                         Installs one workshop stage and preserves private configuration
    config/config.php.example         Provides the private database, model, limit, and URL template
    database/schema.sql               Creates workbook, input, AI-run, line, result, event, and audit tables
    database/upgrade-customer-context.sql  Adds optional context columns to existing V1 databases safely
    lib/bootstrap.php                 Starts sessions, security headers, PDO, CSRF, escaping, and page helpers
    lib/repository.php                Provides shared prepared queries and workflow-event writes
    lib/context.php                   Validates and renders optional customer context, separate from calculations
    lib/presentation.php              Formats saved results and customer context into four editable slides
    lib/genai.php                     Builds prompts, calls ML_GENERATE, validates JSON, and stores suggestions
    lib/money.php                     Validates reviewed lines and calculates fixed-point period totals
    lib/deletion.php                  Confirms names and performs audited cascading deletion
    public/index.php                  Shows the creation form and recent saved comparisons
    public/create.php                 Saves a comparison and both original inputs in one transaction
    public/comparison.php             Reopens one workbook and displays its available actions
    public/context.php                Edits customer details without clearing reviewed lines or results
    public/format.php                 Formats both inputs independently with GenAI
    public/review.php                 Displays source, suggestions, editable values, groups, and decisions
    public/save-review.php            Validates ownership and saves all representative decisions
    public/add-line.php               Adds a manual fallback line and invalidates an old result
    public/calculate.php              Applies review rules and saves a deterministic result snapshot
    public/results.php                Displays period totals and traceable reviewed lines
    public/revise.php                 Replaces source inputs and clears data derived from the old text
    public/duplicate.php              Copies inputs and reviewed lines without copying the result
    public/export.php                 Records an export event and streams a formula-safe CSV workbook
    public/export-pptx.php             Exports a consistent saved result and context snapshot as PowerPoint
    public/delete.php                 Requires exact-name confirmation before audited deletion
    public/help.php                   Provides the in-application quick start and workflow guidance
    public/style.css                  Provides responsive presentation for every application page
    tests/unit.php                    Tests money, review, GenAI-contract, and deletion rules without a database
    tests/context.php                 Tests optional context validation and escaped form/display output
    tests/presentation.php            Tests editable PowerPoint content, notes and package compatibility
    tests/check-context-schema.php    Stops an existing deployment before copying files if columns are missing
    tests/check-database.php          Tests the deployed private connection, active rule, and audit table
    tests/verify-installation.sh      Tests Stage 5 files, PHP syntax, Apache, and local routes
    ```

    The files interact through this request flow:

    ```text
    Browser
      -> public PHP controller or view
         -> lib/bootstrap.php
            -> private config.php and PDO connection
            -> repository, GenAI, money, or deletion library
               -> tables created by database/schema.sql
    ```

    `deploy.sh` copies `public` files into the Apache document root and keeps the configuration and libraries under `/var/www/ol-value-navigator`. The test files verify individual rules, the private database connection, and the final deployed application. Browser-accessible PHP files never contain the database password.

4. Review the Lab 3 controllers.

    ```bash
    <copy>less public/index.php
    less public/create.php
    less public/comparison.php</copy>
    ```

    Press `q` after each file. Notice these controls:

    * `index.php` renders two separate bounded text areas and lists existing comparisons.
    * `create.php` accepts only `POST`, verifies a CSRF token, validates all required fields, and uses one database transaction.
    * `comparison.php` uses the numeric comparison identifier to reopen both complete original inputs and escapes every displayed value.

## Task 2: Deploy the Lab 3 application stage

1. Run the supplied deployment script with stage `3`.

    ```bash
    <copy>cd ~/ol-value-navigator-application
    sudo bash deploy.sh 3</copy>
    ```

    The script performs these actions:

    * Creates the private application and public web directories.
    * Installs private libraries with group-readable permissions for Apache.
    * Installs public controllers and CSS under the Apache document root.
    * Writes the Lab 3 application-stage identifier (3) to /var/www/ol-value-navigator/stage. PHP reads this value to determine which application features are enabled.
    * Creates the private configuration from the example only when it does not already exist.
    * Restores SELinux file contexts and reloads Apache.

2. Confirm the installed stage and permissions.

    ```bash
    <copy>sudo cat /var/www/ol-value-navigator/stage
    sudo ls -l /var/www/ol-value-navigator/config.php
    ls -l /var/www/html/ol-value-navigator</copy>
    ```

    Confirm that the stage is `3`, the private configuration is owned by `root:apache` with mode `640`, and the public files do not contain a configuration file.

## Task 3: Configure and verify PDO

1. Open the private configuration.

    ```bash
    <copy>sudo vi /var/www/ol-value-navigator/config.php</copy>
    ```

2. Replace only these placeholders:

    * Replace `HEATWAVE_PRIVATE_IP` in `dsn` with the DB System private IP address.
    * Replace `CHANGE_THIS_PASSWORD` with the password for `olvn_app`.

    Keep `dbname=ol_value_navigator`, `charset=utf8mb4`, the `olvn_app` user, the model identifier, and the size limits unchanged. If the password contains a single quote or backslash, prefix that character with a backslash in the PHP single-quoted string.

3. Press Esc to leave insert mode.

4. Type:

    ```text
    <copy>:wq</copy>
    ```

5. Press Enter to save the file and exit vi.

6. Install the database connection checker in the private application directory, then run it as the Apache service account.

    ```bash
    <copy>sudo install -o root -g apache -m 0640 \
      ~/ol-value-navigator-application/tests/check-database.php \
      /var/www/ol-value-navigator/check-database.php
    sudo -u apache php /var/www/ol-value-navigator/check-database.php</copy>
    ```

    The checker must be placed outside `/home/opc` because the `apache` account cannot normally traverse the `opc` home directory. The private application directory is not exposed through the Apache document root.

    Confirm that the output begins with `Database connection passed` and shows the server version and `workshop-v1` rule.

7. If the test fails, verify the private IP, application password, private-subnet ingress rule for TCP port `3306`, and the grants from Lab 2. The application intentionally returns a generic browser error and writes only the exception class to the Apache error log.

    > **Checkpoint:** PHP running as Apache can reach the private MySQL HeatWave DB System with the least-privilege application account.

## Task 4: Create and reopen a saved comparison

1. Open the application in your local browser. Replace the placeholder with the compute instance public IP address.

    ```text
    <copy>http://PUBLIC_IP_ADDRESS/ol-value-navigator/</copy>
    ```

2. Confirm that the page contains a comparison name, a **RHEL SKU information** text area, and an **Oracle Linux SKU information** text area.

3. Enter **Lab 3 saved-input test** as the comparison name.

    The form also has **Customer details (optional)**. You can expand it to save a demonstration customer name, objective, scope, and recommended next step with a new comparison. Leave these fields blank for this checkpoint; Lab 5 Task 9 verifies adding them to an existing calculated comparison.

4. Paste this complete demonstration RHEL input.

    ```text
    <copy>DEMO-RHEL-STD | Demonstration RHEL standard support | Quantity 10 | Annual unit price USD 1200.00
    DEMO-RHEL-PREM | Demonstration RHEL premium support | Quantity 2 | Annual unit price USD 2400.00
    Note: synthetic workshop data only.</copy>
    ```

5. Paste this complete demonstration Oracle Linux input.

    ```text
    <copy>DEMO-OL-BASIC | Demonstration Oracle Linux basic support | Quantity 10 | Annual unit price USD 800.00
    DEMO-OL-PREM | Demonstration Oracle Linux premier support | Quantity 2 | Annual unit price USD 1600.00
    Note: synthetic workshop data only.</copy>
    ```

6. Select **Save original inputs**.

7. Confirm that the comparison page displays both complete original inputs and a `DRAFT` status. It must not display the database password, DSN, or private IP address.

8. Select **All comparisons**, and then select **Open** for `Lab 3 saved-input test`.

9. Confirm that both original inputs reopen unchanged.

## Task 5: Verify the database transaction

1. Connect with the application account.

    ```bash
    <copy>mysql --host=HEATWAVE_PRIVATE_IP --user=olvn_app --password --ssl-mode=REQUIRED ol_value_navigator</copy>
    ```

2. Display the saved comparison and the lengths of both complete inputs.

    ```sql
    <copy>SELECT id, name, status, rule_version_id
    FROM comparison
    ORDER BY id DESC
    LIMIT 5;

    SELECT comparison_id, input_side, CHAR_LENGTH(raw_text) AS input_characters
    FROM comparison_input
    ORDER BY comparison_id DESC, input_side;</copy>
    ```

    Confirm that the new comparison has exactly one `RHEL` and one `ORACLE_LINUX` input row.

3. Confirm that the create event was recorded.

    ```sql
    <copy>SELECT comparison_id, event_type, actor_type, outcome, created_at
    FROM application_event
    ORDER BY id DESC
    LIMIT 5;</copy>
    ```

4. Exit the MySQL client.

    ```sql
    <copy>EXIT;</copy>
    ```

    > **Checkpoint:** The browser creates, lists, and reopens a complete comparison. Both original inputs and the creation event are stored together without a master catalog.

## Conclusion

You have built the saved-input workflow. In the next lab, you will enable MySQL HeatWave GenAI formatting, representative editing, alignment, decisions, and manual fallback.

## Learn More

* [PHP PDO](https://www.php.net/manual/en/book.pdo.php)
* [PHP prepared statements](https://www.php.net/manual/en/pdo.prepared-statements.php)
* [PHP session security](https://www.php.net/manual/en/session.security.ini.php)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
