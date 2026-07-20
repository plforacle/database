#!/usr/bin/env bash
set -u

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

check "operating system is Oracle Linux" bash -c '. /etc/os-release; test "$ID" = ol'
check "major version is 9" bash -c '. /etc/os-release; test "${VERSION_ID%%.*}" = 9'
check "running kernel is an Oracle Linux RHCK" bash -c 'case "$(uname -r)" in *uek*) exit 1;; esac; rpm -qf "/boot/vmlinuz-$(uname -r)" --qf "%{VENDOR}\n" | grep -q Oracle'
check "httpd is active" systemctl is-active --quiet httpd
check "httpd is enabled" systemctl is-enabled --quiet httpd
check "workload marker is returned" bash -c "curl --fail --silent http://127.0.0.1/ | grep -q MIGRATION_WORKLOAD_OK"
check "SELinux is enforcing" bash -c "test \"$(getenforce)\" = Enforcing"
check "HTTP is allowed by firewalld" bash -c "sudo firewall-cmd --query-service=http >/dev/null"
check "no failed systemd units" bash -c "test \"$(systemctl --failed --no-legend | wc -l)\" -eq 0"

if (( failures > 0 )); then
  printf '%d validation check(s) failed.\n' "$failures"
  exit 1
fi

echo "Migration validation passed."
