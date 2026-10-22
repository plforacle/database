# Lab 5: Manage and Patch Oracle Linux

## Introduction

The converted VM now uses Oracle Linux repositories and packages. In this lab, you review the Oracle Linux software sources, check for available updates, apply standard package updates with DNF, determine whether a reboot is recommended, and confirm that the workshop application remains healthy.

Oracle Linux provides two kernel choices. The Red Hat Compatible Kernel (RHCK) is used for this workshop because it keeps the initial post-migration kernel experience close to RHEL. The Unbreakable Enterprise Kernel (UEK) is Oracle's optimized kernel for Oracle Linux. You will review both choices but keep RHCK as the running kernel.

### Objectives

In this lab, you will:

- Confirm the Oracle Linux operating-system and kernel identity.
- Review the enabled Oracle Linux repositories.
- Check for available package and security updates.
- Apply standard Oracle Linux package updates with DNF.
- Determine whether the system recommends a reboot.
- Verify that the application remains healthy after maintenance.

### Prerequisites

Before beginning this lab, confirm that you have:

- Completed Lab 4.
- SSH access to the converted Oracle Linux 9.8 instance.
- A healthy Apache workload after conversion and reboot.
- Outbound network access from the instance to Oracle Linux repositories.

Estimated Lab Time: 25 minutes

## Task 1: Confirm the Oracle Linux system

1. Display the operating-system identity:

    ```bash
    <copy>
    grep -E '^(PRETTY_NAME|ID|VERSION_ID)=' /etc/os-release
    </copy>
    ```

    Confirm that the output identifies Oracle Linux 9.8.

2. Display the running kernel and its owning package:

    ```bash
    <copy>
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    </copy>
    ```

    Confirm that the package vendor identifies Oracle.

3. List the installed RHCK and UEK kernel packages:

    ```bash
    <copy>
    rpm -qa 'kernel*' | sort
    rpm -qa 'kernel-uek*' | sort
    </copy>
    ```

    It is normal for the second command to return no output when UEK is not installed.

4. Keep the standard `kernel` packages as the running RHCK for this workshop. No configuration change is required.

    RHCK packages use names such as `kernel`, `kernel-core`, and `kernel-modules`; they do not normally include `rhck` in the package name. Packages whose names begin with `kernel-uek` identify UEK. Do not install UEK or change the default boot kernel in this workshop.

## Task 2: Review the Oracle Linux repositories

1. List the enabled repositories:

    ```bash
    <copy>
    sudo dnf repolist --enabled
    </copy>
    ```

2. Confirm that the Oracle Linux 9 BaseOS and AppStream repositories appear:

    - `ol9_baseos_latest`
    - `ol9_appstream`

3. After Task 1 confirms that RHCK is running and no `kernel-uek` packages are installed, disable the UEK repository for this RHCK workshop:

    ```bash
    <copy>
    sudo dnf config-manager --set-disabled ol9_UEKR7
    sudo dnf repolist --enabled
    </copy>
    ```

    Require `ol9_baseos_latest` and `ol9_appstream`, with `ol9_UEKR7` absent from the enabled list. If UEK is installed or running, stop for instructor review before changing repositories. Do not remove or switch kernels to complete this step.

    The UEK repository also supplies userspace tools. A standard upgrade can select its newer `xfsprogs` even without installing a UEK kernel. Oracle's [UEK R7 userspace package guidance](https://docs.oracle.com/en/operating-systems/uek/7/relnotes7.0/uek7.0-RelatedUserSpacePackages.html) restricts these packages to UEK R7. Keep this repository disabled while maintaining the workshop's RHCK system.

    Disabling the repository does not replace packages already installed from it. If an earlier update installed UEK userspace packages, stop for an instructor-reviewed package correction before continuing.

4. Display a summary of the enabled repository configuration:

    ```bash
    <copy>
    sudo dnf repoinfo --enabled
    </copy>
    ```

    The repositories provide the packages and updates used to maintain the converted system.

## Task 3: Review available updates

1. Refresh the repository metadata:

    ```bash
    <copy>
    sudo dnf makecache
    </copy>
    ```

2. Check for available package updates:

    ```bash
    <copy>
    if sudo dnf check-update; then
      UPDATE_CHECK_STATUS=0
    else
      UPDATE_CHECK_STATUS=$?
    fi
    printf 'Update check exit status: %s\n' "$UPDATE_CHECK_STATUS"
    </copy>
    ```

    Status `100` means updates are available; it is not an update failure. Status `0` means no updates are available. Stop and review any other status or repository error. These are the [documented DNF check-update return values](https://dnf.readthedocs.io/en/latest/command_ref.html#check-update-command).

3. Display the update advisory summary:

    ```bash
    <copy>
    sudo dnf updateinfo summary
    </copy>
    ```

4. List available security advisories:

    ```bash
    <copy>
    sudo dnf updateinfo list --security
    </copy>
    ```

    No listed security advisory is a valid result when the system is already current.

## Task 4: Apply standard package updates

1. Confirm that the application is healthy before maintenance:

    ```bash
    <copy>
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

2. Preview the update transaction before applying it:

    ```bash
    <copy>
    sudo dnf --assumeno --setopt=assumeyes=False upgrade
    </copy>
    ```

    Require a resolved plan from the intended Oracle repositories. A resolved transaction deliberately declined with `Operation aborted.` normally returns status `1`; a dependency or repository error does not pass this review. Confirm that the plan does not install UEK, select UEK userspace packages, remove retained source kernels, or change the Oracle Linux 9.8 release. Resolve any discrepancy before continuing.

3. Apply the reviewed package updates:

    ```bash
    <copy>
    sudo dnf upgrade -y
    UPGRADE_STATUS=$?
    printf 'Upgrade exit status: %s\n' "$UPGRADE_STATUS"
    </copy>
    ```

    Require status `0` and no transaction, scriptlet or loader errors. DNF might update packages without changing the running kernel until the next reboot. If the result fails or is unknown, preserve the output and stop without rebooting or applying ad hoc repairs.

4. Check the RPM database and package dependencies:

    ```bash
    <copy>
    sudo rpm --verifydb
    sudo dnf check
    </copy>
    ```

    Successful commands return no error.

## Task 5: Determine whether a reboot is recommended

1. Check whether updated packages require a reboot:

    ```bash
    <copy>
    if sudo dnf needs-restarting -r; then
      REBOOT_HINT_STATUS=0
    else
      REBOOT_HINT_STATUS=$?
    fi
    printf 'Reboot hint exit status: %s\n' "$REBOOT_HINT_STATUS"
    </copy>
    ```

    For a successfully executed check, status `0` means no reboot is recommended and status `1` means a reboot is recommended. This is the [needs-restarting plugin's documented convention](https://dnf-plugins-core.readthedocs.io/en/latest/needs_restarting.html). A missing plugin, permission problem or other DNF error is not a valid reboot recommendation. Review the output as well as the status; stop on errors or other statuses.

2. Follow the result that matches your system:

    - For a valid status `0`, continue with Task 6.
    - For a valid status `1`, pass the checks in Steps 3 and 4 before rebooting. Core userspace updates can recommend a reboot even when no kernel package changed.

3. Repeat the candidate's boot verification and source health checks before a maintenance reboot:

    ```bash
    <copy>
    (
      set -e -o pipefail
      cd "$HOME/ol-migration"
      sha256sum --check SHA256SUMS
      sudo bash ./migrate-to-oracle-linux.sh \
        --verify-boot-only --target-version 9.8 --kernel rhck
      sudo rpm --verifydb
      sudo dnf check
      sudo rpm -V libattr libacl
      python3 -c 'import ctypes; ctypes.CDLL("libattr.so.1"); ctypes.CDLL("libacl.so.1"); print("Library load checks: PASS")'
      DEFAULT_KERNEL=$(sudo grubby --default-kernel)
      printf 'Default kernel: %s\n' "$DEFAULT_KERNEL"
      sudo grubby --info "$DEFAULT_KERNEL"
      rpm -qf "$DEFAULT_KERNEL" --qf '%{NAME} %{VERSION}-%{RELEASE}.%{ARCH} %{VENDOR}\n'
      sudo grub2-probe --target=fs_uuid /
      findmnt -n -o UUID --target /
      sudo grub2-probe --target=fs_uuid /boot
      findmnt -n -o UUID --target /boot
      systemctl is-active httpd
      curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
      sha256sum --check "$HOME/ol-migration-evidence/before/application-sha256.txt"
      printf 'LAB5_PRE_REBOOT_CHECKS_PASS\n'
    )
    MAINTENANCE_BOOT_STATUS=$?
    printf 'Maintenance boot check exit status: %s\n' "$MAINTENANCE_BOOT_STATUS"
    </copy>
    ```

    Require status `0`, the pass marker, an Oracle-owned RHCK as default, and matching GRUB and mounted UUIDs for both root and `/boot`. Require the same root device and storage/network boot arguments as the successfully booted Oracle entry captured in Lab 4. Keep the reviewed Oracle crash-reservation default when comparing with the earlier RHEL reference. A working workload or a reboot recommendation cannot override a boot-check failure.

4. Review the boot files and save maintenance evidence before reboot:

    Repeat Lab 4, Task 3's target initramfs and required-driver inspection if the selected kernel or its initramfs changed, including a rebuild caused by a `dracut` update. Require the observed root-storage, LVM, filesystem and NIC dependencies and tooling. Do not infer a safe image from its presence or from an unchanged kernel package alone. Resolve every boot discrepancy without rebooting.

    Save the upgrade result, reboot hint, current kernel/default, boot checks and application checksum in a new maintenance evidence directory. Copy that evidence locally before reboot. Preserve the existing migration evidence and backups.

5. After Steps 3 and 4 pass, perform the recommended reboot:

    ```bash
    <copy>
    sudo reboot
    </copy>
    ```

6. Reconnect using SSH after the reboot:

    ```bash
    <copy>
    ssh -i "<private-key-path>" <ssh-user>@<public-ip>
    </copy>
    ```

    OCI's Running state does not establish a successful guest boot. If reconnect fails, preserve the instance and its evidence and stop this rehearsal.

7. Display and verify the running kernel after maintenance:

    ```bash
    <copy>
    uname -r
    sudo grubby --default-kernel
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    test "$(sudo grubby --default-kernel)" = "/boot/vmlinuz-$(uname -r)"
    </copy>
    ```

    Require the selected Oracle RHCK to be both running and default, then complete Task 6.

## Task 6: Validate the system after maintenance

1. Check for failed services:

    ```bash
    <copy>
    sudo systemctl is-system-running --wait
    systemctl --failed --no-pager
    </copy>
    ```

    Wait for normal boot completion, then require the system to report running with no failed units. An early `starting` result is incomplete; preserve it and repeat the check after startup settles. Review any failed unit before continuing.

2. Verify the Apache service:

    ```bash
    <copy>
    systemctl is-enabled httpd
    systemctl is-active httpd
    </copy>
    ```

3. Verify the local application:

    ```bash
    <copy>
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    sha256sum --check "$HOME/ol-migration-evidence/before/application-sha256.txt"
    </copy>
    ```

4. Verify SELinux and the firewall:

    ```bash
    <copy>
    getenforce
    sudo firewall-cmd --query-service=http
    </copy>
    ```

5. Repeat the package checks after maintenance:

    ```bash
    <copy>
    sudo rpm --verifydb
    sudo dnf check
    </copy>
    ```

    Require both commands to pass.

6. Open `http://<public-ip>/` in your browser and confirm that the workshop page still appears.

## Task 7: Review your knowledge

1. Why does this workshop continue using RHCK?

    RHCK keeps the initial post-migration kernel experience close to RHEL while using an Oracle-maintained Oracle Linux kernel.

2. What is the purpose of `dnf check-update`?

    It reports packages that have newer versions available from the enabled repositories.

3. Why might a reboot be required after a DNF update?

    Updated kernel or core system packages are stored on disk, but the system can continue running older code until it reboots.

4. What proves that package maintenance did not break the workshop application?

    Apache remains enabled and active, the local response contains `MIGRATION_WORKLOAD_OK`, and the public web page remains reachable.

## Learn More

- [Oracle Linux 9 package repositories](https://yum.oracle.com/oracle-linux-9.html)
- [DNF command reference](https://docs.oracle.com/en/operating-systems/oracle-linux/software-management/sfw-mgmt-UseDNFCommand.html)
- [UEK R7 userspace package restrictions](https://docs.oracle.com/en/operating-systems/uek/7/relnotes7.0/uek7.0-RelatedUserSpacePackages.html)
- [Managing kernels and system boot](https://docs.oracle.com/en-us/iaas/oracle-linux/boot/boot-about-system-boot-kernels.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
