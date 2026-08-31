# Lab 3: Build the PHP Input and Saved Comparison Review Pages

## Introduction

This lab walks you through replacing the temporary test page with a PHP application that saves and reopens the complete freeform RHEL and Oracle Linux SKU text in MySQL HeatWave.

Estimated Time: 60 minutes

### About the PHP Application

The prototype uses server-rendered PHP and PDO prepared statements. Database credentials remain outside the public web directory, and the HeatWave DB System is reached through its private IP address.

### Objectives

In this lab, you will:

* Create the application directories.
* Configure a PDO database connection.
* Build a comparison form.
* Capture and save the complete text from both freeform SKU inputs.
* Reopen a saved comparison.

### Prerequisites

This lab assumes you have:

* Access to the workshop Oracle Linux instance.
* A running Apache and PHP environment.
* The private HeatWave DB System and saved-comparison database.

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

1. Create `/var/www/ol-value-navigator/config.php`. Replace the private IP and password placeholders with values from Labs 1 and 2.

    ```php
    <copy>&lt;?php
    return [
        'dsn' =&gt; 'mysql:host=HEATWAVE_PRIVATE_IP;port=3306;dbname=ol_value_navigator;charset=utf8mb4',
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

2. Add a comparison name, a **RHEL SKU information** text area, and an **Oracle Linux SKU information** text area. Both text areas must accept complete freeform text, including any SKUs, descriptions, quantities, prices, and notes.

3. Submit the form with `POST` and validate that the comparison name and both complete freeform inputs are present.

4. Use one transaction and PDO prepared statements to create one `comparison` row and two `comparison_input` rows.

5. Redirect to a review page that loads the saved comparison by its identifier and displays the complete original text from both inputs.

## Task 4: Verify the saved comparison

1. Open `http://PUBLIC_IP_ADDRESS/ol-value-navigator/` in a browser.

2. Paste complete demonstration RHEL and Oracle Linux SKU text into their separate freeform inputs. Include example SKUs, descriptions, quantities, prices, and notes.

3. Open MySQL HeatWave and verify the saved rows.

    ```sql
    <copy>USE ol_value_navigator;
    SELECT * FROM comparison;
    SELECT comparison_id, input_side, raw_text FROM comparison_input;</copy>
    ```

4. Confirm that the page does not display the database password or connection details.

## Learn More

* [PHP PDO documentation](https://www.php.net/manual/en/book.pdo.php)
* [PHP prepared statements](https://www.php.net/manual/en/pdo.prepared-statements.php)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, August 2026
