#!/usr/bin/env bash
set -euo pipefail

# Installs one workshop stage while keeping credentials outside the public document root.
#
# Stage 3 exposes saved inputs, stage 4 adds GenAI review, and stage 5 enables
# calculations and workbook actions. Re-running a stage safely replaces application
# code without replacing an existing private configuration file.
stage="${1:-}"
if [[ ! "$stage" =~ ^(3|4|5)$ ]]; then
  echo "Usage: sudo bash deploy.sh 3|4|5"
  exit 2
fi

# Resolve paths from this script so deployment does not depend on the caller's directory.
if [[ "$stage" = "5" ]]; then
  php -r 'if (!class_exists("ZipArchive")) { fwrite(STDERR, "PHP ZIP extension missing. Complete Lab 5 Task 2 before deploying.\n"); exit(1); }'
fi

source_dir="$(cd "$(dirname "$0")" && pwd)"
private_dir="/var/www/ol-value-navigator"
public_dir="/var/www/html/ol-value-navigator"

# Existing installations must receive the additive database upgrade before new
# controllers are copied. A first deployment uses the fresh Lab 2 schema.
if [[ -f "$private_dir/config.php" ]]; then
  php "$source_dir/tests/check-context-schema.php"
fi

# Private libraries receive group-readable permissions for Apache. Public controllers,
# views, and CSS are installed separately under the Apache document root.
install -d -o root -g apache -m 0750 "$private_dir" "$private_dir/lib"
install -d -o root -g apache -m 0755 "$public_dir"

install -o root -g apache -m 0640 "$source_dir/lib/bootstrap.php" "$private_dir/lib/bootstrap.php"
install -o root -g apache -m 0640 "$source_dir/lib/repository.php" "$private_dir/lib/repository.php"
install -o root -g apache -m 0640 "$source_dir/lib/context.php" "$private_dir/lib/context.php"
install -o root -g apache -m 0640 "$source_dir/lib/forms.php" "$private_dir/lib/forms.php"
install -o root -g apache -m 0640 "$source_dir/lib/genai.php" "$private_dir/lib/genai.php"
install -o root -g apache -m 0640 "$source_dir/lib/money.php" "$private_dir/lib/money.php"
install -o root -g apache -m 0640 "$source_dir/lib/deletion.php" "$private_dir/lib/deletion.php"
install -o root -g apache -m 0640 "$source_dir/lib/presentation.php" "$private_dir/lib/presentation.php"

for file in "$source_dir"/public/*; do
  install -o root -g apache -m 0644 "$file" "$public_dir/$(basename "$file")"
done

# The private stage file is the server-side feature gate used by require_stage().
printf '%s\n' "$stage" > "$private_dir/stage"
chown root:apache "$private_dir/stage"
chmod 0640 "$private_dir/stage"

# Create a placeholder configuration only on first deployment to preserve credentials.
if [[ ! -f "$private_dir/config.php" ]]; then
  install -o root -g apache -m 0640 "$source_dir/config/config.php.example" "$private_dir/config.php"
  echo "Created $private_dir/config.php. Replace its placeholders before opening the application."
fi

# Restore Oracle Linux SELinux labels when available, then reload Apache atomically.
restorecon -RF "$private_dir" "$public_dir" >/dev/null 2>&1 || true
systemctl reload httpd
echo "Oracle Linux Value Navigator application files installed for Lab $stage."
