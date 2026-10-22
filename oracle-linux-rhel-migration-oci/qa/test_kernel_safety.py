#!/usr/bin/env python3
"""Exercise the workshop migration script's boot safety gates without a VM.

Run with Python 3 and Bash (Git Bash is supported on Windows). The real script is
sourced only after its source-safe entry-point guard has been checked. Its
BOOT_DIR, MODULES_ROOT, STATE_DIR, and LOG_FILE point into a temporary fixture.
RPM, DNF, grubby, initramfs inspection, library loading, and dependency tools are
mocked. No package manager, bootloader, reboot, SSH, or OCI operation is run.

These are regression tests for script control flow and failure handling. They
cannot prove that a real RHEL image converts or boots successfully.
"""

from __future__ import annotations

import os
import configparser
from pathlib import Path
import re
import shutil
import subprocess
import tempfile
import unittest


SCRIPT = (
    Path(__file__).resolve().parents[1]
    / "migrate-to-oracle-linux/files/migrate-to-oracle-linux.sh"
)
VERSION = "5.14.0-611.5.1.el9_8.x86_64"
UEK_VERSION = "5.15.0-300.163.18.el8uek.x86_64"
BRANDING_SOLVER_OUTPUT = (
    "Dependencies resolved.\n"
    "Installing:\n oracle-logos-httpd noarch 90.0-5.el9 oracle\n"
    "Removing:\n redhat-logos-httpd noarch 90.0-9.el9 @rhel\n"
    "Transaction Summary\nInstall 1 Package\nRemove 1 Package\nOperation aborted.\n"
)
# Observed in fresh-run-20261020T155618Z. Keep this public regression fixture
# independent of the private, ignored evidence archive.
OBSERVED_LIBATTR_DOWNGRADE = (
    "libattr-2.6.0-1.el9_8.x86_64\tlibattr-2.5.1-3.el9.x86_64\tlibattr\t0\t"
    "2.6.0\t1.el9_8\t3.el9\tx86_64\tnearest-lower-release\n"
)
# Sanitized actual DNF tables from fresh-run-20261020T155618Z.
OBSERVED_KERNEL_SOLVER_TABLE = """Package grubby-8.40-69.el9.x86_64 is already installed.
Dependencies resolved.
================================================================================
 Package              Arch    Version                  Repository          Size
================================================================================
Installing:
 kernel               x86_64  5.14.0-687.5.3.el9_8     ol9_baseos_latest  920 k
 kernel-core          x86_64  5.14.0-687.5.3.el9_8     ol9_baseos_latest   17 M
 kernel-modules       x86_64  5.14.0-687.5.3.el9_8     ol9_baseos_latest   41 M
 kernel-modules-core  x86_64  5.14.0-687.5.3.el9_8     ol9_baseos_latest   32 M
Upgrading:
 grubby               x86_64  8.40-69.0.1.el9          ol9_baseos_latest   43 k

Transaction Summary
================================================================================
Install  4 Packages
Upgrade  1 Package

Total download size: 91 M
Operation aborted.
"""
OBSERVED_LOWER_SOLVER_TABLE = """Dependencies resolved.
========================================================================================
 Package                    Arch    Version                     Repository          Size
========================================================================================
Installing:
 kernel                     x86_64  5.14.0-687.5.3.el9_8        ol9_baseos_latest  920 k
 kernel-core                x86_64  5.14.0-687.5.3.el9_8        ol9_baseos_latest   17 M
 kernel-modules             x86_64  5.14.0-687.5.3.el9_8        ol9_baseos_latest   41 M
 kernel-modules-core        x86_64  5.14.0-687.5.3.el9_8        ol9_baseos_latest   32 M
Upgrading:
 grub2-common               noarch  1:2.06-126.0.2.el9_8        ol9_baseos_latest  965 k
 grub2-efi-x64              x86_64  1:2.06-126.0.2.el9_8        ol9_baseos_latest  1.4 M
 grub2-pc                   x86_64  1:2.06-126.0.2.el9_8        ol9_baseos_latest   16 k
 grub2-pc-modules           noarch  1:2.06-126.0.2.el9_8        ol9_baseos_latest  1.3 M
 grub2-tools                x86_64  1:2.06-126.0.2.el9_8        ol9_baseos_latest  2.0 M
 grub2-tools-minimal        x86_64  1:2.06-126.0.2.el9_8        ol9_baseos_latest  704 k
 selinux-policy             noarch  38.1.75-2.0.1.el9_8         ol9_baseos_latest   47 k
 selinux-policy-targeted    noarch  38.1.75-2.0.1.el9_8         ol9_baseos_latest  7.9 M
Installing dependencies:
 freetype                   x86_64  2.10.4-10.el9_5             ol9_baseos_latest  391 k
 graphite2                  x86_64  1.3.14-9.el9                ol9_baseos_latest  100 k
 grub2-tools-efi            x86_64  1:2.06-126.0.2.el9_8        ol9_baseos_latest  629 k
 grub2-tools-extra          x86_64  1:2.06-126.0.2.el9_8        ol9_baseos_latest  950 k
 harfbuzz                   x86_64  2.7.4-10.el9                ol9_baseos_latest  632 k
 libpng                     x86_64  2:1.6.37-15.el9_8           ol9_baseos_latest  115 k
Downgrading:
 NetworkManager             x86_64  1:1.54.3-2.0.1.el9          ol9_baseos_latest  2.4 M
 NetworkManager-libnm       x86_64  1:1.54.3-2.0.1.el9          ol9_baseos_latest  2.0 M
 NetworkManager-team        x86_64  1:1.54.3-2.0.1.el9          ol9_baseos_latest   37 k
 NetworkManager-tui         x86_64  1:1.54.3-2.0.1.el9          ol9_baseos_latest  253 k
 acl                        x86_64  2.3.1-4.el9                 ol9_baseos_latest   87 k
 attr                       x86_64  2.5.1-3.el9                 ol9_baseos_latest   78 k
 bluez                      x86_64  5.85-1.el9                  ol9_baseos_latest  1.3 M
 cockpit-bridge             noarch  356-1.0.2.el9               ol9_baseos_latest  826 k
 cockpit-system             noarch  356-1.0.2.el9               ol9_baseos_latest  5.5 M
 cockpit-ws                 x86_64  356-1.0.2.el9               ol9_baseos_latest  1.2 M
 cockpit-ws-selinux         x86_64  356-1.0.2.el9               ol9_baseos_latest   49 k
 coreutils                  x86_64  8.32-40.0.1.el9             ol9_baseos_latest  1.2 M
 coreutils-common           x86_64  8.32-40.0.1.el9             ol9_baseos_latest  2.1 M
 curl                       x86_64  7.76.1-40.el9               ol9_baseos_latest  303 k
 dbus-broker                x86_64  28-7.el9                    ol9_baseos_latest  181 k
 device-mapper              x86_64  9:1.02.207-4.el9            ol9_baseos_latest  155 k
 device-mapper-event        x86_64  9:1.02.207-4.el9            ol9_baseos_latest   38 k
 device-mapper-event-libs   x86_64  9:1.02.207-4.el9            ol9_baseos_latest   31 k
 device-mapper-libs         x86_64  9:1.02.207-4.el9            ol9_baseos_latest  179 k
 dracut                     x86_64  057-110.git20260130.0.1.el9 ol9_baseos_latest  693 k
 dracut-config-generic      x86_64  057-110.git20260130.0.1.el9 ol9_baseos_latest   12 k
 dracut-network             x86_64  057-110.git20260130.0.1.el9 ol9_baseos_latest  148 k
 dracut-squash              x86_64  057-110.git20260130.0.1.el9 ol9_baseos_latest   13 k
 expat                      x86_64  2.5.0-6.el9                 ol9_baseos_latest  124 k
 firewalld                  noarch  1.3.4-18.0.1.el9_7          ol9_baseos_latest  841 k
 firewalld-filesystem       noarch  1.3.4-18.0.1.el9_7          ol9_baseos_latest  9.5 k
 glib2                      x86_64  2.68.4-19.el9               ol9_baseos_latest  2.7 M
 glibc                      x86_64  2.34-266.0.1.el9_8          ol9_baseos_latest  2.0 M
 glibc-common               x86_64  2.34-266.0.1.el9_8          ol9_baseos_latest  333 k
 glibc-gconv-extra          x86_64  2.34-266.0.1.el9_8          ol9_baseos_latest  1.8 M
 glibc-minimal-langpack     x86_64  2.34-266.0.1.el9_8          ol9_baseos_latest   32 k
 gnutls                     x86_64  3.8.10-3.el9                ol9_baseos_latest  1.4 M
 gzip                       x86_64  1.12-1.el9                  ol9_baseos_latest  193 k
 kbd                        x86_64  2.4.0-11.el9                ol9_baseos_latest  496 k
 kbd-legacy                 noarch  2.4.0-11.el9                ol9_baseos_latest  771 k
 kbd-misc                   noarch  2.4.0-11.el9                ol9_baseos_latest  2.2 M
 kernel-tools               x86_64  5.14.0-687.5.3.el9_8        ol9_baseos_latest  1.2 M
 kernel-tools-libs          x86_64  5.14.0-687.5.3.el9_8        ol9_baseos_latest  933 k
 krb5-libs                  x86_64  1.21.1-9.0.1.el9_8          ol9_baseos_latest  777 k
 libacl                     x86_64  2.3.1-4.el9                 ol9_baseos_latest   22 k
 libarchive                 x86_64  3.5.3-9.el9_7               ol9_baseos_latest  395 k
 libattr                    x86_64  2.5.1-3.el9                 ol9_baseos_latest   19 k
 libcap                     x86_64  2.48-10.el9                 ol9_baseos_latest   80 k
 libcurl                    x86_64  7.76.1-40.el9               ol9_baseos_latest  283 k
 libeconf                   x86_64  0.4.1-5.el9                 ol9_baseos_latest   32 k
 libgcrypt                  x86_64  1.10.0-11.el9               ol9_baseos_latest  512 k
 libldb                     x86_64  4.23.5-8.el9_8              ol9_baseos_latest  197 k
 libnghttp2                 x86_64  1.43.0-6.el9_8.1            ol9_baseos_latest   72 k
 libsolv                    x86_64  0.7.24-4.el9                ol9_baseos_latest  407 k
 libssh                     x86_64  0.10.4-18.el9               ol9_baseos_latest  220 k
 libssh-config              noarch  0.10.4-18.el9               ol9_baseos_latest  8.0 k
 libtasn1                   x86_64  4.16.0-9.el9                ol9_baseos_latest   80 k
 libusbx                    x86_64  1.0.26-1.el9                ol9_baseos_latest   81 k
 libxml2                    x86_64  2.9.13-14.el9_7             ol9_baseos_latest  756 k
 lvm2                       x86_64  9:2.03.33-4.el9             ol9_baseos_latest  1.6 M
 lvm2-libs                  x86_64  9:2.03.33-4.el9             ol9_baseos_latest  1.0 M
 microcode_ctl              noarch  4:20260210-1.0.1.el9_8      ol9_baseos_latest   16 M
 nftables                   x86_64  1:1.0.9-6.el9_7             ol9_baseos_latest  434 k
 openssh                    x86_64  9.9p1-7.0.2.el9_8           ol9_baseos_latest  446 k
 openssh-clients            x86_64  9.9p1-7.0.2.el9_8           ol9_baseos_latest  793 k
 openssh-server             x86_64  9.9p1-7.0.2.el9_8           ol9_baseos_latest  563 k
 openssl                    x86_64  1:3.5.5-2.0.1.el9_8         ol9_baseos_latest  1.6 M
 openssl-fips-provider      x86_64  3.0.7-8.0.1.el9             ol9_baseos_latest  8.3 k
 openssl-fips-provider-so   x86_64  3.0.7-8.0.1.el9             ol9_baseos_latest  575 k
 openssl-libs               x86_64  1:3.5.5-2.0.1.el9_8         ol9_baseos_latest  2.3 M
 p11-kit                    x86_64  0.26.2-1.el9                ol9_baseos_latest  670 k
 p11-kit-trust              x86_64  0.26.2-1.el9                ol9_baseos_latest  161 k
 python3-firewall           noarch  1.3.4-18.0.1.el9_7          ol9_baseos_latest  539 k
 python3-idna               noarch  2.10-7.0.1.el9_4.1          ol9_baseos_latest  129 k
 python3-libxml2            x86_64  2.9.13-14.el9_7             ol9_baseos_latest  237 k
 python3-nftables           x86_64  1:1.0.9-6.el9_7             ol9_baseos_latest   30 k
 python3-pip-wheel          noarch  21.3.1-1.el9                ol9_baseos_latest  1.1 M
 python3-urllib3            noarch  1.26.5-6.el9_7.1            ol9_baseos_latest  314 k
 rsync                      x86_64  3.2.5-3.el9_7.2             ol9_baseos_latest  434 k
 sg3_utils                  x86_64  1.47-10.el9                 ol9_baseos_latest  1.1 M
 sg3_utils-libs             x86_64  1.47-10.el9                 ol9_baseos_latest  101 k
 shim-x64                   x86_64  16.1-1.0.2.el9              ol9_baseos_latest  510 k
 sos                        noarch  4.10.2-2.0.1.el9_8          ol9_baseos_latest  2.4 M
 sqlite-libs                x86_64  3.34.1-10.el9_8             ol9_baseos_latest  643 k
 tar                        x86_64  2:1.34-11.el9               ol9_baseos_latest  927 k
 tuned                      noarch  2.27.0-1.0.1.el9            ol9_baseos_latest  787 k

Transaction Summary
========================================================================================
Install    10 Packages
Upgrade     8 Packages
Downgrade  81 Packages

Total download size: 181 M
Operation aborted.
"""
PLAN_HEADER = "source_nevra\ttarget_nevra\tname\tepoch\tversion\tsource_release\ttarget_release\tarch\tstatus\n"
# Sanitized actual source-3 kernel transaction error from 20261020T202843Z.
OBSERVED_GRUB_PROBE_ERROR = (
    "grub2-probe: error: ../grub-core/kern/disk.c:240:disk "
    "lvmid/source-volume-group/source-logical-volume not found."
)
BASH = shutil.which("bash")
if os.name == "nt" and Path("C:/Program Files/Git/bin/bash.exe").is_file():
    BASH = "C:/Program Files/Git/bin/bash.exe"


def bash_path(path: Path) -> str:
    value = path.resolve().as_posix()
    if re.match(r"^[A-Za-z]:/", value):
        return f"/{value[0].lower()}/{value[3:]}"
    return value


HARNESS = r'''#!/usr/bin/env bash
set -Eeuo pipefail
source "$1"
FIXTURE_ROOT="$2"
BOOT_DIR="$FIXTURE_ROOT/boot"
MODULES_ROOT="$FIXTURE_ROOT/modules"
DRACUT_INSTALL="$FIXTURE_ROOT/dracut-install"
EFI_FIRMWARE_DIR="$FIXTURE_ROOT/firmware/efi"
STATE_DIR="$FIXTURE_ROOT/state"
LOG_FILE="$STATE_DIR/migration.log"
KERNEL_FLAVOR="${MOCK_KERNEL_FLAVOR:-rhck}"
DNF_CMD="dnf"
DRY_RUN=0
SOURCE_FAMILY=rhel
SOURCE_ID=rhel
SOURCE_MAJOR=9
SOURCE_VERSION_ID=9.8
EFFECTIVE_TARGET_VERSION=9.8
TARGET_VERSION=9.8
MIGRATION_MODE=preserve-release
BASEARCH=x86_64
ORACLE_TARGET_EXCLUDE_ARGS=("--exclude=*.el9_9*")

# Keep script logging in the fixture even if a host happens to have LOG_ROOT.
log() { printf '%s: %s\n' "$1" "${*:2}" >&2; }

rpm() {
    local format="" is_owner=0 verify=0 expect_format=0 item="" arg name version release vendor epoch row
    printf 'rpm %s\n' "$*" >> "$STATE_DIR/mock.calls"
    for arg in "$@"; do
        if (( expect_format )); then format="$arg"; expect_format=0; continue; fi
        case "$arg" in
            --qf|--queryformat) expect_format=1 ;;
            -qf) is_owner=1 ;;
            -V|--verify) verify=1 ;;
            -*) ;;
            *) item="$arg" ;;
        esac
    done
    if [[ " $* " == *" --nodeps "* || " $* " == *" -e "* || " $* " == *" --erase "* ]]; then
        printf 'Forbidden standalone RPM erasure in regression fixture\n' >&2
        return 98
    fi
    if [[ " $* " == *" -qa "* && -f "$FIXTURE_ROOT/rpmdb.tsv" ]]; then
        while IFS=$'\t' read -r name epoch version release arg vendor; do
            row="$format"
            row="${row//\%\{NAME\}/$name}"
            row="${row//\%\{EPOCHNUM\}/$epoch}"
            row="${row//\%\{VERSION\}/$version}"
            row="${row//\%\{RELEASE\}/$release}"
            row="${row//\%\{ARCH\}/$arg}"
            row="${row//\%\{VENDOR\}/$vendor}"
            row="${row//\%\{NVRA\}/$name-$version-$release.$arg}"
            printf '%b' "$row"
        done < "$FIXTURE_ROOT/rpmdb.tsv"
        return
    fi
    if (( verify )); then
        if [[ ( "$item" == shim-* || "$item" == grub2-* ) && -n "${MOCK_BOOTCHAIN_VERIFY_OUTPUT:-}" ]]; then
            printf '%s\n' "${MOCK_BOOTCHAIN_VERIFY_OUTPUT//@BOOT_DIR@/$BOOT_DIR}"
            return "${MOCK_BOOTCHAIN_VERIFY_STATUS:-1}"
        fi
        if [[ ( "$item" == shim-* || "$item" == grub2-* ) && "${MOCK_BOOTCHAIN_VERIFY_FAIL:-0}" == 1 ]]; then
            printf 'S.5...... /boot/efi/EFI/oracle/shimx64.efi\n'
            return 1
        fi
        if [[ "$item" == libattr && "${MOCK_LIBATTR_CORRUPT:-0}" == 1 ]]; then
            printf 'S.5...... /usr/lib64/libattr.so.1\n'
            return 1
        fi
        if [[ "$item" == libacl && "${MOCK_LIBACL_CORRUPT:-0}" == 1 ]]; then
            printf 'S.5...... /usr/lib64/libacl.so.1\n'
            return 1
        fi
        if [[ "$item" == dracut-core && "${MOCK_DRACUT_CORE_CORRUPT:-0}" == 1 ]]; then
            printf 'S.5...... /usr/lib/dracut/dracut-install\n'
            return 1
        fi
        if [[ "$item" == dracut && "${MOCK_DRACUT_CORRUPT:-0}" == 1 ]]; then
            printf 'S.5...... /usr/lib/dracut/dracut-install\n'
            return 1
        fi
        return 0
    fi
    if [[ "${MOCK_LIBATTR_MISSING:-0}" == 1 && " $* " == *" libattr "* ]]; then return 1; fi
    if [[ "${MOCK_LIBACL_MISSING:-0}" == 1 && " $* " == *" libacl "* ]]; then return 1; fi
    if [[ "${MOCK_DRACUT_MISSING:-0}" == 1 && " $* " == *" dracut "* ]]; then return 1; fi
    # The observed RHEL 9.8 image packages the helper in dracut and has no
    # dracut-core RPM. Model that layout unless the split layout is requested.
    if (( ! is_owner )) && [[ "${MOCK_DRACUT_CORE_PRESENT:-0}" != 1 && " $* " == *" dracut-core "* ]]; then
        return 1
    fi
    if (( is_owner )) && [[ "$item" == "$DRACUT_INSTALL" ]]; then
        if [[ "${MOCK_DRACUT_HELPER_UNOWNED:-0}" == 1 ]]; then return 1; fi
        name="${MOCK_DRACUT_HELPER_OWNER:-dracut}"
        format="${format//\%\{NAME\}/$name}"
        printf '%b' "$format"
        return
    fi
    if (( ! is_owner )) && [[ "$item" =~ ^kernel(-core|-modules|-modules-core|-modules-extra)?-[0-9]+: ]]; then
        if [[ "${MOCK_KERNEL_COLLISION:-0}" == 1 &&
            ( -z "${MOCK_KERNEL_COLLISION_PACKAGE:-}" || "$item" == "${MOCK_KERNEL_COLLISION_PACKAGE}-"* ) ]]; then
            printf 'Red Hat, Inc.\n'
            return 0
        fi
        if [[ "${MOCK_EXACT_KERNEL_INSTALLED:-0}" == 1 ]]; then
            printf 'Oracle America\n'
            return 0
        fi
        return 1
    fi
    if (( ! is_owner )) && [[ "$item" == redhat-logos-httpd ]]; then
        if [[ "${MOCK_BRANDING_ABSENT:-0}" == 1 ]]; then return 1; fi
        printf '%s\n' "${MOCK_BRANDING_NVRA:-redhat-logos-httpd-90.0-9.el9.noarch}"
        return
    fi
    if (( ! is_owner )) && [[ "$item" == shim-* || "$item" == grub2-* ]]; then
        if [[ "$format" == *'%{FILEMTIMES}'* ]]; then
            if [[ -f "$FIXTURE_ROOT/bootchain-file-times.tsv" ]]; then
                while IFS= read -r row; do
                    printf '%s\n' "${row//@BOOT_DIR@/$BOOT_DIR}"
                done < "$FIXTURE_ROOT/bootchain-file-times.tsv"
            else
                printf '%s\t%s\n' "$BOOT_DIR/efi/EFI/oracle/shimx64.efi" "${MOCK_EFI_EXPECTED_MTIME:-1767725055}"
            fi
            return "${MOCK_BOOTCHAIN_FILETIMES_STATUS:-0}"
        fi
        if [[ " $* " == *" -ql "* ]]; then
            printf '%s\n' "${MOCK_EFI_FILEPATH:-$BOOT_DIR/efi/EFI/oracle/shimx64.efi}"
            return
        fi
        if [[ "${MOCK_DEFERRED_ABSENT:-0}" == 1 ]]; then return 1; fi
        if [[ "${MOCK_DEFERRED_VENDOR_WRONG:-0}" == 1 || ! -f "$STATE_DIR/mock.bootchain-installed" ]]; then
            printf 'Red Hat, Inc.\n'
        else
            printf 'Oracle America\n'
        fi
        return
    fi
    if (( is_owner )) && [[ "$item" == "$BOOT_DIR/efi/"* ]]; then
        if [[ "${MOCK_EFI_OWNER_MISSING:-0}" == 1 ]]; then return 1; fi
        printf '%s\n' "${MOCK_EFI_OWNER_VENDOR:-Oracle America}"
        return
    fi
    vendor="${MOCK_VENDOR:-Oracle America}"
    if [[ "$KERNEL_FLAVOR" == rhck ]]; then name="kernel-core"; else name="kernel-uek-core"; fi
    version="${MOCK_VERSION%%-*}"
    release="${MOCK_VERSION#*-}"
    release="${release%.x86_64}"
    if (( is_owner )); then
        name="${MOCK_OWNER:-$name}"
        vendor="${MOCK_OWNER_VENDOR:-Oracle America}"
        release="${MOCK_OWNER_RELEASE:-$release}"
    fi
    if [[ -n "$format" ]]; then
        format="${format//\%\{NAME\}/$name}"
        format="${format//\%\{VERSION\}/$version}"
        format="${format//\%\{RELEASE\}/$release}"
        format="${format//\%\{ARCH\}/x86_64}"
        format="${format//\%\{VENDOR\}/$vendor}"
        printf '%b' "$format"
        if (( ! is_owner )) && [[ "${MOCK_OTHER_VENDOR_NEWER:-0}" == 1 ]]; then
            printf '999.0-1.el9.x86_64\tRed Hat, Inc.\n'
        fi
    else
        printf '%s\n' "$item"
    fi
}

findmnt() {
    printf 'findmnt %s\n' "$*" >> "$STATE_DIR/mock.calls"
    printf '%s\n' "${MOCK_EFI_FSTYPE:-vfat}"
    return "${MOCK_FINDMNT_STATUS:-0}"
}

python3() {
    printf 'python3 %s\n' "$*" >> "$STATE_DIR/mock.calls"
    if [[ "${MOCK_LIBRARY_LOAD_FAIL:-0}" == 1 || -f "$STATE_DIR/mock.runtime-broken" ]]; then
        printf 'OSError: libattr.so.1: cannot open shared object file\n' >&2
        return 1
    fi
    if [[ "${MOCK_LIBACL_LOAD_FAIL:-0}" == 1 && "$*" == *libacl.so.1* ]]; then
        printf 'OSError: libacl.so.1: cannot open shared object file\n' >&2
        return 1
    fi
}
ldd() {
    printf 'ldd %s\n' "$*" >> "$STATE_DIR/mock.calls"
    # EL9 grubby may be a shell wrapper, so checking it with ldd is an error.
    if [[ "${1:-}" == *grubby ]]; then printf 'not a dynamic executable\n' >&2; return 1; fi
    if [[ "${MOCK_LDD_FAIL:-0}" == 1 ]]; then return 1; fi
    if [[ "${MOCK_LINKAGE_MISSING:-0}" == 1 ]]; then
        printf 'libattr.so.1 => not found\n'
    else
        printf 'libattr.so.1 => /usr/lib64/libattr.so.1 (0x1234)\n'
    fi
}
dracut() {
    printf 'dracut %s\n' "$*" >> "$STATE_DIR/mock.calls"
    case "${1:-}" in
        # The observed healthy RHEL 9.8 dracut prints information and exits 1
        # for these options. They do not distinguish a working runtime.
        --version) printf 'dracut-057 fixture\n'; return 1 ;;
        --help) printf 'Usage: dracut [OPTION...]\n'; return 1 ;;
        --list-modules)
            if [[ "${MOCK_DRACUT_FAIL:-0}" == 1 ]]; then return 1; fi
            if [[ "${MOCK_DRACUT_EMPTY:-0}" == 1 ]]; then return 0; fi
            printf 'base\nkernel-modules\n'
            ;;
        *) printf 'Unexpected dracut invocation: %s\n' "$*" >&2; return 98 ;;
    esac
}
depmod() {
    printf 'depmod %s\n' "$*" >> "$STATE_DIR/mock.calls"
    if [[ "${MOCK_DEPMOD_FAIL:-0}" == 1 ]]; then return 1; fi
    printf 'kmod version 25\n'
}
lsinitrd() {
    printf 'lsinitrd %s\n' "$*" >> "$STATE_DIR/mock.calls"
    if [[ "${MOCK_INITRAMFS_INVALID:-0}" == 1 ]]; then
        printf 'Image is not a valid initramfs\n' >&2
        return 1
    fi
    if [[ "${MOCK_INITRAMFS_HEADER_ONLY:-0}" == 1 ]]; then
        printf 'Image: %s/initramfs-%s.img\n' "$BOOT_DIR" "$MOCK_VERSION"
    elif [[ "${MOCK_INITRAMFS_WRONG_VERSION:-0}" == 1 ]]; then
        printf 'usr/lib/modules/4.18.0-old.el8.x86_64/kernel/drivers/virtio.ko\n'
    else
        printf 'usr/lib/modules/%s/kernel/drivers/virtio.ko\n' "$MOCK_VERSION"
    fi
}
grubby() {
    printf 'grubby %s\n' "$*" >> "$STATE_DIR/mock.calls"
    case "${1:-}" in
        --info=*)
            if [[ "${MOCK_GRUBBY_INFO_FAIL:-0}" == 1 ]]; then return 1; fi
            printf 'index=0\nkernel="%s/vmlinuz-%s"\n' "$BOOT_DIR" "$MOCK_VERSION"
            if [[ "${MOCK_GRUBBY_INITRD_MISMATCH:-0}" == 1 ]]; then
                printf 'initrd="%s/initramfs-old.img"\n' "$BOOT_DIR"
            else
                printf 'initrd="%s/initramfs-%s.img"\n' "$BOOT_DIR" "$MOCK_VERSION"
            fi
            ;;
        --set-default)
            if [[ "${MOCK_GRUBBY_SET_FAIL:-0}" == 1 ]]; then return 1; fi
            if [[ "${MOCK_GRUBBY_SET_NO_EFFECT:-0}" != 1 ]]; then
                printf '%s\n' "$2" > "$STATE_DIR/mock.default"
            fi
            ;;
        --default-kernel)
            if [[ "${MOCK_GRUBBY_READ_FAIL:-0}" == 1 ]]; then return 1; fi
            cat "$STATE_DIR/mock.default"
            ;;
        *) printf 'Unexpected grubby invocation: %s\n' "$*" >&2; return 98 ;;
    esac
}
mock_dnf() {
    printf 'mock_dnf %s\n' "$*" >> "$STATE_DIR/mock.calls"
    if [[ " $* " == *" repoquery "* ]]; then
        if [[ "${MOCK_REPOQUERY_FAIL:-0}" == 1 ]]; then return 1; fi
        if [[ " $* " == *" oraclelinux-release "* ]]; then
            cat "$FIXTURE_ROOT/oracle-release.tsv"
        else
            cat "$FIXTURE_ROOT/kernel-available.tsv"
        fi
        return
    fi
    if [[ " $* " == *" --assumeno "* ]]; then
        if [[ "${MOCK_SECOND_SOLVER_FAIL:-0}" == 1 && -s "$STATE_DIR/mock.mutations" ]]; then
            printf 'Error: changed RPMDB now makes the next transaction unsafe\n'
            return 1
        fi
        if [[ -n "${MOCK_SOLVER_FAIL_OPERATION:-}" && " $* " == *" ${MOCK_SOLVER_FAIL_OPERATION} "* ]]; then
            printf 'Error: conflicting requests in %s\n' "$MOCK_SOLVER_FAIL_OPERATION"
            return 1
        fi
        local solver_output="${MOCK_SOLVER_OUTPUT:-$'Dependencies resolved.\nTransaction Summary\nInstall 4 Packages\nOperation aborted.'}"
        if [[ "${MOCK_REQUIRE_KERNEL_FOR_BOOTCHAIN:-0}" == 1 && ( " $* " == *" shim-"* || " $* " == *" grub2-"* ) && -z "${MOCK_SOLVER_OUTPUT:-}" ]]; then
            if [[ -f "$STATE_DIR/mock.kernel-installed" ]]; then
                solver_output=$'Dependencies resolved.\nInstalling:\n shim-x64 x86_64 15.8-12.el9 oracle\nTransaction Summary\nOperation aborted.'
            else
                solver_output="$(printf 'Dependencies resolved.\nInstalling:\n kernel x86_64 %s ol9_baseos_latest\n kernel-core x86_64 %s ol9_baseos_latest\n kernel-modules x86_64 %s ol9_baseos_latest\n kernel-modules-core x86_64 %s ol9_baseos_latest\nTransaction Summary\nOperation aborted.' "${MOCK_VERSION%.x86_64}" "${MOCK_VERSION%.x86_64}" "${MOCK_VERSION%.x86_64}" "${MOCK_VERSION%.x86_64}")"
            fi
        fi
        printf '%s\n' "$solver_output"
        return "${MOCK_SOLVER_STATUS:-1}"
    fi
    printf '%s\n' "${MOCK_TRANSACTION_OUTPUT:-Installed: kernel. Complete!}"
    local mutations=0
    if [[ -s "$STATE_DIR/mock.mutations" ]]; then mutations="$(<"$STATE_DIR/mock.mutations")"; fi
    printf '%s\n' "$((mutations + 1))" > "$STATE_DIR/mock.mutations"
    if [[ "${MOCK_BREAK_RUNTIME_AFTER_TRANSACTION:-0}" == 1 ]]; then
        : > "$STATE_DIR/mock.runtime-broken"
    fi
    if [[ "${MOCK_TRANSACTION_STATUS:-0}" == 0 ]]; then
        if [[ " $* " == *" install kernel-0:"* ]]; then : > "$STATE_DIR/mock.kernel-installed"; fi
        if [[ " $* " == *" shim-"* || " $* " == *" grub2-"* ]]; then : > "$STATE_DIR/mock.bootchain-installed"; fi
    fi
    if [[ "${MOCK_TRANSACTION_STATUS:-0}" == 0 && -f "$FIXTURE_ROOT/rpmdb.after.tsv" ]]; then
        command cp "$FIXTURE_ROOT/rpmdb.after.tsv" "$FIXTURE_ROOT/rpmdb.tsv"
    fi
    return "${MOCK_TRANSACTION_STATUS:-0}"
}
dnf() { mock_dnf "$@"; }
xargs() {
    # The tested package lists contain one NEVRA per line and fewer than the
    # requested batch size. Execute the shell mock with those exact arguments.
    local input="" item
    local -a entries=()
    while (( $# )); do
        case "$1" in
            -r) shift ;;
            -a) input="$2"; shift 2 ;;
            -n) shift 2 ;;
            *) break ;;
        esac
    done
    [[ -n "$input" ]] || { printf 'Unexpected xargs usage\n' >&2; return 98; }
    while IFS= read -r item; do [[ -z "$item" ]] || entries+=("$item"); done < "$input"
    (( ${#entries[@]} )) || return 0
    "$@" "${entries[@]}"
}
tee() {
    if [[ "${MOCK_TEE_FAIL:-0}" == 1 ]]; then command cat > /dev/null; return 1; fi
    command tee "$@"
}

TEST_KERNEL_PKG="$(selected_kernel_package)"
case "$3" in
    dependencies) check_kernel_install_dependencies ;;
    transaction) run_kernel_transaction mock_dnf -y install "$TEST_KERNEL_PKG" ;;
    artifacts) verify_kernel_boot_artifacts "$TEST_KERNEL_PKG" ;;
    set-default) set_default_boot_kernel "$TEST_KERNEL_PKG" ;;
    install) install_selected_kernel ;;
    resolve) resolve_selected_kernel_plan ;;
    solver) check_selected_kernel_solver ;;
    package-solver) check_package_solver_plan fixture-nearest downgrade "$STATE_DIR/nearest-lower-reinstall.nevra" ;;
    reinstall-solvers) check_reinstall_plan_solvers ;;
    branding-solver) check_branding_swap_solver ;;
    source-removal-plan) remove_rhel_only_packages --plan-only ;;
    eligibility) check_reinstall_plan_eligibility ;;
    catalogue) check_target_catalogue ;;
    pinned-release)
        check_target_catalogue
        install_oracle_release_packages
        ;;
    repository-plan)
        # Python creates this directory because Git Bash mkdir can misinterpret
        # Windows temporary paths. Still validate the real function's request.
        mkdir() {
            [[ "$#" -eq 2 && "$1" == -p && "$2" == "$STATE_DIR/dnf.repos.d" && -d "$2" ]] || {
                printf 'Unexpected repository directory request\n' >&2
                return 98
            }
        }
        set_oracle_repo_args
        write_bootstrap_repos
        command cp "$STATE_DIR/dnf.repos.d/oraclelinux-migration-bootstrap.repo" "$STATE_DIR/bootstrap.repo.snapshot"
        printf '%s\n' "${ORACLE_REPO_ARGS[@]}" > "$STATE_DIR/oracle-repo.args"
        printf '%s\n' "${ORACLE_TARGET_EXCLUDE_ARGS[@]}" > "$STATE_DIR/target-exclude.args"
        ;;
    defer-plan) plan_deferred_bootchain_packages ;;
    matching-library-plan)
        REINSTALL_ALL=1
        repoquery_available_nevras() { command cut -f 6 "$FIXTURE_ROOT/oracle-available.tsv" > "$1"; }
        repoquery_available_packages() { command cp "$FIXTURE_ROOT/oracle-available.tsv" "$1"; }
        repoquery_update_exception_packages() { : > "$1"; }
        generate_html_reports() { :; }
        build_reinstall_plan
        check_reinstall_plan_eligibility
        ;;
    deferred-solver) check_deferred_bootchain_solver ;;
    deferred-bootchain)
        install_selected_kernel
        install_deferred_bootchain_packages
        ;;
    premature-bootchain) run_nonkernel_transaction premature-bootchain "" install shim-x64-15.8-12.el9.x86_64 ;;
    eligibility-main|catalogue-main)
        # Exercise the real main order with its environment and mutation
        # stages stubbed. The actual eligibility function remains in place.
        for stub in parse_args detect_source select_migration_mode prepare_environment snapshot_system \
            check_kernel_state check_rpmdb confirm write_bootstrap_repos resolve_selected_kernel_plan \
            check_selected_kernel_solver build_reinstall_plan check_reinstall_plan_solvers check_branding_swap_solver \
            plan_deferred_bootchain_packages check_deferred_bootchain_solver; do
            eval "$stub() { :; }"
        done
        if [[ "$3" == eligibility-main ]]; then check_target_catalogue() { :; }; fi
        if [[ "$3" == catalogue-main ]]; then check_reinstall_plan_eligibility() { :; }; fi
        remove_rhel_only_packages() {
            [[ "${1:-}" == --plan-only ]] && return 0
            printf 'UNEXPECTED_MAIN_MUTATION:remove_rhel_only_packages\n' >&2
            exit 97
        }
        for stub in disable_source_repos remove_source_release_packages install_oracle_release_packages \
            refresh_oracle_repos remove_amazon_only_packages remove_el10_split_firmware_packages \
            reinstall_exact_packages reinstall_nearest_packages switch_source_modules_to_oracle_streams \
            distro_sync_oracle remove_remaining_amazon_only_packages install_update_exception_packages \
            install_selected_kernel replace_source_vendor_packages reassert_selected_kernel_default \
            install_deferred_bootchain_packages cleanup_bootstrap_repos verify_result print_summary; do
            eval "$stub() { printf 'UNEXPECTED_MAIN_MUTATION:$stub\\n' >&2; exit 97; }"
        done
        DRY_RUN=0
        main
        ;;
    nonkernel) run_nonkernel_transaction fixture-nearest "" downgrade libexample-1.0-1.el9.x86_64 ;;
    nonkernel-libattr) run_nonkernel_transaction fixture-libattr "" downgrade libattr-2.5.1-3.el9.x86_64 ;;
    nonkernel-libacl) run_nonkernel_transaction fixture-libacl "" downgrade libacl-2.3.1-4.el9.x86_64 ;;
    nonkernel-sequence)
        run_nonkernel_transaction fixture-first "" downgrade libexample-1.0-1.el9.x86_64
        run_nonkernel_transaction fixture-second "" install second-example-1.0-1.el9.x86_64
        ;;
    exact) reinstall_exact_packages ;;
    update-exception) install_update_exception_packages ;;
    resolve-install)
        resolve_selected_kernel_plan
        check_selected_kernel_solver
        install_selected_kernel
        ;;
    nearest) reinstall_nearest_packages ;;
    replace)
        repoquery_available_packages() { command cp "$FIXTURE_ROOT/oracle-available.tsv" "$1"; }
        replace_source_vendor_packages
        ;;
    verify) verify_selected_kernel ;;
    preserve)
        remove_nonrunning_runtime_kernels
        remove_source_vendor_runtime_kernels
        ;;
    healthy)
        check_kernel_install_dependencies
        run_kernel_transaction mock_dnf -y install "$TEST_KERNEL_PKG"
        verify_kernel_boot_artifacts "$TEST_KERNEL_PKG"
        set_default_boot_kernel "$TEST_KERNEL_PKG"
        verify_selected_kernel
        ;;
    *) printf 'Unknown test operation\n' >&2; exit 99 ;;
esac
printf 'SAFETY_GATE_PASSED\n'
'''


@unittest.skipUnless(BASH, "Bash is required (Git Bash works on Windows)")
class KernelSafetyTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls) -> None:
        cls.source = SCRIPT.read_text(encoding="utf-8")
        # Refuse to source an upstream script whose entry point runs immediately.
        guard = r'if \[\[ "\$\{BASH_SOURCE\[0\]\}" == "\$0" \]\]; then\s+main "\$@"\s+fi\s*$'
        if not re.search(guard, cls.source):
            raise AssertionError("Migration script lacks the required source-safe main guard")

    def run_case(
        self,
        operation: str,
        *,
        overrides: dict[str, str] | None = None,
        remove: tuple[str, ...] = (),
        empty: tuple[str, ...] = (),
        initial_default: str | None = None,
        state_files: dict[str, str] | None = None,
        fixture_files: dict[str, str] | None = None,
        fixture_mtimes_ns: dict[str, int] | None = None,
        extra_dirs: tuple[str, ...] = (),
    ) -> tuple[subprocess.CompletedProcess[str], dict[str, str]]:
        with tempfile.TemporaryDirectory(prefix="migration-safety-") as directory:
            fixture = Path(directory)
            for name in extra_dirs:
                (fixture / name).mkdir(parents=True, exist_ok=True)
            version = UEK_VERSION if (overrides or {}).get("MOCK_KERNEL_FLAVOR") == "uek" else VERSION
            files = {
                "image": fixture / "boot" / f"vmlinuz-{version}",
                "initramfs": fixture / "boot" / f"initramfs-{version}.img",
                "modules": fixture / "modules" / version / "kernel",
                "module": fixture / "modules" / version / "kernel/drivers/virtio.ko",
                "modules.dep": fixture / "modules" / version / "modules.dep",
                "dracut-install": fixture / "dracut-install",
            }
            for name, path in files.items():
                if name in remove or (name == "module" and "modules" in remove):
                    continue
                if name == "modules":
                    path.mkdir(parents=True)
                else:
                    path.parent.mkdir(parents=True, exist_ok=True)
                    content = "" if name in empty else "fixture\n"
                    if name == "dracut-install":
                        content = '#!/usr/bin/env bash\nif [[ "${MOCK_DRACUT_INSTALL_FAIL:-0}" == 1 ]]; then exit 1; fi\nprintf "dracut-install fixture help\\n"\n'
                    path.write_text(content, encoding="utf-8", newline="\n")
                    if name == "dracut-install":
                        path.chmod(0o755)
            state = fixture / "state"
            state.mkdir(exist_ok=True)
            (state / "kernel-install.nevra").write_text(f"kernel-0:{version}\ngrubby\n", encoding="utf-8", newline="\n")
            kernel_version, kernel_release = version.rsplit(".x86_64", 1)[0].split("-", 1)
            metadata = "".join(
                f"{name}\t0\t{kernel_version}\t{kernel_release}\tx86_64\tOracle America\t{name}-0:{version}\n"
                for name in ("kernel", "kernel-core", "kernel-modules", "kernel-modules-core")
            )
            (fixture / "kernel-available.tsv").write_text(metadata, encoding="utf-8", newline="\n")
            (fixture / "oracle-release.tsv").write_text(
                "oraclelinux-release\t0\t9.8\t1.0.1.el9\tx86_64\tOracle America\toraclelinux-release-0:9.8-1.0.1.el9.x86_64\n",
                encoding="utf-8", newline="\n",
            )
            for name, content in (state_files or {}).items():
                (state / name).write_text(content, encoding="utf-8", newline="\n")
            for name, content in (fixture_files or {}).items():
                path = fixture / name
                path.parent.mkdir(parents=True, exist_ok=True)
                path.write_text(content, encoding="utf-8", newline="\n")
            for name, timestamp_ns in (fixture_mtimes_ns or {}).items():
                os.utime(fixture / name, ns=(timestamp_ns, timestamp_ns))
            (state / "mock.default").write_text(
                initial_default or f"{bash_path(fixture)}/boot/vmlinuz-{version}\n",
                encoding="utf-8",
            )
            harness = fixture / "harness.sh"
            harness.write_text(HARNESS, encoding="utf-8", newline="\n")
            env = os.environ.copy()
            # Prevent inherited scenario settings from affecting another run.
            for key in tuple(env):
                if key.startswith("MOCK_"):
                    del env[key]
            env.update(overrides or {})
            env["MOCK_VERSION"] = version
            result = subprocess.run(
                [BASH, "--noprofile", "--norc", bash_path(harness), bash_path(SCRIPT), bash_path(fixture), operation],
                capture_output=True,
                text=True,
                encoding="utf-8",
                errors="replace",
                env=env,
                timeout=20,
                check=False,
            )
            reports = {
                item.name: item.read_text(encoding="utf-8", errors="replace")
                for item in state.iterdir()
                if item.is_file()
            }
            for name in (fixture_files or {}):
                reports[f"fixture.{name}"] = (fixture / name).read_text(encoding="utf-8", errors="replace")
            return result, reports

    def assert_rejected(self, operation: str, *, expect_error: bool = True, **kwargs) -> dict[str, str]:
        result, reports = self.run_case(operation, **kwargs)
        output = result.stdout + result.stderr
        self.assertNotEqual(result.returncode, 0, output)
        if expect_error:
            self.assertIn("ERROR:", output, output)
        self.assertNotIn("SAFETY_GATE_PASSED", output, output)
        return reports

    def test_healthy_candidate_passes_all_gates(self):
        result, reports = self.run_case("healthy")
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertIn("SAFETY_GATE_PASSED", result.stdout)
        self.assertIn("Installed: kernel", reports["kernel-install.transaction.log"])
        self.assertIn(f"vmlinuz-{VERSION}", reports["kernel-default.verified"])

    def test_healthy_uek_branch_passes_same_safety_gates(self):
        result, reports = self.run_case("healthy", overrides={"MOCK_KERNEL_FLAVOR": "uek"})
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertIn(f"vmlinuz-{UEK_VERSION}", reports["kernel-default.verified"])

    def test_newer_source_vendor_kernel_does_not_override_oracle_selection(self):
        result, reports = self.run_case("healthy", overrides={"MOCK_OTHER_VENDOR_NEWER": "1"})
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertIn(f"vmlinuz-{VERSION}", reports["kernel-default.verified"])

    def test_main_checks_dependencies_before_changing_distribution(self):
        main = re.search(r"(?ms)^main\(\) \{\n(.*?)^\}", self.source).group(1)
        self.assertLess(main.index("check_kernel_install_dependencies"), main.index("remove_source_release_packages"))
        self.assertLess(main.index("check_kernel_install_dependencies"), main.index("disable_source_repos"))
        self.assertNotIn("remove_nonrunning_runtime_kernels", main)
        self.assertNotIn("remove_source_vendor_runtime_kernels", main)
        for gate in ("check_target_catalogue", "resolve_selected_kernel_plan", "check_selected_kernel_solver", "build_reinstall_plan", "plan_deferred_bootchain_packages", "check_reinstall_plan_eligibility", "check_reinstall_plan_solvers", "check_branding_swap_solver", "check_deferred_bootchain_solver"):
            self.assertLess(main.index(gate), main.index("disable_source_repos"))
        self.assertLess(main.index("remove_rhel_only_packages --plan-only"), main.index("build_reinstall_plan"))
        self.assertLess(main.index("exit 0"), main.index("disable_source_repos"))
        self.assertLess(main.index("install_selected_kernel"), main.index("install_deferred_bootchain_packages"))
        self.assertLess(main.index("install_deferred_bootchain_packages"), main.index("replace_source_vendor_packages"))

    def test_current_oracle_catalogue_98_is_checked_without_minor_filter(self):
        result, reports = self.run_case("catalogue")
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertIn("\t9.8\t", reports["target-catalogue.oracle-release.selected.tsv"])
        query = next(line for line in reports["mock.calls"].splitlines() if line.startswith("mock_dnf "))
        self.assertIn("oraclelinux-release", query)
        self.assertIn("--refresh", query.split())
        self.assertIn("--disableexcludes=all", query.split())
        self.assertNotIn("--exclude=*.el9_9*", query)

    def test_release_install_uses_the_exact_checked_catalogue_identity(self):
        result, reports = self.run_case("pinned-release")
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertEqual(reports["oracle-release-install.nevra"], "oraclelinux-release-0:9.8-1.0.1.el9.x86_64\noraclelinux-release-el9\n")
        mutation = next(line for line in reports["mock.calls"].splitlines() if line.startswith("mock_dnf -y "))
        self.assertIn("install oraclelinux-release-0:9.8-1.0.1.el9.x86_64 oraclelinux-release-el9", mutation)
        self.assertIn("--exclude=kernel", mutation.split())
        self.assertIn("--exclude=shim-*", mutation.split())

    def test_release_dependency_cannot_silently_advance_to_99(self):
        reports = self.assert_rejected(
            "pinned-release",
            overrides={"MOCK_SOLVER_OUTPUT": "Dependencies resolved.\nUpgrading:\n oraclelinux-release x86_64 9.9-1.el9_9 ol9_baseos_latest\nTransaction Summary\nOperation aborted."},
        )
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])

    def test_workshop_uses_updated_baseos_and_appstream_with_future_minor_exclusions(self):
        result, reports = self.run_case("repository-plan", extra_dirs=("state/dnf.repos.d",))
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        repositories = configparser.ConfigParser(interpolation=None)
        repositories.read_string(reports["bootstrap.repo.snapshot"])
        self.assertTrue(repositories["ol9_baseos_latest"]["baseurl"].endswith("/OL9/baseos/latest/$basearch/"))
        self.assertTrue(repositories["ol9_appstream"]["baseurl"].endswith("/OL9/appstream/$basearch/"))
        self.assertEqual(repositories["ol9_baseos_latest"]["gpgcheck"], "1")
        self.assertIn("--exclude=*.el9_9*", reports["target-exclude.args"].splitlines())
        self.assertNotIn("--exclude=*.el9_8*", reports["target-exclude.args"].splitlines())

    def test_noarch_current_oracle_release_is_accepted(self):
        result, _ = self.run_case(
            "catalogue", fixture_files={"oracle-release.tsv": "oraclelinux-release\t0\t9.8\t1.el9\tnoarch\tOracle America\toraclelinux-release-0:9.8-1.el9.noarch\n"}
        )
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)

    def test_future_catalogue_stops_real_main_before_any_source_mutation(self):
        result, reports = self.run_case(
            "catalogue-main", fixture_files={"oracle-release.tsv": "oraclelinux-release\t0\t9.9\t1.el9_9\tx86_64\tOracle America\toraclelinux-release-0:9.9-1.el9_9.x86_64\n"}
        )
        output = result.stdout + result.stderr
        self.assertEqual(result.returncode, 1, output)
        self.assertIn("not Oracle Linux 9.8", output)
        self.assertNotIn("UNEXPECTED_MAIN_MUTATION", output)
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])
        self.assertIn("\t9.9\t", reports["target-catalogue.oracle-release.tsv"])

    def test_missing_release_catalogue_is_rejected(self):
        self.assert_rejected("catalogue", fixture_files={"oracle-release.tsv": ""})

    def test_non_oracle_release_catalogue_is_rejected(self):
        self.assert_rejected(
            "catalogue", fixture_files={"oracle-release.tsv": "oraclelinux-release\t0\t9.8\t1.el9\tx86_64\tRed Hat, Inc.\toraclelinux-release-0:9.8-1.el9.x86_64\n"}
        )

    def test_ambiguous_release_catalogue_is_rejected(self):
        self.assert_rejected(
            "catalogue",
            fixture_files={"oracle-release.tsv": "oraclelinux-release\t0\t9.8\t1.el9\tx86_64\tOracle America\toraclelinux-release-0:9.8-1.el9.x86_64\noraclelinux-release\t0\t9.8\t2.el9\tx86_64\tOracle America\toraclelinux-release-0:9.8-2.el9.x86_64\n"},
        )

    def test_release_catalogue_query_failure_is_rejected(self):
        self.assert_rejected("catalogue", overrides={"MOCK_REPOQUERY_FAIL": "1"})

    def test_matching_updated_oracle_libraries_form_an_exact_eligible_plan(self):
        source = "libattr\t0\t2.6.0\t1.el9_8\tx86_64\tRed Hat, Inc.\nlibacl\t0\t2.4.0\t1.el9_8\tx86_64\tRed Hat, Inc.\n"
        names = "libattr-2.6.0-1.el9_8.x86_64\nlibacl-2.4.0-1.el9_8.x86_64\n"
        result, reports = self.run_case(
            "matching-library-plan",
            state_files={"rpmdb.before.tsv": source, "rpmdb.before.nevra": names},
            fixture_files={"oracle-available.tsv": "libattr\t0\t2.6.0\t1.el9_8\tx86_64\tlibattr-2.6.0-1.el9_8.x86_64\nlibacl\t0\t2.4.0\t1.el9_8\tx86_64\tlibacl-2.4.0-1.el9_8.x86_64\n"},
        )
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertEqual(set(reports["exact-standard-reinstall.nevra"].splitlines()), set(names.splitlines()))
        self.assertEqual(reports["nearest-lower-reinstall.nevra"], "")

    def test_bootchain_targets_are_deferred_out_of_all_bulk_plans(self):
        shim = "shim-x64-15.8-12.el9.x86_64"
        grub = "grub2-efi-x64-2.06-115.el9.x86_64"
        lower = "shim-ia32-15.8-10.el9.x86_64"
        result, reports = self.run_case(
            "defer-plan",
            state_files={
                "exact-reinstall.tsv": PLAN_HEADER + f"{shim}\t{shim}\tshim-x64\t0\t15.8\t12.el9\t12.el9\tx86_64\texact\n",
                "nearest-reinstall.tsv": PLAN_HEADER + f"grub2-efi-x64-2.06-114.el9.x86_64\t{grub}\tgrub2-efi-x64\t0\t2.06\t114.el9\t115.el9\tx86_64\tnearest-higher-release\nshim-ia32-15.8-12.el9.x86_64\t{lower}\tshim-ia32\t0\t15.8\t12.el9\t10.el9\tx86_64\tnearest-lower-release\n",
                "unavailable-reinstall.tsv": PLAN_HEADER,
                "exact-standard-reinstall.nevra": f"{shim}\nlibexample-1.0-1.el9.x86_64\n",
                "nearest-higher-standard-reinstall.nevra": f"{grub}\nother-example-1.0-2.el9.x86_64\n",
                "nearest-lower-reinstall.nevra": f"{lower}\nlow-example-1.0-1.el9.x86_64\n",
            },
        )
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertEqual(set(reports["bootchain-deferred.nevra"].splitlines()), {shim, grub, lower})
        for plan in ("exact-standard-reinstall.nevra", "nearest-higher-standard-reinstall.nevra", "nearest-lower-reinstall.nevra"):
            self.assertNotRegex(reports[plan], r"(?m)^(shim|grub2)-")
            self.assertIn("example", reports[plan])

    def test_missing_oracle_bootchain_replacement_is_rejected(self):
        self.assert_rejected(
            "defer-plan", state_files={"unavailable-reinstall.tsv": PLAN_HEADER + "shim-x64-15.8-12.el9.x86_64\t\tshim-x64\t0\t15.8\t12.el9\t\tx86_64\tunavailable\n"}
        )

    def test_deferred_bootchain_solver_accepts_only_planned_oracle_kernel(self):
        result, reports = self.run_case(
            "deferred-solver", overrides={"MOCK_REQUIRE_KERNEL_FOR_BOOTCHAIN": "1"}, state_files={"bootchain-deferred.nevra": "shim-x64-15.8-12.el9.x86_64\n"}
        )
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertIn("kernel-core", reports["bootchain-deferred.solver.log"])
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])

    def test_actual_padded_dnf_kernel_table_is_accepted_for_its_exact_plan(self):
        result, _ = self.run_case(
            "solver",
            overrides={"MOCK_SOLVER_OUTPUT": OBSERVED_KERNEL_SOLVER_TABLE},
            state_files={"kernel-install.nevra": "kernel-0:5.14.0-687.5.3.el9_8.x86_64\ngrubby\n"},
        )
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)

    def test_actual_nearest_lower_dnf_table_is_rejected_as_unsafe_bulk_work(self):
        reports = self.assert_rejected(
            "package-solver",
            overrides={"MOCK_SOLVER_OUTPUT": OBSERVED_LOWER_SOLVER_TABLE},
            state_files={"nearest-lower-reinstall.nevra": "libexample-1.0-1.el9.x86_64\n"},
        )
        self.assertIn("kernel-modules-core", reports["fixture-nearest.solver.log"])
        self.assertIn("libattr", reports["fixture-nearest.solver.log"])
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])

    def test_kernel_action_under_an_unknown_heading_is_rejected(self):
        self.assert_rejected(
            "solver",
            overrides={"MOCK_SOLVER_OUTPUT": f"Dependencies resolved.\nRelocating packages:\n kernel-core x86_64 {VERSION.removesuffix('.x86_64')} ol9_baseos_latest 17 M\nTransaction Summary\nOperation aborted."},
        )

    def test_wrapped_kernel_action_row_is_rejected(self):
        self.assert_rejected(
            "solver",
            overrides={"MOCK_SOLVER_OUTPUT": f"Dependencies resolved.\nInstalling:\n kernel-modules-core\n x86_64 {VERSION.removesuffix('.x86_64')} ol9_baseos_latest 32 M\nTransaction Summary\nOperation aborted."},
        )

    def test_matching_kernel_from_unexpected_repository_is_rejected(self):
        self.assert_rejected(
            "solver",
            overrides={"MOCK_SOLVER_OUTPUT": f"Dependencies resolved.\nInstalling:\n kernel-core x86_64 {VERSION.removesuffix('.x86_64')} rhel_baseos 17 M\nTransaction Summary\nOperation aborted."},
        )

    def test_deferred_bootchain_solver_rejects_another_kernel_version(self):
        self.assert_rejected(
            "deferred-solver",
            overrides={"MOCK_SOLVER_OUTPUT": "Dependencies resolved.\nInstalling:\n kernel-core x86_64 5.14.0-unplanned.el9 ol9_baseos_latest\nTransaction Summary\nOperation aborted."},
            state_files={"bootchain-deferred.nevra": "shim-x64-15.8-12.el9.x86_64\n"},
        )

    def test_deferred_bootchain_solver_rejects_a_critical_library_downgrade(self):
        self.assert_rejected(
            "deferred-solver",
            overrides={"MOCK_SOLVER_OUTPUT": "Dependencies resolved.\nDowngrading:\n libacl x86_64 2.3.1-4.el9 ol9_baseos_latest\nTransaction Summary\nOperation aborted."},
            state_files={"bootchain-deferred.nevra": "shim-x64-15.8-12.el9.x86_64\n"},
        )

    def test_bootchain_dependency_cannot_install_a_kernel_before_kernel_phase(self):
        reports = self.assert_rejected("premature-bootchain", overrides={"MOCK_REQUIRE_KERNEL_FOR_BOOTCHAIN": "1"})
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])
        self.assertIn("--exclude=shim-*", reports["mock.calls"])
        self.assertIn("--exclude=grub2-*", reports["mock.calls"])

    def test_deferred_bootchain_installs_after_kernel_and_checks_exact_vendor(self):
        result, reports = self.run_case(
            "deferred-bootchain", overrides={"MOCK_REQUIRE_KERNEL_FOR_BOOTCHAIN": "1"}, state_files={"bootchain-deferred.nevra": "shim-x64-15.8-12.el9.x86_64\ngrub2-common-2.06-115.el9.noarch\n"}
        )
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        calls = reports["mock.calls"].splitlines()
        mutations = [line for line in calls if line.startswith("mock_dnf -y ")]
        self.assertEqual(len(mutations), 3, reports["mock.calls"])
        self.assertIn(f"install kernel-0:{VERSION} grubby", mutations[0])
        self.assertIn("install shim-x64-15.8-12.el9.x86_64 grub2-common-2.06-115.el9.noarch", mutations[1])
        self.assertIn("reinstall shim-x64-15.8-12.el9.x86_64 grub2-common-2.06-115.el9.noarch", mutations[2])
        self.assertLess(next(i for i, line in enumerate(calls) if line.startswith("grubby --set-default")), calls.index(mutations[1]))
        for line in mutations[1:]:
            self.assertNotIn("--exclude=shim-*", line)
            self.assertNotIn("--exclude=grub2-*", line)
            self.assertIn("--exclude=kernel-core", line.split())
        self.assertIn("rpm -q shim-x64-15.8-12.el9.x86_64 --qf %{VENDOR}", reports["mock.calls"])

    def test_deferred_bootchain_rejects_failed_kernel_artifacts_before_shim(self):
        reports = self.assert_rejected(
            "deferred-bootchain", remove=("initramfs",), state_files={"bootchain-deferred.nevra": "shim-x64-15.8-12.el9.x86_64\n"}
        )
        self.assertNotIn("mock.bootchain-installed", reports)

    def test_deferred_bootchain_rejects_unchanged_source_vendor(self):
        self.assert_rejected(
            "deferred-bootchain", overrides={"MOCK_DEFERRED_VENDOR_WRONG": "1"}, state_files={"bootchain-deferred.nevra": "shim-x64-15.8-12.el9.x86_64\n"}
        )

    def test_deferred_bootchain_rejects_missing_exact_target(self):
        self.assert_rejected(
            "deferred-bootchain", overrides={"MOCK_DEFERRED_ABSENT": "1"}, state_files={"bootchain-deferred.nevra": "shim-x64-15.8-12.el9.x86_64\n"}
        )

    def test_deferred_bootchain_rejects_package_file_verification_failure(self):
        self.assert_rejected(
            "deferred-bootchain", overrides={"MOCK_BOOTCHAIN_VERIFY_FAIL": "1"}, state_files={"bootchain-deferred.nevra": "shim-x64-15.8-12.el9.x86_64\n"}
        )

    def test_deferred_bootchain_verifies_nonempty_oracle_owned_efi_binary(self):
        result, reports = self.run_case(
            "deferred-bootchain",
            extra_dirs=("firmware/efi",),
            fixture_files={"boot/efi/EFI/oracle/shimx64.efi": "fixture EFI binary\n"},
            state_files={"bootchain-deferred.nevra": "shim-x64-15.8-12.el9.x86_64\n"},
        )
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertIn("shimx64.efi", reports["bootchain-efi-files.tsv"])
        self.assertIn("rpm -V --noconfig shim-x64-15.8-12.el9.x86_64", reports["mock.calls"])

    def fat_timestamp_case_options(self, **overrides):
        options = {
            "overrides": {"MOCK_BOOTCHAIN_VERIFY_OUTPUT": ".......T.    @BOOT_DIR@/efi/EFI/oracle/shimx64.efi"},
            "extra_dirs": ("firmware/efi",),
            "fixture_files": {"boot/efi/EFI/oracle/shimx64.efi": "fixture EFI binary\n"},
            "fixture_mtimes_ns": {"boot/efi/EFI/oracle/shimx64.efi": 1767725054_000000000},
            "state_files": {"bootchain-deferred.nevra": "shim-x64-15.8-12.el9.x86_64\n"},
        }
        for key, value in overrides.items():
            if isinstance(value, dict):
                options[key].update(value)
            else:
                options[key] = value
        return options

    def test_observed_seven_fat_timestamp_only_rows_pass_with_exact_two_second_truncation(self):
        paths = (
            "EFI/BOOT/BOOTX64.EFI", "EFI/BOOT/fbx64.efi", "EFI/redhat/BOOTX64.CSV",
            "EFI/redhat/mmx64.efi", "EFI/redhat/shim.efi", "EFI/redhat/shimx64-oracle.efi",
            "EFI/redhat/shimx64.efi",
        )
        rows = "\n".join(f".......T.    @BOOT_DIR@/efi/{path}" for path in paths)
        files = {f"boot/efi/{path}": "fixture EFI payload\n" for path in paths}
        files["bootchain-file-times.tsv"] = "".join(f"@BOOT_DIR@/efi/{path}\t1767725055\n" for path in paths)
        options = self.fat_timestamp_case_options(
            overrides={"MOCK_BOOTCHAIN_VERIFY_OUTPUT": rows}, fixture_files=files,
            fixture_mtimes_ns={f"boot/efi/{path}": 1767725054_000000000 for path in paths},
        )
        result, reports = self.run_case("deferred-bootchain", **options)
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertEqual(len(reports["bootchain-fat-mtime.tsv"].splitlines()), 8)
        self.assertEqual(len(reports["bootchain-rpm.verify"].splitlines()), 7)
        self.assertIn("accepted 7 exact FAT", result.stderr)

    def test_fat_timestamp_allowance_keeps_every_other_rpm_attribute_strict(self):
        for flags in ("S......T.", "..5....T.", ".M.....T.", ".....U.T.", "......GT.", ".......T?", "missing"):
            with self.subTest(flags=flags):
                options = self.fat_timestamp_case_options(overrides={
                    "MOCK_BOOTCHAIN_VERIFY_OUTPUT": f"{flags}    @BOOT_DIR@/efi/EFI/oracle/shimx64.efi"
                })
                self.assert_rejected("deferred-bootchain", **options)

    def test_timestamp_only_rows_on_nonfat_filesystem_are_rejected(self):
        self.assert_rejected("deferred-bootchain", **self.fat_timestamp_case_options(overrides={"MOCK_EFI_FSTYPE": "xfs"}))

    def test_timestamp_only_row_outside_esp_directory_is_rejected(self):
        self.assert_rejected("deferred-bootchain", **self.fat_timestamp_case_options(
            overrides={"MOCK_BOOTCHAIN_VERIFY_OUTPUT": ".......T.    @BOOT_DIR@/other/shimx64.efi"},
            fixture_files={"boot/other/shimx64.efi": "fixture binary\n"},
        ))

    def test_fat_timestamp_offsets_other_than_exact_odd_second_truncation_are_rejected(self):
        for actual in (1767725052, 1767725053, 1767725055, 1767725056):
            with self.subTest(actual=actual):
                self.assert_rejected("deferred-bootchain", **self.fat_timestamp_case_options(
                    fixture_mtimes_ns={"boot/efi/EFI/oracle/shimx64.efi": actual * 1_000000000},
                ))

    def test_even_rpm_timestamp_cannot_use_fat_timestamp_allowance(self):
        self.assert_rejected("deferred-bootchain", **self.fat_timestamp_case_options(
            overrides={"MOCK_EFI_EXPECTED_MTIME": "1767725056"},
            fixture_mtimes_ns={"boot/efi/EFI/oracle/shimx64.efi": 1767725055_000000000},
        ))

    def test_fat_timestamp_allowance_rejects_subsecond_file_time(self):
        self.assert_rejected("deferred-bootchain", **self.fat_timestamp_case_options(
            fixture_mtimes_ns={"boot/efi/EFI/oracle/shimx64.efi": 1767725054_100000000},
        ))

    def test_ambiguous_or_missing_rpm_file_time_is_rejected(self):
        row = "@BOOT_DIR@/efi/EFI/oracle/shimx64.efi\t1767725055\n"
        for manifest in (row + row, "", row.replace("1767725055", "invalid")):
            with self.subTest(manifest=manifest):
                self.assert_rejected("deferred-bootchain", **self.fat_timestamp_case_options(
                    fixture_files={"bootchain-file-times.tsv": manifest},
                ))

    def test_fat_allowance_rejects_unexpected_rpm_verify_status(self):
        for status in ("0", "2"):
            with self.subTest(status=status):
                self.assert_rejected("deferred-bootchain", **self.fat_timestamp_case_options(
                    overrides={"MOCK_BOOTCHAIN_VERIFY_STATUS": status},
                ))

    def test_fat_allowance_rejects_unverifiable_filesystem_or_package_metadata(self):
        for setting in ("MOCK_FINDMNT_STATUS", "MOCK_BOOTCHAIN_FILETIMES_STATUS", "MOCK_EFI_OWNER_MISSING"):
            with self.subTest(setting=setting):
                self.assert_rejected("deferred-bootchain", **self.fat_timestamp_case_options(overrides={setting: "1"}))

    def test_fat_allowance_requires_single_oracle_file_owner(self):
        for vendor in ("Red Hat, Inc.", "Oracle America\nRed Hat, Inc."):
            with self.subTest(vendor=vendor):
                self.assert_rejected("deferred-bootchain", **self.fat_timestamp_case_options(overrides={"MOCK_EFI_OWNER_VENDOR": vendor}))

    def test_fat_allowance_rejects_empty_payload(self):
        self.assert_rejected("deferred-bootchain", **self.fat_timestamp_case_options(
            fixture_files={"boot/efi/EFI/oracle/shimx64.efi": ""},
        ))

    def test_uefi_missing_binary_is_rejected(self):
        self.assert_rejected(
            "deferred-bootchain", extra_dirs=("firmware/efi",), state_files={"bootchain-deferred.nevra": "shim-x64-15.8-12.el9.x86_64\n"}
        )

    def test_uefi_empty_binary_is_rejected(self):
        self.assert_rejected(
            "deferred-bootchain",
            extra_dirs=("firmware/efi",),
            fixture_files={"boot/efi/EFI/oracle/shimx64.efi": ""},
            state_files={"bootchain-deferred.nevra": "shim-x64-15.8-12.el9.x86_64\n"},
        )

    def test_uefi_binary_owned_by_source_vendor_is_rejected(self):
        self.assert_rejected(
            "deferred-bootchain",
            extra_dirs=("firmware/efi",),
            fixture_files={"boot/efi/EFI/oracle/shimx64.efi": "fixture EFI binary\n"},
            overrides={"MOCK_EFI_OWNER_VENDOR": "Red Hat, Inc."},
            state_files={"bootchain-deferred.nevra": "shim-x64-15.8-12.el9.x86_64\n"},
        )

    def test_libacl_downgrade_remains_ineligible(self):
        reports = self.assert_rejected("eligibility", state_files={"nearest-lower-reinstall.nevra": "libacl-2.3.1-4.el9.x86_64\n"})
        self.assertEqual(reports["libacl-transition.blocked.nevra"], "libacl-2.3.1-4.el9.x86_64\n")

    def test_direct_libacl_downgrade_cannot_bypass_eligibility(self):
        reports = self.assert_rejected("nonkernel-libacl")
        self.assertNotIn("mock_dnf", reports.get("mock.calls", ""))

    def test_observed_rhel_libattr_transition_stops_main_before_source_changes(self):
        result, reports = self.run_case(
            "eligibility-main",
            state_files={
                "nearest-reinstall.tsv": "source_nevra\ttarget_nevra\tname\tepoch\tversion\tsource_release\ttarget_release\tarch\tstatus\n" + OBSERVED_LIBATTR_DOWNGRADE,
                "nearest-lower-reinstall.nevra": "libattr-2.5.1-3.el9.x86_64\n",
            },
        )
        output = result.stdout + result.stderr
        self.assertEqual(result.returncode, 1, output)
        self.assertRegex(output, r"libattr downgrade .*not qualified")
        self.assertIn("RHEL 9.8", output)
        self.assertNotIn("UNEXPECTED_MAIN_MUTATION", output)
        self.assertNotIn("SAFETY_GATE_PASSED", output)
        self.assertNotIn("mock_dnf", reports.get("mock.calls", ""))
        self.assertEqual(reports["libattr-transition.blocked.nevra"], "libattr-2.5.1-3.el9.x86_64\n")

    def test_noncritical_lower_plan_remains_eligible(self):
        result, _ = self.run_case("eligibility", state_files={"nearest-lower-reinstall.nevra": "libexample-1.0-1.el9.x86_64\n"})
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)

    def test_direct_libattr_downgrade_cannot_bypass_eligibility_block(self):
        reports = self.assert_rejected("nonkernel-libattr")
        self.assertNotIn("mock_dnf", reports.get("mock.calls", ""))

    def test_solver_added_libattr_downgrade_is_rejected(self):
        reports = self.assert_rejected(
            "nonkernel",
            overrides={"MOCK_SOLVER_OUTPUT": "Dependencies resolved.\nDowngrading:\n libattr x86_64 2.5.1-3.el9 oracle\nTransaction Summary\nOperation aborted."},
        )
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])

    def test_bulk_solver_cannot_add_a_runtime_kernel(self):
        reports = self.assert_rejected(
            "nonkernel",
            overrides={"MOCK_SOLVER_OUTPUT": "Dependencies resolved.\nInstalling:\n kernel-core x86_64 5.14.0-611.5.1.el9_8 oracle\nTransaction Summary\nOperation aborted."},
        )
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])

    def test_bulk_solver_cannot_remove_a_runtime_kernel(self):
        reports = self.assert_rejected(
            "nonkernel",
            overrides={"MOCK_SOLVER_OUTPUT": "Dependencies resolved.\nRemoving:\n kernel x86_64 5.14.0-611.5.1.el9_8 @rhel\nTransaction Summary\nOperation aborted."},
        )
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])

    def test_bulk_solver_cannot_obsolete_a_runtime_kernel(self):
        reports = self.assert_rejected(
            "nonkernel",
            overrides={"MOCK_SOLVER_OUTPUT": "Dependencies resolved.\nInstalling:\n libexample x86_64 1.0-1.el9 oracle\n replacing kernel-core x86_64 5.14.0-611.5.1.el9_8 @rhel\nTransaction Summary\nOperation aborted."},
        )
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])

    def test_nonkernel_solver_and_transaction_use_identical_protected_arguments(self):
        result, reports = self.run_case("nonkernel")
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        calls = reports["mock.calls"].splitlines()
        solver = next(line for line in calls if line.startswith("mock_dnf --assumeno "))
        actual = next(line for line in calls if line.startswith("mock_dnf -y "))
        self.assertEqual(
            solver.removeprefix("mock_dnf --assumeno --setopt=assumeyes=False "),
            actual.removeprefix("mock_dnf -y "),
        )
        for package in ("kernel", "kernel-core", "kernel-modules", "kernel-modules-core", "kernel-modules-extra", "kernel-uek", "kernel-uek-core", "kernel-uek-modules", "kernel-uek-modules-core", "kernel-uek-modules-extra"):
            self.assertIn(f"--exclude={package}", actual.split())
        self.assertTrue(any(line.startswith("python3 ") for line in calls[calls.index(actual) + 1:]))
        self.assertIn("Installed: kernel", reports["fixture-nearest.transaction.log"])

    def test_nonkernel_zero_exit_loader_error_is_rejected_and_logged(self):
        line = "systemctl: error while loading shared libraries: libattr.so.1: cannot open shared object file"
        reports = self.assert_rejected("nonkernel", overrides={"MOCK_TRANSACTION_OUTPUT": line})
        self.assertIn(line, reports["fixture-nearest.transaction.log"])

    def test_nonkernel_zero_exit_scriptlet_failure_is_rejected_and_logged(self):
        line = "warning: %posttrans(kernel-core) scriptlet failed, exit status 1"
        reports = self.assert_rejected("nonkernel", overrides={"MOCK_TRANSACTION_OUTPUT": line})
        self.assertIn(line, reports["fixture-nearest.transaction.log"])

    def test_nonkernel_nonzero_transaction_exit_is_rejected_and_logged(self):
        reports = self.assert_rejected("nonkernel", overrides={"MOCK_TRANSACTION_STATUS": "1", "MOCK_TRANSACTION_OUTPUT": "DNF failed"})
        self.assertIn("DNF failed", reports["fixture-nearest.transaction.log"])

    def test_nonkernel_runtime_is_rechecked_after_the_transaction(self):
        reports = self.assert_rejected("nonkernel", overrides={"MOCK_BREAK_RUNTIME_AFTER_TRANSACTION": "1"})
        self.assertIn("fixture-nearest.transaction.log", reports)
        self.assertEqual(reports["mock.mutations"].strip(), "1")
        self.assertIn("OSError: libattr.so.1", reports["migration.log"])

    def test_next_transaction_is_resolved_again_after_the_rpmdb_changes(self):
        reports = self.assert_rejected("nonkernel-sequence", overrides={"MOCK_SECOND_SOLVER_FAIL": "1"})
        calls = reports["mock.calls"].splitlines()
        self.assertEqual(sum(line.startswith("mock_dnf --assumeno ") for line in calls), 2)
        self.assertEqual(sum(line.startswith("mock_dnf -y ") for line in calls), 1)
        self.assertNotIn("fixture-second.transaction.log", reports)

    def test_each_nearest_batch_resolves_against_the_current_rpmdb(self):
        reports = self.assert_rejected(
            "nearest",
            overrides={"MOCK_SECOND_SOLVER_FAIL": "1"},
            state_files={"nearest-lower-reinstall.nevra": "".join(f"example-{number}-1.0-1.el9.x86_64\n" for number in range(1, 82))},
        )
        mutations = [line for line in reports["mock.calls"].splitlines() if line.startswith("mock_dnf -y ")]
        self.assertEqual(len(mutations), 1)
        self.assertIn("example-80-1.0-1.el9.x86_64", mutations[0])
        self.assertNotIn("example-81-1.0-1.el9.x86_64", mutations[0])

    def test_kernel_installation_routes_through_checked_transaction(self):
        body = re.search(r"(?ms)^install_selected_kernel\(\) \{\n(.*?)^\}", self.source).group(1)
        self.assertLess(body.index("check_kernel_install_dependencies"), body.index("run_kernel_transaction"))
        self.assertEqual(body.count('run_kernel_transaction "$DNF_CMD"'), 2)
        self.assertGreater(body.index("set_default_boot_kernel"), body.rindex("run_kernel_transaction"))

    def test_install_selected_kernel_uses_complete_gate_chain(self):
        result, reports = self.run_case("install")
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        calls = reports["mock.calls"]
        self.assertLess(calls.index("rpm -V libattr"), calls.index("mock_dnf"))
        self.assertLess(calls.index("mock_dnf"), calls.index("grubby --set-default"))
        self.assertIn(f"install kernel-0:{VERSION} grubby", calls)

    def test_exact_oracle_kernel_plan_is_installed_with_newer_source_kernel_present(self):
        result, reports = self.run_case("resolve-install", overrides={"MOCK_OTHER_VENDOR_NEWER": "1"})
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertEqual(reports["kernel-install.nevra"], f"kernel-0:{VERSION}\ngrubby\n")
        installs = [line for line in reports["mock.calls"].splitlines() if line.startswith("mock_dnf -y ")]
        self.assertEqual(len(installs), 1, reports["mock.calls"])
        self.assertIn(f"install kernel-0:{VERSION} grubby", installs[0])
        self.assertNotIn("install kernel grubby", installs[0])

    def test_equal_source_kernel_collision_stops_before_solver_or_install(self):
        reports = self.assert_rejected("resolve-install", overrides={"MOCK_KERNEL_COLLISION": "1"})
        calls = reports["mock.calls"]
        self.assertIn("repoquery", calls)
        self.assertNotIn("--assumeno", calls)
        self.assertNotIn("mock_dnf -y", calls)
        self.assertNotIn("grubby --set-default", calls)

    def test_equal_source_kernel_core_collision_is_also_rejected(self):
        reports = self.assert_rejected(
            "resolve-install", overrides={"MOCK_KERNEL_COLLISION": "1", "MOCK_KERNEL_COLLISION_PACKAGE": "kernel-core"}
        )
        self.assertIn(f"rpm -q kernel-core-0:{VERSION}", reports["mock.calls"])
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])

    def test_missing_exact_kernel_plan_stops_before_install(self):
        reports = self.assert_rejected("install", state_files={"kernel-install.nevra": ""})
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])

    def test_kernel_metadata_query_failure_is_rejected_before_install(self):
        reports = self.assert_rejected("resolve-install", overrides={"MOCK_REPOQUERY_FAIL": "1"})
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])

    def test_non_oracle_kernel_metadata_is_rejected(self):
        self.assert_rejected(
            "resolve", fixture_files={"kernel-available.tsv": f"kernel\t0\t5.14.0\t611.5.1.el9_8\tx86_64\tRed Hat, Inc.\tkernel-0:{VERSION}\n"}
        )

    def test_resolved_declined_kernel_solver_is_accepted_without_mutation(self):
        result, reports = self.run_case("solver")
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertEqual(reports["kernel-install.solver.status"].strip(), "1")
        self.assertIn("--assumeno --setopt=assumeyes=False", reports["mock.calls"])
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])

    def test_kernel_solver_dependency_error_is_rejected(self):
        self.assert_rejected("solver", overrides={"MOCK_SOLVER_OUTPUT": "Error: conflicting requests\nProblem: nothing provides libattr.so.1"})

    def test_kernel_solver_that_would_remove_packages_is_rejected(self):
        self.assert_rejected(
            "solver",
            overrides={"MOCK_SOLVER_OUTPUT": "Dependencies resolved.\nRemoving:\n libattr x86_64\nTransaction Summary\nOperation aborted."},
        )

    def test_kernel_solver_unrecognized_exit_is_rejected(self):
        self.assert_rejected("solver", overrides={"MOCK_SOLVER_STATUS": "2"})

    def test_kernel_solver_nothing_to_do_requires_installed_oracle_identity(self):
        self.assert_rejected("solver", overrides={"MOCK_SOLVER_STATUS": "0", "MOCK_SOLVER_OUTPUT": "Nothing to do."})

    def test_kernel_solver_nothing_to_do_accepts_exact_installed_oracle(self):
        result, _ = self.run_case(
            "solver", overrides={"MOCK_SOLVER_STATUS": "0", "MOCK_SOLVER_OUTPUT": "Nothing to do.", "MOCK_EXACT_KERNEL_INSTALLED": "1"}
        )
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)

    def test_all_reinstall_plan_solvers_are_checked_without_package_mutation(self):
        result, reports = self.run_case(
            "reinstall-solvers",
            state_files={
                "exact-standard-reinstall.nevra": "libfoo-1.0-1.el9.x86_64\n",
                "nearest-higher-standard-reinstall.nevra": "libbar-1.0-2.el9.x86_64\n",
                "nearest-lower-reinstall.nevra": "libexample-2.4.48-5.el9.x86_64\n",
                "update-exception-reinstall.nevra": "tzdata-2026a-1.el9.noarch\n",
            },
        )
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        calls = reports["mock.calls"]
        for label in ("exact-reinstall", "nearest-higher", "nearest-lower", "update-exception"):
            self.assertEqual(reports[f"{label}.solver.status"].strip(), "1")
        self.assertIn("reinstall libfoo-1.0-1.el9.x86_64", calls)
        self.assertIn("--nobest install libbar-1.0-2.el9.x86_64", calls)
        self.assertIn("downgrade libexample-2.4.48-5.el9.x86_64", calls)
        self.assertIn("--enablerepo=ol9_baseos_exception_latest install tzdata-2026a-1.el9.noarch", calls)
        self.assertNotIn("mock_dnf -y", calls)

    def test_nearest_lower_solver_blocker_stops_before_later_plans(self):
        reports = self.assert_rejected(
            "reinstall-solvers",
            overrides={"MOCK_SOLVER_FAIL_OPERATION": "downgrade"},
            state_files={
                "exact-standard-reinstall.nevra": "libfoo-1.0-1.el9.x86_64\n",
                "nearest-higher-standard-reinstall.nevra": "libbar-1.0-2.el9.x86_64\n",
                "nearest-lower-reinstall.nevra": "libexample-2.4.48-5.el9.x86_64\n",
                "update-exception-reinstall.nevra": "tzdata-2026a-1.el9.noarch\n",
            },
        )
        self.assertIn("nearest-lower.solver.log", reports)
        self.assertNotIn("update-exception.solver.log", reports)
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])

    def test_reinstall_solver_rejects_unproven_abort(self):
        self.assert_rejected(
            "package-solver",
            overrides={"MOCK_SOLVER_OUTPUT": "Operation aborted."},
            state_files={"nearest-lower-reinstall.nevra": "libexample-2.4.48-5.el9.x86_64\n"},
        )

    def test_reinstall_solver_rejects_dependency_removals(self):
        self.assert_rejected(
            "package-solver",
            overrides={"MOCK_SOLVER_OUTPUT": "Dependencies resolved.\nRemoving dependent packages:\n systemd\nTransaction Summary\nOperation aborted."},
            state_files={"nearest-lower-reinstall.nevra": "libexample-2.4.48-5.el9.x86_64\n"},
        )

    def test_branding_swap_solver_accepts_only_the_expected_source_removal(self):
        result, reports = self.run_case("branding-solver", overrides={"MOCK_SOLVER_OUTPUT": BRANDING_SOLVER_OUTPUT})
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertEqual(reports["branding-swap.solver.status"].strip(), "1")
        self.assertIn("swap redhat-logos-httpd-90.0-9.el9.noarch oracle-logos-httpd", reports["mock.calls"])
        self.assertNotIn("mock_dnf -y", reports["mock.calls"])

    def test_branding_swap_solver_rejects_another_package_removal(self):
        self.assert_rejected(
            "branding-solver",
            overrides={"MOCK_SOLVER_OUTPUT": BRANDING_SOLVER_OUTPUT.replace("Transaction Summary", " systemd x86_64 252-1.el9 @rhel\nTransaction Summary")},
        )

    def test_branding_swap_solver_rejects_wrapped_removal_rows(self):
        self.assert_rejected(
            "branding-solver",
            overrides={"MOCK_SOLVER_OUTPUT": BRANDING_SOLVER_OUTPUT.replace("redhat-logos-httpd noarch", "redhat-logos-httpd\n noarch")},
        )

    def test_branding_swap_solver_rejects_a_plan_without_expected_removal(self):
        self.assert_rejected("branding-solver")

    def test_branding_swap_solver_skips_an_absent_source_branding_package(self):
        result, reports = self.run_case("branding-solver", overrides={"MOCK_BRANDING_ABSENT": "1"})
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertNotIn("mock_dnf", reports["mock.calls"])

    def test_branding_swap_solver_rejects_ambiguous_source_identity(self):
        reports = self.assert_rejected(
            "branding-solver", overrides={"MOCK_BRANDING_NVRA": "redhat-logos-httpd-90.0-9.el9.noarch\nredhat-logos-httpd-91.0-1.el9.noarch"}
        )
        self.assertNotIn("mock_dnf", reports["mock.calls"])

    def test_source_only_package_plan_does_not_erase_without_dry_run(self):
        source = "subscription-manager\t0\t1.29.0\t1.el9\tx86_64\tRed Hat, Inc.\n"
        result, reports = self.run_case(
            "source-removal-plan",
            state_files={"rpmdb.before.tsv": source},
            fixture_files={"rpmdb.tsv": source},
        )
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertIn("subscription-manager-1.29.0-1.el9.x86_64", reports["rhel-only-packages.removed.nevra"])
        self.assertNotIn("rpm -qa", reports["mock.calls"])
        self.assertNotIn("rpm -e", reports["mock.calls"])
        self.assertNotIn("mock_dnf", reports["mock.calls"])
        self.assertEqual(reports["fixture.rpmdb.tsv"], source)

    def test_bulk_nearest_replacements_do_not_silently_skip_broken_packages(self):
        result, reports = self.run_case(
            "nearest",
            state_files={
                "nearest-higher-reinstall.nevra": "libbar-1.0-2.el9.x86_64\n",
                "nearest-higher-standard-reinstall.nevra": "libbar-1.0-2.el9.x86_64\n",
                "nearest-lower-reinstall.nevra": "libexample-2.4.48-5.el9.x86_64\n",
            },
        )
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        calls = reports["mock.calls"]
        self.assertIn("install libbar-1.0-2.el9.x86_64", calls)
        self.assertIn("downgrade libexample-2.4.48-5.el9.x86_64", calls)
        self.assertNotIn("--skip-broken", calls)

    def test_bulk_nearest_failure_stops_the_script(self):
        self.assert_rejected(
            "nearest",
            expect_error=False,
            overrides={"MOCK_TRANSACTION_STATUS": "1"},
            state_files={"nearest-lower-reinstall.nevra": "libexample-2.4.48-5.el9.x86_64\n"},
        )

    def test_noncritical_same_name_replacement_uses_dnf_downgrade(self):
        result, reports = self.run_case(
            "replace",
            fixture_files={
                "rpmdb.tsv": "libexample\t0\t2.4.48\t9.el9\tx86_64\tRed Hat, Inc.\n",
                "rpmdb.after.tsv": "libexample\t0\t2.4.48\t5.el9\tx86_64\tOracle America\n",
                "oracle-available.tsv": "libexample\t0\t2.4.48\t5.el9\tx86_64\tlibexample-2.4.48-5.el9.x86_64\n",
            },
        )
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertRegex(reports["mock.calls"], r"mock_dnf .* downgrade libexample-2\.4\.48-5\.el9\.x86_64")
        self.assertNotIn("rpm -e", reports["mock.calls"])
        self.assertNotIn("--nodeps", reports["mock.calls"])
        self.assertEqual(reports["redhat-vendor-packages.after-reinstall.tsv"], "")

    def test_renamed_branding_replacement_uses_atomic_dnf_swap(self):
        result, reports = self.run_case(
            "replace",
            overrides={"MOCK_SOLVER_OUTPUT": BRANDING_SOLVER_OUTPUT},
            fixture_files={
                "rpmdb.tsv": "redhat-logos-httpd\t0\t90.0\t9.el9\tnoarch\tRed Hat, Inc.\n",
                "rpmdb.after.tsv": "oracle-logos-httpd\t0\t90.0\t5.el9\tnoarch\tOracle America\n",
                "oracle-available.tsv": "oracle-logos-httpd\t0\t90.0\t5.el9\tnoarch\toracle-logos-httpd-90.0-5.el9.noarch\n",
            },
        )
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertRegex(reports["mock.calls"], r"mock_dnf .* swap redhat-logos-httpd-90\.0-9\.el9\.noarch oracle-logos-httpd-90\.0-5\.el9\.noarch")
        self.assertNotIn("rpm -e", reports["mock.calls"])
        self.assertNotIn("--nodeps", reports["mock.calls"])

    def test_failed_fallback_replacement_retains_original_package(self):
        source = "libexample\t0\t2.4.48\t9.el9\tx86_64\tRed Hat, Inc.\n"
        reports = self.assert_rejected(
            "replace",
            expect_error=False,
            overrides={"MOCK_TRANSACTION_STATUS": "1"},
            fixture_files={
                "rpmdb.tsv": source,
                "rpmdb.after.tsv": "libexample\t0\t2.4.48\t5.el9\tx86_64\tOracle America\n",
                "oracle-available.tsv": "libexample\t0\t2.4.48\t5.el9\tx86_64\tlibexample-2.4.48-5.el9.x86_64\n",
            },
        )
        self.assertEqual(reports["fixture.rpmdb.tsv"], source)
        self.assertNotIn("rpm -e", reports["mock.calls"])

    def test_install_stops_before_transaction_when_runtime_is_unhealthy(self):
        reports = self.assert_rejected("install", overrides={"MOCK_LIBATTR_MISSING": "1"})
        self.assertNotIn("mock_dnf", reports.get("mock.calls", ""))

    def test_install_stops_before_boot_changes_after_scriptlet_error(self):
        reports = self.assert_rejected(
            "install", overrides={"MOCK_TRANSACTION_OUTPUT": "warning: %posttrans(kernel-core) scriptlet failed, exit status 1"}
        )
        self.assertNotIn("grubby --set-default", reports.get("mock.calls", ""))

    def test_observed_grub_probe_error_stops_kernel_before_explicit_boot_default_change(self):
        reports = self.assert_rejected(
            "install", overrides={"MOCK_TRANSACTION_OUTPUT": OBSERVED_GRUB_PROBE_ERROR}
        )
        self.assertIn(OBSERVED_GRUB_PROBE_ERROR, reports["kernel-install.transaction.log"])
        self.assertNotIn("grubby --set-default", reports.get("mock.calls", ""))

    def test_observed_grub_probe_error_stops_nonkernel_before_next_batch(self):
        reports = self.assert_rejected(
            "nonkernel-sequence", overrides={"MOCK_TRANSACTION_OUTPUT": OBSERVED_GRUB_PROBE_ERROR}
        )
        self.assertIn(OBSERVED_GRUB_PROBE_ERROR, reports["fixture-first.transaction.log"])
        self.assertNotIn("fixture-second", reports.get("mock.calls", ""))

    def test_grub_tool_fatal_errors_are_rejected_in_both_transaction_types(self):
        for operation in ("transaction", "nonkernel"):
            for line in (
                "/usr/sbin/grub2-probe: error: disk not found",
                "  grub2-mkconfig: ERROR: failed to write configuration",
                "grub-install: fatal: unable to write boot sector",
                "grubby: error: unable to set default",
            ):
                with self.subTest(operation=operation, line=line):
                    self.assert_rejected(operation, overrides={"MOCK_TRANSACTION_OUTPUT": line})

    def test_kernel_cleanup_functions_retain_source_kernels(self):
        result, reports = self.run_case("preserve")
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        calls = reports.get("mock.calls", "")
        self.assertNotIn("mock_dnf", calls)
        self.assertNotIn("rpm -e", calls)
        self.assertIn("retains all source runtime kernels", result.stderr)

    def test_missing_libattr_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_LIBATTR_MISSING": "1"})

    def test_missing_libacl_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_LIBACL_MISSING": "1"})

    def test_modified_libacl_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_LIBACL_CORRUPT": "1"})

    def test_unloadable_libacl_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_LIBACL_LOAD_FAIL": "1"})

    def test_modified_libattr_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_LIBATTR_CORRUPT": "1"})

    def test_unloadable_libattr_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_LIBRARY_LOAD_FAIL": "1"})

    def test_missing_dynamic_link_dependency_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_LINKAGE_MISSING": "1"})

    def test_failed_library_inspection_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_LDD_FAIL": "1"})

    def test_missing_dracut_package_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_DRACUT_MISSING": "1"})

    def test_unusable_dracut_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_DRACUT_FAIL": "1"})

    def test_dracut_module_listing_is_used_for_health_check(self):
        result, reports = self.run_case("dependencies")
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertIn("dracut --list-modules", reports["mock.calls"])
        self.assertNotIn("dracut --version", reports["mock.calls"])
        self.assertNotIn("dracut --help", reports["mock.calls"])

    def test_empty_dracut_module_list_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_DRACUT_EMPTY": "1"})

    def test_missing_dracut_install_helper_is_rejected(self):
        self.assert_rejected("dependencies", remove=("dracut-install",))

    def test_unusable_dracut_install_helper_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_DRACUT_INSTALL_FAIL": "1"})

    def test_modified_dracut_core_is_rejected(self):
        self.assert_rejected(
            "dependencies",
            overrides={"MOCK_DRACUT_HELPER_OWNER": "dracut-core", "MOCK_DRACUT_CORE_PRESENT": "1", "MOCK_DRACUT_CORE_CORRUPT": "1"},
        )

    def test_helper_owned_by_dracut_passes_without_dracut_core_package(self):
        result, reports = self.run_case("dependencies")
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertIn("rpm -V dracut\n", reports["mock.calls"])
        self.assertNotIn("rpm -V dracut-core", reports["mock.calls"])

    def test_helper_owned_by_dracut_core_passes_split_package_layout(self):
        result, reports = self.run_case(
            "dependencies", overrides={"MOCK_DRACUT_HELPER_OWNER": "dracut-core", "MOCK_DRACUT_CORE_PRESENT": "1"}
        )
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertIn("rpm -V dracut-core", reports["mock.calls"])

    def test_modified_dracut_helper_owner_package_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_DRACUT_CORRUPT": "1"})

    def test_unowned_dracut_helper_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_DRACUT_HELPER_UNOWNED": "1"})

    def test_dracut_helper_owned_by_unexpected_package_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_DRACUT_HELPER_OWNER": "unrelated-package"})

    def test_failed_grubby_dependency_probe_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_GRUBBY_READ_FAIL": "1"})

    def test_unusable_depmod_is_rejected(self):
        self.assert_rejected("dependencies", overrides={"MOCK_DEPMOD_FAIL": "1"})

    def test_nonzero_dnf_exit_is_rejected_and_logged(self):
        reports = self.assert_rejected(
            "transaction", overrides={"MOCK_TRANSACTION_STATUS": "1", "MOCK_TRANSACTION_OUTPUT": "DNF failed"}
        )
        self.assertIn("DNF failed", reports["kernel-install.transaction.log"])

    def test_transaction_log_write_failure_is_rejected(self):
        self.assert_rejected("transaction", overrides={"MOCK_TEE_FAIL": "1"})

    def test_zero_dnf_exit_with_scriptlet_error_is_rejected_and_logged(self):
        line = "warning: %posttrans(kernel-uek-core) scriptlet failed, exit status 1"
        reports = self.assert_rejected("transaction", overrides={"MOCK_TRANSACTION_OUTPUT": line})
        self.assertIn(line, reports["kernel-install.transaction.log"])

    def test_zero_dnf_exit_with_loader_error_is_rejected(self):
        self.assert_rejected(
            "transaction",
            overrides={"MOCK_TRANSACTION_OUTPUT": "cp: error while loading shared libraries: libattr.so.1: cannot open shared object file"},
        )

    def test_zero_dnf_exit_with_dracut_error_is_rejected(self):
        self.assert_rejected("transaction", overrides={"MOCK_TRANSACTION_OUTPUT": "dracut: FATAL: Could not install module"})

    def test_zero_dnf_exit_with_depmod_error_is_rejected(self):
        self.assert_rejected("transaction", overrides={"MOCK_TRANSACTION_OUTPUT": "depmod: ERROR: could not open modules directory"})

    def test_missing_kernel_image_is_rejected(self):
        self.assert_rejected("artifacts", remove=("image",))

    def test_empty_kernel_image_is_rejected(self):
        self.assert_rejected("artifacts", empty=("image",))

    def test_missing_initramfs_is_rejected(self):
        self.assert_rejected("artifacts", remove=("initramfs",))

    def test_empty_initramfs_is_rejected(self):
        self.assert_rejected("artifacts", empty=("initramfs",))

    def test_invalid_initramfs_is_rejected(self):
        self.assert_rejected("artifacts", overrides={"MOCK_INITRAMFS_INVALID": "1"})

    def test_initramfs_for_other_kernel_is_rejected(self):
        self.assert_rejected("artifacts", overrides={"MOCK_INITRAMFS_WRONG_VERSION": "1"})

    def test_initramfs_header_does_not_substitute_for_module_contents(self):
        self.assert_rejected("artifacts", overrides={"MOCK_INITRAMFS_HEADER_ONLY": "1"})

    def test_missing_module_directory_is_rejected(self):
        self.assert_rejected("artifacts", remove=("modules",))

    def test_missing_module_dependency_index_is_rejected(self):
        self.assert_rejected("artifacts", remove=("modules.dep",))

    def test_empty_kernel_module_tree_is_rejected(self):
        self.assert_rejected("artifacts", remove=("module",))

    def test_empty_kernel_module_file_is_rejected(self):
        self.assert_rejected("artifacts", empty=("module",))

    def test_source_vendor_kernel_is_not_used_as_oracle_fallback(self):
        self.assert_rejected("artifacts", overrides={"MOCK_VENDOR": "Red Hat, Inc."})

    def test_image_owned_by_source_vendor_is_rejected(self):
        self.assert_rejected("artifacts", overrides={"MOCK_OWNER_VENDOR": "Red Hat, Inc."})

    def test_image_owned_by_unrelated_package_is_rejected(self):
        self.assert_rejected("artifacts", overrides={"MOCK_OWNER": "unrelated-package"})

    def test_image_owned_by_different_kernel_version_is_rejected(self):
        self.assert_rejected("artifacts", overrides={"MOCK_OWNER_RELEASE": "1.old"})

    def test_missing_bootloader_entry_is_rejected(self):
        self.assert_rejected("artifacts", overrides={"MOCK_GRUBBY_INFO_FAIL": "1"})

    def test_bootloader_entry_with_wrong_initramfs_is_rejected(self):
        self.assert_rejected("artifacts", overrides={"MOCK_GRUBBY_INITRD_MISMATCH": "1"})

    def test_default_is_not_changed_when_artifacts_are_missing(self):
        reports = self.assert_rejected("set-default", remove=("initramfs",))
        self.assertNotIn("grubby --set-default", reports.get("mock.calls", ""))

    def test_failed_grubby_set_default_is_rejected(self):
        self.assert_rejected("set-default", overrides={"MOCK_GRUBBY_SET_FAIL": "1"})

    def test_grubby_set_default_without_effect_is_rejected(self):
        self.assert_rejected("set-default", overrides={"MOCK_GRUBBY_SET_NO_EFFECT": "1"}, initial_default="/boot/vmlinuz-old\n")

    def test_grubby_default_read_failure_is_rejected(self):
        self.assert_rejected("set-default", overrides={"MOCK_GRUBBY_READ_FAIL": "1"})

    def test_verification_rejects_wrong_default(self):
        self.assert_rejected("verify", initial_default="/boot/vmlinuz-old\n")

    def test_final_verification_rechecks_artifacts(self):
        self.assert_rejected("verify", remove=("image",))


if __name__ == "__main__":
    unittest.main(verbosity=2)
