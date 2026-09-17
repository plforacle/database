#!/usr/bin/env bash
# Existing V3 server only. No OCI provisioning, schema changes or application updates.
set -euo pipefail
STAGE=
cleanup() {
    unset APP_PASSWORD
    if [[ -n "$STAGE" ]]; then
        echo "Unfinished staging directory preserved at $STAGE; it is not the active application." >&2
    fi
}
trap cleanup EXIT
trap 'echo "STOP at line $LINENO. Correct the reported issue and rerun this same installer. Existing files are not overwritten." >&2' ERR
[[ $EUID -eq 0 ]] || { echo "Run: sudo bash install.sh"; exit 1; }
[[ $(hostname -s) == ol-value-navigator-3-app ]] || { echo "This installer is restricted to ol-value-navigator-3-app."; exit 1; }
SOURCE=$(cd -- "$(dirname -- "$0")" && pwd -P)
TARGET=/opt/olvn-v3/package-r3
CONFIG=/etc/olvn-v3-package
APACHE=/etc/httpd/conf.d/olvn-v3-package.conf
MODE=bundled
for tool in php runuser semanage restorecon sha256sum curl httpd ss flock cmp mktemp stat ln unlink; do
    command -v "$tool" >/dev/null || { echo "Missing prerequisite: $tool. Complete Lab 1 and PHP extensions first."; exit 1; }
done
exec 9>/run/lock/olvn-v3-install.lock
flock -n 9 || { echo "Another V3 installation is running."; exit 1; }
for path in /opt/olvn-v3 "$TARGET" "$CONFIG" "$CONFIG/runtime.json" "$APACHE"; do
    [[ ! -L "$path" ]] || { echo "Refusing symlink: $path"; exit 1; }
done
(cd "$SOURCE" && sha256sum --quiet -c SHA256SUMS)
php "$SOURCE/offline-test.php"
php -r 'if (!function_exists("posix_geteuid") || posix_getpwnam("apache") === false) { fwrite(STDERR,"PHP POSIX support and the apache account are required.\n"); exit(1); }'
httpd -t
semanage port -l | awk '$1=="http_port_t" {print}' | grep -qw 8009 || { echo "SELinux does not list port 8009 for HTTP."; exit 1; }

# Resume only this exact package; never treat an existing directory as permission to overwrite.
if [[ -e "$TARGET" ]]; then
    [[ -d "$TARGET" && -f "$TARGET/SHA256SUMS" ]] || { echo "Existing target is not a complete staged package. Preserved for inspection."; exit 1; }
    cmp -s "$SOURCE/SHA256SUMS" "$TARGET/SHA256SUMS" || { echo "Existing package differs. An upgrade needs separate review."; exit 1; }
    (cd "$TARGET" && sha256sum --quiet -c SHA256SUMS)
fi
if [[ -e "$CONFIG" ]]; then
    [[ -d "$CONFIG" && $(stat -c '%U:%a' "$CONFIG") == apache:700 ]] || { echo "Unexpected private directory ownership or permissions."; exit 1; }
fi
if [[ -e "$APACHE" ]]; then
    cmp -s "$SOURCE/apache.conf" "$APACHE" || { echo "Existing Apache configuration differs. It was not changed."; exit 1; }
fi
LISTENER=$(ss -H -ltnp 'sport = :8009')
if [[ -n "$LISTENER" ]]; then
    [[ -f "$APACHE" && "$LISTENER" == *'"httpd"'* ]] || { echo "Port 8009 is in use by another configuration or process."; exit 1; }
fi

# Check credentials BEFORE creating application/configuration directories.
if [[ -f "$CONFIG/runtime.json" ]]; then
    CREDENTIAL_MODE=existing
    php "$SOURCE/configure.php" --preflight --existing
elif [[ -f /etc/olvn-v3/runtime/database.json && -f /etc/olvn-v3/runtime/connection.json ]]; then
    CREDENTIAL_MODE=reuse
    php "$SOURCE/configure.php" --preflight --reuse "$MODE"
else
    CREDENTIAL_MODE=prompt
    read -r -p "V3 private DB IP: " DB_IP </dev/tty
    read -r -s -p "Application password: " APP_PASSWORD </dev/tty
    printf '\n'
    printf '%s' "$APP_PASSWORD" | php "$SOURCE/configure.php" --preflight "$DB_IP" "$MODE"
fi
echo "Preflight passed. Using the bundled catalog; HeatWave remains the database."

if [[ ! -e "$TARGET" ]]; then
    install -d -m 0755 /opt/olvn-v3
    STAGE=$(mktemp -d /opt/olvn-v3/.package-r3-stage.XXXXXX)
    cp -R -- "$SOURCE/." "$STAGE/"
    chown -R root:root "$STAGE"
    find "$STAGE" -type d -exec chmod 0755 {} +
    find "$STAGE" -type f -exec chmod 0644 {} +
    (cd "$STAGE" && sha256sum --quiet -c SHA256SUMS)
    mv -T -- "$STAGE" "$TARGET"
    STAGE=
fi
if [[ ! -e "$CONFIG" ]]; then
    install -d -o apache -g apache -m 0700 "$CONFIG"
fi
if [[ ! -e "$TARGET/app/var/private/coverage" ]]; then
    install -d -o apache -g apache -m 0700 "$TARGET/app/var/private/coverage"
fi
case "$CREDENTIAL_MODE" in
    existing) echo "Keeping existing verified credentials." ;;
    reuse) runuser -u apache -- php "$TARGET/configure.php" --reuse "$MODE" ;;
    prompt) printf '%s' "$APP_PASSWORD" | runuser -u apache -- php "$TARGET/configure.php" "$DB_IP" "$MODE" ;;
esac
unset APP_PASSWORD

ensure_label() {
    local pattern=$1 type=$2
    if semanage fcontext -l -C | awk -v p="$pattern" -v t="$type" '$1==p && index($NF, ":" t ":") {found=1} END {exit !found}'; then
        return
    fi
    # An existing conflicting rule fails rather than being silently modified.
    semanage fcontext -a -t "$type" "$pattern"
}
ensure_label '/opt/olvn-v3/package-r3(/.*)?' httpd_sys_content_t
ensure_label '/opt/olvn-v3/package-r3/app/var/private(/.*)?' httpd_sys_rw_content_t
ensure_label '/etc/olvn-v3-package(/.*)?' httpd_sys_content_t
restorecon -R "$TARGET" "$CONFIG"
runuser -u apache -- php "$TARGET/check.php"
NEW_APACHE=0
if [[ ! -e "$APACHE" ]]; then
    APACHE_STAGE=$(mktemp /etc/httpd/conf.d/.olvn-v3-stage.XXXXXX)
    install -m 0644 "$TARGET/apache.conf" "$APACHE_STAGE"
    ln -T -- "$APACHE_STAGE" "$APACHE"
    unlink -- "$APACHE_STAGE"
    NEW_APACHE=1
fi
if ! httpd -t; then
    if [[ "$NEW_APACHE" == 1 ]]; then
        BACKUP=$(mktemp "$TARGET/apache.conf.failed.XXXXXX")
        mv -T -- "$APACHE" "$BACKUP"
        echo "New Apache configuration moved aside to $BACKUP; service not reloaded."
    fi
    exit 1
fi
systemctl reload httpd
echo "INSTALLED. Next: sudo bash /opt/olvn-v3/package-r3/test.sh"
echo "No public endpoint was opened. Existing files and database records were preserved."
