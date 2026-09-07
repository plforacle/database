#!/usr/bin/env bash
set -euo pipefail

# End-to-end Stage 5 deployment smoke test.
#
# The script fails at the first unmet condition. It checks private runtime files,
# the public document root, every deployed PHP file, the Apache service, public
# authentication pages, and anonymous redirects on protected routes. It
# intentionally avoids printing private configuration or changing application
# state.
private_dir="/var/www/ol-value-navigator-2"
public_dir="/var/www/html/ol-value-navigator-2"

# Confirm the private/public split and the final workshop feature stage.
test -r "$private_dir/config.php"
test -r "$private_dir/stage"
test "$(cat "$private_dir/stage")" = "5"
test -r "$public_dir/index.php"

# Lint the exact PHP copies Apache and command-line tests will execute.
for file in "$private_dir"/lib/*.php "$public_dir"/*.php; do
  php -l "$file" >/dev/null
done

# Verify the service and the public authentication routes.
systemctl is-active --quiet httpd
base_url="http://localhost/ol-value-navigator-2"
curl --fail --silent --show-error "$base_url/login.php" | grep --quiet "Sign in"
curl --fail --silent --show-error "$base_url/register.php" | grep --quiet "Create an account"

# Anonymous requests must be redirected by each representative protected route.
for route in "" "help.php" "comparison.php?id=1" "export.php?id=1"; do
  response_headers="$(curl --silent --show-error --dump-header - --output /dev/null "$base_url/$route")"
  status_code="$(printf '%s\n' "$response_headers" | awk 'NR == 1 { print $2 }')"
  location="$(printf '%s\n' "$response_headers" | awk 'tolower($1) == "location:" { print $2 }' | tr -d '\r')"
  test "$status_code" = "303"
  test "$location" = "/ol-value-navigator-2/login.php"
done

echo "Application files, PHP syntax, Apache, public authentication pages, and protected-route redirects passed verification."
