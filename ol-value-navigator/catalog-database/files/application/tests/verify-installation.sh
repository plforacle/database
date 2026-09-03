#!/usr/bin/env bash
set -euo pipefail

private_dir="/var/www/ol-value-navigator"
public_dir="/var/www/html/ol-value-navigator"

test -r "$private_dir/config.php"
test -r "$private_dir/stage"
test "$(cat "$private_dir/stage")" = "5"
test -r "$public_dir/index.php"

for file in "$private_dir"/lib/*.php "$public_dir"/*.php; do
  php -l "$file" >/dev/null
done

systemctl is-active --quiet httpd
curl --fail --silent --show-error http://localhost/ol-value-navigator/ >/dev/null

echo "Application files, PHP syntax, Apache, and the local browser route passed verification."

