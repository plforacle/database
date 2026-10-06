# Lab 7: Validate and Hand Off

## Introduction

Compare the Oracle Linux VM with the CentOS Stream baseline and confirm that its workload, services, and security settings survived the migration. When testing is finished, tell the instructor that your assigned VM is ready for review and cleanup.

Estimated Lab Time: 30 minutes

### Objectives

- Capture post-migration evidence.
- Compare identity, packages, kernel, and application state.
- Hand the VM and cleanup details back to the instructor.

### Prerequisites

- Completed Lab 6 and connected to the migrated instance.
- The `centos-before.tar.gz` archive and the boot-volume backup from earlier labs.

## Task 1: Capture the final state

1. Create an after-migration evidence directory and capture the OS and package state:

    ```bash
    EVIDENCE="$HOME/ol-migration-evidence/after"
    mkdir -p "$EVIDENCE"
    cat /etc/os-release > "$EVIDENCE/os-release.txt"
    uname -a > "$EVIDENCE/kernel.txt"
    sudo dnf repolist --enabled > "$EVIDENCE/repositories.txt"
    rpm -qa --qf '%{NAME}\t%{VERSION}-%{RELEASE}.%{ARCH}\t%{VENDOR}\n' | sort > "$EVIDENCE/packages.tsv"
    ```

2. Record service, security, and application results:

    ```bash
    systemctl is-active httpd > "$EVIDENCE/httpd-active.txt"
    getenforce > "$EVIDENCE/selinux.txt"
    sudo firewall-cmd --list-all > "$EVIDENCE/firewall.txt"
    curl --fail --silent http://127.0.0.1/ > "$EVIDENCE/application.html"
    sha256sum /var/www/html/index.html > "$EVIDENCE/application-sha256.txt"
    tar -C "$HOME/ol-migration-evidence" -czf "$HOME/ol-migration-evidence/oracle-after.tar.gz" after
    ```

## Task 2: Compare and pass the final validation gate

1. Compare the before and after evidence:

    ```bash
    EVIDENCE="$HOME/ol-migration-evidence/after"
    diff -u "$HOME/ol-migration-evidence/before/os-release.txt" "$EVIDENCE/os-release.txt" || true
    diff -u "$HOME/ol-migration-evidence/before/repositories.txt" "$EVIDENCE/repositories.txt" || true
    diff -u "$HOME/ol-migration-evidence/before/application-sha256.txt" "$EVIDENCE/application-sha256.txt"
    diff -u "$HOME/ol-migration-evidence/before/selinux.txt" "$EVIDENCE/selinux.txt" || true
    ```

    OS identity and repository differences are expected. The webpage checksum should match exactly because the workshop did not change the application file. Investigate an unexpected SELinux change.

2. Check the final application, services, and kernel:

    ```bash
    grep '^ID=' /etc/os-release
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VENDOR}\n'
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    systemctl --failed --no-pager
    ```

    The VM must report `ID="ol"`, an Oracle kernel package, active Apache, the workload marker, and no failed systemd units. If a check fails, preserve evidence and investigate before declaring success.

3. Review the migration report for `3rd Party`, `unavailable`, `source-vendor-retained`, or other unresolved entries. Record any accepted exception. Save copies of the report, migration log, and evidence archives outside the VM if your instructor requires them before cleanup.

## Task 3: Hand off your results

1. Record your assigned VM name, public IP, compartment, boot-volume backup name, and the final validation results. Save the migration report, log, and evidence archives outside the VM if your instructor requests them.

2. Tell the instructor that your VM is ready for review. Do not terminate the instance or delete the boot-volume backup unless the instructor specifically assigns you that cleanup task.

3. The instructor will remove participant VMs and backups after collecting any required results. Shared custom images, Object Storage objects, VCNs, and compartments remain under instructor control and must not be deleted by participants.

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, September 2026
