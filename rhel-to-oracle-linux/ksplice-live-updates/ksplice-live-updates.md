# Lab 4: Apply Ksplice Live Updates

## Introduction

Apply available live patches to the Oracle Linux RHCK running after migration. Compare the boot identity, patch list, and application checks before and after the operation. The migration reboot is complete; this exercise should not require another reboot.

Estimated Time: 25 minutes.

### Objectives

- Verify the Oracle-provided running kernel and Ksplice client.
- Preview and apply available live patches.
- Record the unchanged boot identity and application checks.

### Prerequisites

- Completed Lab 3 on the same assigned server.
- Instructor-confirmed Ksplice access and a rehearsed supported kernel.
- Outbound access to the Ksplice services.

## Task 1: Establish the pre-patch evidence

1. Confirm the kernel identity and application:

    ```bash
    <copy>
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VENDOR}\n'
    systemctl is-active httpd
    curl --fail --silent --show-error http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Continue only with an Oracle-provided RHCK. Oracle no longer maintains Ksplice patches for Red Hat-provided kernels. See [maintained kernels](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-MaintainedKernels.html).

2. Record the current boot identity:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-migration-evidence/ksplice"
    cat /proc/sys/kernel/random/boot_id > "$HOME/ol-migration-evidence/ksplice/boot-id-before.txt"
    uptime -s
    </copy>
    ```

## Task 2: Verify or install the prepared client

1. Check whether Uptrack is installed:

    ```bash
    <copy>
    command -v uptrack-upgrade
    </copy>
    ```

    If the command is present, continue to Task 3. A missing command can return exit status `1`.

2. If it is absent, use the installer staged and reviewed by your instructor:

    ```bash
    <copy>
    cd /opt/rhel-to-oracle-linux
    sha256sum --check install-uptrack.sha256
    </copy>
    ```

    Require `install-uptrack: OK`. Stop on a mismatch.

3. Run the verified installer:

    ```bash
    <copy>
    sudo sh /opt/rhel-to-oracle-linux/install-uptrack
    command -v uptrack-upgrade
    </copy>
    ```

    The instructor selected the installer and entitlement method for this training environment. Do not add personal access keys or enable automatic patching during the exercise. Client installation must succeed before proceeding.

## Task 3: Preview the available patches

1. Inspect installed patches and preview available patches:

    ```bash
    <copy>
    sudo uptrack-show
    sudo uptrack-upgrade -n
    printf 'Preview exit status: %s\n' "$?"
    </copy>
    ```

    Record the available patch descriptions in your notes. Authentication, network, and unsupported-kernel errors need investigation; they are not proof that the kernel is current.

2. If there are no applicable patches, contact the instructor. Record the exercise as preview-only unless patches are actually applied. An instructor may provide a rehearsed fallback server; identify that server separately and do not present its results as evidence from your migrated server.

## Task 4: Apply and verify the patches

1. When patches are available, apply them:

    ```bash
    <copy>
    sudo uptrack-upgrade -y
    printf 'Patch exit status: %s\n' "$?"
    </copy>
    ```

    Require exit status `0` and review the patch results. Do not reboot.

2. Capture the patch list and effective kernel:

    ```bash
    <copy>
    sudo uptrack-show > "$HOME/ol-migration-evidence/ksplice/installed-patches.txt"
    uname -r
    sudo uptrack-uname -r
    cat "$HOME/ol-migration-evidence/ksplice/installed-patches.txt"
    </copy>
    ```

    The base release from `uname -r` can remain unchanged. Some enablement patches also leave the effective release unchanged. Use the installed-patch list and command results to establish what was applied.

3. Compare boot identity and verify the application:

    ```bash
    <copy>
    cat /proc/sys/kernel/random/boot_id > "$HOME/ol-migration-evidence/ksplice/boot-id-after.txt"
    diff -u "$HOME/ol-migration-evidence/ksplice/boot-id-before.txt" \
      "$HOME/ol-migration-evidence/ksplice/boot-id-after.txt"
    systemctl is-active httpd
    curl --fail --silent --show-error http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    An unchanged boot ID confirms no reboot between the captures. Successful application checks establish its health at those times; they do not measure uninterrupted availability throughout the operation.

4. Record applied patches, errors, or preview-only results. The next lab uses OS Management Hub for a package job. It does not configure OS Management Hub-controlled Ksplice.

## Learn More

- [Uptrack commands](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-UsingtheuptrackupgradeCommandtoManageKspliceUpdates.html).

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle.
- **Last Updated By/Date** - Workshop adaptation draft, October 19, 2026.
