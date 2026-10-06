# Lab 4: Migrate to Oracle Linux

## Introduction

The verified migration script will replace CentOS distribution packages and repositories with Oracle Linux 9 packages on the same OCI VM. It will install Oracle Linux RHCK. Review its reports before rebooting, then confirm the operating system and workload after the restart.

Estimated Lab Time: 45 minutes

### Objectives

- Run an in-place CentOS Stream 9 to Oracle Linux 9 migration.
- Review migration reports before rebooting.
- Restart into an Oracle Linux kernel and verify Apache.

### Prerequisites

- Completed Lab 3 with a successful dry run and an Available boot-volume backup.
- The verified script remains in `$HOME/ol-migration`.
- The Apache page returns `MIGRATION_WORKLOAD_OK`.

## Task 1: Reconfirm the migration gate

1. Reconnect to the source VM, enter the migration directory, and verify the source identity:

    ```bash
    cd "$HOME/ol-migration"
    grep -E '^(NAME|VERSION_ID|ID)=' /etc/os-release
    sha256sum migrate-to-oracle-linux.sh
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    ```

    Confirm `ID="centos"`, version 9, the SHA-256 value from Lab 3, and a healthy page. Confirm the boot-volume backup still shows Available in OCI.

## Task 2: Run the migration

1. Start the migration on the source VM:

    ```bash
    sudo ./migrate-to-oracle-linux.sh \
      -y \
      --target-version latest \
      --kernel rhck
    ```

    Package synchronization may take time. Keep the SSH session open and watch for an error or package conflict. If it fails, preserve the log path shown by the script and stop before attempting another migration.

2. When the script reports completion, note its state directory and log path. **Do not reboot yet.** Complete Task 3 first. The script's standard reboot message points to the action in Task 4.

## Task 3: Inspect the pre-reboot reports

1. Locate the most recent state directory and its summary files:

    ```bash
    RUN_DIR=$(sudo ls -1dt /var/lib/migrate-to-oracle-linux/* | head -1)
    printf 'Run directory: %s\n' "$RUN_DIR"
    sudo ls -lh "$RUN_DIR"
    sudo ls -1t /var/log/migrate-to-oracle-linux/*.log | head -1
    ```

2. Review the migration map and packages lacking a replacement:

    ```bash
    sudo sed -n '1,30p' "$RUN_DIR/migration-rpm-map.tsv"
    sudo sed -n '1,40p' "$RUN_DIR/unavailable-reinstall.nevra"
    ```

    The TSV file includes a status column. Look for `3rd Party`, `unavailable`, `source-vendor-retained`, `downgraded`, and `removed-source-kernel`. Investigate entries that affect the workshop workload. An empty unavailable file is a valid result.

3. Record the paths to `migration-rpm-map.html` and the log in your notes. The HTML report is useful for later review; the TSV and log can be inspected directly over SSH.

## Task 4: Reboot into Oracle Linux

1. Restart the VM:

    ```bash
    sudo reboot
    ```

2. Wait for the OCI instance to return to Running and reconnect with the same SSH key and public IP:

    ```bash
    ssh -i <private-key-path> cloud-user@<public-ip>
    ```

3. Confirm Oracle Linux, the running kernel package, repositories, and application:

    ```bash
    cat /etc/os-release
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    sudo dnf repolist --enabled
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    ```

    Expect `ID="ol"`, Oracle Linux major version 9, an Oracle-provided kernel package, Oracle repositories, and the workload marker. The exact minor version can change as Oracle Linux 9 latest advances.

4. If the VM does not boot or the workload fails, preserve the migration log and report state. Use OCI console history or serial console for boot errors, and follow the instructor's tested boot-volume restore procedure when recovery is required.

## Learn More

- [Migration script documentation and reports](https://github.com/oracle/migrate-to-ol/blob/main/README-migrate-to-oracle-linux.md)
- [OCI serial console troubleshooting](https://docs.oracle.com/en-us/iaas/Content/Compute/References/serialconsole.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, September 2026
