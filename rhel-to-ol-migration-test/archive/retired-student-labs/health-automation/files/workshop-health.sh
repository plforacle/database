#!/usr/bin/env bash
set -u
set -o pipefail
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
printf 'Health check at %s\n' "$(date -Is)"
check 'Apache is active' systemctl is-active --quiet httpd
check 'Original workload responds' bash -o pipefail -c \
  'curl --fail --silent --show-error --max-time 5 http://127.0.0.1/ | grep -q MIGRATION_WORKLOAD_OK'
disk_ok() {
  df -P / | awk 'NR == 2 {gsub(/%/, "", $5); exit !($5 + 0 < 90)}'
}
check 'Root filesystem is below 90 percent used' disk_ok
if (( failures > 0 )); then
  printf 'Health check failed: %s check(s).\n' "$failures"
  exit 1
fi
printf 'HEALTH_CHECK_OK\n'
