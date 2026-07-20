# Lab 4: Migrate to Oracle Linux

## Introduction

You are ready to convert the RHEL source VM in place. The migration script replaces distribution release packages and repositories, synchronizes packages with Oracle Linux 9.8, installs Oracle Linux RHCK, and creates detailed migration reports.

The VM remains the same OCI instance. Its operating-system identity, repositories, packages, and kernel change.

### Objectives

In this lab, you will:

- Execute a same-major-version in-place migration.
- Review package and repository changes.
- Reboot into an Oracle Linux RHCK.
- Confirm that SSH and the workload survived the conversion.

Estimated Lab Time: 40 minutes

## Task 1: Reconfirm the migration gate

1. Connect to the RHEL source VM and enter the migration working directory:

    ```bash
    <copy>
    cd "$HOME/ol-migration"
    </copy>
    ```

2. Confirm the verified script remains unchanged:

    ```bash
    <copy>
    echo '5435b59e2f0e672111fd1545b2b428cabe473e7c116a353716c2f7de869d0a42  migrate-to-oracle-linux.sh' \
      | sha256sum --check
    </copy>
    ```

3. Confirm the source and workload state:

    ```bash
    <copy>
    grep -E '^(ID|VERSION_ID)=' /etc/os-release
    uname -r
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

4. In the OCI Console, confirm that the `ol-migrate-before-conversion` boot-volume backup is Available.

5. Continue only when all checks pass.

## Task 2: Execute the migration

1. Start the migration to Oracle Linux 9.8 and select RHCK:

    ```bash
    <copy>
    sudo ./migrate-to-oracle-linux.sh \
      -y \
      --target-version 9.8 \
      --kernel rhck
    </copy>
    ```

    The operation changes repositories, distribution packages, and the installed kernel. Do not close the SSH session while the command is running.

2. Monitor the major phases:

    - Source validation and system snapshot
    - Oracle repository bootstrap
    - RHEL release-package removal
    - Oracle Linux release-package installation
    - Package replacement and distribution synchronization
    - Oracle Linux RHCK installation
    - Final identity and report checks

3. If the command fails, do not rerun it blindly. Preserve the displayed log path and review the last completed phase.

4. At successful completion, record the run directory and log file.

## Task 3: Inspect the converted system before reboot

1. Verify that the installed user space identifies as Oracle Linux:

    ```bash
    <copy>
    cat /etc/os-release
    rpm -q oraclelinux-release oraclelinux-release-el9
    </copy>
    ```

2. Verify Oracle repositories:

    ```bash
    <copy>
    sudo dnf repolist --enabled
    </copy>
    ```

3. Inspect installed Oracle Linux kernel packages and the default kernel:

    ```bash
    <copy>
    rpm -qa 'kernel*' | sort
    sudo grubby --default-kernel
    </copy>
    ```

4. Locate the newest migration run directory:

    ```bash
    <copy>
    RUN_DIR="$(sudo ls -1dt /var/lib/migrate-to-oracle-linux/* | head -1)"
    echo "$RUN_DIR"
    sudo find "$RUN_DIR" -maxdepth 1 -type f -printf '%f\n' | sort
    </copy>
    ```

5. Review the package migration summary if present:

    ```bash
    <copy>
    sudo sed -n '1,40p' "$RUN_DIR/migration-rpm-map.tsv"
    </copy>
    ```

    Pay attention to entries marked `3rd Party`, `unavailable`, `removed-rhel-only`, or `source-vendor-retained`.

## Task 4: Reboot into Oracle Linux RHCK

1. Reboot the VM:

    ```bash
    <copy>
    sudo reboot
    </copy>
    ```

2. Wait for the OCI instance to return to Running.

3. Reconnect using the same public IP, SSH private key, and `cloud-user` account:

    ```bash
    <copy>
    ssh -i <private-key-path> cloud-user@<public-ip>
    </copy>
    ```

4. Confirm the operating system and running kernel:

    ```bash
    <copy>
    cat /etc/os-release
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    </copy>
    ```

    Expected results:

    - `ID="ol"`
    - `VERSION_ID="9.8"`
    - The running kernel package vendor identifies Oracle.
    - The kernel name does not contain `uek` on the core RHCK path.

## Task 5: Perform the immediate health check

1. Check failed services:

    ```bash
    <copy>
    systemctl --failed --no-pager
    </copy>
    ```

2. Verify the application service:

    ```bash
    <copy>
    systemctl is-enabled httpd
    systemctl is-active httpd
    </copy>
    ```

3. Verify the local workload response:

    ```bash
    <copy>
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

4. Verify SELinux and the firewall:

    ```bash
    <copy>
    getenforce
    sudo firewall-cmd --query-service=http
    </copy>
    ```

5. Open `http://<public-ip>/` in your browser and confirm the workshop page still appears.

6. If any immediate check fails, preserve the migration logs before attempting remediation.

## Task 6: Review what changed and what stayed the same

1. Identify elements that changed:

    - Distribution identity and release packages
    - Repository definitions and signing keys
    - Support and update source
    - Installed and default kernel package
    - Some vendor-specific packages

2. Identify elements that stayed operational:

    - OCI instance identity and networking
    - SSH account and public key
    - Apache configuration and content
    - Systemd service management
    - Firewall and SELinux operating concepts
    - RPM and DNF administration model

3. Confirm the lab checkpoint:

    The same OCI VM now runs Oracle Linux 9.8 with an Oracle Linux RHCK, and the workload is reachable.

## Learn More

- [Migration script documentation](https://github.com/oracle/migrate-to-ol/blob/main/README-migrate-to-oracle-linux.md)
- [Managing kernels and system boot on Oracle Linux](https://docs.oracle.com/en-us/iaas/oracle-linux/boot/boot-about-system-boot-kernels.htm)
- [Oracle Linux 9 package repositories](https://yum.oracle.com/oracle-linux-9.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, July 2026

