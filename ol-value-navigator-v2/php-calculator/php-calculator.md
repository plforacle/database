# Lab 3: Deploy the PHP Foundation and Saved-Input Workflow

## Introduction

In this lab, you deploy the working PHP application foundation. A representative registers an application-managed account and signs in through a secure PHP session. The application saves the complete RHEL and Oracle Linux freeform text in one transaction, lists only the signed-in user's comparisons, and reopens either original input without exposing database credentials.

The supplied source is the application you will continue enabling in Labs 4 and 5. A stage file keeps later features unavailable until the lab that explains and verifies them.

Estimated Time: 60 minutes

### Objectives

In this lab, you will:

* Review the complete application source layout.
* Deploy the application for the Lab 3 stage.
* Configure the private PDO connection.
* Verify the database connection from the Apache service account.
* Register, log in, and verify the secure session workflow.
* Create, list, and reopen a user-owned saved comparison.
* Verify transactional storage and safe browser output.

### Prerequisites

This lab assumes you have:

* Completed Lab 2.
* The application package extracted under `~/ol-value-navigator-2-application`.
* The MySQL HeatWave DB System private IP address.
* The private password for `olvn2_app`.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Review the application structure

1. Change to the application source directory.

    ```bash
    <copy>cd ~/ol-value-navigator-2-application</copy>
    ```

2. List the application files.

    ```bash
    <copy>find . -maxdepth 2 -type f | sort</copy>
    ```

3. Use this compact source map to understand each supplied file.

    ```text
    deploy.sh                         Installs one workshop stage and preserves private configuration
    config/config.php.example         Provides the private database, model, limit, and URL template
    database/schema.sql               Creates user, workbook, input, AI-run, line, result, event, and audit tables
    lib/bootstrap.php                 Enforces secure sessions, authentication, security headers, PDO, CSRF, and page helpers
    lib/repository.php                Provides user and owner-scoped workbook queries and workflow-event writes
    lib/genai.php                     Builds prompts, calls ML_GENERATE, validates JSON, and stores suggestions
    lib/money.php                     Validates reviewed lines and calculates fixed-point period totals
    lib/deletion.php                  Confirms names and performs audited cascading deletion
    public/login.php                  Verifies a password and starts a fresh authenticated session
    public/register.php               Validates a new account and stores a PHP-generated password hash
    public/logout.php                 Verifies a POST and CSRF token, then destroys the session
    public/index.php                  Shows the creation form and the current user's recent comparisons
    public/create.php                 Saves an owned comparison and both original inputs in one transaction
    public/comparison.php             Reopens one owned workbook and displays its available actions
    public/format.php                 Formats both inputs independently with GenAI
    public/review.php                 Displays source, suggestions, editable values, groups, and decisions
    public/save-review.php            Validates ownership and saves all representative decisions
    public/add-line.php               Adds a manual fallback line and invalidates an old result
    public/calculate.php              Applies review rules and saves a deterministic result snapshot
    public/results.php                Displays period totals and traceable reviewed lines
    public/revise.php                 Replaces source inputs and clears data derived from the old text
    public/duplicate.php              Copies inputs and reviewed lines without copying the result
    public/export.php                 Records an export event and streams a formula-safe CSV workbook
    public/delete.php                 Requires exact-name confirmation before audited deletion
    public/help.php                   Provides the in-application quick start and workflow guidance
    public/style.css                  Provides responsive presentation for every application page
    tests/unit.php                    Tests core logic, authentication controls, route guards, and ownership contracts
    tests/check-database.php          Tests the private connection, auth schema, and rolled-back two-user owner filter
    tests/verify-installation.sh      Tests Stage 5 files, PHP syntax, Apache, auth pages, and protected-route redirects
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

    `deploy.sh` copies `public` files into the Apache document root and keeps the configuration and libraries under `/var/www/ol-value-navigator-2`. The test files verify individual rules, the private database connection, and the final deployed application. Browser-accessible PHP files never contain the database password.

4. Review the Lab 3 controllers.

    ```bash
    <copy>less public/index.php
    less public/create.php
    less public/comparison.php</copy>
    ```

    Press `q` after each file. Notice these controls:

    * `login.php` uses a generic failure message, verifies PHP password hashes, throttles repeated failures, and rotates the session identifier after authentication.
    * `register.php` validates usernames and 12-to-128-character passwords before storing a PHP-generated password hash.
    * `index.php` requires login, renders two separate bounded text areas, and lists only the current user's comparisons.
    * `create.php` accepts only `POST`, verifies a CSRF token, validates all required fields, assigns the current user as owner, and uses one database transaction.
    * `comparison.php` uses both the numeric comparison identifier and current user identifier to reopen complete original inputs, and escapes every displayed value.

## Task 2: Deploy the Lab 3 application stage

1. Run the supplied deployment script with stage `3`.

    ```bash
    <copy>cd ~/ol-value-navigator-2-application
    sudo bash deploy.sh 3</copy>
    ```

    The script performs these actions:

    * Creates the private application and public web directories.
    * Installs private libraries with group-readable permissions for Apache.
    * Installs public controllers and CSS under the Apache document root.
    * Writes the Lab 3 application-stage identifier (3) to /var/www/ol-value-navigator-2/stage. PHP reads this value to determine which application features are enabled.
    * Creates the private configuration from the example only when it does not already exist.
    * Restores SELinux file contexts and reloads Apache.

2. Confirm the installed stage and permissions.

    ```bash
    <copy>sudo cat /var/www/ol-value-navigator-2/stage
    sudo ls -l /var/www/ol-value-navigator-2/config.php
    ls -l /var/www/html/ol-value-navigator-2</copy>
    ```

    Confirm that the stage is `3`, the private configuration is owned by `root:apache` with mode `640`, and the public files do not contain a configuration file.

## Task 3: Configure and verify PDO

1. Open the private configuration.

    ```bash
    <copy>sudo vi /var/www/ol-value-navigator-2/config.php</copy>
    ```

2. Replace only these placeholders:

    * Replace `HEATWAVE_PRIVATE_IP` in `dsn` with the DB System private IP address.
    * Replace `CHANGE_THIS_PASSWORD` with the password for `olvn2_app`.

    Keep `dbname=ol_value_navigator_2`, `charset=utf8mb4`, the `olvn2_app` user, the model identifier, and the size limits unchanged. If the password contains a single quote or backslash, prefix that character with a backslash in the PHP single-quoted string.

3. Press Esc to leave insert mode.

4. Type:

    ```text
    <copy>:wq</copy>
    ```

5. Press Enter to save the file and exit vi.

6. Install the database connection checker in the private application directory, then run it as the Apache service account.

    ```bash
    <copy>sudo install -o root -g apache -m 0640 \
      ~/ol-value-navigator-2-application/tests/check-database.php \
      /var/www/ol-value-navigator-2/check-database.php
    sudo -u apache php /var/www/ol-value-navigator-2/check-database.php</copy>
    ```

    The checker must be placed outside `/home/opc` because the `apache` account cannot normally traverse the `opc` home directory. The private application directory is not exposed through the Apache document root.

    Confirm that the output begins with `Database connection passed`, shows the server version and `workshop-v2` rule, reports that the authentication schema and two-user ownership filter are ready, and confirms that temporary rows were rolled back.

7. If the test fails, verify the private IP, application password, private-subnet ingress rule for TCP port `3306`, and the grants from Lab 2. The application intentionally returns a generic browser error and writes only the exception class to the Apache error log.

    > **Checkpoint:** PHP running as Apache can reach the private MySQL HeatWave DB System with the least-privilege application account.

## Task 4: Create and reopen a saved comparison

1. Open the application in your local browser. Replace the placeholder with the compute instance public IP address.

    ```text
    <copy>http://PUBLIC_IP_ADDRESS/ol-value-navigator-2/</copy>
    ```

2. On the **Sign in** page, select **Register**.

3. Create a unique workshop username. Use 3 to 64 characters, start with a letter, and use only letters, numbers, periods, underscores, or hyphens.

4. Enter and confirm a demonstration-only passphrase containing 12 to 128 characters, then select **Register**.

5. Confirm that the **Comparisons** page opens, the header displays **Signed in as** followed by your username, and the page contains a comparison name, a **RHEL SKU information** text area, and an **Oracle Linux SKU information** text area.

6. Enter **Lab 3 saved-input test** as the comparison name.

7. Paste this complete demonstration RHEL input.

    ```text
    <copy>DEMO-RHEL-STD | Demonstration RHEL standard support | Quantity 10 | Annual unit price USD 1200.00
    DEMO-RHEL-PREM | Demonstration RHEL premium support | Quantity 2 | Annual unit price USD 2400.00
    Note: synthetic workshop data only.</copy>
    ```

8. Paste this complete demonstration Oracle Linux input.

    ```text
    <copy>DEMO-OL-BASIC | Demonstration Oracle Linux basic support | Quantity 10 | Annual unit price USD 800.00
    DEMO-OL-PREM | Demonstration Oracle Linux premier support | Quantity 2 | Annual unit price USD 1600.00
    Note: synthetic workshop data only.</copy>
    ```

9. Select **Save original inputs**.

10. Confirm that the comparison page displays both complete original inputs and a `DRAFT` status. It must not display the database password, DSN, private IP address, or another user's comparison.

11. Select **All comparisons**, and then select **Open** for `Lab 3 saved-input test` under **Your saved comparisons**.

12. Confirm that both original inputs reopen unchanged.

13. Select **Logout**. Confirm that the **Sign in** page opens and reports that you have been logged out.

14. Log in again with the account you created. Confirm that `Lab 3 saved-input test` remains available under **Your saved comparisons**.

## Task 5: Verify the database transaction

1. Connect with the application account.

    ```bash
    <copy>mysql --host=HEATWAVE_PRIVATE_IP --user=olvn2_app --password --ssl-mode=REQUIRED ol_value_navigator_2</copy>
    ```

2. Display the saved comparison and the lengths of both complete inputs.

    ```sql
    <copy>SELECT c.id, u.username AS owner_username, c.name, c.status, c.rule_version_id
    FROM comparison c
    JOIN user_account u ON u.id = c.owner_user_id
    ORDER BY c.id DESC
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

    > **Checkpoint:** Registration, login, secure session logout, and relogin work. The browser creates, lists, and reopens the signed-in user's complete comparison. Both original inputs and the creation event are stored together without a master catalog.

## Conclusion

You have built the authenticated, user-owned saved-input workflow. In the next lab, you will enable MySQL HeatWave GenAI formatting, representative editing, alignment, decisions, and manual fallback.

## Learn More

* [PHP PDO](https://www.php.net/manual/en/book.pdo.php)
* [PHP prepared statements](https://www.php.net/manual/en/pdo.prepared-statements.php)
* [PHP session security](https://www.php.net/manual/en/session.security.ini.php)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
