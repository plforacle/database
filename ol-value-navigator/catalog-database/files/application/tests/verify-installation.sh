#!/usr/bin/env bash
set -euo pipefail

# End-to-end Stage 5 deployment smoke test.
#
# The script fails at the first unmet condition. It checks private runtime files,
# the public document root, every deployed PHP file, the Apache service, and both
# the landing and Help routes through localhost. It intentionally avoids printing
# the private configuration or making a state-changing application request.
private_dir="/var/www/ol-value-navigator"
public_dir="/var/www/html/ol-value-navigator"

# Confirm the private/public split and the final workshop feature stage.
test -r "$private_dir/config.php"
test -r "$private_dir/stage"
test "$(cat "$private_dir/stage")" = "5"
test -r "$public_dir/index.php"
test -r "$public_dir/export-pptx.php"
test -r "$private_dir/lib/presentation.php"
php -r 'if (!class_exists("ZipArchive")) { fwrite(STDERR, "PHP ZIP extension missing. Complete Lab 5 Task 2.\n"); exit(1); }'

# Lint the exact PHP copies Apache and command-line tests will execute.
for file in "$private_dir"/lib/*.php "$public_dir"/*.php; do
  php -l "$file" >/dev/null
done

# Verify the service and two representative read-only HTTP routes.
systemctl is-active --quiet httpd
curl --fail --silent --show-error http://localhost/ol-value-navigator/ >/dev/null
curl --fail --silent --show-error http://localhost/ol-value-navigator/help.php | grep --quiet "Help and Quick Start"

echo "Application files, PHP syntax, Apache, and the local application and Help routes passed verification."
