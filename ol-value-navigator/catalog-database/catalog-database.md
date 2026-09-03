# Lab 2: Create the Workbook-Style Database Schema

## Introduction

In this lab, you download the Oracle Linux Value Navigator source and create its complete database schema in the MySQL HeatWave DB System. The schema preserves the two original freeform inputs, every AI formatting run, the original AI suggestions, representative corrections and decisions, aligned comparison groups, calculated result snapshots, and workflow events.

The schema stores product text only inside a representative-created comparison. It does not contain or maintain a master RHEL or Oracle Linux SKU catalog.

Estimated Time: 45 minutes

### Objectives

In this lab, you will:

* Download and extract the packaged application source used by the remaining labs.
* Create the seven application tables and demonstration rule version.
* Create the PHP database account.
* Grant application data access and MySQL HeatWave GenAI access.
* Verify the schema and application account.

### Prerequisites

This lab assumes you have:

* The working Oracle Linux instance from Lab 1.
* The active MySQL HeatWave DB System and private IP address from Lab 1.
* The DB System administrator password created in Lab 1.
* The successful `sys.ML_GENERATE` result from the Lab 1 checkpoint.

> **Note:** Use demonstration information only. Version 1 has no login, user ownership, or production data-governance controls.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Download the workshop application package

1. Connect to the Oracle Linux compute instance as `opc` if you are not already connected.

2. Install the tools used to download and extract the application package.

    ```bash
    <copy>sudo dnf install -y curl unzip</copy>
    ```

3. Download the application ZIP from this lab's `files` directory in the LiveLabs repository.

    ```bash
    <copy>cd ~
    curl --fail --location \
      --output ol-value-navigator-application.zip \
      https://raw.githubusercontent.com/oracle-livelabs/database/main/ol-value-navigator/catalog-database/files/ol-value-navigator-application.zip</copy>
    ```

    You can also [download the application package](files/ol-value-navigator-application.zip) through the rendered lab page and transfer it to the compute instance if direct GitHub access is restricted.

4. Verify the downloaded package checksum.

    ```bash
    <copy>cd ~
    echo '13c075b5073366108b102146f6a799ece92fcbbb513e7d7253c1e54aaa050267  ol-value-navigator-application.zip' | sha256sum --check</copy>
    ```

    Confirm that the command returns `ol-value-navigator-application.zip: OK`.

5. Extract the package into a dedicated working directory.

    ```bash
    <copy>mkdir -p ~/ol-value-navigator-application
    unzip -o ~/ol-value-navigator-application.zip -d ~/ol-value-navigator-application</copy>
    ```

6. List the extracted application assets.

    ```bash
    <copy>find ~/ol-value-navigator-application -maxdepth 2 -type f | sort</copy>
    ```

    Confirm that the output includes `database/schema.sql`, `deploy.sh`, PHP files under `lib` and `public`, and tests under `tests`.

    > **Checkpoint:** The complete workshop application source is available on the compute instance.

## Task 2: Review and load the schema

1. Review the SQL file before executing it.

    ```bash
    <copy>less ~/ol-value-navigator-application/database/schema.sql</copy>
    ```

    Press `q` to exit `less`.

2. Understand what the file creates.

    | Table | Purpose |
    | --- | --- |
    | `calculation_rule_version` | Identifies the deterministic PHP rule used for a saved result |
    | `comparison` | Represents one saved comparison workbook and its workflow state |
    | `comparison_input` | Preserves the complete RHEL and Oracle Linux freeform inputs |
    | `ai_formatting_run` | Records the model, outcome, and validated response for each formatting attempt |
    | `comparison_line` | Preserves AI suggestions separately from representative-reviewed values and alignment decisions |
    | `comparison_result` | Stores the annual, three-year, and five-year result snapshot |
    | `application_event` | Records AI, representative, and application workflow events |

3. Load the schema as the DB System administrator. Replace the private IP placeholder.

    ```bash
    <copy>mysql --host=HEATWAVE_PRIVATE_IP --user=olvnadmin --password --ssl-mode=REQUIRED &lt; ~/ol-value-navigator-application/database/schema.sql</copy>
    ```

4. Enter the DB System administrator password when prompted.

    The schema file creates the database with `utf8mb4`, creates all tables with foreign keys and fixed-precision decimal money columns, and loads `workshop-v1` calculation-rule metadata.

## Task 3: Create the PHP database account

1. Connect as the DB System administrator.

    ```bash
    <copy>mysql --host=HEATWAVE_PRIVATE_IP --user=olvnadmin --password --ssl-mode=REQUIRED</copy>
    ```

2. Create the application account. Replace `CHANGE_THIS_PASSWORD` with a new private password.

    ```sql
    <copy>CREATE USER 'olvn_app'@'%'
      IDENTIFIED BY 'CHANGE_THIS_PASSWORD';</copy>
    ```

    If you are repeating the lab and the account already exists, reset its password instead.

    ```sql
    <copy>ALTER USER 'olvn_app'@'%'
      IDENTIFIED BY 'CHANGE_THIS_PASSWORD';</copy>
    ```

    Store this password securely. Do not add the real value to the repository, a shell script, or a command-line argument.

3. Grant the data permissions required by the PHP application.

    ```sql
    <copy>GRANT SELECT
      ON ol_value_navigator.calculation_rule_version
      TO 'olvn_app'@'%';

    GRANT SELECT, INSERT, UPDATE, DELETE
      ON ol_value_navigator.comparison
      TO 'olvn_app'@'%';

    GRANT SELECT, INSERT, UPDATE, DELETE
      ON ol_value_navigator.comparison_input
      TO 'olvn_app'@'%';

    GRANT SELECT, INSERT, DELETE
      ON ol_value_navigator.ai_formatting_run
      TO 'olvn_app'@'%';

    GRANT SELECT, INSERT, UPDATE, DELETE
      ON ol_value_navigator.comparison_line
      TO 'olvn_app'@'%';

    GRANT SELECT, INSERT, UPDATE, DELETE
      ON ol_value_navigator.comparison_result
      TO 'olvn_app'@'%';

    GRANT SELECT, INSERT
      ON ol_value_navigator.application_event
      TO 'olvn_app'@'%';</copy>
    ```

4. Grant access to the MySQL HeatWave GenAI system routine.

    ```sql
    <copy>GRANT SELECT, EXECUTE
      ON sys.*
      TO 'olvn_app'@'%';</copy>
    ```

    The application uses the single-row `sys.ML_GENERATE` routine. It does not receive schema creation, table alteration, or user-administration privileges.

5. Display the resulting grants, and then exit.

    ```sql
    <copy>SHOW GRANTS FOR 'olvn_app'@'%';
    EXIT;</copy>
    ```

## Task 4: Verify the schema and GenAI access

1. Connect with the new application account.

    ```bash
    <copy>mysql --host=HEATWAVE_PRIVATE_IP --user=olvn_app --password --ssl-mode=REQUIRED ol_value_navigator</copy>
    ```

2. Confirm that all seven tables exist.

    ```sql
    <copy>SHOW TABLES;</copy>
    ```

    Confirm that the output contains:

    ```text
    ai_formatting_run
    application_event
    calculation_rule_version
    comparison
    comparison_input
    comparison_line
    comparison_result
    ```

3. Confirm the active calculation-rule version.

    ```sql
    <copy>SELECT version_label, governance_status, active
    FROM calculation_rule_version;</copy>
    ```

    Confirm that `workshop-v1` is active and has the `DEMONSTRATION` governance status.

4. Confirm that the application account can call MySQL HeatWave GenAI.

    ```sql
    <copy>SELECT sys.ML_GENERATE(
      'Return the word READY.',
      JSON_OBJECT(
        'task', 'generation',
        'model_id', 'mistral-7b-instruct-v3',
        'language', 'en',
        'temperature', 0
      )
    );</copy>
    ```

    Wait for the response and confirm that its `text` field contains `READY`.

5. Confirm that no master catalog table exists.

    ```sql
    <copy>SELECT table_name
    FROM information_schema.tables
    WHERE table_schema = 'ol_value_navigator'
      AND table_name LIKE '%catalog%';</copy>
    ```

    The query must return an empty result.

6. Exit the MySQL client.

    ```sql
    <copy>EXIT;</copy>
    ```

    > **Checkpoint:** The application account can manage saved-comparison records and call `sys.ML_GENERATE`, but the schema has no master RHEL or Oracle Linux SKU catalog.

You have created the full persistence layer. In the next lab, you will deploy the PHP foundation and use it to create, list, and reopen comparison workbooks.

## Learn More

* [MySQL HeatWave GenAI roles and privileges](https://dev.mysql.com/doc/heatwave/en/mys-hw-genai-privileges.html)
* [MySQL fixed-point data types](https://dev.mysql.com/doc/refman/8.4/en/fixed-point-types.html)
* [MySQL access control](https://dev.mysql.com/doc/refman/8.4/en/access-control.html)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
