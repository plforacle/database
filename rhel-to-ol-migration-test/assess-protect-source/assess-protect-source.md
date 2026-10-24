# Lab 3: Assess Readiness and Protect the Source

## Introduction

Begin this lab after the prepared custom image has been moved to the destination OCI tenancy and a new RHEL instance has been launched from it. Connect to that instance using its public IP, SSH key, and `cloud-user` account. Red Hat registration was removed in Lab 2 and is not required here.

First capture the destination instance baseline. Then inspect repositories, packages, disk space, services, and the running kernel. You then execute the pinned migration script in dry-run mode and create a boot-volume backup before changing the operating system.

The migration script is maintained in an Oracle-owned GitHub repository, but the repository states that the scripts are community-based and are not officially supported by Oracle.

### Objectives

In this lab, you will:

- Capture a repeatable baseline on the destination instance before migration.
- Assess the source system for common migration risks.
- Download and verify a pinned migration script.
- Run a non-changing migration dry run.
- Create and verify a boot-volume backup.
- Document the rollback decision point.

Estimated Lab Time: 40 minutes

## Task 1: Create the baseline evidence bundle

1. Create an evidence directory:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-migration-evidence/before"
    EVIDENCE="$HOME/ol-migration-evidence/before"
    </copy>
    ```

2. Capture the operating system, kernel, repositories, and packages:

    ```bash
    <copy>
    cat /etc/os-release > "$EVIDENCE/os-release.txt"
    uname -a > "$EVIDENCE/kernel.txt"
    sudo dnf repolist --enabled > "$EVIDENCE/repositories.txt"
    rpm -qa --qf '%{NAME}\t%{EPOCHNUM}:%{VERSION}-%{RELEASE}.%{ARCH}\t%{VENDOR}\n' \
      | sort > "$EVIDENCE/packages.tsv"
    </copy>
    ```

3. Capture services, networking, firewall, and SELinux:

    ```bash
    <copy>
    systemctl is-enabled httpd > "$EVIDENCE/httpd-enabled.txt"
    systemctl is-active httpd > "$EVIDENCE/httpd-active.txt"
    sudo ss -lntup > "$EVIDENCE/listening-ports.txt"
    ip address show > "$EVIDENCE/addresses.txt"
    ip route show > "$EVIDENCE/routes.txt"
    getenforce > "$EVIDENCE/selinux.txt"
    sudo firewall-cmd --list-all > "$EVIDENCE/firewall.txt"
    </copy>
    ```

4. Capture the workload response and checksum:

    ```bash
    <copy>
    curl --fail --silent http://127.0.0.1/ > "$EVIDENCE/application.html"
    sha256sum /var/www/html/index.html > "$EVIDENCE/application-sha256.txt"
    </copy>
    ```

5. Create an archive:

    ```bash
    <copy>
    tar -C "$HOME/ol-migration-evidence" -czf \
      "$HOME/ol-migration-evidence/rhel-before.tar.gz" before
    ls -lh "$HOME/ol-migration-evidence/rhel-before.tar.gz"
    </copy>
    ```

6. Confirm the source checkpoint:

    ```bash
    <copy>
    grep '^ID=' /etc/os-release
    systemctl is-active httpd
    getenforce
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Expected results are `ID="rhel"`, `active`, `Enforcing`, and the workload marker.

## Task 2: Assess the source operating system

1. Confirm the source version and architecture:

    ```bash
    <copy>
    . /etc/os-release
    printf 'ID=%s VERSION_ID=%s ARCH=%s\n' "$ID" "$VERSION_ID" "$(uname -m)"
    </copy>
    ```

    Continue only when the source is RHEL 9.8 on x86_64.

2. Verify that the newest installed kernel is running:

    ```bash
    <copy>
    uname -r
    rpm -q kernel-core | sort -V
    sudo grubby --default-kernel
    </copy>
    ```

    If the default or newest kernel differs from the running kernel, reboot and repeat this check.

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

    No enabled source repositories are expected after the Lab 2 registration cleanup. Oracle repositories are configured by the migration script.

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

## Task 3: Download and verify the pinned migration script

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

## Task 4: Run the migration dry run

1. Run the non-changing assessment for an Oracle Linux 9.8 RHCK target:

    ```bash
    <copy>
    sudo ./migrate-to-oracle-linux.sh \
      --dry-run \
      --target-version 9.8 \
      --kernel rhck
    </copy>
    ```

2. Review the final status and the run directory reported by the script.

3. Locate the newest migration state directory and log:

    ```bash
    <copy>
    sudo ls -1dt /var/lib/migrate-to-oracle-linux/* | head -1
    sudo ls -1t /var/log/migrate-to-oracle-linux/*.log | head -1
    </copy>
    ```

4. Do not continue if the dry run reports an unresolved error involving:

    - Unsupported source release or architecture
    - Cross-major target selection
    - Broken RPM database
    - Insufficient disk space
    - A source kernel newer than the running kernel
    - Unreachable Oracle repositories

## Task 5: Create the pre-migration recovery point

1. Flush filesystem changes and stop the workshop application:

    ```bash
    <copy>
    sync
    sudo systemctl stop httpd
    </copy>
    ```

2. In the OCI Console, open the `ol-migrate-rhel-source` instance.

3. Under **Resources**, open the boot volume.

4. Select **Create manual backup**.

5. Enter `ol-migrate-before-conversion` as the backup name and create the backup.

6. Monitor the backup until its state becomes Available.

7. Return to the SSH session and restart the application:

    ```bash
    <copy>
    sudo systemctl start httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

8. Record the backup OCID and creation time in your workshop notes.

## Task 6: Pass the migration gate

1. Confirm every required condition:

    - Dry run completed without an unresolved blocker.
    - The verified script checksum returned OK.
    - The baseline evidence bundle was captured on the destination instance.
    - Red Hat registration was removed and subscription plugins were disabled in Lab 2.
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
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026

