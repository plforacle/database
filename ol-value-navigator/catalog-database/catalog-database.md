# Lab 2: Create the MySQL HeatWave Database for Inputs, Reviews, Results, and Audit History

## Introduction

In this lab, you create the MySQL HeatWave database that gives the Oracle Linux Value Navigator the persistence of an Excel workbook. A representative can save a comparison, reopen it, review the original input and formatted lines, revise decisions, and reproduce the results.

The database stores each comparison as a user-created snapshot. It does not maintain master RHEL or Oracle Linux product catalogs and does not claim that a saved SKU, description, or price is authoritative outside that comparison.

Estimated Time: 60 minutes

### About the Application Flow

The application follows this flow:

1. The representative creates a comparison.
2. The representative pastes the complete RHEL SKU text into the RHEL freeform input.
3. MySQL HeatWave GenAI extracts and formats possible RHEL SKUs, descriptions, quantities, and prices.
4. The representative pastes the complete Oracle Linux SKU text into the Oracle Linux freeform input.
5. MySQL HeatWave GenAI extracts and formats possible Oracle Linux SKUs, descriptions, quantities, and prices.
6. The representative reviews, corrects, aligns, and confirms both sides.
7. PHP calculates annual, three-year, and five-year totals.
8. The application saves the inputs, confirmed lines, decisions, rule version, and results.
9. The representative can reopen, revise, duplicate, or export the comparison.

MySQL HeatWave acts like the saved workbook file. Product data exists only as part of the comparison in which the representative supplied and confirmed it.

### Objectives

In this lab, you will:

* Create the application database and PHP account.
* Record calculation-rule versions.
* Store the complete RHEL and Oracle Linux freeform SKU text for each saved comparison.
* Store AI-formatted and representative-confirmed lines.
* Store calculated result snapshots.
* Record AI and human workflow events.
* Apply least-privilege permissions.

### Prerequisites

This lab assumes you have:

* A running Oracle Linux instance from Lab 1.
* An active MySQL HeatWave DB System running version 9.0 Innovation or later.
* An active HeatWave cluster with Lakehouse and GenAI enabled.
* Terminal access to the instance.
* The MySQL HeatWave administrator password created in Lab 1.

> **Note:** Use demonstration information only in this prototype. Authentication, ownership, encryption, retention, and deletion controls are required before storing customer information.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Create the database and application account

1. Connect to the private HeatWave DB System as its administrator. Replace the private-IP placeholder.

    ```bash
    <copy>mysql --host=HEATWAVE_PRIVATE_IP --user=olvnadmin --password --ssl-mode=REQUIRED</copy>
    ```

2. Enter the MySQL HeatWave administrator password when prompted.

3. Create the application database and local PHP account. Replace `CHANGE_THIS_PASSWORD` with a private password.

    ```sql
    <copy>CREATE DATABASE ol_value_navigator
      CHARACTER SET utf8mb4
      COLLATE utf8mb4_0900_ai_ci;

    CREATE USER 'olvn_app'@'%'
      IDENTIFIED BY 'CHANGE_THIS_PASSWORD';

    USE ol_value_navigator;</copy>
    ```

    Store the password securely. Do not add it to the workshop repository.

## Task 2: Create the rule-version and comparison tables

1. Create the table that identifies the PHP calculation rules used for a saved result.

    ```sql
    <copy>CREATE TABLE calculation_rule_version (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      version_label VARCHAR(64) NOT NULL UNIQUE,
      description VARCHAR(500) NOT NULL,
      source_reference VARCHAR(255) NOT NULL,
      governance_status ENUM('DEMONSTRATION','APPROVED','RETIRED')
        NOT NULL DEFAULT 'DEMONSTRATION',
      approved_by VARCHAR(255) NULL,
      approved_at DATETIME NULL,
      active TINYINT(1) NOT NULL DEFAULT 1,
      created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    );</copy>
    ```

2. Create the table representing one saved comparison.

    ```sql
    <copy>CREATE TABLE comparison (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(255) NOT NULL,
      status ENUM('DRAFT','NEEDS_REVIEW','CONFIRMED','CALCULATED')
        NOT NULL DEFAULT 'DRAFT',
      rule_version_id BIGINT UNSIGNED NULL,
      created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
      CONSTRAINT fk_comparison_rule_version
        FOREIGN KEY (rule_version_id)
        REFERENCES calculation_rule_version(id)
    );</copy>
    ```

    > **Checkpoint:** A comparison has a name, workflow status, calculation-rule version, and timestamps.

## Task 3: Create the input and formatted-line tables

1. Create the table that preserves the complete RHEL and Oracle Linux freeform SKU text supplied for a comparison.

    ```sql
    <copy>CREATE TABLE comparison_input (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      comparison_id BIGINT UNSIGNED NOT NULL,
      input_side ENUM('RHEL','ORACLE_LINUX') NOT NULL,
      raw_text MEDIUMTEXT NOT NULL,
      created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
      CONSTRAINT fk_input_comparison
        FOREIGN KEY (comparison_id)
        REFERENCES comparison(id) ON DELETE CASCADE,
      UNIQUE KEY unique_comparison_input (comparison_id, input_side)
    );</copy>
    ```

2. Create the table that stores AI-formatted and representative-reviewed lines.

    ```sql
    <copy>CREATE TABLE comparison_line (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      comparison_input_id BIGINT UNSIGNED NOT NULL,
      line_number INT UNSIGNED NOT NULL,
      comparison_group INT UNSIGNED NULL,
      sku VARCHAR(128) NULL,
      description VARCHAR(500) NULL,
      quantity DECIMAL(12,2) NULL,
      annual_unit_price DECIMAL(14,2) NULL,
      review_status ENUM(
        'AI_SUGGESTED',
        'CONFIRMED',
        'EXCLUDED',
        'UNRESOLVED'
      ) NOT NULL DEFAULT 'AI_SUGGESTED',
      representative_note VARCHAR(500) NULL,
      created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
      CONSTRAINT fk_line_input
        FOREIGN KEY (comparison_input_id)
        REFERENCES comparison_input(id) ON DELETE CASCADE,
      UNIQUE KEY unique_input_line (comparison_input_id, line_number),
      CHECK (quantity IS NULL OR quantity &gt; 0),
      CHECK (annual_unit_price IS NULL OR annual_unit_price &gt;= 0)
    );</copy>
    ```

    The same `comparison_group` value can align related RHEL and Oracle Linux lines. An unresolved line remains visible but is not included in confirmed totals.

    > **Checkpoint:** The database can preserve the complete original text from both inputs and the reviewed rows without treating them as a master product catalog.

## Task 4: Create the result and event tables

1. Create the table that preserves the calculated result snapshot.

    ```sql
    <copy>CREATE TABLE comparison_result (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      comparison_id BIGINT UNSIGNED NOT NULL UNIQUE,
      rhel_annual_total DECIMAL(16,2) NOT NULL,
      oracle_linux_annual_total DECIMAL(16,2) NOT NULL,
      rhel_three_year_total DECIMAL(16,2) NOT NULL,
      oracle_linux_three_year_total DECIMAL(16,2) NOT NULL,
      rhel_five_year_total DECIMAL(16,2) NOT NULL,
      oracle_linux_five_year_total DECIMAL(16,2) NOT NULL,
      calculated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      CONSTRAINT fk_result_comparison
        FOREIGN KEY (comparison_id)
        REFERENCES comparison(id) ON DELETE CASCADE,
      CHECK (rhel_annual_total &gt;= 0),
      CHECK (oracle_linux_annual_total &gt;= 0)
    );</copy>
    ```

2. Create the append-only event table.

    ```sql
    <copy>CREATE TABLE application_event (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      comparison_id BIGINT UNSIGNED NOT NULL,
      event_type ENUM(
        'AI_FORMATTING_COMPLETED',
        'REPRESENTATIVE_CONFIRMED',
        'CALCULATION_COMPLETED',
        'COMPARISON_EXPORTED'
      ) NOT NULL,
      actor_type ENUM('AI','REPRESENTATIVE','APPLICATION') NOT NULL,
      outcome ENUM('COMPLETED','CONFIRMED','REJECTED','FAILED') NOT NULL,
      created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      CONSTRAINT fk_event_comparison
        FOREIGN KEY (comparison_id)
        REFERENCES comparison(id) ON DELETE CASCADE,
      INDEX idx_event_comparison (comparison_id, created_at)
    );</copy>
    ```

    > **Checkpoint:** Saved results and workflow events can be traced back to one comparison.

## Task 5: Apply least-privilege permissions

1. Allow PHP to read rule versions and manage saved comparison records.

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

    GRANT SELECT, INSERT, UPDATE, DELETE
      ON ol_value_navigator.comparison_line
      TO 'olvn_app'@'%';

    GRANT SELECT, INSERT, UPDATE, DELETE
      ON ol_value_navigator.comparison_result
      TO 'olvn_app'@'%';

    GRANT SELECT, INSERT
      ON ol_value_navigator.application_event
      TO 'olvn_app'@'%';

    FLUSH PRIVILEGES;</copy>
    ```

2. Display the application account permissions.

    ```sql
    <copy>SHOW GRANTS FOR 'olvn_app'@'%';</copy>
    ```

    The PHP account can manage comparisons but cannot create, alter, or drop database tables.

## Task 6: Load rule metadata and verify the database

1. Insert the demonstration calculation-rule version.

    ```sql
    <copy>INSERT INTO calculation_rule_version
      (version_label, description, source_reference, governance_status)
    VALUES
      ('workshop-v1',
       'Annual cost equals quantity multiplied by annual unit price; three-year and five-year costs use the confirmed annual amount.',
       'Oracle Linux Value Navigator workshop demonstration rules',
       'DEMONSTRATION');</copy>
    ```

2. Display the tables.

    ```sql
    <copy>SHOW TABLES;</copy>
    ```

    Confirm that the output contains:

    ```text
    application_event
    calculation_rule_version
    comparison
    comparison_input
    comparison_line
    comparison_result
    ```

3. Confirm that no master product-catalog table exists.

    ```sql
    <copy>SELECT table_name
    FROM information_schema.tables
    WHERE table_schema = 'ol_value_navigator'
      AND table_name LIKE '%catalog%';</copy>
    ```

    The query should return an empty result.

4. Exit MySQL HeatWave.

    ```sql
    <copy>EXIT;</copy>
    ```

    > **Checkpoint:** MySQL HeatWave can preserve a complete comparison like a saved workbook, but it does not maintain a master RHEL or Oracle Linux SKU catalog.

You have created the persistence layer for saved comparisons. In the next lab, you will build the PHP interface for creating, saving, and reopening comparisons.

## Learn More

* [MySQL HeatWave GenAI](https://dev.mysql.com/doc/heatwave/en/mys-hw-genai-overview.html)
* [MySQL HeatWave access control and account management](https://dev.mysql.com/doc/refman/8.4/en/access-control.html)
* [PHP PDO documentation](https://www.php.net/manual/en/book.pdo.php)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, August 2026
