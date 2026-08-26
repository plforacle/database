# Lab 6: Apply Live Updates with Oracle Ksplice

## Introduction

Oracle Ksplice applies critical kernel updates to a running system without requiring a reboot. In this lab, you install Ksplice on the Oracle Linux 9.8 instance that you migrated in the preceding labs, inspect available updates, apply them, and verify that the Apache workload remains available.

This workshop instance runs the Red Hat Compatible Kernel (RHCK). Oracle Linux 9 RHCK is supported by Ksplice. The custom image imported for this workshop is a bring-your-own-image, so install Ksplice after the migration instead of assuming it is already present.

Estimated Lab Time: 25 minutes

### Objectives

In this lab, you will:

* Confirm that the migrated instance is running Oracle Linux and RHCK.
* Install the Oracle Ksplice client.
* Preview and apply available live updates without rebooting.
* Verify the effective Ksplice kernel and the Apache workload.

### Prerequisites

* You completed **Lab 5: Manage and Patch Oracle Linux**.
* You can connect to `ol-migrate-rhel-source` using SSH.
* The instance has outbound HTTPS access to `www.ksplice.com`.

## Task 1: Confirm the Migrated Oracle Linux Kernel

1. Connect to the migrated instance if you are not already connected.

    ```bash
    ssh cloud-user@<public-ip-address>
    ```

2. Confirm the operating system, architecture, running kernel, and owning RPM:

    ```bash
    grep -E '^(NAME|VERSION|ID|PRETTY_NAME)=' /etc/os-release
    uname -m
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" \
      --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    ```

    The output should identify Oracle Linux 9.8, `x86_64`, and an Oracle-provided kernel RPM such as `kernel-core`.

    > **Note:** RHCK package names use the standard `kernel`, `kernel-core`, and `kernel-modules` names. They do not contain the word `rhck`. Packages whose names begin with `kernel-uek` are UEK packages, which are not the kernel used in this workshop.

## Task 2: Install the Ksplice Client

1. Download the Oracle Ksplice installer. This minimal imported image might not include `wget`, so use `curl`:

    ```bash
    sudo curl --fail --location \
      --output /tmp/install-uptrack-oc \
      https://www.ksplice.com/uptrack/install-uptrack-oc
    ```

2. Run the installer:

    ```bash
    sudo sh /tmp/install-uptrack-oc
    ```

    The installer adds the `uptrack` client and any required dependencies. It also reports the effective kernel version and prompts you to run `uptrack-upgrade` to apply live updates.

    > **Note:** Do not reboot. Ksplice applies supported kernel updates to the running RHCK kernel.

## Task 3: Review Available Ksplice Updates

1. Confirm that the update command is installed:

    ```bash
    command -v uptrack-upgrade
    ```

    Expected result:

    ```text
    /usr/sbin/uptrack-upgrade
    ```

2. Display the currently installed Ksplice updates and preview available updates:

    ```bash
    sudo uptrack-show
    sudo uptrack-upgrade -n
    ```

    The preview lists actions that Ksplice can apply to the running kernel.

    > **Note:** An empty update list is valid. It means no Ksplice update is currently available for this exact RHCK version. If the preview is empty, skip Task 4 and continue with Task 5.

## Task 4: Apply Live Updates

1. Apply the updates shown in the preview:

    ```bash
    sudo uptrack-upgrade -y
    ```

2. The command completes without restarting the instance. Ksplice changes the effective running kernel in memory while the base kernel reported by `uname -r` can remain the same.

## Task 5: Verify Ksplice and the Workload

1. Display the base running kernel, Ksplice effective kernel, and installed updates:

    ```bash
    uname -r
    sudo uptrack-uname -r
    sudo uptrack-show
    ```

    `uptrack-show` should list the updates installed in Task 4. For enablement updates, `uptrack-uname -r` can show the same kernel release as `uname -r`; the installed-update list is the confirmation that Ksplice applied the live changes.

2. Confirm that the migrated Apache workload remains available without a reboot:

    ```bash
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    ```

    Expected results include:

    ```text
    active
    <p id="status">MIGRATION_WORKLOAD_OK</p>
    ```

3. Check for failed services:

    ```bash
    systemctl --failed --no-pager
    ```

    A successful result reports `0 loaded units listed.`

## Task 6: Review

You installed Oracle Ksplice on the migrated Oracle Linux RHCK instance, applied available live updates, and verified the application without rebooting. Continue to the cleanup lab when you are ready to remove the workshop resources.

## Learn More

* [Install Ksplice on Oracle Linux](https://docs.oracle.com/en-us/iaas/oracle-linux/oci/install-ksplice.htm)
* [Manage Ksplice updates with uptrack-upgrade](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-UsingtheuptrackupgradeCommandtoManageKspliceUpdates.html)
* [Ksplice-maintained kernels](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-MaintainedKernels.html)

## Acknowledgements

* **Author**: Oracle Linux Team
* **Last Updated By/Date**: Oracle Linux Team, August 2026
