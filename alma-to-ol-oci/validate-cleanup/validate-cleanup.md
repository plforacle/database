# Lab 8: Validate and Clean Up

## Introduction

A changed operating-system identity is not enough to declare migration success. In this lab, you capture post-migration evidence, compare it with the AlmaLinux baseline, verify the workload and security state, review exceptions, and remove the disposable resources recorded in your ledger. The final validation gate is a checklist that must pass before you declare the migration successful.

### Objectives

In this lab, you will:

- Capture post-migration evidence.
- Compare the AlmaLinux and Oracle Linux states.
- Validate the workload, services, networking, firewall, and SELinux.
- Review package-migration exceptions.
- Export Linux and OS Management Hub evidence.
- Unregister the instance and remove workshop-specific management resources.
- Remove disposable OCI resources.

### Prerequisites

Before beginning this lab, confirm that you have:

- Recorded Lab 6 OS Management Hub registration and job results.
- Recorded Lab 7 Ksplice results, including any unsupported-kernel or no-applicable-update result.
- SSH access to the migrated Oracle Linux 9 instance.
- The AlmaLinux baseline evidence bundle from Lab 2.
- A healthy post-migration Apache workload.
- Permission to delete the disposable OCI resources listed in your workshop ledger.
- Approval to perform the cleanup tasks, which permanently delete the workshop resources.

Estimated Lab Time: 40 minutes

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
    rpm -q cloud-init oracle-cloud-agent > "$EVIDENCE/cloud-packages.txt" 2>&1 || true
    systemctl is-enabled httpd > "$EVIDENCE/httpd-enabled.txt"
    systemctl is-active httpd > "$EVIDENCE/httpd-active.txt"
    systemctl --failed --no-pager > "$EVIDENCE/failed-services.txt"
    sudo ss -lntup > "$EVIDENCE/listening-ports.txt"
    ip address show > "$EVIDENCE/addresses.txt"
    ip route show > "$EVIDENCE/routes.txt"
    systemctl status cloud-init oracle-cloud-agent --no-pager > "$EVIDENCE/cloud-services.txt" 2>&1 || true
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

5. Archive the evidence, including the OS Management Hub and Ksplice records. Create an empty Ksplice folder if the running kernel was unsupported and you skipped live patching:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-migration-evidence/ksplice"
    tar -C "$HOME/ol-migration-evidence" -czf \
      "$HOME/ol-migration-evidence/oracle-linux-after.tar.gz" after osmh ksplice
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

    OS Management Hub registration can replace the earlier repository configuration with attached service-managed sources. Review that expected change against Lab 6's saved repository evidence.

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

## Task 4: Check OCI integrations

1. Compare the recorded network addresses and routes. A distribution conversion should not require new OCI network resources. Verify SSH and the browser test through the same lab address.

2. Check cloud-init and Oracle Cloud Agent if they were present before migration:

    ```bash
    <copy>
    rpm -q cloud-init oracle-cloud-agent
    systemctl status cloud-init oracle-cloud-agent --no-pager
    </copy>
    ```

    An absent package is not a migration failure if it was absent in the baseline. For an installed agent, verify the plugins you use in the OCI Console. Do not assume conversion installs or configures all platform-image integrations.

3. Verify that the instance is Active in OS Management Hub, its inventory is populated, and Lab 6 job outcomes are recorded. This agent is required for the new management lab even if it was absent before migration. Record any Ksplice job result from Lab 7.

4. Review the before-and-after network, firewall, and SELinux files. Resolve unexpected differences before declaring success.

## Task 5: Review migration exceptions

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

3. Review the general package map for retained AlmaLinux packages and unavailable replacements:

    ```bash
    <copy>
    sudo awk -F '	' 'NR == 1 || $7 ~ /source-vendor-retained|unavailable|downgraded/ || $6 ~ /AlmaLinux/' \
      "$RUN_DIR/migration-rpm-map.tsv"
    </copy>
    ```

4. Classify each exception as:

    - Accepted third-party application dependency
    - Package requiring a replacement
    - Package requiring application-owner validation
    - Migration blocker requiring rollback

## Task 6: Complete the migration decision

1. Declare the migration successful only when all of the following are true:

    - Oracle Linux 9.8 was confirmed immediately after conversion; the current Oracle Linux 9 release is recorded after maintenance.
    - An Oracle Linux RHCK is running.
    - Oracle Linux repositories respond.
    - The RPM database is healthy.
    - Apache is enabled and active.
    - Application content and checksum match the baseline.
    - Networking, SELinux, and firewall checks pass.
    - Package exceptions are reviewed and accepted.
    - Package update and reboot-check results are recorded.

2. If a required condition fails, decide whether to remediate or restore from the pre-migration backup.

## Task 7: Export evidence

1. Archive the migration logs and reports before deleting the VM:

    ```bash
    <copy>
    sudo tar -C / -czf /tmp/alma-to-ol-migration-reports.tar.gz \
      var/lib/migrate-to-oracle-linux var/log/migrate-to-oracle-linux
    sudo chown "$(id -u):$(id -g)" /tmp/alma-to-ol-migration-reports.tar.gz
    </copy>
    ```

2. From your local Windows PowerShell terminal, download the reports and evidence. Replace the key path with the full path to your private key file. The second remote path is relative to the SSH user's Linux home directory:

    ```powershell
    <copy>
    scp -i "<private-key-path>" "<ssh-user>@<public-ip>:/tmp/alma-to-ol-migration-reports.tar.gz" .
    scp -i "<private-key-path>" "<ssh-user>@<public-ip>:ol-migration-evidence/oracle-linux-after.tar.gz" .
    </copy>
    ```

3. Save the OS Management Hub profile, inventory, job messages, and change-history screenshots from Labs 6 and 7 on your workstation. Verify the downloaded archive and ledger are available before proceeding. Unregistration removes the service history.

## Task 8: Unregister OS Management Hub and remove lab management resources

1. Match each planned deletion to a workshop-owned OCID. Keep shared sources, existing groups, and existing policies. Keep the instance and the Lab 6 IAM policy available until unregistration completes.

2. In **OS Management Hub**, open **Jobs**, filter by `alma-to-ol-lab`, and inspect **Scheduled jobs**. Delete the workshop jobs in the ledger, including `alma-to-ol-security-once` if it remains listed. Open a job's **Actions**, select **Delete**, and confirm. Wait for any running work request to finish first. See [deleting a scheduled job](https://docs.oracle.com/en-us/iaas/osmh/doc/delete-scheduled-job.htm).

3. Open **OS Management Hub**, **Instances**, and the migrated VM. Confirm the ledger's Compute and managed-instance OCIDs, then select **Unregister**. Verify it leaves the managed-instance list. The Lab 6 `INSTANCE_UPDATE` policy allows the service to disable the plugin. Check the Compute instance's Oracle Cloud Agent panel for the disabled OS Management Hub plugin. Simply disabling the plugin does not unregister the managed instance. See [unregistering an instance](https://docs.oracle.com/en-us/iaas/osmh/doc/unregister-instance.htm).

4. While the VM still exists, inspect `sudo dnf repolist --enabled`. Unregistration restores the repository configuration from before registration. Compare it with the `osmh/repositories-before.txt` evidence. Investigate unregistration errors before deleting IAM resources or terminating the VM.

5. In **OS Management Hub**, **Profiles**, select `alma-to-ol-lab`, open the ledger's `alma-to-ol-ol9-profile`, and delete it. Keep root vendor sources and service-provided profiles because they may be shared. See [deleting a profile](https://docs.oracle.com/en-us/iaas/osmh/doc/delete-profile.htm).

6. As an administrator, delete only the root policy `alma-to-ol-osmh-policy` and dynamic group `alma-to-ol-osmh-instances` created for this workshop, after checking their OCIDs and dependencies. The policy resides in the root compartment, and the dynamic group resides in its identity domain. Keep the existing user group used in Lab 6 and any pre-existing shared IAM resources.

## Task 9: Remove disposable Compute and network resources

1. Match the remaining resources to workshop-owned OCIDs. Terminate `alma-to-ol-source` and select deletion of its disposable boot volume. Remove `alma-to-ol-recovery-test` and its restored boot volume if they still exist.

2. Delete the workshop backup `alma-to-ol-before-conversion` only after evidence is saved and the recovery point is no longer needed. Remove unused restored lab volumes recorded as workshop-owned.

3. In **Networking**, open `alma-to-ol-vcn` and choose **Delete VCN**. Review its dependencies. Remove remaining lab VNIC attachments or other blocking resources, then delete the dedicated VCN and its wizard-created resources. Keep shared networks.

4. In **Identity & Security**, **Compartments**, open `alma-to-ol-lab`. After the lab resources are deleted, choose **Delete compartment** when your permissions allow it. Check for remaining regional resources if deletion is blocked.

5. Check the ledger. Confirm removal of the workshop jobs, managed-instance registration, profile, policy, dynamic group, Compute instances, boot volumes, backup, network, and compartment. Confirm pre-existing resources remain.

## Task 10: Final knowledge review

1. Why was the migration performed on the same VM?

    The exercise demonstrates an in-place distribution conversion while preserving the OCI instance, workload, configuration, and evidence.

2. Why was a boot-volume backup mandatory?

    The migration changes repositories, packages, release identity, and the boot kernel. The backup provides a recovery point if the system cannot return to a validated state.

3. Why is `/etc/os-release` insufficient proof of success?

    It does not prove that the kernel, services, applications, packages, networking, security controls, and management integrations still operate correctly.

4. Which parts of the AlmaLinux administration experience remained familiar?

    RPM and DNF package management, systemd, SELinux, firewalld, SSH, standard file locations, and common automation patterns remained recognizable.

5. Which source resources must remain?

    Keep any pre-existing or shared resources outside the lab. Remove the new workshop resources in the ledger after saving evidence.

6. Why must the instance be unregistered before deleting it?

    Unregistration removes its OS Management Hub record and restores the earlier repository configuration. Save the service history first and keep the cleanup IAM policy until unregistration completes.

## Learn More

- [Oracle Cloud Agent](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/manage-plugins.htm)
- [Deleting an OCI Compute instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/terminatinginstance.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Perside Foster with Codex assistance, October 2026
