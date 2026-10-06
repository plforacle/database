# Lab 7: Apply Live Updates with Oracle Ksplice

## Introduction

Use OS Management Hub and the Ksplice client to apply available kernel live updates to the migrated Oracle Linux instance. Check the RHCK support status, preview updates, submit a Console job, and verify the effective kernel and Apache workload without rebooting for the live update.

For OCI instances, OS Management Hub uses the `ksplice` package and an attached Ksplice software source. See [OS Management Hub Ksplice requirements](https://docs.oracle.com/en-us/iaas/osmh/doc/linux-package-management.htm).

Estimated Lab Time: 35 minutes

### Objectives

- Confirm that the running Oracle-owned RHCK is supported.
- Install or verify the OCI Ksplice client through OS Management Hub.
- Preview and apply available kernel live updates.
- Verify the effective kernel and unchanged workload.
- Record the job and live-update results.

### Prerequisites

- You completed Lab 6 and the instance is Active in OS Management Hub.
- The Oracle Linux 9 x86_64 Ksplice source is attached to the instance.
- You can connect to `alma-to-ol-source` using SSH and sudo.
- The instance can reach OS Management Hub and `www.ksplice.com` over HTTPS.
- Any reboot required by Lab 6's standard package updates is complete.

## Task 1: Confirm the migrated Oracle Linux kernel

1. In your SSH session, inspect the operating system and kernel:

    ```bash
    <copy>
    grep -E '^(NAME|VERSION|ID|PRETTY_NAME)=' /etc/os-release
    uname -m
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    </copy>
    ```

2. Confirm Oracle Linux 9, x86_64, and an Oracle-owned RHCK. Check the running release in [Ksplice-maintained kernels](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-MaintainedKernels.html). If unsupported, record that outcome and continue to Lab 8 without claiming live patching succeeded.

    RHCK uses package names such as `kernel-core`; its name does not contain `rhck`. UEK is outside this workshop's kernel path.

## Task 2: Verify the OCI Ksplice client

1. Inspect the installed client packages:

    ```bash
    <copy>
    rpm -q ksplice uptrack ksplice-offline uptrack-offline
    </copy>
    ```

    Missing offline packages are expected on this OCI path. If you completed an earlier version of this workshop, you may already have Uptrack installed and live patches applied. Preserve that evidence; do not remove clients or patches merely to repeat the exercise.

2. If `ksplice` is missing, open **OS Management Hub**, **Instances**, `alma-to-ol-source`, and **Packages**. Under **Available packages**, select `ksplice`, choose **Install**, run **Immediately**, and name the job `alma-to-ol-install-ksplice`. Review dependencies, submit, and wait for a **Succeeded** work request. Check the child request's logs if installation fails.

    OCI uses the online `ksplice` package, rather than the non-OCI `ksplice-offline` instructions. Existing Uptrack can be part of the Oracle Linux 9 online client's dependencies; do not treat its presence alone as a conflict. See [Oracle Linux 9 client installation](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-InstallingtheKspliceClientsFromULN.html) for the package relationship. OCI access does not require ULN registration.

3. Verify the command and package:

    ```bash
    <copy>
    rpm -q ksplice
    sudo ksplice --help
    </copy>
    ```

    If the package is unavailable, verify the Lab 6 Ksplice and OCI Included sources. Investigate dependency or access errors before proceeding. Do not install the standalone Uptrack script as an alternative to the OS Management Hub client setup.

## Task 3: Preview kernel live updates

1. Save the boot identifier and current patch state:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-migration-evidence/ksplice"
    cat /proc/sys/kernel/random/boot_id > "$HOME/ol-migration-evidence/ksplice/boot-id-before.txt"
    sudo ksplice kernel show
    sudo ksplice -n kernel upgrade
    </copy>
    ```

2. Record whether the preview succeeds and whether updates are available. A successful preview with no applicable updates is valid. Connectivity, configuration, entitlement, and unsupported-kernel errors require investigation.

    If no updates are available, skip Task 4 and perform Task 5. Record that no live updates were applied. See [Ksplice command usage](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-UsingthekspliceCommandtoManagetheKspliceEnhancedClient.html).

## Task 4: Apply kernel live updates through OS Management Hub

1. In the managed instance's Console details page, select **Create update job**. Choose **Immediately** and name it `alma-to-ol-ksplice-kernel`.

2. Choose **Apply specific update categories** and the **Ksplice kernel** update option. Select kernel updates only for this exercise. Review the instance and submit the job. See [instance update jobs](https://docs.oracle.com/en-us/iaas/osmh/doc/create-scheduled-job-instance.htm).

3. Open **Jobs**, **Work requests** and wait for completion. Inspect **Messages**, including the child request if present. Save the work-request OCID, terminal status, and applied update details. A failed job is not a successful no-update result.

    Do not run a parallel DNF or Ksplice operation while the job is running. Kernel live updates do not require a reboot. Ordinary package updates can still require one.

## Task 5: Verify Ksplice and the workload

1. Check the base kernel, effective kernel, and installed live updates:

    ```bash
    <copy>
    uname -r
    sudo ksplice kernel uname -r
    sudo ksplice kernel show
    </copy>
    ```

    Compare with the preview and job messages. Enablement updates may leave the effective release string unchanged; the installed-update list and successful job provide the patch evidence.

2. Verify that this live-update exercise did not reboot the instance:

    ```bash
    <copy>
    cat /proc/sys/kernel/random/boot_id > "$HOME/ol-migration-evidence/ksplice/boot-id-after.txt"
    diff -u "$HOME/ol-migration-evidence/ksplice/boot-id-before.txt" \
      "$HOME/ol-migration-evidence/ksplice/boot-id-after.txt"
    </copy>
    ```

    Expect no difference.

3. Check Apache, the local application, and failed services:

    ```bash
    <copy>
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    systemctl --failed --no-pager
    </copy>
    ```

    Expect active Apache, the health marker, and no unexpected failed services. Verify the public page from your workstation.

4. Save the patch state:

    ```bash
    <copy>
    sudo ksplice kernel show > "$HOME/ol-migration-evidence/ksplice/installed-updates.txt"
    sudo ksplice kernel uname -r > "$HOME/ol-migration-evidence/ksplice/effective-kernel.txt"
    </copy>
    ```

    In OS Management Hub, review the effective kernel and instance change history after inventory refresh. Capture the Console job result if you submitted a job.

## Task 6: Review

1. Record the kernel support result, client packages, preview outcome, updates available and applied, job status, and workload checks. Keep a no-update outcome separate from errors. Continue to **Lab 8: Validate and Clean Up**.

## Learn More

- [OS Management Hub Ksplice configuration and verification](https://docs.oracle.com/en-us/iaas/osmh/doc/linux-package-management.htm)
- [Ksplice command reference](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-UsingthekspliceCommandtoManagetheKspliceEnhancedClient.html)
- [Ksplice on OCI](https://docs.oracle.com/en-us/iaas/oracle-linux/oci/install-ksplice.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Perside Foster with Codex assistance, October 2026
