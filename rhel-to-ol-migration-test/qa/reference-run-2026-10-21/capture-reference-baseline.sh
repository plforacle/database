#!/usr/bin/env bash
# Capture evidence only. Does not install packages, change services, or reboot.
set -uo pipefail
umask 077

sudo -v || exit 1
evidence_root="$HOME/ol-migration-evidence"
stamp="$(date -u +%Y%m%dT%H%M%SZ)"
out="$evidence_root/before-$stamp"
mkdir -p "$evidence_root" || exit 1
mkdir "$out" || exit 1
printf 'file\texit_status\n' > "$out/exit-statuses.tsv"
failures=0

capture() {
    local name="$1" rc
    shift
    printf '%q ' "$@" >> "$out/commands.txt"
    printf '\n' >> "$out/commands.txt"
    "$@" > "$out/$name" 2>&1
    rc=$?
    printf '%s\t%s\n' "$name" "$rc" >> "$out/exit-statuses.tsv"
    printf '%s: exit=%s\n' "$name" "$rc"
    if (( rc != 0 )); then failures=$((failures + 1)); fi
}

package_inventory() {
    rpm -qa --qf '%{NAME}\t%{EPOCHNUM}:%{VERSION}-%{RELEASE}.%{ARCH}\t%{VENDOR}\n' | LC_ALL=C sort
}

firmware_mode() {
    if [[ -d /sys/firmware/efi ]]; then printf 'UEFI\n'; else printf 'BIOS\n'; fi
}

capture captured-at.txt date -u --iso-8601=seconds
capture capture-helper-sha256.txt sha256sum "$0"
capture os-release.txt cat /etc/os-release
capture kernel.txt uname -a
capture repositories.txt sudo dnf repolist --enabled
capture packages.tsv package_inventory
capture rpm-database.txt sudo rpm --verifydb
capture package-history.txt sudo dnf history list
capture update-transaction-1.txt sudo dnf history info 1
capture subscription-identity.txt sudo subscription-manager identity
capture httpd-enabled.txt systemctl is-enabled httpd
capture httpd-active.txt systemctl is-active httpd
capture firewalld-active.txt systemctl is-active firewalld
capture failed-services.txt systemctl --failed --no-pager
capture listening-ports.txt sudo ss -lntup
capture addresses.txt ip address show
capture routes.txt ip route show
capture selinux.txt getenforce
capture firewall.txt sudo firewall-cmd --list-all
capture application.html curl --fail --silent --show-error http://127.0.0.1/
capture application-marker.txt grep -F MIGRATION_WORKLOAD_OK "$out/application.html"
capture application-sha256.txt sha256sum /var/www/html/index.html
capture firmware-mode.txt firmware_mode
capture block-devices.txt lsblk -o NAME,SIZE,TYPE,FSTYPE,UUID,MOUNTPOINTS
capture mounts.txt findmnt
capture fstab.txt cat /etc/fstab
capture kernel-command-line.txt cat /proc/cmdline
capture boot-default-kernel.txt sudo grubby --default-kernel
capture boot-default-entry.txt sudo grubby --info=DEFAULT
capture boot-entries.txt sudo grubby --info=ALL
capture grub-environment.txt sudo grub2-editenv list
capture boot-files.txt sudo ls -l /boot /boot/loader/entries
capture boot-filesystems-space.txt df -h / /boot /boot/efi

default_kernel="$(cat "$out/boot-default-kernel.txt")"
if [[ "$default_kernel" == /boot/vmlinuz-* && -s "$default_kernel" ]]; then
    kernel_version="${default_kernel#/boot/vmlinuz-}"
    capture default-kernel-config.txt sudo cat "/boot/config-$kernel_version"
    capture default-initramfs-modules.txt sudo lsinitrd -m "/boot/initramfs-$kernel_version.img"
    capture default-initramfs-contents.txt sudo lsinitrd "/boot/initramfs-$kernel_version.img"
    capture kernel-module-builtins.txt cat "/lib/modules/$kernel_version/modules.builtin"
else
    printf 'Cannot identify a nonempty default kernel file.\n' > "$out/boot-capture-error.txt"
    printf 'boot-capture-error.txt\t1\n' >> "$out/exit-statuses.tsv"
    failures=$((failures + 1))
fi

capture dnf.log sudo cat /var/log/dnf.log
capture dnf-rpm.log sudo cat /var/log/dnf.rpm.log
capture source-update-command.log cat "$HOME/ol-migration/rhel-update.log"
capture workload-install.log cat "$HOME/ol-migration/workload-install.log"
printf 'Failed captures: %s\n' "$failures" | tee "$out/capture-summary.txt"

archive="$out.tar.gz"
tar -C "$evidence_root" -czf "$archive" "$(basename "$out")" || exit 1
sha256sum "$archive" > "$archive.sha256" || exit 1
printf '\nEvidence archive: %s\nArchive checksum: %s.sha256\n' "$archive" "$archive"
if (( failures != 0 )); then
    printf 'Baseline capture exit=1; review failed captures before proceeding.\n'
    exit 1
fi
printf 'Baseline capture exit=0\n'
