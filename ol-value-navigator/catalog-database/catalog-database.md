# Lab 2: Create the Subscription Catalog Database

## Introduction

This lab walks you through creating the database that stores the workshop catalog, governed mappings, comparisons, and reviewed comparison lines.

Estimated Time: 45 minutes

### About the Workshop Catalog

The workshop catalog contains demonstration values only. The application reads SKUs, prices, and mappings from tables instead of embedding them in PHP code. This separation makes the calculation easier to review and maintain.

### Objectives

In this lab, you will:

* Create the application database and database account.
* Create catalog, mapping, comparison, and line tables.
* Load demonstration catalog rows.
* Verify the stored data.

### Prerequisites

This lab assumes you have:

* A running Oracle Linux instance from Lab 1.
* Oracle MySQL Server installed and running.
* Terminal access to the instance.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Create the database and application account

1. Open the MySQL command-line client as the MySQL root user.

    ```bash
    <copy>mysql -u root -p</copy>
    ```

2. Create the database and an application account. Replace `CHANGE_THIS_PASSWORD` with a private password that is not committed to source control.

    ```sql
    <copy>CREATE DATABASE ol_value_navigator;
    CREATE USER 'olvn_app'@'localhost' IDENTIFIED BY 'CHANGE_THIS_PASSWORD';
    GRANT SELECT, INSERT, UPDATE, DELETE ON ol_value_navigator.* TO 'olvn_app'@'localhost';
    FLUSH PRIVILEGES;</copy>
    ```

## Task 2: Create the catalog tables

1. Select the application database.

    ```sql
    <copy>USE ol_value_navigator;</copy>
    ```

2. Create the RHEL catalog table.

    ```sql
    <copy>CREATE TABLE rhel_catalog (
      id INT AUTO_INCREMENT PRIMARY KEY,
      sku VARCHAR(64) NOT NULL UNIQUE,
      description VARCHAR(255) NOT NULL,
      annual_usd_price DECIMAL(12,2) NOT NULL,
      active TINYINT(1) NOT NULL DEFAULT 1
    );</copy>
    ```

3. Create the Oracle Linux option and mapping tables.

    ```sql
    <copy>CREATE TABLE oracle_linux_option (
      id INT AUTO_INCREMENT PRIMARY KEY,
      option_code VARCHAR(64) NOT NULL UNIQUE,
      description VARCHAR(255) NOT NULL,
      annual_usd_price DECIMAL(12,2) NOT NULL,
      active TINYINT(1) NOT NULL DEFAULT 1
    );

    CREATE TABLE sku_mapping (
      id INT AUTO_INCREMENT PRIMARY KEY,
      rhel_sku VARCHAR(64) NOT NULL,
      oracle_option_code VARCHAR(64) NOT NULL,
      rationale VARCHAR(500) NOT NULL,
      UNIQUE KEY unique_mapping (rhel_sku, oracle_option_code)
    );</copy>
    ```

## Task 3: Create the comparison tables

1. Create the comparison and comparison-line tables.

    ```sql
    <copy>CREATE TABLE comparison (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(255) NOT NULL,
      status ENUM('DRAFT','NEEDS_REVIEW','READY','CONFIRMED','INCOMPLETE') NOT NULL DEFAULT 'DRAFT',
      created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE comparison_line (
      id INT AUTO_INCREMENT PRIMARY KEY,
      comparison_id INT NOT NULL,
      rhel_sku VARCHAR(64) NOT NULL,
      quantity DECIMAL(12,2) NOT NULL,
      decision ENUM('CONFIRMED','EXCLUDED','UNRESOLVED') NOT NULL DEFAULT 'UNRESOLVED',
      exclusion_reason VARCHAR(500) NULL,
      FOREIGN KEY (comparison_id) REFERENCES comparison(id)
    );</copy>
    ```

## Task 4: Load and verify demonstration data

1. Insert the demonstration catalog and mapping rows.

    ```sql
    <copy>INSERT INTO rhel_catalog (sku, description, annual_usd_price) VALUES
      ('DEMO-RHEL-STD', 'Demonstration RHEL standard support', 1200.00),
      ('DEMO-RHEL-PRM', 'Demonstration RHEL premium support', 1800.00);

    INSERT INTO oracle_linux_option (option_code, description, annual_usd_price) VALUES
      ('DEMO-OL-BASIC', 'Demonstration Oracle Linux Basic support', 600.00),
      ('DEMO-OL-PREMIER', 'Demonstration Oracle Linux Premier support', 1000.00);

    INSERT INTO sku_mapping (rhel_sku, oracle_option_code, rationale) VALUES
      ('DEMO-RHEL-STD', 'DEMO-OL-BASIC', 'Demonstration workshop mapping'),
      ('DEMO-RHEL-PRM', 'DEMO-OL-PREMIER', 'Demonstration workshop mapping');</copy>
    ```

2. Verify the catalog and mapping rows.

    ```sql
    <copy>SELECT * FROM rhel_catalog;
    SELECT * FROM oracle_linux_option;
    SELECT * FROM sku_mapping;</copy>
    ```

## Learn More

* [MySQL 8.4 Reference Manual](https://dev.mysql.com/doc/refman/8.4/en/)
* [PHP PDO documentation](https://www.php.net/manual/en/book.pdo.php)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, August 2026
