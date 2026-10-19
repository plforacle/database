"""Local structural checks. This is not the official LiveLabs validator."""

import ast
from collections import Counter
import json
from pathlib import Path
import re
import sys

ROOT = Path(__file__).resolve().parents[1]


def main():
    checks = []

    def check(name, passed, detail=''):
        checks.append({'check': name, 'passed': bool(passed), 'detail': detail})

    for path in sorted(ROOT.rglob('*.md')):
        name = path.relative_to(ROOT).as_posix()
        content = path.read_text(encoding='utf-8')
        outside = []
        in_code = False
        code_indent = 0
        task = False
        step_seen = False
        for line_no, line in enumerate(content.splitlines(), 1):
            if re.match(r'^\s*```', line):
                if not in_code:
                    code_indent = len(line) - len(line.lstrip())
                    if task and step_seen:
                        check(name + ': code nesting at ' + str(line_no), code_indent >= 4)
                in_code = not in_code
                continue
            if in_code:
                continue
            outside.append(line)
            if line.startswith('#'):
                task = bool(re.match(r'^## Task \d+: ', line))
                step_seen = False
            elif task and re.match(r'^\d+\. ', line):
                step_seen = True
            elif task and step_seen and line.strip():
                check(name + ': nested content at ' + str(line_no), line.startswith('    '))
        prose = '\n'.join(outside)
        check(name + ': first line H1', content.lstrip().startswith('# '))
        check(name + ': one H1', len(re.findall(r'^# ', prose, re.M)) == 1)
        for heading in ('## Introduction', '### Objectives', '## Acknowledgements'):
            check(name + ': ' + heading, heading in prose)
        time_pattern = r'Estimated Workshop Time:' if path.name == 'introduction.md' else r'Estimated(?: [A-Za-z]+)* Time:'
        check(name + ': time field', re.search(time_pattern, prose))
        check(name + ': balanced fences', not in_code)
        check(name + ': balanced copy tags', content.count('<copy>') == content.count('</copy>'))
        tasks = list(map(int, re.findall(r'^## Task (\d+): ', prose, re.M)))
        check(name + ': task sequence', tasks == list(range(1, len(tasks) + 1)))
        check(name + ': no forbidden dash', chr(8212) not in content)
        check(name + ': no trailing whitespace', not any(line.rstrip() != line for line in content.splitlines()))
        for match in re.finditer(r'(!?)\[([^\]]*)\]\(([^)]+)\)', content):
            target = match.group(3)
            if match.group(1):
                check(name + ': image alt text', bool(match.group(2).strip()))
            if re.match(r'^(?:https?://|mailto:|#)', target):
                continue
            local = (path.parent / target.split('#')[0]).resolve()
            check(name + ': link ' + target, local.is_file())

    manifest_path = ROOT / 'workshops/partner/manifest.json'
    manifest = json.loads(manifest_path.read_text(encoding='utf-8'))
    check('workshop title', manifest['workshoptitle'] == 'RHEL to Oracle Linux')
    check('entry point', (manifest_path.parent / 'index.html').is_file())
    labs = []
    for tutorial in manifest['tutorials']:
        filename = tutorial['filename']
        target = (manifest_path.parent / filename).resolve()
        check('manifest target ' + tutorial['title'], target.is_file())
        if target.is_file():
            text = target.read_text(encoding='utf-8')
            if tutorial['title'].startswith('Lab '):
                labs.append(tutorial['title'])
                check('manifest heading ' + tutorial['title'], text.splitlines()[0] == '# ' + tutorial['title'])
            check('participant has no Console step: ' + tutorial['title'],
                  not re.search(r'(?im)^\d+\. .*\b(?:OCI|Console|compartment|OCID|VCN)\b', text))
    check('six ordered labs', [int(re.match(r'Lab (\d+)', title).group(1)) for title in labs] == list(range(1, 7)))
    instructor_path = ROOT / 'workshops/instructor/manifest.json'
    instructor = json.loads(instructor_path.read_text(encoding='utf-8'))
    check('instructor entry point', (instructor_path.parent / 'index.html').is_file())
    check('instructor setup first', instructor['tutorials'][0]['filename'] == '../../instructor-setup/instructor-setup.md')
    check('instructor guide second', instructor['tutorials'][1]['filename'] == '../../instructor-guide/instructor-guide.md')
    check('instructor includes partner sequence', instructor['tutorials'][2:] == manifest['tutorials'])
    check('instructor content excluded from partner menu', not any('instructor-' in t['filename'] for t in manifest['tutorials']))
    for tutorial in instructor['tutorials']:
        check('instructor manifest target ' + tutorial['title'],
              (instructor_path.parent / tutorial['filename']).resolve().is_file())
    for path in sorted(ROOT.rglob('*.json')):
        try:
            json.loads(path.read_text(encoding='utf-8'))
            check('JSON parse ' + path.relative_to(ROOT).as_posix(), True)
        except ValueError as exc:
            check('JSON parse ' + path.relative_to(ROOT).as_posix(), False, str(exc))
    for path in sorted(ROOT.rglob('*.py')):
        ast.parse(path.read_text(encoding='utf-8'), filename=str(path))
        check('Python parse ' + path.relative_to(ROOT).as_posix(), True)
    for path in ROOT.rglob('*'):
        if path.is_file() and path.suffix in {'.sh', '.py'}:
            check('LF endings ' + path.relative_to(ROOT).as_posix(), b'\r' not in path.read_bytes())
    failures = [row for row in checks if not row['passed']]
    result = {
        'scope': 'Local static checks only; no cloud or official validator execution',
        'counts': dict(Counter('passed' if c['passed'] else 'failed' for c in checks)),
        'checks': checks,
        'official_validator': 'Unavailable: skill runner references a validator absent from this environment',
        'runtime_pilot': 'Not run',
        'browser_rendering': 'Not run',
        'screenshots': 'None captured; optional terminal evidence awaits pilot',
        'freesql': 'Not applicable'
    }
    (ROOT / 'qa/static-results.json').write_text(json.dumps(result, indent=2) + '\n', encoding='utf-8')
    print(json.dumps(result['counts']))
    for failure in failures:
        print(failure['check'], failure['detail'])
    return bool(failures)


if __name__ == '__main__':
    sys.exit(main())
