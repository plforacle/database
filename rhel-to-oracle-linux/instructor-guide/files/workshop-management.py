#!/usr/bin/env python3
"""Terminal exercise helper. IAM, not this wrapper, controls service access."""

import argparse
import json
import os
from pathlib import Path
import re
import subprocess
import sys

CONFIG = Path('/etc/rhel-to-oracle-linux/management.json')
STATE = Path.home() / 'ol-migration-evidence' / 'osmh'
TERMINAL = {'SUCCEEDED', 'FAILED', 'CANCELED', 'SKIPPED'}


def load_config(path=CONFIG):
    cfg = json.loads(path.read_text(encoding='utf-8'))
    for key in ('region', 'managed_instance_id', 'oci_cli'):
        if not isinstance(cfg.get(key), str) or not cfg[key] or 'REPLACE' in cfg[key]:
            raise ValueError('Instructor must configure ' + key)
    if not Path(cfg['oci_cli']).is_absolute():
        raise ValueError('oci_cli must be an absolute path')
    if not isinstance(cfg.get('allowed_packages'), list) or not cfg['allowed_packages']:
        raise ValueError('Instructor must configure allowed_packages')
    if not all(isinstance(p, str) and re.fullmatch(r'[A-Za-z0-9][A-Za-z0-9+_.-]*', p)
               for p in cfg['allowed_packages']):
        raise ValueError('Invalid allowed package names')
    return cfg


def request(cfg, resource, action, *args):
    cmd = [cfg['oci_cli'], 'os-management-hub', resource, action,
           '--auth', 'instance_principal', '--region', cfg['region'],
           '--output', 'json', '--config-file', '/dev/null',
           '--cli-rc-file', '/dev/null', '--max-retries', '0',
           '--connection-timeout', '10', '--read-timeout', '60'] + list(args)
    env = dict(os.environ, OCI_CLI_AUTO_PROMPT='false')
    result = subprocess.run(cmd, capture_output=True, text=True, timeout=120, env=env)
    if result.returncode:
        raise RuntimeError(result.stderr.strip() or 'Service command failed')
    return json.loads(result.stdout)


def save(name, data):
    STATE.mkdir(parents=True, exist_ok=True)
    (STATE / name).write_text(json.dumps(data, indent=2) + '\n', encoding='utf-8')


def items(payload):
    data = payload.get('data', {})
    if isinstance(data, list):
        return data
    if isinstance(data, dict) and isinstance(data.get('items'), list):
        return data['items']
    raise ValueError('Unexpected package response; ask the instructor to review the CLI version')


def show_packages(payload):
    rows = items(payload)
    print('Matching packages: ' + str(len(rows)))
    for row in rows:
        print('\t'.join(str(row.get(k, '')) for k in
                        ('display-name', 'version', 'release', 'architecture')))


def last_request():
    record = json.loads((STATE / 'last-request.json').read_text(encoding='utf-8'))
    return record['work_request_id']


def job_status(cfg):
    payload = request(cfg, 'work-request', 'get', '--work-request-id', last_request())
    save('job-status.json', payload)
    status = payload.get('data', {}).get('status')
    if not status:
        raise ValueError('Missing work request status in service response')
    print('Job state: ' + status)
    print('Tracking identifier: ' + last_request())
    if status in {'FAILED', 'CANCELED', 'SKIPPED'}:
        errors = request(cfg, 'work-request', 'list-errors',
                         '--work-request-id', last_request(), '--all')
        save('job-errors.json', errors)
        print('Inspect job-errors.json with your instructor.')
        return 1
    return 0


def install(cfg, package):
    if package not in cfg['allowed_packages']:
        raise ValueError('Package is not assigned for this exercise')
    # Refuse a second request when prior tracking or submission is unresolved.
    if (STATE / 'submission-pending.json').exists():
        raise ValueError('An earlier submission needs instructor review before any retry')
    if (STATE / 'last-request.json').exists():
        previous = request(cfg, 'work-request', 'get', '--work-request-id', last_request())
        if previous.get('data', {}).get('status') not in TERMINAL:
            raise ValueError('An earlier request is still active; use job-status')
        raise ValueError('An exercise request already exists; ask the instructor before repeating')
    available = request(cfg, 'managed-instance', 'list-available-packages',
                        '--managed-instance-id', cfg['managed_instance_id'],
                        '--display-name', package, '--all')
    if not items(available):
        raise ValueError('Assigned package is not available in the service')
    STATE.mkdir(parents=True, exist_ok=True)
    with (STATE / 'submission-pending.json').open('x', encoding='utf-8') as marker:
        json.dump({'package': package}, marker)
    # A timeout or parsing error might follow a successful submission. Keep the
    # marker until the instructor reconciles the service history.
    payload = request(cfg, 'managed-instance', 'install-packages',
                      '--managed-instance-id', cfg['managed_instance_id'],
                      '--package-names', json.dumps([package]))
    save('submission.json', payload)
    work_id = payload.get('opc-work-request-id')
    if not work_id:
        raise ValueError('Submission has no tracking identifier; ask the instructor before retrying')
    save('last-request.json', {'work_request_id': work_id, 'package': package})
    (STATE / 'submission-pending.json').unlink()
    print('Request submitted. Completion has not been confirmed.')
    print('Tracking identifier: ' + work_id)
    print('Run: workshop-management job-status')


def main(argv=None):
    parser = argparse.ArgumentParser(description='Inspect the assigned server and run its prepared management exercise.')
    sub = parser.add_subparsers(dest='action', required=True)
    sub.add_parser('status')
    for action in ('inventory', 'available-packages'):
        sub.add_parser(action).add_argument('package')
    sub.add_parser('install').add_argument('package')
    sub.add_parser('job-status')
    args = parser.parse_args(argv)
    cfg = load_config()
    if args.action == 'status':
        payload = request(cfg, 'managed-instance', 'get',
                          '--managed-instance-id', cfg['managed_instance_id'])
        save('instance-status.json', payload)
        data = payload.get('data', {})
        status = data.get('status', 'UNKNOWN')
        print('Server: ' + str(data.get('display-name', 'assigned server')))
        print('Managed status: ' + status)
        print('Last check-in: ' + str(data.get('time-last-checkin', 'not reported')))
        return 0 if status == 'NORMAL' else 1
    if args.action in ('inventory', 'available-packages'):
        action = 'list-installed-packages' if args.action == 'inventory' else 'list-available-packages'
        payload = request(cfg, 'managed-instance', action,
                          '--managed-instance-id', cfg['managed_instance_id'],
                          '--display-name', args.package, '--all')
        save(args.action + '.json', payload)
        show_packages(payload)
        return 0 if items(payload) else 1
    if args.action == 'install':
        install(cfg, args.package)
        return 0
    return job_status(cfg)


if __name__ == '__main__':
    try:
        sys.exit(main())
    except (OSError, ValueError, KeyError, RuntimeError, subprocess.TimeoutExpired) as exc:
        print('Management exercise stopped: ' + str(exc), file=sys.stderr)
        print('Preserve the evidence and ask the instructor for help.', file=sys.stderr)
        sys.exit(1)
