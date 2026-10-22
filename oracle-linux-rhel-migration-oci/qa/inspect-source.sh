#!/usr/bin/env bash
# Source checks without package changes. Pass an independently recorded fresh
# instance OCID as argument 1, optionally the restored baseline directory as 2,
# and a GRUB inspection evidence directory as 3. Writes evidence only.
set -uo pipefail
expected_instance=${1:?Expected fresh instance OCID is required}
baseline_dir=${2:-"$HOME/ol-migration-evidence/before"}
failures=0
reviews=0
check() {
    local label=$1
    shift
    if "$@"; then
        printf 'PASS: %s\n' "$label"
    else
        printf 'FAIL: %s\n' "$label"
        failures=$((failures + 1))
    fi
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
check "RHEL 9.8 x86_64" bash -c '. /etc/os-release; test "$ID" = rhel && test "$VERSION_ID" = 9.8 && test "$(uname -m)" = x86_64'
check "fresh OCI instance identity" bash -o pipefail -c 'curl --fail --silent --show-error --noproxy "*" --max-time 10 -H "Authorization: Bearer Oracle" http://169.254.169.254/opc/v2/instance/ | python3 -c '\''import json,sys; actual=json.load(sys.stdin)["id"]; print(actual); assert actual==sys.argv[1]'\'' "$1"' _ "$expected_instance"
check "noninteractive sudo" sudo -n true
check "expected enabled RHUI repositories and no Oracle migration repositories" bash -o pipefail -c '
    sudo -n dnf -q repolist --enabled | python3 -c '\''
import re, sys
text = sys.stdin.read()
print(text)
repo_ids = {line.split()[0] for line in text.splitlines() if line.strip()}
required = {
    "rhui-rhel-9-for-x86_64-baseos-rhui-rpms",
    "rhui-rhel-9-for-x86_64-appstream-rhui-rpms",
}
missing = required - repo_ids
oracle = sorted(value for value in repo_ids if re.match(r"ol[0-9]+[_-]", value, re.I))
if missing or oracle:
    print("Missing RHUI IDs:", sorted(missing), "Oracle migration IDs:", oracle)
    raise SystemExit(1)
'\''
'
check "source package dependencies" sudo -n dnf check
check "RPM database" sudo -n rpm --verifydb
check "kernel installation packages" rpm -q libattr libacl dracut kmod coreutils grubby python3
check "library and dracut package integrity" sudo -n rpm -V libattr libacl dracut
check "libattr and libacl loaders" python3 -c 'import ctypes; ctypes.CDLL("libattr.so.1"); ctypes.CDLL("libacl.so.1")'
check "dracut helper" bash -c '/usr/lib/dracut/dracut-install --help >/dev/null'
check "dracut module listing" bash -c 'dracut --list-modules >/dev/null'
check "source boot files" sudo -n bash -c '
    set -euo pipefail
    version=$(rpm -q kernel-core --qf "%{VERSION}-%{RELEASE}.%{ARCH}\n" | sort -V | tail -1)
    test "$(uname -r)" = "$version"
    image="/boot/vmlinuz-$version"
    initramfs="/boot/initramfs-$version.img"
    test -s "$image"
    test -s "$initramfs"
    test -s "/lib/modules/$version/modules.dep"
    lsinitrd "$initramfs" >/dev/null
    grubby --info "$image"
    test "$(grubby --default-kernel)" = "$image"
'
grub_evidence_dir=${3:-"$HOME/ol-migration-evidence/source-grub-$(date -u +%Y%m%dT%H%M%SZ)"}
check "read-only source GRUB probes and kernel-install hook provenance" source_grub_preflight "$grub_evidence_dir"
check "workload service" systemctl is-active httpd
check "workload enabled after restored boot" systemctl is-enabled httpd
check "local workload" bash -o pipefail -c 'curl --fail --silent --show-error --max-time 10 http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK'
check "restored application baseline checksum" sha256sum --check "$baseline_dir/application-sha256.txt"

# A successful cloud-init command can still report degraded completion.
# Surface recoverable warnings for explicit review instead of accepting exit 0.
if cloud-init status --format json | python3 -c '
import json, sys
data = json.load(sys.stdin)
print(json.dumps(data, indent=2, sort_keys=True))
def contains_messages(value):
    if isinstance(value, dict):
        return any(contains_messages(item) for item in value.values())
    if isinstance(value, list):
        return any(contains_messages(item) for item in value)
    return bool(value)
def collect(value, key):
    if not isinstance(value, dict):
        return False
    return contains_messages(value.get(key)) or any(
        collect(item, key) for item in value.values() if isinstance(item, dict))
if data.get("status") != "done" or collect(data, "errors"):
    print("FAIL: cloud-init incomplete or reports errors")
    raise SystemExit(1)
if "degraded" in str(data.get("extended_status", "")).lower() or collect(data, "recoverable_errors"):
    print("REVIEW REQUIRED: cloud-init completed with degradation or recoverable warnings")
    raise SystemExit(2)
'; then
    printf 'PASS: cloud-init completed without reported errors or degradation\n'
else
    cloud_init_status=$?
    if (( cloud_init_status == 2 )); then
        printf 'REVIEW REQUIRED: classify cloud-init warnings for this restored boot; exit 0 alone is insufficient\n'
        reviews=$((reviews + 1))
    else
        printf 'FAIL: cloud-init completion and status inspection\n'
        failures=$((failures + 1))
    fi
fi
df -h / /boot /boot/efi /var /tmp
printf 'Source inspection failures: %d\n' "$failures"
printf 'Source inspection items requiring review: %d\n' "$reviews"
printf 'Also verify public HTTP, security state, live RHUI reachability and Console backup/volume lineage.\n'
(( failures == 0 && reviews == 0 ))
