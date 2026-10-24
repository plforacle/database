# Lab 8: Explore Oracle Linux Kernel Choices

## Introduction

The core conversion used Oracle Linux RHCK and was validated in Lab 7. Now explore the Unbreakable Enterprise Kernel (UEK) as a separate post-migration choice. Install UEK 8, select it for boot, reboot, and verify the same Apache application. Keep the working RHCK installed as a fallback.

Kernel choice depends on workload requirements, hardware, and software certification. This small Apache exercise demonstrates boot and application continuity; it does not establish performance improvements or certify another application. Switching the boot kernel requires a reboot, unlike the Ksplice live-patching exercise in Lab 9.

### Objectives

In this lab, you will:

- Distinguish RHCK from UEK and record the working RHCK boot path.
- Install UEK 8 from Oracle repositories.
- Select and boot UEK, then verify the application.
- Identify a fallback procedure without removing the original kernel.

Estimated Lab Time: 30 minutes

### Prerequisites

Complete Lab 7 with a successful RHCK migration decision and saved evidence. Use the Oracle Linux 9.8 x86_64 destination VM and keep your SSH key and connection details available. The original application checksum must still match its baseline. Run these changes only on the workshop VM.

Beginners should follow the guided path. Experienced learners can explain and perform the kernel selection independently, but must record the fallback path and pass the same verification checks.

## Task 1: Record the working kernel and compare choices

1. Confirm the validated starting state and save its boot path:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-practice-reports/kernel-choice"
    cat /etc/os-release
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    printf '/boot/vmlinuz-%s\n' "$(uname -r)" \
      > "$HOME/ol-practice-reports/kernel-choice/rhck-kernel-path.txt"
    sudo grubby --default-kernel
    cat /proc/sys/kernel/random/boot_id \
      > "$HOME/ol-practice-reports/kernel-choice/boot-id-before-uek.txt"
    sha256sum --check "$HOME/ol-migration-evidence/before/application-sha256.txt"
    </copy>
    ```

    Start from Oracle Linux 9.8, an Oracle-owned kernel without `uek` in its running version, and checksum OK. Stop if migration validation is incomplete. Do not overwrite the migration evidence archives.

2. Inspect the installed kernels and available repositories:

    ```bash
    <copy>
    rpm -qa 'kernel*' | sort
    sudo grubby --info=ALL
    sudo dnf repolist --all | grep -E 'ol9_(baseos_latest|appstream|UEKR)'
    </copy>
    ```

    RHCK is the compatibility-focused kernel used for the conversion. UEK is Oracle's alternative kernel with its own release cadence and engineering. `uname -r` reports the running kernel; `grubby --default-kernel` identifies the one selected for the next normal boot. The two can differ before a reboot.

## Task 2: Select the UEK 8 repository and install the kernel

1. Confirm that `ol9_UEKR8` is defined, then select it:

    ```bash
    <copy>
    sudo dnf config-manager --disable ol9_UEKR7
    sudo dnf config-manager --enable ol9_UEKR8
    sudo dnf repolist --enabled
    </copy>
    ```

    The pinned migration may leave UEK R7 enabled. This exercise deliberately selects UEK 8, documented for Oracle Linux 9.8. If the UEK 8 repository is not defined, stop and have the instructor check the `oraclelinux-release-el9` repository package and current Oracle instructions. Do not invent a repository URL or disable signature checking.

2. Preview the kernel installation without changes:

    ```bash
    <copy>
    sudo dnf install --assumeno kernel-uek
    </copy>
    ```

    `Operation aborted` is expected for the preview. Review dependencies and disk requirements. Stop if the transaction proposes removing the working RHCK, replacing unrelated applications, or cannot resolve dependencies.

3. Install the selected kernel interactively:

    ```bash
    <copy>
    sudo dnf install kernel-uek
    rpm -q kernel-uek
    df -h / /boot
    </copy>
    ```

    Confirm with `y` only when the transaction matches the reviewed preview. Keep the installed RHCK packages. If installation fails, retain the output and current boot selection; do not reboot into an incomplete installation. This step selects a kernel package, not a full-system upgrade.

## Task 3: Select UEK and reboot

1. Find the installed UEK boot image and inspect its ownership:

    ```bash
    <copy>
    UEK_KERNEL="$(find /boot -maxdepth 1 -type f -name 'vmlinuz-*el9uek*' | sort -V | tail -1)"
    printf 'UEK boot image: %s\n' "$UEK_KERNEL"
    test -f "$UEK_KERNEL" && rpm -qf "$UEK_KERNEL" \
      --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    cat "$HOME/ol-practice-reports/kernel-choice/rhck-kernel-path.txt"
    </copy>
    ```

    The UEK path must be nonempty, identify an existing file, contain `el9uek`, and belong to an Oracle package. Confirm the saved RHCK file also exists. Do not proceed with an empty or unverified path.

2. Select UEK and verify the resulting default:

    ```bash
    <copy>
    if test -n "$UEK_KERNEL" && test -f "$UEK_KERNEL"; then
      sudo grubby --set-default "$UEK_KERNEL"
    fi
    sudo grubby --default-kernel
    </copy>
    ```

    The default must match the verified UEK image. If it does not, correct the selection before rebooting.

3. Reboot, then reconnect from your local terminal using the same destination IP and key:

    ```bash
    <copy>
    sudo reboot
    </copy>
    ```

    ```bash
    <copy>
    ssh -i <private-key-path> cloud-user@<public-ip>
    </copy>
    ```

    Wait for SSH to accept connections again. The following task runs in the new SSH session; shell variables from before the reboot are no longer set.

## Task 4: Verify UEK and the original application

1. Verify the booted kernel, default, and OS identity:

    ```bash
    <copy>
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    sudo grubby --default-kernel
    grep -E '^(ID|VERSION_ID)=' /etc/os-release
    cat /proc/sys/kernel/random/boot_id \
      > "$HOME/ol-practice-reports/kernel-choice/boot-id-after-uek.txt"
    </copy>
    ```

    Expect Oracle Linux 9.8, an Oracle-owned kernel with `el9uek` in its version, and a matching boot default. The boot ID should differ from the one recorded in Task 1 because this exercise rebooted. Record the actual version; do not require a fixed build number.

2. Validate the application and security state:

    ```bash
    <copy>
    systemctl is-enabled httpd
    systemctl is-active httpd
    systemctl --failed --no-pager
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    sha256sum --check "$HOME/ol-migration-evidence/before/application-sha256.txt"
    getenforce
    sudo firewall-cmd --query-service=http
    </copy>
    ```

    Expect Apache enabled/active, no unexpected failed units, the marker, checksum OK, SELinux Enforcing, and HTTP allowed. Verify `http://<public-ip>/` in the browser again. Keep this result separate from the earlier RHCK migration validation.

## Task 5: Understand fallback and complete the checkpoint

1. Challenge: explain why installing UEK does not immediately change `uname -r`, why selecting a default does not prove that kernel is running, and why an unchanged application checksum is useful after the kernel change. Beginners can use Tasks 1 through 4 as evidence.

2. Review the fallback commands. Run them only if you need to return to the saved RHCK and SSH is available:

    ```bash
    <copy>
    RHCK_KERNEL="$(cat "$HOME/ol-practice-reports/kernel-choice/rhck-kernel-path.txt")"
    if test -n "$RHCK_KERNEL" && test -f "$RHCK_KERNEL"; then
      sudo grubby --set-default "$RHCK_KERNEL"
    fi
    sudo grubby --default-kernel
    </copy>
    ```

    Verify that the displayed path is the saved RHCK. Reboot only after confirming it, then repeat the application checks. If you choose to stay on RHCK, disable UEK repositories to keep future kernel maintenance aligned with that choice:

    ```bash
    <copy>
    sudo dnf config-manager --disable ol9_UEKR7 ol9_UEKR8
    </copy>
    ```

    If the VM cannot boot or accept SSH, use the instance console or your approved recovery procedure to select the retained RHCK, or rebuild the disposable VM from the prepared image. Do not remove kernels while diagnosing an inaccessible system. Record a fallback as a fallback result, not a successful UEK test.

3. In the normal guided path, leave UEK selected and running. Record the RHCK path, booted UEK version, application result, and fallback decision in `$HOME/ol-practice-reports/kernel-choice/result.txt`. Continue to Lab 9 only after the chosen Oracle kernel and application are healthy. Ksplice can be evaluated on a maintained Oracle UEK or Oracle RHCK; a retained Red Hat-built kernel is not an acceptable substitute.

## Learn More

- [Oracle Linux 9.8 kernel installation guidance](https://docs.oracle.com/en/operating-systems/oracle-linux/9/relnotes9.8/ol9-features-Installation.html)
- [Changing the default kernel](https://docs.oracle.com/en/operating-systems/oracle-linux/9/boot/boot-UsinggrubbyToManageKernels_change_the_default_kernel.html)
- [Oracle Linux kernel reference](https://docs.oracle.com/en/operating-systems/oracle-linux/9/boot/oracle_linux9_kernel_version_matrix.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
