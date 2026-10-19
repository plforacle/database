#!/usr/bin/env bash
set -euo pipefail

# Run only on a disposable instructor-owned RHEL training source.
if [[ "${1:-}" != --training-source ]]; then
  echo "Usage: bash prepare-workload.sh --training-source" >&2
  exit 2
fi
. /etc/os-release
if [[ "$ID" != rhel || "${VERSION_ID%%.*}" != 9 || "$(uname -m)" != x86_64 ]]; then
  echo "Expected a RHEL 9 x86_64 training source." >&2
  exit 1
fi
if [[ -e /var/www/html/index.html ]]; then
  echo "Existing web page found. Review it rather than overwriting application content." >&2
  exit 1
fi
sudo dnf install -y httpd firewalld curl policycoreutils tmux python3
sudo tee /var/www/html/index.html > /dev/null <<'EOF'
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>RHEL to Oracle Linux</title></head>
<body><h1>RHEL to Oracle Linux</h1><p id="status">MIGRATION_WORKLOAD_OK</p></body>
</html>
EOF
sudo systemctl enable --now firewalld
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --reload
sudo restorecon -Rv /var/www/html
sudo systemctl enable --now httpd
curl --fail --silent --show-error --max-time 15 http://127.0.0.1/ | grep -q MIGRATION_WORKLOAD_OK
echo "Training workload prepared. No blanket OS upgrade or management registration was performed."
