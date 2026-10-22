# Lab 8: Validate and Clean Up

## Introduction

A changed operating-system identity is not enough to declare migration success. In this lab, you capture post-migration evidence, compare it with the RHEL baseline, verify the workload and security state, review exceptions, and remove only the resources approved for cleanup. The final validation gate is a checklist that must pass before you declare the migration successful.

**Preserve-list requirement:** Keep the original failed migration instance and its boot volume. Record their OCIDs and exclude them from every cleanup action. Keep the existing network and compartment while either preserved resource or another instance depends on them. A repeated lab name is not sufficient to identify a deletion target.

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
    test "$VERSION_ID" = 9.8
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{VENDOR}\n' | grep Oracle
    test "$(sudo grubby --default-kernel)" = "/boot/vmlinuz-$(uname -r)"
    </copy>
    ```

    Repeat Lab 4, Task 3's independent boot-artifact, storage and network inspection against the current Oracle RHCK. Save it in a new final-review directory without overwriting the migration or maintenance records. Require a nonempty kernel image and matching initramfs, module tree and dependency file; a readable initramfs; an Oracle-owned default kernel matching the running RHCK; and a matching GRUB/BLS kernel and initrd entry. Direct root and `/boot` GRUB probes must agree with mounted filesystem UUIDs. The root device, LVM identities and mappings must still match the accepted source. Verify the observed root-storage, LVM, filesystem and NIC dependencies and tooling inside the target initramfs as in Lab 4. Preserve the accepted storage and network arguments and the separately reviewed Oracle crash-reservation default. An unexplained difference remains a stop condition. Retained source kernels are deliberate exceptions; they do not replace an actual Oracle RHCK boot.

    **Post-Ksplice verification:** The frozen workshop.5 candidate passed its boot checks before Ksplice installation. After Lab 6, do not assume the same executable layout. In the October 22 run, Oracle Uptrack `1.2.80` deliberately replaced the `depmod` and `modprobe` links with package-owned Bash wrappers. The candidate's repeated `--verify-boot-only` returned `1` because it attempted `ldd` on the `depmod` script as though it were an ELF executable. Preserve that original STOP result. It is not a candidate pass, and it does not waive the independent final boot checks above.

    If, and only if, the failure is this exact Uptrack wrapper case, have the instructor review the installed package provenance and link chain with these non-changing commands:

    ```bash
    <copy>
    rpm -q uptrack kmod
    rpm -qf /sbin/depmod.ksplice /sbin/ksplice-depmod /sbin/modprobe.ksplice \
      /usr/bin/kmod /usr/bin/bash /usr/bin/python3 \
      --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    sudo rpm -V --noconfig uptrack
    sudo rpm -V bash python3 python3-libs libattr libacl coreutils
    DRACUT_OWNER=$(rpm -qf /usr/lib/dracut/dracut-install --qf '%{NAME}')
    sudo rpm -V "$DRACUT_OWNER"
    sudo rpm -V kmod
    KMOD_VERIFY_STATUS=$?
    printf 'kmod verification status: %s\n' "$KMOD_VERIFY_STATUS"
    readlink /usr/sbin/depmod /usr/sbin/modprobe
    readlink -f /sbin/depmod.ksplice-orig /sbin/modprobe.ksplice-orig
    head -n 1 /sbin/depmod.ksplice /sbin/ksplice-depmod /sbin/modprobe.ksplice
    sudo rpm -q --scripts uptrack
    </copy>
    ```

    Run and record each command's status; all commands above must return `0` except for the specifically reviewed `rpm -V kmod` result. Require Oracle package ownership, clean Uptrack non-configuration payload verification, and clean interpreter, library, coreutils and dracut-helper-owner verification. Do not display Ksplice credentials or configuration secrets. For the tested layout, `rpm -V kmod` returns `1` with exactly these two link-target differences and no other output:

    ```text
    ....L....    /usr/sbin/depmod
    ....L....    /usr/sbin/modprobe
    ```

    The links must point to `/sbin/depmod.ksplice` and `/sbin/modprobe.ksplice`; both `.ksplice-orig` paths must resolve to `/usr/bin/kmod`. The verified wrappers must use the verified Bash interpreter, and the package scriptlets and payload must explain the intentional interposition. `/sbin` and `/usr/sbin` identify the same directory on this image. Unknown wrappers, an unexplained link, missing files, digest or size differences, any additional kmod difference, or a different unexplained package layout remain stop conditions.

    Check the actual ELF executables, interpreters and helper dependencies, then use supported read-only functional commands:

    ```bash
    <copy>
    (
      set -e -o pipefail
      for executable in /usr/bin/kmod /usr/bin/bash /usr/bin/python3 \
        /usr/lib/dracut/dracut-install /usr/bin/cp /usr/bin/install; do
        printf '\nELF header and dependencies: %s\n' "$executable"
        od -An -tx1 -N4 "$executable"
        ldd "$executable"
      done
      /usr/lib/dracut/dracut-install --help
      dracut --list-modules
      depmod --version
      modprobe --version
      sudo grubby --default-kernel
    )
    WRAPPER_RUNTIME_STATUS=$?
    printf 'Wrapper runtime review status: %s\n' "$WRAPPER_RUNTIME_STATUS"
    </copy>
    ```

    Require status `0`, ELF magic `7f 45 4c 46` for every listed executable, and no missing library or loader error. A script can correctly make `ldd` return `1` with `not a dynamic executable`, but that observation alone never passes dependency verification; inspect and verify its interpreter and real executable as above. Do not globally ignore `ldd` failures. `dracut --version` and `grubby --version` are not the supported functional checks used for this review.

    Accept only this fully explained wrapper limitation after all other independent boot, package, security and workload checks pass. Save the original failed candidate result, command outputs and statuses, package and link evidence, and the instructor's separate decision tied to the captured evidence hashes. Do not relabel the candidate result as zero, remove Uptrack, relink commands, rerun conversion, or change the frozen candidate to obtain a pass. The October 22 source-4 run used this separate acceptance after all remaining boot checks passed; a new source or unexplained failure requires its own review.

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

4. In **Compute**, select the fresh, successfully validated rehearsal instance by its recorded OCID. Confirm that its OCID is absent from the preserve list before terminating it. Do not select the original failed instance.

5. Check the attached boot-volume OCID against the fresh run's record and the preserve list before choosing to delete it. Preserve the original failed boot volume. Confirm termination only for the approved fresh instance and disk.

6. In **Block Storage**, delete only the backup OCID for the completed fresh run after its evidence and recovery test are accepted. Do not select a backup by name alone.

7. Keep the reused VCN, subnets, gateways, security rules, and routes while the preserved failed instance or another VM uses them. Delete only separate resources confirmed to belong exclusively to the completed fresh run.

8. Keep the compartment containing preserved resources. Delete an empty, separate rehearsal compartment only when your organization permits it.

9. Reconcile the completed run's resources against the preserve list. Confirm that its approved cleanup is complete and that the original failed instance, failed boot volume, and shared network remain. This direct-image path created no image bucket or custom image to delete.

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

