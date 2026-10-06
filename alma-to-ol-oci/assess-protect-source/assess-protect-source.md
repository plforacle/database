# Lab 3: Assess Readiness and Test Recovery

## Introduction

Migration begins with assessment and a recoverable checkpoint. In this lab, you inspect repositories, packages, disk space, services, and the running kernel. You then run the migration script version pinned for this workshop in dry-run mode and create a boot-volume backup before changing the operating system. Using a specific version and verifying its SHA-256 checksum lets participants verify that they downloaded the same script. A dry run prints planned operations and writes reports and temporary repository definitions without converting the distribution. It does not execute the full package transaction or test the next boot.

The migration script is maintained in an Oracle-owned GitHub repository, but the repository states that the scripts are community-based and are not officially supported by Oracle.

Before running migration commands that modify the operating system, you must pass the migration checkpoint in this lab. This confirms that readiness checks succeeded, the workload is healthy, and an available boot-volume backup provides a recovery point.

### Objectives

In this lab, you will:

- Assess the source system for common migration risks.
- Download and verify a pinned migration script.
- Run a migration dry run without package conversion.
- Create and verify a boot-volume backup.
- Document the rollback decision point.

### Prerequisites

Before beginning this lab, confirm that you have:

- Completed Lab 2.
- SSH access to the running AlmaLinux 9.8 x86_64 source VM.
- A healthy Apache workload that returns `MIGRATION_WORKLOAD_OK`.
- The AlmaLinux baseline evidence bundle created in Lab 2.
- Permission to create and inspect boot-volume backups in OCI.
- Outbound HTTPS access from the source VM to `raw.githubusercontent.com` and `yum.oracle.com`.

> **Note:** This workshop uses the public Oracle Linux yum service. In a private or restricted network, use the migration script's `--yum-mirror` option with an approved internal HTTPS mirror, or its `--proxy` option with an HTTP proxy. Do not use both options together. With `--yum-mirror`, this pinned script disables TLS certificate verification for yum/dnf while retaining RPM signature checks; review that setting before using a private mirror. Confirm that the selected path works during the dry run before creating the recovery point.

Estimated Lab Time: 55 minutes, plus backup and restore operations

## Task 1: Assess the source operating system

1. Confirm the source version and architecture:

    ```bash
    <copy>
    . /etc/os-release
    printf 'ID=%s VERSION_ID=%s ARCH=%s\n' "$ID" "$VERSION_ID" "$(uname -m)"
    </copy>
    ```

    Continue only when the source is AlmaLinux 9.8 on x86_64.

2. Confirm that AlmaLinux is using the newest installed kernel.

    AlmaLinux can keep more than one kernel. A system update may install a newer kernel, but AlmaLinux does not use it until the VM restarts. The following commands show the kernel running now, all installed kernels from oldest to newest, and the kernel selected for the next restart:

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
    df -h / /boot
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

5. Identify packages that are whose vendor field does not identify AlmaLinux:

    ```bash
    <copy>
    rpm -qa --qf '%{NAME}\t%{VENDOR}\n' \
      | awk -F '\t' '$2 !~ /AlmaLinux/ {print}' \
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

2. Download the script from the pinned commit:

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

1. Run the assessment for an Oracle Linux 9.8 RHCK target:

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

    - A AlmaLinux version or processor architecture that the script does not support.
    - A target with a different major version, such as AlmaLinux 8 to Oracle Linux 9.
    - A damaged or inconsistent RPM package database.
    - Insufficient free disk space.
    - A newer kernel that is installed but not currently running.
    - AlmaLinux or Oracle Linux software repositories that the VM cannot reach.

## Task 4: Create and test the recovery point

1. On the lab VM, flush pending writes, then stop the VM cleanly through the OCI Console:

    ```bash
    <copy>
    sync
    </copy>
    ```

2. Wait for Stopped. Open the lab boot volume, select **Backups**, and create a full backup named `alma-to-ol-before-conversion`. Record its OCID. Wait for Available before restarting the lab VM.

3. From the backup's Actions menu, select **Restore Boot Volume**. Name the restored volume `alma-to-ol-recovery-test-boot`, select the lab availability domain, and wait for Available.

4. Create a separate instance from the restored volume named `alma-to-ol-recovery-test`. Use the same boot configuration, E5.Flex with 1 OCPU and 12 GB RAM, both optional security features disabled, and the isolated lab network. Record its new address and resource OCIDs.

5. Connect to the restored instance using the existing SSH account and key. Check that the backup boots as AlmaLinux 9.8 and retains the application:

    ```bash
    <copy>
    . /etc/os-release
    test "$ID" = almalinux && test "$VERSION_ID" = 9.8
    systemctl is-active httpd
    curl --fail --silent --show-error http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    sha256sum /var/www/html/index.html
    </copy>
    ```

    Compare the checksum with the saved baseline. Record boot, SSH, and application results. A restored instance has a new OCI identity and can have a different IP address.

6. Stop the recovery-test VM. Keep the tested backup available. You can remove the recovery-test VM and its restored boot volume after recording the result; keep `alma-to-ol-source` intact.

7. Restart `alma-to-ol-source`, reconnect to its address, and verify the application marker. Confirm that this is the lab migration VM, not the recovery-test VM.

## Task 5: Pass the migration gate

1. Confirm every required condition:

    - Dry run completed without an unresolved blocker.
    - The verified script checksum returned OK.
    - The source is still AlmaLinux 9.8 x86_64 with both optional security features disabled.
    - The boot-volume backup state is Available.
    - A restored copy boots, accepts SSH, and passes the application checks.
    - The baseline archive is saved outside the VM.
    - Apache is active after the backup.
    - The workload marker is returned.

2. If any condition fails, stop here and correct it before continuing.

3. Review the rollback decision:

    If conversion or reboot fails, preserve accessible logs and stop the failed lab VM. Restore the verified backup to a new boot volume and launch a replacement lab instance using the procedure rehearsed above. Validate AlmaLinux, SSH, and the application on the restored instance before using it. This recovery creates a replacement instance; it does not reverse the package transaction on the failed VM.

## Learn More

- [Oracle migration repository](https://github.com/oracle/migrate-to-ol)
- [Migration script documentation](https://github.com/oracle/migrate-to-ol/blob/main/README-migrate-to-oracle-linux.md)
- [Creating a boot-volume backup](https://docs.oracle.com/en-us/iaas/Content/Block/Tasks/create-bv-boot-volume-backup.htm)
- [Restoring a boot volume](https://docs.oracle.com/en-us/iaas/Content/Block/Tasks/create-restore-bv-boot-volume-backup.htm)

### Workshop maintenance note

When refreshing this workshop, test a candidate migration-script commit in a disposable AlmaLinux 9.8 VM before changing the lab. After successful validation, update the pinned commit, SHA-256 checksum, commands, and expected report names as one tested set. Do not substitute an unpinned `main` branch script for the pinned commit.

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Perside Foster with Codex assistance, October 2026
