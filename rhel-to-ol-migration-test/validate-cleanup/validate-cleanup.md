# Lab 5: Validate and Optionally Clean Up

## Introduction

A changed operating-system identity is not enough to declare migration success. In this lab, you capture post-migration evidence, compare it with the RHEL baseline, verify the workload and security state, review exceptions, and save your results. You then choose whether to retain the instance or remove temporary resources.

### Objectives

In this lab, you will:

- Capture post-migration evidence.
- Compare the RHEL and Oracle Linux states.
- Validate the workload, services, networking, firewall, and SELinux.
- Review package-migration exceptions.
- Preserve evidence and the reusable source image.
- Optionally remove temporary OCI resources when finished.

Estimated Lab Time: 30 minutes, plus optional cleanup time

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
    test "$VERSION_ID" = 9.8
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{VENDOR}\n' | grep Oracle
    </copy>
    ```

    Confirm `ID=ol`, version 9.8, an Oracle kernel package vendor, and a running kernel name without `uek`. The exact RHCK build may differ from the original RHEL kernel. The `test` commands return no output when successful; any nonzero exit status is a failed check.

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

2. Review the unavailable source-package plan, if the file exists:

    ```bash
    <copy>
    sudo test -f "$RUN_DIR/unavailable-reinstall.nevra" \
      && sudo cat "$RUN_DIR/unavailable-reinstall.nevra" \
      || echo 'No unavailable package report was generated.'
    </copy>
    ```

    This is a plan generated before the final package replacement passes. Entries can include source release packages that were removed and firmware or branding packages replaced later. It is not the final list of retained Red Hat packages.

3. Review the final retained or unavailable Red Hat-vendor packages:

    ```bash
    <copy>
    sudo test -f "$RUN_DIR/redhat-vendor-oracle-unavailable.tsv" \
      && sudo column -t -s $'\t' "$RUN_DIR/redhat-vendor-oracle-unavailable.tsv" \
      || echo 'No unavailable Red Hat-vendor packages were reported.'
    </copy>
    ```

4. Confirm the current release, firmware, and Apache branding packages:

    ```bash
    <copy>
    rpm -q oraclelinux-release oraclelinux-release-el9 linux-firmware linux-firmware-whence oracle-logos-httpd \
      --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    rpm -q redhat-release redhat-release-eula redhat-logos-httpd
    </copy>
    ```

    Oracle replacements should be installed. The old Red Hat release, EULA, and Apache logo packages should report `is not installed`; a nonzero exit status for that second query is expected. Investigate other missing replacements rather than treating the original plan as the final state.

5. Review any packages that still identify Red Hat as their vendor:

    ```bash
    <copy>
    rpm -qa --qf '%{NAME}\t%{VERSION}-%{RELEASE}\t%{VENDOR}\n' \
      | awk -F '\t' '$3 ~ /Red Hat/ {print}' | sort
    </copy>
    ```

    In the tested October 24, 2026 run, only `insights-core` and `insights-core-selinux` remained from Red Hat. They were deliberately retained as accepted exceptions for this disposable workshop. Their presence alone did not invalidate the conversion or the application checks, and removal is not required for this exercise.

6. Record your exception decision. If only those two Insights packages remain and validation passes, record them as accepted retained packages. Review any additional retained package for application or boot impact before accepting it. Do not assume that a new exception is harmless because this test passed.

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

2. If a required condition fails, preserve logs and decide whether to remediate, restore the optional backup if one was created, or rebuild the disposable VM from `rhel-9-8-apache-source`.

3. Record the migration outcome and accepted package exceptions. A successful conversion does not require registration with a cloud management service.

## Task 6: Preserve evidence and choose whether to clean up

1. Keep the destination instance running if you want to inspect it or continue practicing. Resource deletion is optional and is not part of the migration success criteria.

2. Save the before-and-after archives to your local computer before terminating the instance. From your local terminal, run:

    ```bash
    <copy>
    scp -i <private-key-path> cloud-user@<public-ip>:ol-migration-evidence/rhel-before.tar.gz .
    scp -i <private-key-path> cloud-user@<public-ip>:ol-migration-evidence/oracle-linux-after.tar.gz .
    </copy>
    ```

3. Retain the migration reports and log if needed for review. Record the run directory shown in Task 4 and the corresponding log under `/var/log/migrate-to-oracle-linux/` before deleting the VM.

4. Keep the preparation and destination `rhel-9-8-apache-source` custom images and the prepared export object for future workshops. Keep the raw source image if it is still needed. Deleting a VM does not require deleting these reusable assets.

5. Confirm that registration was removed from the preparation VM in Lab 2. The destination instance was never registered with Red Hat, so there is no destination registration to remove.

6. If you want to keep the instance, skip Task 7 and continue to the knowledge review. If you are finished and have saved your results, follow the optional cleanup task.

## Task 7: Optionally delete temporary OCI resources

1. Confirm that you no longer need the instance and have saved the evidence you want to retain. Perform only the deletions you choose below.

2. In the destination tenancy, open **Compute** and terminate `ol-migrate-rhel-source`. Select boot-volume deletion only if you no longer need that volume.

3. If you created `ol-migrate-before-conversion` and no longer need it, delete that boot-volume backup. Skip this step if no backup was created.

4. Delete the preparation `rhel-9-8-source` VM and its boot volume only if image preparation is finished and the prepared image is retained.

5. Remove the object-read PAR when no further imports require it. Retain the exported image object and its private bucket for reuse. A new PAR can be created for a future import.

6. Delete network security groups and VCN resources only if they were created for this exercise and no retained instances or other resources depend on them. Keep shared networking.

7. Delete a workshop compartment only if it is empty and no longer needed. A compartment containing reusable images or image objects should be retained.

8. Review resources in both tenancies and record what was retained. Do not delete `rhel-9-8-apache-source` or its exported object as routine workshop cleanup.

## Task 8: Final knowledge review

1. Why was the migration performed on the same VM?

    The exercise demonstrates an in-place distribution conversion while preserving the OCI instance, workload, configuration, and evidence.

2. Why is the boot-volume backup optional for this disposable practice VM?

    A backup provides a recovery point. For this disposable workshop, the destination VM can instead be rebuilt from the prepared image. Rebuilding loses changes made after capture, so systems with valuable data need an appropriate verified recovery plan.

3. Why is `/etc/os-release` insufficient proof of success?

    It does not prove that the kernel, services, applications, packages, networking, and security controls still operate correctly.

4. Which parts of the RHEL administration experience remained familiar?

    RPM and DNF package management, systemd, SELinux, firewalld, SSH, standard file locations, and common automation patterns remained recognizable.

5. What must be cleaned up outside OCI?

    The preparation VM must be unregistered from Red Hat before image capture, as completed in Lab 2. The destination instance does not create a new Red Hat registration.

## Learn More

- [Red Hat Subscription Management](https://access.redhat.com/articles/433903)
- [Deleting an OCI Compute instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/terminatinginstance.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026

