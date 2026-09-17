# Install and Check the PHP Application

## Introduction

Install Shawn's application from one ZIP on the Version 3 compute instance. The ZIP includes the PHP dependencies, so you do not run Composer on OCI. Keep the existing compute instance and HeatWave database.

The bundled catalog supplies subscription choices. HeatWave still stores comparisons, revisions, and catalog snapshots and runs GenAI. This private rehearsal uses a shared demo identity, not individual login or user-owned comparisons. Use invented data only.

Estimated Time: 30 minutes

### Objectives

* Download and verify the application package.
* Install it without changing Version 1.
* Check database access, GenAI, and the browser workflows.

### Prerequisites

* Completed Labs 1 and 2.
* SSH access to ol-value-navigator-3-app.
* The application database password, or the working protected credentials from the earlier Lab 3 installation.
* Internet access from the compute instance to the workshop download URL.

## Task 1: Download the package to the compute instance

1. This workshop provides [the R3 application ZIP](../catalog-database/files/ol-value-navigator-v3-r3.zip). All learners use the same published download URL.

    Use R3 for this lab. Do not install older R1/R2 packages. This is not an upgrade procedure for an already installed R1/R2 package.

2. Open Windows PowerShell. Set your private key path and connect to the Version 3 compute instance.

    ```powershell
    <copy>
    $V3Key = 'C:\REPLACE\WITH\YOUR\V3-PRIVATE-KEY.key'
    ssh -i $V3Key opc@158.101.119.245
    </copy>
    ```

3. Confirm that the hostname is **ol-value-navigator-3-app**. Run the remaining Linux commands in this SSH session.

    ```bash
    <copy>
    hostname
    mkdir -m 700 /home/opc/olvn-v3-download-r3
    cd /home/opc/olvn-v3-download-r3
    </copy>
    ```

    If the directory already exists from this same download, inspect it and use it. Do not remove an existing installation.

4. Download the ZIP from the published workshop location.

    ```bash
    <copy>
    V3_ZIP_URL='https://plforacle.github.io/database/ol-value-navigator-v3/catalog-database/files/ol-value-navigator-v3-r3.zip'
    curl --fail --location --proto '=https' --proto-redir '=https' "$V3_ZIP_URL" -o ol-value-navigator-v3-r3.zip
    unset V3_ZIP_URL
    </copy>
    ```

5. Verify the ZIP before extracting it. Continue only if the result is **OK**.

    ```bash
    <copy>
    printf '%s  %s\n' '0dbdd3010e0dcff3e6ccd9db82f9b3b91afebf83c0ca694781d245ea41477167' 'ol-value-navigator-v3-r3.zip' | sha256sum -c -
    </copy>
    ```

## Task 2: Check prerequisites

1. Install the additional PHP extensions and local setup tools. This does not change the PHP module stream selected in Lab 1.

    ```bash
    <copy>
    sudo dnf install -y php-gd php-mbstring php-xml php-process php-pecl-zip php-bcmath policycoreutils-python-utils unzip
    </copy>
    ```

2. Extract the verified ZIP once.

    ```bash
    <copy>
    cd /home/opc/olvn-v3-download-r3
    unzip ol-value-navigator-v3-r3.zip
    cd ol-value-navigator-v3-r3
    </copy>
    ```

    If you already extracted this exact package, use the existing directory instead of overwriting it.

3. Skip this step if all three GenAI grants have already been applied. Perside completed these grants and verified READY through PHP on September 17.

    For a fresh environment, connect as the database administrator. Use the private DB IP recorded in Lab 1 if it differs from this rehearsal address.

    ```bash
    <copy>
    mysql --host=10.0.1.88 --port=3306 --user=olvnadmin --password --ssl-mode=REQUIRED
    </copy>
    ```

4. For a fresh environment only, confirm that CURRENT_USER is the administrator, then apply the package's three scoped grants. Do not run these commands as olvn_v3_app.

    ```sql
    <copy>
    SELECT CURRENT_USER();
    SOURCE /home/opc/olvn-v3-download-r3/ol-value-navigator-v3-r3/genai-grant.sql;
    exit;
    </copy>
    ```

    The grants allow execution of ML_GENERATE, ML_CLUSTER_CHECK, and ML_GENAI_VARIABLE. The application never receives the administrator password.

## Task 3: Install and run the checks

1. On the compute instance, run the installer.

    ```bash
    <copy>
    cd /home/opc/olvn-v3-download-r3/ol-value-navigator-v3-r3
    sudo bash install.sh
    </copy>
    ```

    The installer validates prerequisites and database credentials before copying application files. It reuses working protected credentials from the earlier Lab 3 installation when available. Otherwise, it prompts for the private DB IP and application password. Type values at the prompts; do not replace the prompt text with passwords.

    A successful installation reports **INSTALLED**. It does not migrate the database, delete comparisons, or change Version 1. A retry accepts only matching package files and configuration. If a conflict is reported, stop and share the error without passwords. Do not delete files to bypass the checks.

2. Run the packaged checks.

    ```bash
    <copy>
    sudo bash /opt/olvn-v3/package-r3/test.sh
    </copy>
    ```

    Expect offline calculation checks, an encrypted database connection, a GenAI READY response, and HTTP checks to pass. GenAI can take about a minute. Stop on a failed check. A passing script does not replace the browser tests below.

## Task 4: Open Shawn's GUI privately

1. Open a second Windows PowerShell window. Start the SSH tunnel and leave this window open.

    ```powershell
    <copy>
    $V3Key = 'C:\REPLACE\WITH\YOUR\V3-PRIVATE-KEY.key'
    ssh -i $V3Key -o ExitOnForwardFailure=yes -N -L 127.0.0.1:8080:127.0.0.1:8009 opc@158.101.119.245
    </copy>
    ```

2. Open these pages in your browser:

    * Legacy coverage comparison: http://127.0.0.1:8080/demo/coverage
    * New scenario: http://127.0.0.1:8080/demo/coverage/new
    * Separate extraction demo: http://127.0.0.1:8080/demo

    The application listens only on the compute instance's loopback address. Do not open port 8009 in OCI or the host firewall. The existing port-80 greeting remains available.

    Local browser HTTP travels through the encrypted SSH tunnel. Database traffic is encrypted, but server certificate identity is not verified. This is a private rehearsal, not a public multiuser deployment.

## Task 5: Verify the application workflows

1. Open the legacy coverage comparison at http://127.0.0.1:8080/demo/coverage. Use invented customer data, ten physical systems with two CPUs per system, source SKU **SYN-OS** with quantity ten, and Oracle offering **Synthetic Basic**. Set OLAM required to No. The bundled synthetic annual prices are USD 150 per source unit and USD 60 per Oracle CPU pair.

    The offline test independently checks these synthetic totals:

    | Period | Source cost | Oracle cost |
    | --- | --- | --- |
    | Annual | USD 1,500 | USD 600 |
    | Three years | USD 4,500 | USD 1,800 |
    | Five years | USD 7,500 | USD 3,000 |

    Annual savings are USD 900, or 60 percent. These are test values, not commercial quotes.

2. Save and reopen the comparison. Review and confirm it, then download the editable PowerPoint. Check that the saved values and presentation match the reviewed comparison. Report any failure rather than treating the command-line tests as full acceptance.

3. In the newer scenario workflow, test import using **examples/synthetic-import.tsv** from the ZIP extracted on your computer. Review imported rows and unresolved inputs before confirming. Test saving, reopening, and export in this workflow as well.

    The newer scenario workflow supports 12 and 36 months. It is separate from the legacy annual, three-year, and five-year workflow. Five-year scenario support and real login remain open requirements.

4. Record which browser checks passed and any errors. These checks are pending until you perform them on this package. Close the tunnel with Ctrl+C when finished.

## Troubleshooting

* **Missing prerequisite:** Install the named PHP extension or tool, then retry the same installer.
* **Credential error:** Check the application password. Never paste it into chat or a command example.
* **Permission denied for a GenAI routine:** Confirm Task 2 was completed as olvnadmin. Do not grant broad administrator permissions.
* **Existing configuration differs:** Stop. This installer does not overwrite another package or perform upgrades.
* **Browser cannot connect:** Keep the SSH tunnel open and check that local port 8080 is available.
* **Application error:** Share the safe error and failed step. Do not post protected JSON configuration files.

## Learn More

* [PHP PDO MySQL connection options](https://www.php.net/manual/en/ref.pdo-mysql.php)

## Acknowledgements

* **Authors** - Perside Foster, Mark Atkinson, and Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
