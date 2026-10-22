"""Local structure, reference, bundle, and syntax QA. Not the LiveLabs validator."""

import ast
import difflib
import hashlib
import json
import os
from pathlib import Path
import re
import shutil
import subprocess
import sys

ROOT = Path(__file__).resolve().parents[1]
BASH = shutil.which("bash")
if os.name == "nt" and Path("C:/Program Files/Git/bin/bash.exe").is_file():
    BASH = "C:/Program Files/Git/bin/bash.exe"
failures = []
checks = 0


def check(name, passed):
    global checks
    checks += 1
    if not passed:
        failures.append(name)


def bash_path(path):
    value = path.resolve().as_posix()
    return "/" + value[0].lower() + value[2:] if re.match(r"^[A-Za-z]:/", value) else value


manifest_path = ROOT / "workshops/tenancy/manifest.json"
manifest = json.loads(manifest_path.read_text(encoding="utf-8-sig"))
check("title", manifest["workshoptitle"] == "Migrate Red Hat Enterprise Linux to Oracle Linux on OCI")
check("entry point", (manifest_path.parent / "index.html").is_file())
for tutorial in manifest["tutorials"]:
    filename = tutorial["filename"]
    if filename.startswith("https://"):
        continue
    path = (manifest_path.parent / filename).resolve()
    check("manifest target " + filename, path.is_file())
    if not path.is_file():
        continue
    content = path.read_text(encoding="utf-8-sig")
    check(filename + " first H1", content.startswith("# "))
    check(filename + " fences", len(re.findall(r"^\s*```", content, re.M)) % 2 == 0)
    prose = re.sub(r"^([ ]*)```[^\n]*\n.*?^\1```", "", content, flags=re.M | re.S)
    check(filename + " one H1", len(re.findall(r"^# ", prose, re.M)) == 1)
    for heading in ["## Introduction", "### Objectives", "## Acknowledgements"]:
        check(filename + " " + heading, heading in prose)
    check(filename + " time", bool(re.search(r"Estimated(?: [A-Za-z]+)* Time:", prose)))
    check(filename + " copy tags", content.count("<copy>") == content.count("</copy>"))
    tasks = [int(n) for n in re.findall(r"^## Task (\d+): ", prose, re.M)]
    check(filename + " tasks", tasks == list(range(1, len(tasks) + 1)))
    check(filename + " forbidden dash", chr(8212) not in content)
    if tutorial["title"].startswith("Lab "):
        check(filename + " heading matches manifest", content.splitlines()[0] == "# " + tutorial["title"])
    for match in re.finditer(r"(!?)\[([^\]]*)\]\(([^)]+)\)", content):
        image, alt, target = match.groups()
        if image:
            check(filename + " image alt", bool(alt.strip()))
            check(filename + " image name", bool(re.fullmatch(r"[a-z0-9-]+\.(png|svg|jpg|jpeg|gif)", Path(target).name)))
        if not re.match(r"^(https?://|mailto:|#)", target):
            check(filename + " reference " + target, (path.parent / target.split("#")[0]).is_file())
    # Scope indentation to task content and only lines preceding another step.
    for task in re.split(r"^## ", content, flags=re.M):
        if not task.startswith("Task "):
            continue
        lines = task.splitlines()
        steps = [i for i, line in enumerate(lines) if re.match(r"^\d+\. ", line)]
        for start, end in zip(steps, steps[1:]):
            for line in lines[start + 1:end]:
                check(filename + " nested step content", not line.strip() or line.startswith("    "))
    for snippet in re.findall(r"^\s*```bash\s*\n(.*?)^\s*```", content, re.M | re.S):
        snippet = snippet.replace("<copy>", "").replace("</copy>", "")
        snippet = re.sub(r"<[a-z]+(?:-[a-z]+)+>", "placeholder", snippet)
        if BASH:
            result = subprocess.run([BASH, "-n"], input=snippet, text=True, capture_output=True)
            check(filename + " Bash block syntax: " + result.stderr.strip(), result.returncode == 0)

check("Bash available", bool(BASH))
# Private run evidence is gitignored and may preserve original transport bytes.
# Validate the workshop deliverables without rewriting historical evidence.
for path in sorted(ROOT.rglob("*.sh")):
    if path.is_relative_to(ROOT / "qa/live-evidence"):
        continue
    check(str(path.relative_to(ROOT)) + " LF", b"\r" not in path.read_bytes())
    if BASH:
        result = subprocess.run([BASH, "-n", bash_path(path)], capture_output=True, text=True)
        check(str(path.relative_to(ROOT)) + " Bash syntax: " + result.stderr.strip(), result.returncode == 0)
for path in sorted(ROOT.rglob("*.py")):
    if path.is_relative_to(ROOT / "qa/live-evidence"):
        continue
    check(str(path.relative_to(ROOT)) + " Python syntax", bool(ast.parse(path.read_text(encoding="utf-8-sig"))))
candidate_dir = ROOT / "migrate-to-oracle-linux/files"
launcher_tree = ast.parse((candidate_dir / "run-migration.py").read_text(encoding="utf-8"), feature_version=(3, 9))
check("launcher Python 3.9 syntax", bool(launcher_tree))
checksum_path = candidate_dir / "SHA256SUMS"
check("checksum manifest exists", checksum_path.is_file())
if checksum_path.is_file():
    for line in checksum_path.read_text(encoding="utf-8").splitlines():
        digest, name = line.split("  ", 1)
        check("checksum " + name, hashlib.sha256((candidate_dir / name).read_bytes()).hexdigest() == digest)
original = ROOT / "qa/upstream-migrate-to-oracle-linux.sh"
check("upstream checksum", hashlib.sha256(original.read_bytes()).hexdigest() == "5435b59e2f0e672111fd1545b2b428cabe473e7c116a353716c2f7de869d0a42")
diff = "".join(difflib.unified_diff(original.read_text(encoding="utf-8").splitlines(True),
                                  (candidate_dir / "migrate-to-oracle-linux.sh").read_text(encoding="utf-8").splitlines(True),
                                  fromfile="a/migrate-to-oracle-linux.sh", tofile="b/migrate-to-oracle-linux.sh"))
check("patch reproduces candidate delta", diff == (ROOT / "qa/migration-safety.patch").read_text(encoding="utf-8"))
lab1 = ROOT / "launch-baseline-rhel/launch-baseline-rhel.md"
images = re.findall(r"!\[[^\]]+\]\((images/[^)]+)\)", lab1.read_text(encoding="utf-8"))
check("Lab 1 retains 13 screenshots", len(images) == 13)
check("Lab 1 repaired image naming", all("http" not in Path(image).name for image in images))
print("Local checks: {}; failures: {}".format(checks, len(failures)))
for failure in failures:
    print("FAIL: " + failure)
sys.exit(bool(failures))
