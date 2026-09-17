#!/usr/bin/env bash
set -uo pipefail
[[ $EUID -eq 0 ]] || { echo "Run: sudo bash test.sh"; exit 1; }
HERE=$(cd -- "$(dirname -- "$0")" && pwd -P)
[[ "$HERE" == /opt/olvn-v3/package-r3 ]] || { echo "Run the installed copy: sudo bash /opt/olvn-v3/package-r3/test.sh"; exit 1; }
FAILED=0
php "$HERE/offline-test.php" || FAILED=1
runuser -u apache -- php "$HERE/check.php" --genai || FAILED=1
for route in /health/live /demo /demo/coverage /demo/coverage/new; do
    CODE=$(curl --max-time 30 -sS -o /dev/null -w '%{http_code}' "http://127.0.0.1:8009$route") || CODE=000
    if [[ "$CODE" == 200 ]]; then echo "PASS: GET $route"; else echo "FAIL: GET $route returned $CODE"; FAILED=1; fi
done
for route in /index.php /composer.json /config/app.php /runtime.json; do
    CODE=$(curl --max-time 30 -sS -o /dev/null -w '%{http_code}' "http://127.0.0.1:8009$route") || CODE=000
    if [[ "$CODE" == 403 || "$CODE" == 404 ]]; then echo "PASS: private boundary $route"; else echo "FAIL: private boundary $route returned $CODE"; FAILED=1; fi
done
if [[ $FAILED -ne 0 ]]; then
    echo "TESTS FAILED. Share the failing lines, not passwords. Do not broaden grants blindly."
    exit 1
fi
echo "INSTALLATION CHECKS PASSED. Next: browser save, reopen, confirm, import and export checks in START-HERE.md."
echo "This does not verify real login, user isolation, commercial pricing, or all workflows."
