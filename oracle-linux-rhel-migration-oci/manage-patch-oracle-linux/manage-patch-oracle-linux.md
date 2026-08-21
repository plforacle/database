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

4. Keep RHCK as the running kernel for this workshop.

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

3. Display a summary of the enabled repository configuration:

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
    sudo dnf check-update
    </copy>
    ```

    DNF lists available updates when updates exist. If the system is current, the command returns no package list.

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

2. Apply all available package updates:

    ```bash
    <copy>
    sudo dnf upgrade -y
    </copy>
    ```

    Review the transaction summary. DNF might update packages without changing the running kernel until the next reboot.

3. Check the RPM database and package dependencies:

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
    sudo dnf needs-restarting -r
    </copy>
    ```

2. Follow the result that matches your system:

    - If the command reports that no reboot is needed, continue with Task 6.
    - If the command reports that a reboot is required, reboot the VM:

        ```bash
        <copy>
        sudo reboot
        </copy>
        ```

3. If you rebooted, wait for the OCI instance to return to Running and reconnect:

    ```bash
    <copy>
    ssh -i "<private-key-path>" cloud-user@<public-ip>
    </copy>
    ```

4. Display the running kernel after maintenance:

    ```bash
    <copy>
    uname -r
    sudo grubby --default-kernel
    </copy>
    ```

## Task 6: Validate the system after maintenance

1. Check for failed services:

    ```bash
    <copy>
    systemctl --failed --no-pager
    </copy>
    ```

    Investigate any failed unit before continuing.

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
    </copy>
    ```

4. Verify SELinux and the firewall:

    ```bash
    <copy>
    getenforce
    sudo firewall-cmd --query-service=http
    </copy>
    ```

5. Open `http://<public-ip>/` in your browser and confirm that the workshop page still appears.

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
- [Managing kernels and system boot](https://docs.oracle.com/en-us/iaas/oracle-linux/boot/boot-about-system-boot-kernels.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, July 2026
