#!/usr/bin/env bash
set -uo pipefail

if [[ ! -r /etc/rhel-to-oracle-linux/workshop.env ]]; then
  echo "Missing instructor settings." >&2
  exit 1
fi
. /etc/rhel-to-oracle-linux/workshop.env
if [[ ! "${TARGET_VERSION:-}" =~ ^9\.[0-9]+$ ]]; then
  echo "Instructor must configure a fixed Oracle Linux 9 target release." >&2
  exit 1
fi
failures=0
check() {
  local description="$1"
  shift
  if "$@"; then
    printf 'PASS: %s\n' "$description"
  else
    printf 'FAIL: %s\n' "$description"
    failures=$((failures + 1))
  fi
}
check "Oracle Linux identity and selected version" bash -c '. /etc/os-release; test "$ID" = ol && test "$VERSION_ID" = "$1"' _ "$TARGET_VERSION"
check "x86_64 architecture" bash -c 'test "$(uname -m)" = x86_64'
check "Oracle-provided RHCK" bash -o pipefail -c 'case "$(uname -r)" in *uek*) exit 1;; esac; rpm -qf "/boot/vmlinuz-$(uname -r)" --qf "%{VENDOR}\n" | grep -q Oracle'
check "httpd enabled" systemctl is-enabled --quiet httpd
check "httpd active" systemctl is-active --quiet httpd
check "application response" bash -o pipefail -c 'curl --fail --silent --show-error --max-time 15 http://127.0.0.1/ | grep -q MIGRATION_WORKLOAD_OK'
check "SELinux enforcing" bash -c 'test "$(getenforce)" = Enforcing'
check "HTTP allowed by firewalld" sudo firewall-cmd --query-service=http
check "no failed systemd units" bash -o pipefail -c 'units=$(systemctl --failed --no-legend --plain) || exit; test -z "$units"'
check "RPM database" sudo rpm --verifydb
check "package dependencies" sudo dnf check
check "repository metadata access" sudo dnf makecache
if (( failures > 0 )); then
  printf '%d validation check(s) failed.\n' "$failures" >&2
  exit 1
fi
echo "System validation passed. Review application comparisons and package exceptions separately."
