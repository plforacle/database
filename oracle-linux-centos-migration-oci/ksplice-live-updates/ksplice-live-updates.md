# Lab 6: Apply Live Updates with Oracle Ksplice

## Introduction

Oracle Ksplice can apply supported kernel updates to a running Oracle Linux system without a reboot. This migrated OCI instance runs x86_64 RHCK. Install the Ksplice client, preview available updates, and verify that Apache stays available.

Estimated Lab Time: 25 minutes

### Objectives

- Confirm that the running kernel is supplied by Oracle Linux.
- Install and inspect the Ksplice client.
- Apply available live updates and verify the workload without rebooting.

### Prerequisites

- Completed Lab 5 and connected to the Oracle Linux 9 VM.
- Outbound HTTPS access to `www.ksplice.com`.

## Task 1: Confirm the running kernel

1. Check the OS, architecture, and kernel owner:

    ```bash
    grep -E '^(NAME|VERSION_ID|ID)=' /etc/os-release
    uname -m
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    ```

    Continue only if the system is Oracle Linux 9 x86_64 and the running kernel package is from Oracle.

## Task 2: Install the Ksplice client

1. Download the installer with `curl`. Minimal cloud images may not have `wget`:

    ```bash
    sudo curl --fail --location \
      --output /tmp/install-uptrack-oc \
      https://www.ksplice.com/uptrack/install-uptrack-oc
    ```

2. Run the installer and confirm that the update tool is present:

    ```bash
    sudo sh /tmp/install-uptrack-oc
    command -v uptrack-upgrade
    ```

    The command should report `/usr/sbin/uptrack-upgrade`. Do not reboot for this lab.

## Task 3: Preview and apply live updates

1. Review installed and available updates:

    ```bash
    sudo uptrack-show
    sudo uptrack-upgrade -n
    ```

    If the preview is empty, that is a valid result. Skip the apply step and continue with Task 4.

2. If the preview lists updates, apply them:

    ```bash
    sudo uptrack-upgrade -y
    ```

    The tested RHCK instance received Ksplice enablement updates. Another image date or kernel can produce a different update list.

## Task 4: Verify Ksplice and Apache

1. Compare the base and effective kernel releases and show installed updates:

    ```bash
    uname -r
    sudo uptrack-uname -r
    sudo uptrack-show
    ```

    With enablement updates, `uptrack-uname -r` may match `uname -r`. Use `uptrack-show` to see which updates were installed. If no updates were available, report that result rather than treating it as a failure.

2. Confirm the service and page stayed healthy without rebooting:

    ```bash
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    systemctl --failed --no-pager
    ```

## Learn More

- [Installing Ksplice on Oracle Linux in OCI](https://docs.oracle.com/en-us/iaas/oracle-linux/oci/install-ksplice.htm)
- [Managing updates with uptrack-upgrade](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-UsingtheuptrackupgradeCommandtoManageKspliceUpdates.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, September 2026
