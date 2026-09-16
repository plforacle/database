# Lab 2: Create the Version 3 Application Database

## Introduction

Create the database structure that Shawn's PHP application expects, then create a separate database account for the application. Use the V3 HeatWave DB System from Lab 1. Do not create another DB System.

This lab prepares storage for catalog data, comparisons, and saved revisions. It does not load or activate a pricing catalog. Catalog publication uses Shawn's PHP commands and will be covered with application installation in Lab 3.

Estimated Time: 30 minutes; confirm during rehearsal.

### Objectives

* Transfer the supplied V3 SQL files to the application server.
* Create the `olvn_v3` schema using the reviewed upstream migrations.
* Create a password-protected application database account.
* Verify the schema, connection, and database permissions.

### Prerequisites

* Lab 1 completed, including the successful HeatWave GenAI test.
* The V3 application server's public IP address and your private SSH key.
* The V3 DB System's private IP address and the `olvnadmin` password.
* The Version 3 workshop folder on your computer.

> **Rehearsal status:** These instructions and SQL files have been checked locally against Shawn's source. They have not yet been executed against your HeatWave DB System. Stop at the first unexpected error and share the error text without passwords.

### What the supplied files do

The SQL is based on migrations 001 through 008 in upstream commit `d419d24d035cabc68b219b9e98be23cd05770742`.

| File | Purpose |
| --- | --- |
| [v3-schema.sql](files/v3-schema.sql) | Creates a fresh schema, 23 tables, 62 final triggers, and one stored procedure |
| [v3-runtime-grants.sql](files/v3-runtime-grants.sql) | Gives the application account table-specific permissions |

The schema file omits the upstream fixture account and grant sections. It preserves the tables, constraints, triggers, and baseline metadata. It contains no shared test passwords.

Use only these two files for this lab. The copied `files/application` folder and old application ZIP are Version 1 references, not the V3 installer.

## Task 1: Copy the SQL files to the V3 server

1. Open PowerShell on your computer, not inside your SSH session. Change to the V3 workshop's files directory.

    ```powershell
    <copy>Set-Location "C:\Users\Perside\Documents\GitHub\database\ol-value-navigator-v3\catalog-database\files"
    Get-Item .\v3-schema.sql, .\v3-runtime-grants.sql</copy>
    ```

    If your workshop is stored elsewhere, use its local path instead.

2. Copy both files to the V3 application server. Replace `PATH_TO_PRIVATE_KEY` and `V3_APP_PUBLIC_IP` with your SSH key path and the public IP from Lab 1.

    ```powershell
    <copy>scp -i "PATH_TO_PRIVATE_KEY" .\v3-schema.sql .\v3-runtime-grants.sql opc@V3_APP_PUBLIC_IP:/home/opc/</copy>
    ```

3. Connect to the V3 application server.

    ```powershell
    <copy>ssh -i "PATH_TO_PRIVATE_KEY" opc@V3_APP_PUBLIC_IP</copy>
    ```

4. If you are already at a `mysql>` prompt, enter `exit;` to return to the Linux shell. Confirm the server name and transferred files.

    ```bash
    <copy>hostname
    ls -l /home/opc/v3-schema.sql /home/opc/v3-runtime-grants.sql
    sha256sum /home/opc/v3-schema.sql /home/opc/v3-runtime-grants.sql</copy>
    ```

    The hostname should identify `ol-value-navigator-3-app`. Compare the hashes with the following values.

    | File | SHA-256 |
    | --- | --- |
    | v3-schema.sql | `a029d42ae78189076017699f1644c8bbdb256d4f3223d6bb3598397b7c7a4d9f` |
    | v3-runtime-grants.sql | `0b22088667824343114b22863e59501c2a1af7d883740f10e8fb43f5d7fc9c4d` |

    If either hash differs, stop and check the local file and transfer. Do not run a different file because its name looks correct.

## Task 2: Confirm the target database

1. In the OCI Console, open `ol-value-navigator-3-db` in compartment `ol-value-navigator-3`. Under **Connections**, locate **Primary endpoint → Private IP address**.

    Use this database address, not the application server's address. For the private subnet in Lab 1, it is in the `10.0.1.0/24` range.

2. In the V3 Linux shell, store that address for the commands in this lab. The prompt accepts only the IP address, not a password.

    ```bash
    <copy>read -r -p "V3 DB System private IP address: " V3_DB_IP
    printf 'Database target: %s\n' "$V3_DB_IP"</copy>
    ```

3. Connect as the V3 database administrator.

    ```bash
    <copy>mysql --host="$V3_DB_IP" --user=olvnadmin --password --ssl-mode=REQUIRED</copy>
    ```

    Enter the administrator password at the prompt. Do not add it to the command line.

4. Confirm the server and check whether the schema already exists.

    ```sql
    <copy>SELECT VERSION(), CURRENT_USER(), @@server_uuid;
    SELECT SCHEMA_NAME
    FROM information_schema.SCHEMATA
    WHERE SCHEMA_NAME = 'olvn_v3';</copy>
    ```

    The schema query must return **Empty set** for a first installation. If it returns `olvn_v3`, stop and inspect the existing installation. Do not drop it or rerun the installer blindly.

5. Return to the Linux shell.

    ```sql
    <copy>exit;</copy>
    ```

## Task 3: Install and verify the schema

1. Run the supplied schema file from the V3 Linux shell.

    ```bash
    <copy>mysql --host="$V3_DB_IP" --user=olvnadmin --password --ssl-mode=REQUIRED < /home/opc/v3-schema.sql
    v3_schema_status=$?
    printf 'Schema command exit status: %s\n' "$v3_schema_status"</copy>
    ```

    Enter the administrator password. Expect `V3_SCHEMA_INSTALLED` and exit status `0`.

    The file starts with `CREATE DATABASE olvn_v3`, without `IF NOT EXISTS`. MySQL batch execution stops on an error. Do not add `--force` or use interactive `SOURCE` to bypass that behavior.

    If an error occurs, stop. MySQL schema changes can remain committed even when a later statement fails. Keep the error text and line number; do not delete the database or retry the entire file.

2. Connect to the new schema.

    ```bash
    <copy>mysql --host="$V3_DB_IP" --user=olvnadmin --password --ssl-mode=REQUIRED --database=olvn_v3</copy>
    ```

3. Count the installed objects.

    ```sql
    <copy>SELECT DATABASE() AS selected_schema;
    SELECT COUNT(*) AS table_count
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE';
    SELECT COUNT(*) AS trigger_count
    FROM information_schema.TRIGGERS
    WHERE TRIGGER_SCHEMA = DATABASE();
    SELECT COUNT(*) AS procedure_count
    FROM information_schema.ROUTINES
    WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = 'PROCEDURE';</copy>
    ```

    Expect schema `olvn_v3`, **23 tables**, **62 triggers**, and **1 procedure**.

4. Check the catalog channel and empty comparison tables.

    ```sql
    <copy>SELECT CONVERT(channel USING utf8mb4) AS channel,
           CONVERT(release_id USING utf8mb4) AS release_id, generation
    FROM package_channel;
    SELECT COUNT(*) AS published_packages FROM package_release;
    SELECT COUNT(*) AS saved_comparisons FROM coverage_comparison;</copy>
    ```

    Expect channel `coverage`, `release_id` of `NULL`, generation `0`, and both counts `0`. This is the correct fresh-install state. It does not mean a pricing package is active.

## Task 4: Create the application database account

1. While connected as `olvnadmin`, create the application account.

    ```sql
    <copy>CREATE USER 'olvn_v3_app'@'10.0.0.0/255.255.255.0'
      IDENTIFIED BY RANDOM PASSWORD REQUIRE SSL;</copy>
    ```

    MySQL displays a generated password. Save it immediately in your password manager for Lab 3. Do not share this result or include it in screenshots.

    The account accepts connections from the application subnet and requires encrypted connections. If you used a different application subnet, adapt the account host here and in the grants file before execution.

    If MySQL reports that the account already exists, stop rather than resetting its password.

2. Return to the Linux shell.

    ```sql
    <copy>exit;</copy>
    ```

3. Apply the application permissions.

    ```bash
    <copy>mysql --host="$V3_DB_IP" --user=olvnadmin --password --ssl-mode=REQUIRED < /home/opc/v3-runtime-grants.sql
    v3_grants_status=$?
    printf 'Grants command exit status: %s\n' "$v3_grants_status"</copy>
    ```

    Expect `V3_RUNTIME_GRANTS_INSTALLED` and exit status `0`. Use the administrator password for this command, not the newly generated application password.

    The application can read catalog packages and save comparison records. It cannot create tables, delete records, or publish and activate catalog packages. The administrator account is not the web application's login.

## Task 5: Test the application account

1. Connect with the new application account and its generated password.

    ```bash
    <copy>mysql --host="$V3_DB_IP" --user=olvn_v3_app --password --ssl-mode=REQUIRED --database=olvn_v3</copy>
    ```

2. Check the selected schema, authenticated account, encrypted connection, and permissions.

    ```sql
    <copy>SELECT DATABASE(), CURRENT_USER();
    SHOW SESSION STATUS LIKE 'Ssl_cipher';
    SHOW GRANTS FOR CURRENT_USER();
    SELECT CONVERT(channel USING utf8mb4) AS channel, generation
    FROM package_channel;
    SELECT COUNT(*) AS saved_comparisons FROM coverage_comparison;</copy>
    ```

    Expect `olvn_v3`, the `olvn_v3_app` account, a nonempty cipher value, channel `coverage` at generation `0`, and zero comparisons. Encryption in this test does not verify the server certificate identity; application TLS configuration follows in Lab 3.

3. Check that the application cannot change the catalog selection.

    ```sql
    <copy>UPDATE package_channel SET generation = generation WHERE 1 = 0;</copy>
    ```

    Expect an **UPDATE command denied** error. The `WHERE 1 = 0` condition prevents data changes even if permissions are accidentally too broad. If it succeeds, stop and review the grants.

4. Exit MySQL.

    ```sql
    <copy>exit;</copy>
    ```

    **Checkpoint:** The schema exists, its object counts match, and the application account can read the database but cannot change catalog selection.

## Conclusion

The V3 database structure and application account are ready for application integration. Keep the DB System private IP, schema name `olvn_v3`, account name `olvn_v3_app`, and generated password available for Lab 3.

No catalog package has been activated, and database-account creation does not implement representative sign-in or user-owned comparisons. Lab 3 still needs its PHP installation, catalog publication, and application-access instructions before execution. The GenAI test in Lab 1 used the administrator account; application-account GenAI access must also be checked during integration.

## Learn More

* [MySQL CREATE USER](https://dev.mysql.com/doc/refman/8.4/en/create-user.html)
* [Viewing DB System Details](https://docs.oracle.com/en-us/iaas/mysql-database/doc/viewing-db-system-details.html)
* [SQL source and adaptation notes](files/README.md)

## Acknowledgements

* **Authors** - Perside Foster, Mark Atkinson, and Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
