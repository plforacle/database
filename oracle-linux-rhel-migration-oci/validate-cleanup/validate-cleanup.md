# Lab 8: Validate and Clean Up

## Introduction

A changed operating-system identity is not enough to declare migration success. In this lab, you capture post-migration evidence, compare it with the RHEL baseline, verify the workload and security state, review exceptions, and remove every workshop resource. The final validation gate is a checklist that must pass before you declare the migration successful.

### Objectives

In this lab, you will:

- Capture post-migration evidence.
- Compare the RHEL and Oracle Linux states.
- Validate the workload, services, networking, firewall, and SELinux.
- Review package-migration exceptions.
- Remove OCI resources and the OS Management Hub registration.

### Prerequisites

Before beginning this lab, confirm that you have:

- Completed Lab 7: Manage the Migrated Instance with OS Management Hub.
- Recorded the OS Management Hub profile, package-job results, and any workshop IAM changes.
- SSH access to the migrated Oracle Linux 9.8 instance.
- The RHEL baseline evidence bundle from Lab 2.
- A healthy post-migration Apache workload.
- Permission to unregister services and delete every OCI resource created by the workshop.
- Approval to perform the cleanup tasks, which permanently delete the workshop resources.

Estimated Lab Time: 35 minutes

## Task 1: Capture post-migration evidence

1. Create the post-migration directory:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-migration-evidence/after"
    EVIDENCE="$HOME/ol-migration-evidence/after"
    </copy>
    ```

2. Capture operating-system, kernel, repository, and package state:

    ```bash
    <copy>
    cat /etc/os-release > "$EVIDENCE/os-release.txt"
    uname -a > "$EVIDENCE/kernel.txt"
    sudo dnf repolist --enabled > "$EVIDENCE/repositories.txt"
    rpm -qa --qf '%{NAME}\t%{EPOCHNUM}:%{VERSION}-%{RELEASE}.%{ARCH}\t%{VENDOR}\n' \
      | sort > "$EVIDENCE/packages.tsv"
    </copy>
    ```

3. Capture service, network, firewall, and SELinux state:

    ```bash
    <copy>
    systemctl is-enabled httpd > "$EVIDENCE/httpd-enabled.txt"
    systemctl is-active httpd > "$EVIDENCE/httpd-active.txt"
    systemctl --failed --no-pager > "$EVIDENCE/failed-services.txt"
    sudo ss -lntup > "$EVIDENCE/listening-ports.txt"
    ip address show > "$EVIDENCE/addresses.txt"
    ip route show > "$EVIDENCE/routes.txt"
    getenforce > "$EVIDENCE/selinux.txt"
    sudo firewall-cmd --list-all > "$EVIDENCE/firewall.txt"
    </copy>
    ```

4. Capture the workload response and checksum:

    ```bash
    <copy>
    curl --fail --silent http://127.0.0.1/ > "$EVIDENCE/application.html"
    sha256sum /var/www/html/index.html > "$EVIDENCE/application-sha256.txt"
    </copy>
    ```

5. Archive the evidence:

    ```bash
    <copy>
    tar -C "$HOME/ol-migration-evidence" -czf \
      "$HOME/ol-migration-evidence/oracle-linux-after.tar.gz" after
    </copy>
    ```

## Task 2: Compare the before-and-after evidence

1. Compare the expected operating-system change:

    ```bash
    <copy>
    diff -u \
      "$HOME/ol-migration-evidence/before/os-release.txt" \
      "$HOME/ol-migration-evidence/after/os-release.txt" || true
    </copy>
    ```

2. Compare kernel and repository state:

    ```bash
    <copy>
    diff -u \
      "$HOME/ol-migration-evidence/before/kernel.txt" \
      "$HOME/ol-migration-evidence/after/kernel.txt" || true

    diff -u \
      "$HOME/ol-migration-evidence/before/repositories.txt" \
      "$HOME/ol-migration-evidence/after/repositories.txt" || true
    </copy>
    ```

3. Compare the application checksum:

    ```bash
    <copy>
    diff -u \
      "$HOME/ol-migration-evidence/before/application-sha256.txt" \
      "$HOME/ol-migration-evidence/after/application-sha256.txt"
    </copy>
    ```

    This command should return no difference.

4. Compare the application response:

    ```bash
    <copy>
    diff -u \
      "$HOME/ol-migration-evidence/before/application.html" \
      "$HOME/ol-migration-evidence/after/application.html"
    </copy>
    ```

    This command should return no difference.

5. Count packages by vendor before and after:

    ```bash
    <copy>
    cut -f3 "$HOME/ol-migration-evidence/before/packages.tsv" | sort | uniq -c | sort -nr | head
    cut -f3 "$HOME/ol-migration-evidence/after/packages.tsv" | sort | uniq -c | sort -nr | head
    </copy>
    ```

## Task 3: Run the final validation gate

1. Validate operating-system identity and kernel ownership:

    ```bash
    <copy>
    . /etc/os-release
    test "$ID" = ol
    test "${VERSION_ID%%.*}" = 9
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{VENDOR}\n' | grep Oracle
    </copy>
    ```

2. Validate the application and services:

    ```bash
    <copy>
    systemctl is-enabled --quiet httpd
    systemctl is-active --quiet httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

3. Validate security state:

    ```bash
    <copy>
    test "$(getenforce)" = Enforcing
    sudo firewall-cmd --query-service=http
    </copy>
    ```

4. Validate repository and package management:

    ```bash
    <copy>
    sudo dnf repolist --enabled
    sudo rpm --verifydb
    sudo dnf check
    </copy>
    ```

5. Review failed services:

    ```bash
    <copy>
    systemctl --failed --no-pager
    </copy>
    ```

    Investigate any failed unit before declaring success.

6. Verify the public endpoint again at `http://<public-ip>/`.

## Task 4: Review migration exceptions

1. Locate the newest migration run directory:

    ```bash
    <copy>
    RUN_DIR="$(sudo ls -1dt /var/lib/migrate-to-oracle-linux/* | head -1)"
    echo "$RUN_DIR"
    </copy>
    ```

2. Review packages without an Oracle replacement, if the file exists:

    ```bash
    <copy>
    sudo test -f "$RUN_DIR/unavailable-reinstall.nevra" \
      && sudo cat "$RUN_DIR/unavailable-reinstall.nevra" \
      || echo 'No unavailable package report was generated.'
    </copy>
    ```

3. Review retained or unavailable Red Hat-vendor packages:

    ```bash
    <copy>
    sudo test -f "$RUN_DIR/redhat-vendor-oracle-unavailable.tsv" \
      && sudo column -t -s $'\t' "$RUN_DIR/redhat-vendor-oracle-unavailable.tsv" \
      || echo 'No unavailable Red Hat-vendor packages were reported.'
    </copy>
    ```

4. Classify each exception as:

    - Accepted third-party application dependency
    - Package requiring a replacement
    - Package requiring application-owner validation
    - Migration blocker requiring rollback

## Task 5: Complete the migration decision

1. Declare the migration successful only when all of the following are true:

    - Oracle Linux 9.8 identity is confirmed.
    - An Oracle Linux RHCK is running.
    - Oracle Linux repositories respond.
    - The RPM database is healthy.
    - Apache is enabled and active.
    - Application content and checksum match the baseline.
    - Networking, SELinux, and firewall checks pass.
    - Package exceptions are reviewed and accepted.
    - Package update and reboot-check results are recorded.

2. If a required condition fails, decide whether to remediate or restore from the pre-migration backup.

## Task 6: Preserve evidence and review the source billing record

1. Copy the baseline, post-migration evidence, and migration logs to your local computer before terminating the VM. Use the SSH account and private key recorded in Lab 2:

    ```bash
    <copy>scp -i "<private-key-path>" -r <ssh-user>@<public-ip>:ol-migration-evidence ./</copy>
    ```

2. Review the source image and usage-based RHEL billing information recorded in Lab 1. Record any Oracle-confirmed guidance on charges after in-place conversion. A changed guest OS identity is not evidence that OCI changed its billing classification.

3. This direct-image path did not register a personal Red Hat subscription. No Red Hat portal registration cleanup is required. Complete the OCI resource and OS Management Hub cleanup below.

## Task 7: Delete OCI workshop resources

1. Before terminating the VM, open **OS Management Hub**, select **Jobs**, filter to `ol-migrate-lab`, and inspect **Scheduled jobs**. Delete any remaining workshop schedules, including `ol-migrate-update-scheduled`. Preserve job logs and inventory evidence locally before unregistering; the service history will no longer be available afterward.

2. Confirm with your administrator that the instance dynamic group has the [required permission for unregistering OCI instances](https://docs.oracle.com/en-us/iaas/osmh/doc/unregister-instance.htm). In **OS Management Hub**, open **Instances**, select the workshop instance, and select **Unregister**. Confirm that it disappears from the managed-instance list. Unregistering restores the previous repository configuration. Disabling the plugin alone leaves the management record behind.

3. In **Profiles**, delete the workshop profile recorded in Lab 7. Keep shared vendor software sources and shared IAM resources. Ask your administrator to remove only the workshop-specific policy statements and matching rules recorded in Lab 7 when they are no longer used.

4. In **Compute**, terminate `ol-migrate-rhel-source`.

5. Select the option to delete the attached boot volume and confirm termination.

6. In **Block Storage**, delete the `ol-migrate-before-conversion` boot-volume backup.

7. Delete the VCN resources created by the wizard and any service gateway and route created in Lab 7. Keep networking resources that are shared with other instances.

8. Delete the `ol-migrate-lab` compartment when your organization permits it.

9. Confirm that no workshop Compute instance, boot volume, backup, managed-instance registration, profile, or future update schedule remains. This direct-image path created no image bucket or custom image to delete.

## Task 8: Final knowledge review

1. Why was the migration performed on the same VM?

    The exercise demonstrates an in-place distribution conversion while preserving the OCI instance, workload, configuration, and evidence.

2. Why was a boot-volume backup mandatory?

    The migration changes repositories, packages, release identity, and the boot kernel. The backup provides a recovery point if the system cannot return to a validated state.

3. Why is `/etc/os-release` insufficient proof of success?

    It does not prove that the kernel, services, applications, packages, networking, security controls, and management integrations still operate correctly.

4. Which parts of the RHEL administration experience remained familiar?

    RPM and DNF package management, systemd, SELinux, firewalld, SSH, standard file locations, and common automation patterns remained recognizable.

5. Why is there no Red Hat registration record to remove?

    This OCI-provided RHEL image used RHUI repository access without personal subscription registration. Cleanup focuses on the Compute instance, storage, networking, schedules, and OS Management Hub registration.

## Learn More

- [Deleting an OCI Compute instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/terminatinginstance.htm)
- [Unregistering an OS Management Hub instance](https://docs.oracle.com/en-us/iaas/osmh/doc/unregister-instance.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026

