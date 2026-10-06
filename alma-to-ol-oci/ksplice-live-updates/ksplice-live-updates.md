# Lab 6: Apply Live Updates with Oracle Ksplice

## Introduction

Oracle Ksplice applies critical kernel updates to a running system without requiring a reboot. In this lab, you install Ksplice on the Oracle Linux 9 instance that you migrated in the preceding labs, inspect available updates, apply them, and verify that the Apache workload remains available.

This workshop instance runs the Red Hat Compatible Kernel (RHCK). Oracle Linux 9 RHCK is supported by Ksplice. This instance began as AlmaLinux, so check for the Ksplice client after conversion. Confirm that the running Oracle-owned RHCK appears in the maintained-kernel list before installing or applying updates.

Estimated Lab Time: 25 minutes

### Objectives

In this lab, you will:

* Confirm that the migrated instance is running Oracle Linux and RHCK.
* Install the Oracle Ksplice client.
* Preview and apply available live updates without rebooting.
* Verify the effective Ksplice kernel and the Apache workload.

### Prerequisites

* You completed **Lab 5: Manage and Patch Oracle Linux**.
* You can connect to `alma-to-ol-source` using SSH.
* The instance has outbound HTTPS access to `www.ksplice.com`.

## Task 1: Confirm the Migrated Oracle Linux Kernel

1. Connect to the migrated instance if you are not already connected.

    ```bash
    <copy>
    ssh <ssh-user>@<public-ip-address>
    </copy>
    ```

2. Confirm the operating system, architecture, running kernel, and owning RPM:

    ```bash
    <copy>
    grep -E '^(NAME|VERSION|ID|PRETTY_NAME)=' /etc/os-release
    uname -m
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" \
      --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    </copy>
    ```

    The output should identify Oracle Linux 9, `x86_64`, and an Oracle-provided kernel RPM such as `kernel-core`. Check the [Ksplice-maintained kernels](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-MaintainedKernels.html) for this running version. If it is unsupported, record that result and continue to Lab 7; do not report live patching as completed.

    > **Note:** RHCK package names use the standard `kernel`, `kernel-core`, and `kernel-modules` names. They do not contain the word `rhck`. Packages whose names begin with `kernel-uek` are UEK packages, which are not the kernel used in this workshop.

## Task 2: Install the Ksplice Client

1. Download the Oracle Ksplice installer. Use `curl` on this converted instance:

    ```bash
    <copy>
    sudo curl --fail --location \
      --output /tmp/install-uptrack-oc \
      https://www.ksplice.com/uptrack/install-uptrack-oc
    </copy>
    ```

2. Run the installer:

    ```bash
    <copy>
    sudo sh /tmp/install-uptrack-oc
    </copy>
    ```

    The installer adds the `uptrack` client and any required dependencies. It also reports the effective kernel version and prompts you to run `uptrack-upgrade` to apply live updates.

    > **Note:** Do not reboot. Ksplice applies supported kernel updates to the running RHCK kernel.

## Task 3: Review Available Ksplice Updates

1. Confirm that the update command is installed:

    ```bash
    <copy>
    command -v uptrack-upgrade
    </copy>
    ```

    Expected result:

    ```text
    /usr/sbin/uptrack-upgrade
    ```

2. Display the currently installed Ksplice updates and preview available updates:

    ```bash
    <copy>
    sudo uptrack-show
    sudo uptrack-upgrade -n
    </copy>
    ```

    The preview lists actions that Ksplice can apply to the running kernel. A successful preview with no applicable updates is valid. A connectivity, entitlement, or unsupported-kernel error is a failed check and needs investigation.

    > **Note:** If the preview succeeds with no updates, skip Task 4 and continue with Task 5. Record that no updates were applicable.

## Task 4: Apply Live Updates

1. Apply the updates shown in the preview:

    ```bash
    <copy>
    sudo uptrack-upgrade -y
    </copy>
    ```

2. The command completes without restarting the instance. Ksplice changes the effective running kernel in memory while the base kernel reported by `uname -r` can remain the same.

## Task 5: Verify Ksplice and the Workload

1. Display the base running kernel, Ksplice effective kernel, and installed updates:

    ```bash
    <copy>
    uname -r
    sudo uptrack-uname -r
    sudo uptrack-show
    </copy>
    ```

    `uptrack-show` should list the updates installed in Task 4. For enablement updates, `uptrack-uname -r` can show the same kernel release as `uname -r`; the installed-update list is the confirmation that Ksplice applied the live changes.

2. Confirm that the migrated Apache workload remains available without a reboot:

    ```bash
    <copy>
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Expected results include:

    ```text
    active
    <p id="status">MIGRATION_WORKLOAD_OK</p>
    ```

3. Check for failed services:

    ```bash
    <copy>
    systemctl --failed --no-pager
    </copy>
    ```

    A successful result reports `0 loaded units listed.`

## Task 6: Review

Record whether the kernel was supported, the preview succeeded, updates were available, and any updates were applied. Continue to Lab 7 with the corresponding evidence.

## Learn More

* [Install Ksplice on Oracle Linux](https://docs.oracle.com/en-us/iaas/oracle-linux/oci/install-ksplice.htm)
* [Manage Ksplice updates with uptrack-upgrade](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-UsingtheuptrackupgradeCommandtoManageKspliceUpdates.html)
* [Ksplice-maintained kernels](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-MaintainedKernels.html)

## Acknowledgements

* **Author**: Oracle Linux Team
* **Last Updated By/Date**: Oracle Linux Team, October 2026
