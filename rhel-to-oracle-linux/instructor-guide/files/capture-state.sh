#!/usr/bin/env bash
set -euo pipefail

label="${1:-}"
if [[ ! "$label" =~ ^[a-z][a-z0-9-]*$ ]]; then
  echo "Usage: bash capture-state.sh <snapshot-label>" >&2
  exit 2
fi
out="$HOME/ol-migration-evidence/$label"
if [[ -e "$out" ]]; then
  echo "Snapshot already exists: $out. Preserve it and ask the instructor before retrying." >&2
  exit 1
fi
mkdir -p "$out"
capture() {
  local name="$1"
  shift
  if ! "$@" > "$out/$name" 2> "$out/$name.stderr"; then
    echo "Capture failed: $name. Review $out/$name.stderr; snapshot is incomplete." >&2
    return 1
  fi
}
capture os-release.txt cat /etc/os-release
capture kernel.txt uname -a
capture boot-id.txt cat /proc/sys/kernel/random/boot_id
capture repositories.txt sudo dnf repolist --enabled
capture packages.tsv bash -o pipefail -c "rpm -qa --qf '%{NAME}\t%{EPOCHNUM}:%{VERSION}-%{RELEASE}.%{ARCH}\t%{VENDOR}\n' | sort"
capture httpd-enabled.txt systemctl is-enabled httpd
capture httpd-active.txt systemctl is-active httpd
capture failed-services.txt systemctl --failed --no-pager
capture disk-free.txt df -h /
capture addresses.txt ip address show
capture routes.txt ip route show
capture listening-ports.txt sudo ss -lntup
capture selinux.txt getenforce
capture firewall.txt sudo firewall-cmd --list-all
capture application.html curl --fail --silent --show-error --max-time 15 http://127.0.0.1/
capture application-sha256.txt sha256sum /var/www/html/index.html
date -u +%FT%TZ > "$out/capture-complete.txt"
echo "Capture complete: $out"
