# Lab 3: Assess Readiness and Protect the Source

## Introduction

Before changing the operating system, inspect the CentOS VM, run the migration script in dry-run mode, and create an OCI boot-volume backup. The script version and checksum below match the pinned release used in the RHEL workshop, but this CentOS path still requires an end-to-end validation run before an instructor delivers it.

Estimated Lab Time: 35 minutes

### Objectives

- Check the source kernel, RPM database, repositories, and workload.
- Download and verify a pinned migration script.
- Run a dry run that targets Oracle Linux 9 latest.
- Create an Available boot-volume backup before migration.

### Prerequisites

- Completed Lab 2 and working SSH access to your assigned CentOS VM.
- A healthy Apache workload and either permission to create a boot-volume backup or instructor assistance with that step.
- Outbound HTTPS to `raw.githubusercontent.com` and `yum.oracle.com`.

## Task 1: Assess the CentOS source

1. Confirm the distribution, architecture, and running kernel:

    ```bash
    cat /etc/os-release
    uname -m
    uname -r
    rpm -q kernel-core | sort -V
    sudo grubby --default-kernel
    ```

    Continue only on CentOS Stream 9 x86_64. The running kernel should be the newest installed kernel. If an update installed a newer kernel, reboot, reconnect, and check again.

2. Verify package and repository health:

    ```bash
    df -h /
    sudo rpm --verifydb
    sudo dnf check
    sudo dnf repolist --enabled
    ```

    Investigate a failed check, insufficient disk space, or inaccessible source repositories before continuing.

3. Check for third-party packages and verify the application:

    ```bash
    rpm -qa --qf '%{NAME}\t%{VENDOR}\n' | awk -F '\t' '$2 !~ /CentOS/ {print}' | head -50
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    ```

    Review any unexpected packages with the application owner. Their presence alone does not prove a migration blocker.

## Task 2: Download and verify the pinned script

1. Create a working directory and download the tested script revision:

    ```bash
    mkdir -p "$HOME/ol-migration"
    cd "$HOME/ol-migration"
    MIGRATION_COMMIT=82947c960535cfbb632977c70b0fea67f8e0de7a
    curl --fail --location --output migrate-to-oracle-linux.sh \
      "https://raw.githubusercontent.com/oracle/migrate-to-ol/${MIGRATION_COMMIT}/migrate-to-oracle-linux.sh"
    ```

2. Verify the exact script checksum and inspect its options:

    ```bash
    echo '5435b59e2f0e672111fd1545b2b428cabe473e7c116a353716c2f7de869d0a42  migrate-to-oracle-linux.sh' | sha256sum --check
    chmod 0755 migrate-to-oracle-linux.sh
    ./migrate-to-oracle-linux.sh --version
    ./migrate-to-oracle-linux.sh --help | less
    ```

    The checksum command must print `migrate-to-oracle-linux.sh: OK`. Press `q` to exit `less`. If verification fails, stop and investigate the download.

## Task 3: Run the CentOS Stream dry run

1. Request the latest Oracle Linux 9 target and RHCK:

    ```bash
    sudo ./migrate-to-oracle-linux.sh \
      --dry-run \
      --target-version latest \
      --kernel rhck
    ```

    CentOS Stream does not support a fixed Oracle Linux minor target. Keep `latest`.

2. Record the run paths and review the results:

    ```bash
    sudo ls -1dt /var/lib/migrate-to-oracle-linux/* | head -1
    sudo ls -1t /var/log/migrate-to-oracle-linux/*.log | head -1
    ```

    The dry run should finish without modifying the operating system. Resolve any error affecting packages, repositories, disk space, architecture, or kernel state before proceeding.

## Task 4: Create the recovery point

1. Flush filesystem changes and stop Apache briefly:

    ```bash
    sync
    sudo systemctl stop httpd
    ```

2. In OCI, open your assigned source instance, choose **Storage**, select its boot volume, and choose **Create backup**. Name the backup `ol-centos-before-conversion` and select **Full backup**. If your event role does not allow backups, ask the instructor to create the full backup for your assigned VM before you continue.

3. Wait until the backup state is **Available**. Record its OCID and creation time.

4. Restart Apache and retest the page:

    ```bash
    sudo systemctl start httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    ```

5. Continue only if the script checksum passed, the dry run has no unresolved blocker, the boot-volume backup is Available, and the workload is healthy. If migration fails, preserve the logs and use the backup as the recovery point according to the instructor's tested restore procedure.

## Learn More

- [Migration script documentation](https://github.com/oracle/migrate-to-ol/blob/main/README-migrate-to-oracle-linux.md)
- [OCI boot-volume backups](https://docs.oracle.com/en-us/iaas/Content/Block/Tasks/backingupavolume.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, September 2026
