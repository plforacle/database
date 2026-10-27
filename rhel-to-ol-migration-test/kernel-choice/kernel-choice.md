# Lab 6: Explore Oracle Linux Kernel Choices

## Introduction

You migrated to Oracle Linux RHCK in Lab 4 and tested Ksplice on that running kernel in Lab 5. Now explore the Unbreakable Enterprise Kernel (UEK) as a separate post-migration choice. Install UEK 8, select it for boot, reboot, and verify the running kernel. Keep the working RHCK installed.

Kernel choice depends on workload requirements, hardware, and software certification. This lab demonstrates switching from RHCK to UEK. This deliberate reboot happens after the Ksplice test is complete; it does not invalidate the earlier demonstration of live patching without a reboot.

### Objectives

In this lab, you will:

- Distinguish RHCK from UEK.
- Install UEK 8 from Oracle repositories.
- Select and boot UEK, then verify the running kernel.

Estimated Lab Time: 30 minutes

### Prerequisites

Complete Labs 4 and 5 and retain the Ksplice patch results. Use the same Oracle Linux 9.8 x86_64 destination VM, still running RHCK, and keep your SSH key and connection details available. Run these changes only on the workshop VM.

## Task 1: Check the current kernel and compare choices

1. Confirm the operating system, running kernel, and default boot kernel:

    ```bash
    <copy>
    grep -E '^(ID|VERSION_ID)=' /etc/os-release
    uname -r
    sudo grubby --default-kernel
    </copy>
    ```

    Expect `ID="ol"`, `VERSION_ID="9.8"`, and a running RHCK version without `uek`. The default kernel should match the booted RHCK used in Lab 5. Its `uname -r` value remains the boot version even after Ksplice has advanced the effective kernel version.

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
    </copy>
    ```

    The UEK path must be nonempty, identify an existing file, contain `el9uek`, and belong to an Oracle package. Do not proceed with an empty or unverified path.

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

## Task 4: Verify the running UEK kernel

1. Verify the booted kernel, default, and OS identity:

    ```bash
    <copy>
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    sudo grubby --default-kernel
    grep -E '^(ID|VERSION_ID)=' /etc/os-release
    </copy>
    ```

    Expect Oracle Linux 9.8, an Oracle-owned kernel with `el9uek` in its version, and a matching boot default. Record the actual version; do not require a fixed build number.

    You have completed the main workshop. Keep your VM, kernel results, and patching results for review. If time allows, open optional Lab 7 to explore OSMH. You can use its independent Luna lab option or continue on this VM with the required permissions.

## Learn More

- [Oracle Linux 9.8 kernel installation guidance](https://docs.oracle.com/en/operating-systems/oracle-linux/9/relnotes9.8/ol9-features-Installation.html)
- [Changing the default kernel](https://docs.oracle.com/en/operating-systems/oracle-linux/9/boot/boot-UsinggrubbyToManageKernels_change_the_default_kernel.html)
- [Oracle Linux kernel reference](https://docs.oracle.com/en/operating-systems/oracle-linux/9/boot/oracle_linux9_kernel_version_matrix.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
