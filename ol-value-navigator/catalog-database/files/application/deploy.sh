#!/usr/bin/env bash
set -euo pipefail

stage="${1:-}"
if [[ ! "$stage" =~ ^(3|4|5)$ ]]; then
  echo "Usage: sudo bash deploy.sh 3|4|5"
  exit 2
fi

source_dir="$(cd "$(dirname "$0")" && pwd)"
private_dir="/var/www/ol-value-navigator"
public_dir="/var/www/html/ol-value-navigator"

install -d -o root -g apache -m 0750 "$private_dir" "$private_dir/lib"
install -d -o root -g apache -m 0755 "$public_dir"

install -o root -g apache -m 0640 "$source_dir/lib/bootstrap.php" "$private_dir/lib/bootstrap.php"
install -o root -g apache -m 0640 "$source_dir/lib/repository.php" "$private_dir/lib/repository.php"
install -o root -g apache -m 0640 "$source_dir/lib/genai.php" "$private_dir/lib/genai.php"
install -o root -g apache -m 0640 "$source_dir/lib/money.php" "$private_dir/lib/money.php"
install -o root -g apache -m 0640 "$source_dir/lib/deletion.php" "$private_dir/lib/deletion.php"

for file in "$source_dir"/public/*; do
  install -o root -g apache -m 0644 "$file" "$public_dir/$(basename "$file")"
done

printf '%s\n' "$stage" > "$private_dir/stage"
chown root:apache "$private_dir/stage"
chmod 0640 "$private_dir/stage"

if [[ ! -f "$private_dir/config.php" ]]; then
  install -o root -g apache -m 0640 "$source_dir/config/config.php.example" "$private_dir/config.php"
  echo "Created $private_dir/config.php. Replace its placeholders before opening the application."
fi

restorecon -RF "$private_dir" "$public_dir" >/dev/null 2>&1 || true
systemctl reload httpd
echo "Oracle Linux Value Navigator application files installed for Lab $stage."
