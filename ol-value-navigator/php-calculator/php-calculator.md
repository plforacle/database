# Lab 3: Build the PHP Calculator

## Introduction

This lab walks you through replacing the temporary test page with a PHP application that reads the catalog and saves a comparison in MySQL.

Estimated Time: 60 minutes

### About the PHP Application

The prototype uses server-rendered PHP and PDO prepared statements. Database credentials remain outside the public web directory, and catalog choices come from MySQL.

### Objectives

In this lab, you will:

* Create the application directories.
* Configure a PDO database connection.
* Build a comparison form.
* Save a comparison and RHEL line in MySQL.

### Prerequisites

This lab assumes you have:

* Access to the workshop Oracle Linux instance.
* A running Apache, PHP, and MySQL environment.
* The workshop database and demonstration catalog.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Create the application directories

1. Create the public application directory.

    ```bash
    <copy>sudo mkdir -p /var/www/html/ol-value-navigator
    sudo chown -R opc:apache /var/www/html/ol-value-navigator
    sudo chmod -R 750 /var/www/html/ol-value-navigator</copy>
    ```

2. Create a private configuration directory outside the Apache document root.

    ```bash
    <copy>sudo mkdir -p /var/www/ol-value-navigator
    sudo chown opc:apache /var/www/ol-value-navigator
    sudo chmod 750 /var/www/ol-value-navigator</copy>
    ```

## Task 2: Configure the database connection

1. Create `/var/www/ol-value-navigator/config.php` and add the local database settings. Replace the password placeholder with the private password created in Lab 2.

    ```php
    <copy>&lt;?php
    return [
        'dsn' =&gt; 'mysql:host=localhost;dbname=ol_value_navigator;charset=utf8mb4',
        'user' =&gt; 'olvn_app',
        'password' =&gt; 'CHANGE_THIS_PASSWORD',
    ];</copy>
    ```

2. Restrict access to the configuration file.

    ```bash
    <copy>sudo chown opc:apache /var/www/ol-value-navigator/config.php
    sudo chmod 640 /var/www/ol-value-navigator/config.php</copy>
    ```

3. Create `/var/www/html/ol-value-navigator/database.php` with a PDO connection that enables exception mode.

    ```php
    <copy>&lt;?php
    $config = require '/var/www/ol-value-navigator/config.php';

    $pdo = new PDO(
        $config['dsn'],
        $config['user'],
        $config['password'],
        [PDO::ATTR_ERRMODE =&gt; PDO::ERRMODE_EXCEPTION]
    );</copy>
    ```

## Task 3: Build the comparison form

1. Create `/var/www/html/ol-value-navigator/index.php`.

2. Load the active RHEL catalog rows through `database.php` and display the SKU values in a selection list.

3. Add fields for the comparison name, RHEL SKU, and quantity.

4. Submit the form with `POST` and use PDO prepared statements to create one `comparison` row and one `comparison_line` row.

5. Redirect the learner to a confirmation page that displays the new comparison identifier.

## Task 4: Verify the saved comparison

1. Open `http://PUBLIC_IP_ADDRESS/ol-value-navigator/` in a browser.

2. Create a comparison with one demonstration RHEL SKU and a positive quantity.

3. Open MySQL and verify the saved rows.

    ```sql
    <copy>USE ol_value_navigator;
    SELECT * FROM comparison;
    SELECT * FROM comparison_line;</copy>
    ```

4. Confirm that the page does not display the database password or connection details.

## Learn More

* [PHP PDO documentation](https://www.php.net/manual/en/book.pdo.php)
* [PHP prepared statements](https://www.php.net/manual/en/pdo.prepared-statements.php)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, August 2026
