# Lab 3: Migrate to Oracle Linux

## Introduction

Convert the assigned server to Oracle Linux, inspect the conversion, and reboot into the Oracle-provided RHCK. The application files and the server's SSH access should remain usable; verify both after migration.

Estimated Time: 40 minutes.

### Objectives

- Run the migration using the verified script and selected target release.
- Inspect the converted system before reboot.
- Reconnect and validate Oracle Linux, RHCK, and the application.

### Prerequisites

- All Lab 2 checks passed, including instructor confirmation of recovery readiness.
- The verified script in `$HOME/ol-migration`.
- Authorization to modify and reboot this assigned training server.

## Task 1: Reconfirm the migration checkpoint

1. In your persistent SSH terminal, load the settings and verify the script again:

    ```bash
    <copy>
    . /etc/rhel-to-oracle-linux/workshop.env
    cd "$HOME/ol-migration"
    printf '%s  migrate-to-oracle-linux.sh\n' "$MIGRATION_SHA256" | sha256sum --check
    </copy>
    ```

2. Verify the source identity and application:

    ```bash
    <copy>
    grep -E '^(ID|VERSION_ID)=' /etc/os-release
    systemctl is-active httpd
    curl --fail --silent --show-error http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

3. Confirm the instructor's recovery checkpoint still applies to this server. Continue only when every check passes.

## Task 2: Execute the conversion

1. Start the migration:

    ```bash
    <copy>
    sudo ./migrate-to-oracle-linux.sh -y --target-version "$TARGET_VERSION" --kernel rhck
    printf 'Migration exit status: %s\n' "$?"
    </copy>
    ```

    Keep the terminal session open. If the network disconnects, reconnect and reattach with `tmux attach -t migration` before deciding whether the command stopped. Do not start a second migration command.

2. Review the output for source validation, repository changes, package replacement, kernel installation, and the final summary. Require exit status `0` and successful target verification. If conversion fails, preserve the displayed log path and contact the instructor. Do not reboot or rerun the script without reviewing the failure.

3. Record the migration run directory and log path. The successful completion message may recommend rebooting; first complete Task 3.

## Task 3: Inspect the converted server before reboot

1. Check the installed operating-system identity and repositories:

    ```bash
    <copy>
    cat /etc/os-release
    rpm -q oraclelinux-release oraclelinux-release-el9
    sudo dnf repolist --enabled
    </copy>
    ```

    Confirm Oracle Linux and the instructor-supplied target version. The running kernel can still be the source kernel until reboot.

2. Inspect the installed kernel packages and next boot selection:

    ```bash
    <copy>
    rpm -qa 'kernel*' | sort
    sudo grubby --default-kernel
    </copy>
    ```

    Confirm with the instructor if the selected kernel is unexpected. This workshop uses standard `kernel` and `kernel-core` RHCK packages, rather than `kernel-uek` packages.

3. Review the conversion report if present:

    ```bash
    <copy>
    RUN_DIR="$(sudo ls -1dt /var/lib/migrate-to-oracle-linux/* | head -1)"
    printf '%s\n' "$RUN_DIR"
    sudo find "$RUN_DIR" -maxdepth 1 -type f -printf '%f\n' | sort
    sudo test -f "$RUN_DIR/migration-rpm-map.tsv" && sudo sed -n '1,40p' "$RUN_DIR/migration-rpm-map.tsv"
    </copy>
    ```

    Ask the instructor about unavailable packages or retained source-vendor packages. Report names must match the staged script revision.

## Task 4: Reboot and reconnect

1. Reboot the server:

    ```bash
    <copy>
    sudo reboot
    </copy>
    ```

    SSH disconnects. This planned reboot starts the installed Oracle Linux kernel.

2. From your local terminal, retry the original SSH command after approximately one minute:

    ```bash
    <copy>
    ssh -i "<private-key-path>" <ssh-user>@<assigned-host>
    </copy>
    ```

    Retry at reasonable intervals within the instructor's recovery window. If access does not return, contact the instructor. Do not disable host-key checking if SSH reports an unexpected key change.

3. Confirm the running OS and kernel:

    ```bash
    <copy>
    cat /etc/os-release
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    </copy>
    ```

    Expected results: Oracle Linux at the selected release, an Oracle-provided kernel package, and a kernel release without `uek` for this RHCK exercise. Do not run Ksplice while still using the original Red Hat-provided kernel.

## Task 5: Validate and capture the migrated state

1. Run the validation and capture tools:

    ```bash
    <copy>
    bash /opt/rhel-to-oracle-linux/validate-system.sh
    bash /opt/rhel-to-oracle-linux/capture-state.sh after-migration
    </copy>
    ```

    Both commands must succeed. Investigate failed services, repository errors, application failures, or changed security settings before continuing.

2. Compare the application checksum:

    ```bash
    <copy>
    diff -u "$HOME/ol-migration-evidence/before/application-sha256.txt" \
      "$HOME/ol-migration-evidence/after-migration/application-sha256.txt"
    </copy>
    ```

    No output and exit status `0` mean the application file matches. Keep this post-migration snapshot separate from later management changes. Do not run an unrestricted package upgrade before the Ksplice exercise; use the rehearsed kernel and patch sequence.

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle.
- **Last Updated By/Date** - Workshop adaptation draft, October 19, 2026.
