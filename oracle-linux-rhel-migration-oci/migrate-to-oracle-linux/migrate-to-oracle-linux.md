# Lab 4: Migrate to Oracle Linux

## Introduction

Convert the fresh RHEL 9.8 source to Oracle Linux 9.8 using RHCK. The safety candidate checks kernel dependencies, rejects incomplete kernel installation, and retains source runtime kernels. Retaining a source kernel does not guarantee that converted user space can boot or run with it. The boot-volume backup remains the recovery point.

The previous script reported success despite missing kernel and initramfs files. Its final message and exit status alone do not authorize reboot. This lab requires an independent boot check and a post-reboot SSH and application check.

**Controlled result:** On October 22, 2026, workshop.5 completed conversion and actual reboot on a prepared OCI-provided RHEL 9.8 source. Oracle RHCK `5.14.0-687.58.1.el9_8.x86_64`, SSH, system and package health, SELinux Enforcing, the HTTP firewall rule, the unchanged workload checksum, and local and public HTTP checks passed. The source first required the narrowly reviewed LVM metadata-format correction in Lab 2. See the [controlled migration record](../qa/source4-run-20261022.md). This result applies to the recorded source and verified candidate; every repeat must pass its own checks and recovery checkpoint.

Preserve all failed instances, their disks, backups and evidence, the successful imported-image reference, and the shared network. Do not rerun conversion or reboot a failed guest. Use only the prepared, unconverted source identified in your current checkpoint.

Estimated Lab Time: 45 minutes

### Objectives

- Convert the fresh source with the verified safety candidate.
- Stop on dependency, transaction, kernel artifact, or boot configuration failure.
- Reboot only after all required checks pass.
- Confirm Oracle Linux RHCK, SSH, and the application after reboot.

### Prerequisites

- Review the October 22 tested path with the instructor and confirm that the actual source passes the current readiness and boot checks. A different image or package catalogue requires fresh review.
- Lab 3 passed on the fresh RHEL VM, including dependency checks and the dry run.
- The verified candidate, launcher, and `SHA256SUMS` are in `$HOME/ol-migration`.
- `console-checkpoint.json` records the current Console confirmation from Lab 3, Task 5. Cloud Shell and a local OCI CLI are not required.
- The full boot-volume backup is Available, less than 24 hours old, and was created after source preparation, any approved correction, and the successful RHEL reboot. A pre-correction backup alone does not meet this prepared-source checkpoint.
- The source image, instance, boot volume, backup, SSH account, and fresh public IP are recorded.
- Apache is healthy and returns `MIGRATION_WORKLOAD_OK`.

## Task 1: Reconfirm the conversion checkpoint

1. Connect to the fresh VM using its new public IP:

    ```bash
    <copy>
    ssh -i "<private-key-path>" <ssh-user>@<fresh-public-ip>
    </copy>
    ```

2. Verify the bundle and source state:

    ```bash
    <copy>
    cd "$HOME/ol-migration"
    sha256sum --check SHA256SUMS
    grep -E '^(ID|VERSION_ID)=' /etc/os-release
    uname -r
    sudo grubby --default-kernel
    systemctl is-active httpd
    curl --fail --silent --show-error http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Require two checksum results of OK, RHEL 9.8, the expected source kernel, and a healthy application. Do not continue on a converted or partially converted guest.

3. Save the working source boot configuration before conversion:

    ```bash
    <copy>
    (
      set -e -o pipefail
      BOOT_BEFORE="$HOME/ol-migration-evidence/boot-before"
      mkdir -p "$BOOT_BEFORE"
      record_parent_modules() {
        local LABEL=$1 DEVICE_NODE
        DEVICE_NODE=$(readlink -f "$2")
        while [[ "$DEVICE_NODE" == /sys/devices/* ]]; do
          if test -e "$DEVICE_NODE/driver/module"; then
            printf '%s %s\n' "$LABEL" "$(readlink -f "$DEVICE_NODE/driver/module")"
          fi
          DEVICE_NODE=${DEVICE_NODE%/*}
        done
        return 0
      }
      source_grub_preflight() {
          local evidence=$1 label status path target actual expected root_device hook hook_index=0
          local probe_failures=0
          local grub_error='(^|[[:space:]/])grub(2)?-[[:alnum:]_-]+:[[:space:]]*(error|fatal):'
          for target in grub2-probe findmnt lvs sha256sum rpm readlink python3; do
              command -v "$target" >/dev/null || {
                  printf 'REVIEW REQUIRED: missing source boot inspection tool %s\n' "$target" >&2
                  return 1
              }
          done
          if ! mkdir -p "$(dirname "$evidence")" || ! mkdir -m 0700 "$evidence"; then
              printf 'FAIL: use a new GRUB evidence directory; existing records are preserved: %s\n' "$evidence" >&2
              return 1
          fi
          record_grub_probe() {
              local label=$1 status scan_status
              shift
              if "$@" > "$evidence/$label.stdout" 2> "$evidence/$label.stderr"; then
                  status=0
              else
                  status=$?
              fi
              printf '%s\n' "$status" > "$evidence/$label.status" || return 1
              if grep -Eiq "$grub_error" "$evidence/$label.stdout" "$evidence/$label.stderr"; then
                  scan_status=0
              else
                  scan_status=$?
              fi
              if (( status != 0 || scan_status != 1 )); then
                  printf 'FAIL: source boot inspection %s, status %s\n' "$label" "$status" >&2
                  cat "$evidence/$label.stdout" "$evidence/$label.stderr" >&2
                  probe_failures=$((probe_failures + 1))
              fi
          }
          for path in / /boot; do
              label=root
              [[ "$path" == /boot ]] && label=boot
              for target in device drive fs fs_uuid abstraction; do
                  record_grub_probe "$label-$target" sudo -n grub2-probe --target="$target" "$path" || return 1
              done
              record_grub_probe "$label-mounted-uuid" findmnt -n -o UUID --target "$path" || return 1
              actual=$(tr -d '[:space:]' < "$evidence/$label-fs_uuid.stdout")
              expected=$(tr -d '[:space:]' < "$evidence/$label-mounted-uuid.stdout")
              if [[ -z "$actual" || "$actual" != "$expected" ]]; then
                  printf 'FAIL: GRUB and mounted filesystem UUIDs differ for %s\n' "$path" >&2
                  probe_failures=$((probe_failures + 1))
              fi
          done
          root_device=$(cat "$evidence/root-device.stdout")
          if [[ "$root_device" == /dev/* && "$root_device" != *$'\n'* ]]; then
              record_grub_probe root-device-fs sudo -n grub2-probe --device "$root_device" --target=fs || return 1
          else
              printf 'FAIL: GRUB did not identify one root device for the separate device probe\n' >&2
              probe_failures=$((probe_failures + 1))
          fi
          record_grub_probe lvs sudo -n lvs --reportformat json \
              -o vg_name,vg_uuid,lv_name,lv_uuid,lv_path || return 1
          record_grub_probe kernel-hook-inventory sudo -n python3 -c \
              'import glob,os,sys; sys.stdout.buffer.write(b"".join(os.fsencode(p)+b"\0" for p in sorted(glob.glob("/usr/lib/kernel/install.d/*.install")+glob.glob("/etc/kernel/install.d/*.install"))))' || return 1
          : > "$evidence/kernel-hook-paths.tsv" || return 1
          while IFS= read -r -d "" hook; do
              hook_index=$((hook_index + 1))
              printf '%s\t%s\t%s\n' "$hook_index" "$hook" "$(readlink -f "$hook")" \
                  >> "$evidence/kernel-hook-paths.tsv" || return 1
              if [[ ! -f "$hook" && "$(readlink -f "$hook")" != /dev/null ]]; then
                  printf 'FAIL: unreadable or unsupported kernel-install hook %s\n' "$hook" >&2
                  probe_failures=$((probe_failures + 1))
                  continue
              fi
              record_grub_probe "hook-$hook_index-content" sudo -n cat "$hook" || return 1
              record_grub_probe "hook-$hook_index-sha256" sudo -n sha256sum "$hook" || return 1
              record_grub_probe "hook-$hook_index-rpm-owner" sudo -n rpm -qf "$hook" \
                  --qf '%{NAME}\t%{VERSION}-%{RELEASE}.%{ARCH}\t%{VENDOR}\n' || return 1
          done < "$evidence/kernel-hook-inventory.stdout"
          if (( hook_index == 0 )); then
              printf 'FAIL: no kernel-install hooks were captured\n' >&2
              probe_failures=$((probe_failures + 1))
          fi
          printf 'Source GRUB inspection failures: %s; evidence: %s\n' "$probe_failures" "$evidence"
          (( probe_failures == 0 ))
      }
      source_grub_preflight "$BOOT_BEFORE/grub-preflight"
      uname -r > "$BOOT_BEFORE/running-kernel.txt"
      sudo grubby --info "/boot/vmlinuz-$(uname -r)" > "$BOOT_BEFORE/grubby.txt"
      sudo lsinitrd -m "/boot/initramfs-$(uname -r).img" > "$BOOT_BEFORE/dracut-modules.txt"
      sudo lsinitrd "/boot/initramfs-$(uname -r).img" > "$BOOT_BEFORE/initramfs.txt"
      findmnt -n -o SOURCE,FSTYPE,UUID / > "$BOOT_BEFORE/root.txt"
      lsblk -o NAME,TYPE,FSTYPE,UUID,PKNAME,MOUNTPOINTS > "$BOOT_BEFORE/storage.txt"
      ROOT_SOURCE=$(findmnt -n -o SOURCE /)
      ROOT_FSTYPE=$(findmnt -n -o FSTYPE /)
      lsblk -s -n -l -o KNAME,TYPE,TRAN "$ROOT_SOURCE" > "$BOOT_BEFORE/root-chain.txt"
      : > "$BOOT_BEFORE/root-device-paths.txt"
      for BLOCK_NAME in $(lsblk -s -n -l -o KNAME "$ROOT_SOURCE"); do
        if test -d "/sys/class/block/$BLOCK_NAME/dm"; then
          printf '%s %s\n' "$BLOCK_NAME" /sys/module/dm_mod
        fi
        if test -e "/sys/class/block/$BLOCK_NAME/device"; then
          printf '%s %s\n' "$BLOCK_NAME" "$(readlink -f "/sys/class/block/$BLOCK_NAME/device")" \
            >> "$BOOT_BEFORE/root-device-paths.txt"
          record_parent_modules "$BLOCK_NAME" "/sys/class/block/$BLOCK_NAME/device"
        fi
      done > "$BOOT_BEFORE/root-drivers.txt"
      for INTERFACE in /sys/class/net/*; do
        if test -e "$INTERFACE/device"; then
          record_parent_modules "${INTERFACE##*/}" "$INTERFACE/device"
        fi
      done > "$BOOT_BEFORE/nic-drivers.txt"
      if awk '$3 == "iscsi" {found=1} END {exit !found}' "$BOOT_BEFORE/root-chain.txt" || \
          grep -Eq '/session[0-9]+/' "$BOOT_BEFORE/root-device-paths.txt"; then
        printf 'yes\n' > "$BOOT_BEFORE/root-uses-iscsi.txt"
        for SOURCE_MODULE in /sys/module/*iscsi*; do
          if test -d "$SOURCE_MODULE"; then
            printf 'iscsi %s\n' "$SOURCE_MODULE" >> "$BOOT_BEFORE/root-drivers.txt"
          fi
        done
      else
        printf 'no\n' > "$BOOT_BEFORE/root-uses-iscsi.txt"
      fi
      {
        printf '%s\n' "$ROOT_FSTYPE"
        awk '{n=split($2,p,"/"); print p[n]}' \
          "$BOOT_BEFORE/root-drivers.txt" "$BOOT_BEFORE/nic-drivers.txt"
      } | sort -u > "$BOOT_BEFORE/required-modules.txt"
      if test -d /sys/firmware/efi; then
        printf 'UEFI\n'
      else
        printf 'BIOS\n'
      fi > "$BOOT_BEFORE/firmware-mode.txt"
    )
    SOURCE_BOOT_STATUS=$?
    printf 'Source boot capture exit status: %s\n' "$SOURCE_BOOT_STATUS"
    </copy>
    ```

    Require status `0` and readable evidence files. The read-only GRUB probes must finish without errors and identify the mounted root and boot filesystem UUIDs. Review `grub-preflight` for each probe's output, error output and status, the VG/LV UUID inventory, and kernel-install hook contents, hashes and RPM owners. Missing tools, unowned hooks or probe failures require review before conversion. An existing GRUB evidence directory blocks overwrite; retain it and use a new capture directory. Do not run kernel-install or regenerate GRUB as an inspection step. Review the actual root-device chain, parent driver modules, NIC drivers, filesystem and firmware mode. The source GRUB entry and initramfs provide the reference for Task 3. Confirm that `root-uses-iscsi.txt` agrees with the observed storage path and transport; an inherited `netroot=iscsi` argument alone does not establish iSCSI use. Resolve missing or ambiguous driver evidence before conversion. Do not start conversion if this reference is missing.

4. In the OCI Console, match the Full, Available backup's source boot-volume OCID to the boot volume attached to this fresh instance. If the backup or attachment changed, record a new Console checkpoint using Lab 3, Task 5. A backup from the preserved failed instance is not a checkpoint for this VM.

5. Record the backup creation time and the documented recovery procedure. The launcher matches the instance ID in the recorded confirmation to OCI Instance Metadata Service v2 and checks the required fields and timestamps. It cannot authenticate a manual Console confirmation, discover the current boot-volume attachment, or query live backup state. Your Console check of the instance, volume, backup type, and Available state remains mandatory.

## Task 2: Execute conversion with recorded status

1. Run the launcher from the SSH session. Use an explicit evidence directory in your home directory:

    ```bash
    <copy>
    cd "$HOME/ol-migration"
    sudo python3 ./run-migration.py \
      --console-checkpoint "$PWD/console-checkpoint.json" \
      --evidence-dir "$HOME/ol-migration-evidence/conversion"
    MIGRATION_STATUS=$?
    printf 'Launcher exit status: %s\n' "$MIGRATION_STATUS"
    </copy>
    ```

    The launcher validates bundle checksums, source identity, instance identity, and the required manual checkpoint fields before executing the candidate. It labels this evidence as a manual Console confirmation. Before changing source packages, the candidate checks the exact Oracle kernel and replacement transaction plans. Expected `Operation aborted.` messages in these preflight plans mean DNF deliberately declined them without applying changes; the candidate must report that each solver passed before conversion continues. It records output and statuses for conversion, the independent boot check, and `dnf check`. It never reboots the VM. Existing CLI users may use the original paired `--backup-json` and `--attachment-json` options instead of `--console-checkpoint`.

    Require this recorded order within workshop.5: validate the coherent repository catalogue and 9.8 release gate; apply general replacements with runtime kernels and deferred shim/GRUB excluded; check runtime dependencies; install the explicit Oracle kernel and verify its initial boot files; apply the deferred bootloader group; then reassert the selected default and verify the complete boot state again. These are candidate stages, not commands to perform manually. A solved source-state bootloader plan may predict the selected Oracle kernel; the actual deferred transaction must not add or remove runtime kernels.

2. Keep the session open. If it disconnects, do not launch another conversion. Establish whether the existing run is still active from a new session on this fresh VM. A lost session or an OCI Running state does not establish migration success.

3. Require launcher exit status `0` and successful results for every stage. If any stage fails, or if the result is unknown, **stop without rebooting**. Preserve launcher evidence and the upstream run directory and log shown in the output. Do not rerun conversion, remove kernels, force packages, or attempt ad hoc dependency repairs on the converted guest. End this rehearsal and use its recorded recovery point.

4. The safety candidate retains source runtime kernels, stops on known RPM scriptlet and loader errors during the kernel transaction, and verifies boot artifacts before and after remaining package work. A successful dry run does not exercise these transactions.

## Task 3: Pass the independent pre-reboot check

1. Confirm Oracle Linux identity, repositories, and package consistency:

    ```bash
    <copy>
    cat /etc/os-release
    rpm -q oraclelinux-release oraclelinux-release-el9
    sudo dnf repolist --enabled
    sudo rpm --verifydb
    sudo dnf check
    </copy>
    ```

    Require Oracle Linux 9.8, usable Oracle repositories, and no RPM or dependency errors. The running kernel may still be the original RHEL kernel before reboot.

2. Repeat the candidate's read-only boot verification immediately before reboot and record its actual status:

    ```bash
    <copy>
    sudo bash ./migrate-to-oracle-linux.sh \
      --verify-boot-only --target-version 9.8 --kernel rhck
    BOOT_CHECK_STATUS=$?
    printf 'Boot check exit status: %s\n' "$BOOT_CHECK_STATUS"
    </copy>
    ```

    Require status `0`. The check must find an Oracle-owned RHCK, a nonempty kernel image and initramfs, a matching module tree and dependency file, a readable initramfs archive, a matching GRUB kernel and initrd entry, and the selected kernel as default. It also checks runtime dependencies used during kernel installation. An RPM database entry alone is insufficient.

3. Inspect and retain the selected boot entry:

    ```bash
    <copy>
    DEFAULT_KERNEL=$(sudo grubby --default-kernel)
    printf '%s\n' "$DEFAULT_KERNEL"
    sudo grubby --info "$DEFAULT_KERNEL"
    rpm -qf "$DEFAULT_KERNEL" --qf '%{NAME} %{VERSION}-%{RELEASE}.%{ARCH} %{VENDOR}\n'
    rpm -qa 'kernel*' | sort
    </copy>
    ```

    The default must be the verified Oracle RHCK. Review retained Red Hat runtime packages as deliberate exceptions. Do not remove them before the first validated boot.

4. Inspect the target initramfs and compare its storage and network boot configuration with the source reference:

    ```bash
    <copy>
    (
      set -e -o pipefail
      BOOT_BEFORE="$HOME/ol-migration-evidence/boot-before"
      BOOT_AFTER="$HOME/ol-migration-evidence/boot-after"
      mkdir -p "$BOOT_AFTER"
      DEFAULT_KERNEL=$(sudo grubby --default-kernel)
      TARGET_KERNEL_VERSION=${DEFAULT_KERNEL#/boot/vmlinuz-}
      TARGET_INITRAMFS="/boot/initramfs-${TARGET_KERNEL_VERSION}.img"
      sudo grubby --info "$DEFAULT_KERNEL" > "$BOOT_AFTER/grubby.txt"
      sudo lsinitrd -m "$TARGET_INITRAMFS" > "$BOOT_AFTER/dracut-modules.txt"
      sudo lsinitrd "$TARGET_INITRAMFS" > "$BOOT_AFTER/initramfs.txt"
      findmnt -n -o SOURCE,FSTYPE,UUID / > "$BOOT_AFTER/root.txt"
      diff -u "$BOOT_BEFORE/root.txt" "$BOOT_AFTER/root.txt"
      cat "$BOOT_BEFORE/grubby.txt" "$BOOT_AFTER/grubby.txt"
      cat "$BOOT_BEFORE/dracut-modules.txt" "$BOOT_AFTER/dracut-modules.txt"
      cat "$BOOT_BEFORE/root-chain.txt" "$BOOT_BEFORE/root-device-paths.txt"
      cat "$BOOT_BEFORE/firmware-mode.txt" "$BOOT_BEFORE/root-uses-iscsi.txt"
      test -s "$BOOT_BEFORE/required-modules.txt"
      while IFS= read -r DRIVER; do
        printf '\nTarget dependencies for %s:\n' "$DRIVER"
        sudo modprobe -S "$TARGET_KERNEL_VERSION" --show-depends "$DRIVER"
      done < "$BOOT_BEFORE/required-modules.txt" | tee "$BOOT_AFTER/required-driver-paths.txt"
    )
    STORAGE_BOOT_STATUS=$?
    printf 'Storage boot inspection exit status: %s\n' "$STORAGE_BOOT_STATUS"
    </copy>
    ```

    Require status `0` and no change to the root device, filesystem or UUID. These commands do not load drivers. Check the following in the saved entries and archive listings:

    - The target GRUB `root` value identifies the same root UUID or logical volume. Preserve all source storage and network boot arguments, including any `netroot=iscsi`, `rd.iscsi.firmware=1`, `rd.lvm.lv`, `ip`, `ifname`, `bootdev` and `rd.neednet` values. Do not remove inherited arguments or replace working values based on a guessed transport.
    - Compare every remaining boot argument and document any difference before reboot. In the accepted source-4 run, the only non-version argument difference was `crashkernel`: an Oracle-owned, RPM-verified kernel-install hook selected the vendor default `1G-64G:448M,64G-:512M`. The initial strict comparison stopped; a separate review verified that provenance and unchanged storage and network arguments before accepting this specific crash-dump policy change. No BLS entry was edited. Do not globally ignore `crashkernel` differences or other argument changes. Preserve the original discrepancy and the instructor's evidence-based decision; an unexplained or unacceptable difference remains a stop condition.
    - The target initramfs supplies the observed root-storage, device-mapper, filesystem and NIC driver dependencies recorded in `required-driver-paths.txt`. For the tested source-4, the observed path was VirtIO SCSI with LVM and XFS: `sd_mod`, `virtio_scsi`, `virtio_pci`, `dm_mod` and `xfs`, with `virtio_net` for the NIC. Use the captured source evidence if another VM differs. Archive paths can start with `usr/lib/modules` instead of `lib/modules`. A driver built into the target kernel needs no separate archive file.
    - Require `lvm` tooling and the source-equivalent `lvm`/`dm` support when the observed root uses LVM. Require iSCSI transport drivers, the `iscsi` dracut module and `iscsistart` only when the actual source root uses iSCSI. The tested source-4's working initramfs had no `iscsi` module or `iscsistart`; inherited iSCSI boot arguments did not make them mandatory for its VirtIO root. Network provider names can differ, such as `network-manager` and `network-legacy`, while still supplying the required network configuration.
    - Review any `etc/cmdline.d/*.conf` files listed in the target archive. Use `sudo lsinitrd <target-initramfs-path> <listed-file-path>` with the paths from the listing to read a file. Embedded root or network settings must agree with the working source configuration.
    - Match the source firmware mode. For BIOS, require the Oracle `grub2-pc` and `grub2-pc-modules` packages and a correct GRUB/BLS kernel entry. For UEFI, require the candidate's Oracle shim/GRUB EFI payload checks to pass. Do not impose EFI-file requirements on a BIOS source. These inspections still need an actual boot test.

    **Stop without rebooting** if the baseline is missing, an observed required driver or tool is absent, boot arguments conflict, or the comparison is unresolved. Save the inspection evidence for the instructor. A readable initramfs containing arbitrary kernel modules does not establish that it can reach this VM's actual root filesystem.

5. Verify Apache and the local workload:

    ```bash
    <copy>
    systemctl is-active httpd
    curl --fail --silent --show-error http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

6. In the normal SSH login shell, export the protected reports and launcher evidence to an archive owned by your SSH account:

    ```bash
    <copy>
    sudo tar -C / -czf - var/log/migrate-to-oracle-linux var/lib/migrate-to-oracle-linux \
      > "$HOME/ol-migration-evidence/migration-system-reports.tar.gz"
    sudo tar -C "$HOME/ol-migration-evidence" -czf - . \
      > "$HOME/ol-migration-evidence-before-reboot.tar.gz"
    </copy>
    ```

    Require both archives to complete successfully. From your local terminal, copy the archive before reboot:

    ```bash
    <copy>
    scp -i "<private-key-path>" \
      <ssh-user>@<fresh-public-ip>:ol-migration-evidence-before-reboot.tar.gz ./
    </copy>
    ```

    The archive includes the protected migration logs, state reports, and launcher output. Keep it with the baseline and backup record.

7. Continue only when conversion status, the repeated boot check, storage and network boot inspection, package checks, and workload checks all passed and evidence is saved. The instructor must review any discrepancy. These checks do not substitute for the actual reboot and reconnect test.

## Task 4: Reboot and verify the running Oracle kernel

1. After Task 3 passes, reboot the fresh VM:

    ```bash
    <copy>
    sudo reboot
    </copy>
    ```

2. Reconnect to the fresh VM with the recorded key and account:

    ```bash
    <copy>
    ssh -i "<private-key-path>" <ssh-user>@<fresh-public-ip>
    </copy>
    ```

    Running in the OCI Console does not prove guest boot or SSH readiness. If reconnect fails, record the failure and stop this rehearsal. Preserve this instance, its disk, and its backup for the recovery procedure. Do not resume debugging the original failed VM.

3. Verify the operating system and kernel actually running:

    ```bash
    <copy>
    cat /etc/os-release
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VERSION}-%{RELEASE}.%{ARCH} %{VENDOR}\n'
    sudo grubby --default-kernel
    test "$(sudo grubby --default-kernel)" = "/boot/vmlinuz-$(uname -r)"
    </copy>
    ```

    Require `ID="ol"`, `VERSION_ID="9.8"`, an Oracle-owned RHCK without `uek` in its release, and equality between the running and default kernel. Booting a retained RHEL kernel does not pass this lab.

## Task 5: Verify services and the application

1. Check the service, workload, and security state:

    ```bash
    <copy>
    systemctl --failed --no-pager
    systemctl is-enabled httpd
    systemctl is-active httpd
    curl --fail --silent --show-error http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    getenforce
    sudo firewall-cmd --query-service=http
    sudo dnf check
    </copy>
    ```

    Require no failed units, Apache enabled and active, the workload marker, SELinux Enforcing, the HTTP firewall rule, and no dependency errors.

2. Open `http://<fresh-public-ip>/` in your browser. Confirm the workshop page remains reachable through the reused network.

3. Compare the application checksum with the RHEL baseline:

    ```bash
    <copy>
    sha256sum --check "$HOME/ol-migration-evidence/before/application-sha256.txt"
    </copy>
    ```

4. Record post-reboot results in the rehearsal record. Declare this conversion successful only after SSH, the running Oracle RHCK, package consistency, security state, and workload checks pass. Continue to Lab 5 after instructor acceptance. Do not remove source kernels or the backup at this checkpoint.

## Learn More

- [Pinned migration script source](https://github.com/oracle/migrate-to-ol/blob/82947c960535cfbb632977c70b0fea67f8e0de7a/migrate-to-oracle-linux.sh)
- [Oracle Linux 9 boot management](https://docs.oracle.com/en/operating-systems/oracle-linux/9/boot/index.html)
- [Changing the default kernel with grubby](https://docs.oracle.com/en/operating-systems/oracle-linux/9/boot/boot-UsinggrubbyToManageKernels_change_the_default_kernel.html)
- [Dracut initramfs inspection and boot arguments](https://cdn.kernel.org/pub/linux/utils/boot/dracut/dracut.html)
- [Boot-volume backups](https://docs.oracle.com/en-us/iaas/Content/Block/Concepts/bootvolumebackups.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
