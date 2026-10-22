#!/usr/bin/env bash
set -uo pipefail

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
check "release is 9.8" bash -c '. /etc/os-release; test "$VERSION_ID" = 9.8'
check "running kernel is an Oracle Linux RHCK" bash -o pipefail -c 'case "$(uname -r)" in *uek*) exit 1;; esac; rpm -qf "/boot/vmlinuz-$(uname -r)" --qf "%{VENDOR}\n" | grep -q Oracle'
check "running kernel is the boot default" bash -c 'test "$(grubby --default-kernel)" = "/boot/vmlinuz-$(uname -r)"'
check "running kernel image exists" test -s "/boot/vmlinuz-$(uname -r)"
check "running kernel initramfs is readable" bash -c 'test -s "/boot/initramfs-$(uname -r).img" && lsinitrd "/boot/initramfs-$(uname -r).img" >/dev/null'
check "running kernel module dependencies exist" test -s "/lib/modules/$(uname -r)/modules.dep"
check "kernel runtime library payloads are intact" rpm -V libattr libacl
check "kernel runtime libraries load" python3 -c 'import ctypes; ctypes.CDLL("libattr.so.1"); ctypes.CDLL("libacl.so.1")'
check "RPM database is healthy" rpm --verifydb
check "package dependencies are consistent" dnf check
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
