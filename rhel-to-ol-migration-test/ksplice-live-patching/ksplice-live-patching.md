# Lab 5: Explore Rebootless Patching with Ksplice

## Introduction

Continue on the Oracle RHCK used for migration in Lab 4. Uptrack is the Ksplice client. It downloads and applies supported kernel fixes without rebooting the system. You will inspect available patches, apply them when available, and check the boot identity and application response afterward. Lab 6 introduces UEK after this patching test is complete.

**BYOI (Bring Your Own Image)** means importing your own operating-system image into OCI to create a VM. In this workshop, you imported the instructor-prepared RHEL image, launched a VM from it, and migrated that VM to Oracle Linux.

OCI gives your VM access to the Ksplice update service without registration with the Unbreakable Linux Network (ULN).

Standard Oracle Linux images supplied by OCI normally have the Ksplice client installed. Our VM started from a custom RHEL image, and Uptrack was missing after migration in our test. Task 2 checks for Uptrack and installs it only if it is missing.

For on-premises systems, Ksplice requires an eligible Oracle Linux support subscription, such as Premier Support, and the installation procedure for that environment.

### Objectives

In this lab, you will:

- Check kernel maintenance and Ksplice access requirements.
- Install Uptrack on the converted OCI instance.
- Inspect and apply available live kernel updates when supported.
- Verify the applied patches, unchanged boot identity, and application response.
- Distinguish live patching from on-disk package maintenance.

Estimated Lab Time: 30 minutes

### Prerequisites

Complete the migration, report review, reboot, and webpage checks in Lab 4. Keep the script-generated migration reports unchanged. Use the same healthy Oracle Linux 9.8 destination VM, still running its maintained Oracle RHCK, with internet access and sudo privileges. The instructor must verify that applicable Ksplice patches are available for this RHCK before class, using the maintained-kernels documentation linked below.

To complete the full live-patching test, at least one available patch must actually be applied. A successful client installation or no-updates check is a useful result, but leaves the patch-application test incomplete. Resolve access, kernel-support, or availability issues with the instructor before claiming the full test passed.

## Task 1: Check the starting kernel and application

1. Create a separate report directory for this attempt and record the starting boot state:

    ```bash
    <copy>
    REPORT="$HOME/ol-practice-reports/ksplice/$(date -u +%Y%m%dT%H%M%SZ)"
    mkdir -p "$REPORT"
    printf 'Report directory: %s\n' "$REPORT"
    cat /proc/sys/kernel/random/boot_id > "$REPORT/boot-id-before.txt"
    uname -r | tee "$REPORT/boot-kernel-before.txt"
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    </copy>
    ```

    Confirm the running kernel belongs to Oracle, has no `uek` in its version, and is the RHCK verified in Lab 4. Keep this SSH session open so `REPORT` remains available. If you reconnect, set `REPORT` to the directory printed above.

2. Check the application before patching:

    ```bash
    <copy>
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Expect `active` and the workload marker. Resolve an application failure before continuing.

## Task 2: Install the client on the OCI BYOI instance

1. Check whether the Uptrack client is already available:

    ```bash
    <copy>
    command -v uptrack-show
    rpm -q uptrack
    </copy>
    ```

    A missing command/package is expected on this converted custom image. If installed, retain it and continue to Task 3. First-time installation is covered below.

2. On the OCI instance only, download the official installer into a working directory:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-ksplice"
    cd "$HOME/ol-ksplice"
    curl --fail --location --output install-uptrack-oc \
      https://www.ksplice.com/uptrack/install-uptrack-oc
    </copy>
    ```

    Continue only after a successful download from the official HTTPS endpoint. If the download or installation fails, retain the error; do not disable TLS or package-signature checks or download a replacement from an unverified source.

3. Run the installer without enabling unattended updates:

    ```bash
    <copy>
    sudo sh install-uptrack-oc
    command -v uptrack-show
    rpm -q uptrack
    </copy>
    ```

    Do not pass `--autoinstall` in this exercise. Inspect the installer's output because setup may apply updates while configuring the client. Record any such patches. If installation fails, save the error and review access and kernel support with the instructor before proceeding.

## Task 3: Inspect and apply available live kernel updates

1. Inspect the applied state, effective kernel, and available updates:

    ```bash
    <copy>
    sudo uptrack-show | tee "$REPORT/installed-updates-before.txt"
    sudo uptrack-uname -r | tee "$REPORT/effective-kernel-after-install.txt"
    sudo uptrack-upgrade -n > "$REPORT/available-updates.txt" 2>&1
    UPTRACK_STATUS=$?
    cat "$REPORT/available-updates.txt"
    printf "Preview exit status: %s\n" "$UPTRACK_STATUS"
    </copy>
    ```

    Interpret the message, not just the exit status. `uname -r` reports the booted kernel; `uptrack-uname -r` reports the effective version recognized by Uptrack. They need not differ when no live patches have changed the effective version.

2. If supported updates are listed, apply them and record the resulting state:

    ```bash
    <copy>
    sudo uptrack-upgrade -y
    PATCH_STATUS=$?
    printf 'Apply exit status: %s\n' "$PATCH_STATUS"
    sudo uptrack-show | tee "$REPORT/applied-updates.txt"
    sudo uptrack-uname -r | tee "$REPORT/effective-kernel-after.txt"
    </copy>
    ```

    Run the apply command only when the preview lists applicable updates. Confirm a successful apply result and actual patch identifiers in `uptrack-show`. If the command fails, save the output and investigate with the instructor before retrying.

3. If no updates are available, record the kernel and outcome. If setup already applied patches, review its output and the installed patch identifiers. A newly released kernel can have no published patches yet; see the [maintained-kernels guidance](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-MaintainedKernels.html).

    The October 27, 2026 test applied 135 patches to Oracle RHCK `5.14.0-687.5.3.el9_8` and reached effective version `5.14.0-687.48.1.el9_8` without rebooting during patching. Counts and availability can change. If your RHCK has no updates, ask the instructor to review the kernel and available patches before continuing. Leave the patch-application test incomplete until actual updates can be applied. Do not remove patches to create an artificial before state.

    An access, network, or unsupported-kernel error also requires investigation. Preserve the output for the instructor.

## Task 4: Verify the applied patches and running system

1. Confirm the VM has not rebooted and the boot-kernel version is unchanged:

    ```bash
    <copy>
    cat /proc/sys/kernel/random/boot_id > "$REPORT/boot-id-after.txt"
    uname -r > "$REPORT/boot-kernel-after.txt"
    diff -u "$REPORT/boot-id-before.txt" "$REPORT/boot-id-after.txt"
    diff -u "$REPORT/boot-kernel-before.txt" "$REPORT/boot-kernel-after.txt"
    </copy>
    ```

    Both comparisons should produce no differences. A changed boot ID means a reboot occurred during this attempt.

2. Review the patch state and check the application:

    ```bash
    <copy>
    sudo uptrack-show | tee "$REPORT/installed-updates-after.txt"
    sudo uptrack-uname -r
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Confirm the patch identifiers applied in Task 3 are present. Expect `active` and the workload marker. These application checks show responses before and after patching; they do not measure every request during patch application. `Installed updates: None` does not demonstrate successful patch application.

## Task 5: Record the test result

1. Record the kernel, patch identifiers, and outcome in `$REPORT/result.txt`:

    - Full patch test passed: actual patches were applied, boot identity stayed unchanged, and the application responded before and after patching.
    - No updates available: client installation and availability checks worked, but actual patch application remains untested.
    - Test blocked: record the installation, access, support, apply, or verification error for follow-up.

2. Review the result with the instructor. Resolve any incomplete full test before marking the patch-application exercise complete. Keep the reports, then continue to Lab 6: Explore Oracle Linux Kernel Choices.

    Live patches update the running kernel. Updating on-disk packages remains part of normal maintenance so that a future boot uses current software.

## Learn More

- [Installing Ksplice on OCI BYOI](https://docs.oracle.com/en-us/iaas/oracle-linux/oci/install-ksplice.htm)
- [Maintained Ksplice kernels](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-MaintainedKernels.html)
- [Uptrack command reference](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-UsingtheuptrackupgradeCommandtoManageKspliceUpdates.html)
- [Ksplice availability and entitlement](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-AboutOracleKsplice.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
