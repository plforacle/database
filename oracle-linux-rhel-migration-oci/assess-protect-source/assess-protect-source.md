# Lab 3: Assess Readiness and Protect the Source

## Introduction

Migration begins with assessment and a recoverable checkpoint. In this lab, you inspect repositories, packages, disk space, services, and the running kernel. You then run the specific migration script version tested for this workshop in dry-run mode and create a boot-volume backup before changing the operating system. Using a specific version and verifying its SHA-256 checksum ensures that every participant runs the same tested script. A dry run checks migration readiness and reports problems without performing the operating-system conversion.

The migration script is maintained in an Oracle-owned GitHub repository, but the repository states that the scripts are community-based and are not officially supported by Oracle.

Before running migration commands that modify the operating system, you must pass the migration checkpoint in this lab. This confirms that readiness checks succeeded, the workload is healthy, and an available boot-volume backup provides a recovery point.

### Objectives

In this lab, you will:

- Assess the source system for common migration risks.
- Download and verify a pinned migration script.
- Run a non-changing migration dry run.
- Create and verify a boot-volume backup.
- Document the rollback decision point.

### Prerequisites

Before beginning this lab, confirm that you have:

- Completed Lab 2.
- SSH access to the running RHEL 9.8 x86_64 source VM.
- A healthy Apache workload that returns `MIGRATION_WORKLOAD_OK`.
- The RHEL baseline evidence bundle created in Lab 2.
- Permission to create and inspect boot-volume backups in OCI.

Estimated Lab Time: 30 minutes

## Task 1: Assess the source operating system

1. Confirm the source version and architecture:

    ```bash
    <copy>
    . /etc/os-release
    printf 'ID=%s VERSION_ID=%s ARCH=%s\n' "$ID" "$VERSION_ID" "$(uname -m)"
    </copy>
    ```

    Continue only when the source is RHEL 9.8 on x86_64.

2. Confirm that RHEL is using the newest installed kernel.

    RHEL can keep more than one kernel. A system update may install a newer kernel, but RHEL does not use it until the VM restarts. The following commands show the kernel running now, all installed kernels from oldest to newest, and the kernel selected for the next restart:

    ```bash
    <copy>
    uname -r
    rpm -q kernel-core | sort -V
    sudo grubby --default-kernel
    </copy>
    ```

    Compare the three results. The version shown by `uname -r` should match the newest installed `kernel-core` version and the version shown in the default kernel path. Older installed kernels are normal and provide fallback options. If the newest version does not match the running version, reboot the VM, reconnect, and repeat this step.

3. Check disk space and the RPM database:

    ```bash
    <copy>
    df -h /
    sudo rpm --verifydb
    sudo dnf check
    </copy>
    ```

4. Inventory enabled repositories:

    ```bash
    <copy>
    sudo dnf repolist --enabled
    sudo find /etc/yum.repos.d -maxdepth 1 -type f -name '*.repo' -print
    </copy>
    ```

5. Identify packages that are not signed by Red Hat or have a different vendor:

    ```bash
    <copy>
    rpm -qa --qf '%{NAME}\t%{VENDOR}\n' \
      | awk -F '\t' '$2 !~ /Red Hat/ {print}' \
      | sort | head -50
    </copy>
    ```

    Third-party packages are not automatically proof of a blocker. They require application-owner review.

6. Confirm the workshop workload remains healthy:

    ```bash
    <copy>
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    getenforce
    </copy>
    ```

## Task 2: Download and verify the pinned migration script

1. Create a working directory:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-migration"
    cd "$HOME/ol-migration"
    </copy>
    ```

2. Download the script from the tested commit:

    ```bash
    <copy>
    MIGRATION_COMMIT=82947c960535cfbb632977c70b0fea67f8e0de7a
    curl --fail --location --output migrate-to-oracle-linux.sh \
      "https://raw.githubusercontent.com/oracle/migrate-to-ol/${MIGRATION_COMMIT}/migrate-to-oracle-linux.sh"
    </copy>
    ```

3. Verify its SHA-256 checksum:

    ```bash
    <copy>
    echo '5435b59e2f0e672111fd1545b2b428cabe473e7c116a353716c2f7de869d0a42  migrate-to-oracle-linux.sh' \
      | sha256sum --check
    </copy>
    ```

    Expected output:

    ```text
    migrate-to-oracle-linux.sh: OK
    ```

4. Make the verified script executable and inspect its version and help:

    ```bash
    <copy>
    chmod 0755 migrate-to-oracle-linux.sh
    ./migrate-to-oracle-linux.sh --version
    ./migrate-to-oracle-linux.sh --help | less
    </copy>
    ```

    After reviewing the help text, press the letter **q** to exit the **less** viewer and return to the shell prompt.

## Task 3: Run the migration dry run

1. Run the non-changing assessment for an Oracle Linux 9.8 RHCK target:

    ```bash
    <copy>
    sudo ./migrate-to-oracle-linux.sh \
      --dry-run \
      --target-version 9.8 \
      --kernel rhck
    </copy>
    ```

2. Review the final dry-run summary.

    Confirm that the script completed its assessment without converting the operating system. Look for messages marked as errors, failures, or blockers. A warning does not always prevent migration, but you must understand and resolve any warning that affects repositories, packages, disk space, or the running kernel before continuing.

    Record the run directory displayed near the end of the output. The directory contains reports and snapshots from this assessment and helps you investigate any reported problem.

3. Locate the newest migration state directory and log:

    ```bash
    <copy>
    sudo ls -1dt /var/lib/migrate-to-oracle-linux/* | head -1
    sudo ls -1t /var/log/migrate-to-oracle-linux/*.log | head -1
    </copy>
    ```

4. Decide whether it is safe to continue.

    Do not continue if the dry run reports an unresolved error or blocker. Correct the problem and repeat the dry run before proceeding. Common blockers include:

    The script reports problems in the terminal while the dry run runs and records the same information in the log identified in Step 3. The following command opens the newest migration log:

    ```bash
    <copy>
    LATEST_LOG=$(sudo ls -1t /var/log/migrate-to-oracle-linux/*.log | head -1)
    sudo less "$LATEST_LOG"
    </copy>
    ```

    In the `less` viewer, enter `/ERROR` and press Enter to search for errors. You can also search for `/WARN` and `/FAIL`. Press the letter **q** to close the log and return to the shell prompt. The examples below are possible problems, not a separate list or screen that you must locate.

    - A RHEL version or processor architecture that the script does not support.
    - A target with a different major version, such as RHEL 8 to Oracle Linux 9.
    - A damaged or inconsistent RPM package database.
    - Insufficient free disk space.
    - A newer kernel that is installed but not currently running.
    - RHEL or Oracle Linux software repositories that the VM cannot reach.

## Task 4: Record Red Hat registration identity

1. Record the system identity without recording credentials:

    ```bash
    <copy>
    sudo subscription-manager identity | tee \
      "$HOME/ol-migration-evidence/before/red-hat-identity.txt"
    </copy>
    ```

2. Note the system name or consumer ID. You will use it to remove the temporary registration after migration.

## Task 5: Create the pre-migration recovery point

1. Write pending filesystem changes to disk, then temporarily stop the Apache web server before creating the boot-volume backup:

    ```bash
    <copy>
    sync
    sudo systemctl stop httpd
    </copy>
    ```

2. In the OCI Console, open the `ol-migrate-rhel-source` instance.

3. On the instance details page, select the **Storage** tab. Under **Boot volumes**, select the boot-volume name attached to the instance.

4. On the boot-volume details page, select **Create backup**.

5. Enter the following values:

    - Name: `ol-migrate-before-conversion`
    - Backup type: **Full backup**

6. Select **Create boot volume backup**.

7. Open the boot volume's **Backups** tab and wait until the backup state becomes Available.

8. Return to the SSH session and restart Apache, then verify that the workshop webpage responds:

    ```bash
    <copy>
    sudo systemctl start httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

9. Record the backup OCID and creation time in your workshop notes.

## Task 6: Pass the migration gate

1. Confirm every required condition:

    - Dry run completed without an unresolved blocker.
    - The verified script checksum returned OK.
    - The RHEL registration identity was recorded.
    - The boot-volume backup state is Available.
    - Apache is active after the backup.
    - The workload marker is returned.

2. If any condition fails, stop here and correct it before continuing.

3. Review the rollback decision:

    If the migration fails and the VM cannot return to a stable boot and application state within the bootcamp recovery window, preserve the logs, stop troubleshooting, and restore from the pre-migration boot-volume backup using the instructor-tested procedure.

## Learn More

- [Oracle migration repository](https://github.com/oracle/migrate-to-ol)
- [Migration script documentation](https://github.com/oracle/migrate-to-ol/blob/main/README-migrate-to-oracle-linux.md)
- [Creating a boot-volume backup](https://docs.oracle.com/en-us/iaas/Content/Block/Tasks/backingupavolume.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, July 2026

