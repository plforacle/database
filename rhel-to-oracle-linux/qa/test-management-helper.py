"""Offline behavior checks. No cloud request or package installation is performed."""

import contextlib
import importlib.util
import io
import json
from pathlib import Path
import subprocess
import tempfile
import unittest
from unittest.mock import patch

path = Path(__file__).resolve().parents[1] / 'instructor-guide/files/workshop-management.py'
spec = importlib.util.spec_from_file_location('workshop_management', path)
helper = importlib.util.module_from_spec(spec)
spec.loader.exec_module(helper)


class ManagementHelperTests(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.TemporaryDirectory()
        self.addCleanup(self.tmp.cleanup)
        self.state = Path(self.tmp.name)
        self.patcher = patch.object(helper, 'STATE', self.state)
        self.patcher.start()
        self.addCleanup(self.patcher.stop)
        self.cfg = {'region': 'test-region', 'managed_instance_id': 'test-instance',
                    'oci_cli': str(Path(self.tmp.name) / 'oci'), 'allowed_packages': ['tree']}
        self.output = io.StringIO()
        self.stdout = contextlib.redirect_stdout(self.output)
        self.stdout.__enter__()
        self.addCleanup(self.stdout.__exit__, None, None, None)

    def test_unfilled_configuration_is_rejected(self):
        config = self.state / 'config.json'
        config.write_text(json.dumps(dict(self.cfg, region='REPLACE_WITH_REGION')))
        with self.assertRaises(ValueError):
            helper.load_config(config)

    def test_collection_and_list_shapes(self):
        row = {'display-name': 'httpd'}
        self.assertEqual(helper.items({'data': {'items': [row]}}), [row])
        self.assertEqual(helper.items({'data': [row]}), [row])
        with self.assertRaises(ValueError):
            helper.items({'data': {}})

    def test_instance_principal_and_no_mutation_retries(self):
        result = subprocess.CompletedProcess([], 0, '{"data":{}}', '')
        with patch.object(helper.subprocess, 'run', return_value=result) as run:
            helper.request(self.cfg, 'managed-instance', 'get')
        argv = run.call_args.args[0]
        self.assertEqual(argv[argv.index('--auth') + 1], 'instance_principal')
        self.assertEqual(argv[argv.index('--max-retries') + 1], '0')
        self.assertEqual(argv[argv.index('--region') + 1], 'test-region')
        self.assertFalse(run.call_args.kwargs.get('shell', False))

    def test_permission_error_remains_an_error(self):
        result = subprocess.CompletedProcess([], 1, '', 'NotAuthorizedOrNotFound')
        with patch.object(helper.subprocess, 'run', return_value=result):
            with self.assertRaisesRegex(RuntimeError, 'NotAuthorized'):
                helper.request(self.cfg, 'managed-instance', 'get')

    def test_unassigned_package_causes_no_service_call(self):
        with patch.object(helper, 'request') as request:
            with self.assertRaises(ValueError):
                helper.install(self.cfg, 'unassigned')
            request.assert_not_called()

    def test_unavailable_package_is_not_submitted(self):
        with patch.object(helper, 'request', return_value={'data': {'items': []}}) as request:
            with self.assertRaisesRegex(ValueError, 'not available'):
                helper.install(self.cfg, 'tree')
            self.assertEqual(request.call_count, 1)

    def test_ambiguous_submission_prevents_retry(self):
        available = {'data': {'items': [{'display-name': 'tree'}]}}
        with patch.object(helper, 'request', side_effect=[available, RuntimeError('timeout')]):
            with self.assertRaises(RuntimeError):
                helper.install(self.cfg, 'tree')
        self.assertTrue((self.state / 'submission-pending.json').exists())
        with patch.object(helper, 'request') as request:
            with self.assertRaisesRegex(ValueError, 'earlier submission'):
                helper.install(self.cfg, 'tree')
            request.assert_not_called()

    def test_missing_tracking_identifier_does_not_imply_success(self):
        available = {'data': {'items': [{'display-name': 'tree'}]}}
        with patch.object(helper, 'request', side_effect=[available, {'data': {}}]):
            with self.assertRaisesRegex(ValueError, 'no tracking identifier'):
                helper.install(self.cfg, 'tree')
        self.assertTrue((self.state / 'submission-pending.json').exists())
        self.assertFalse((self.state / 'last-request.json').exists())

    def test_successful_submission_is_tracked_but_not_completion(self):
        available = {'data': {'items': [{'display-name': 'tree'}]}}
        with patch.object(helper, 'request', side_effect=[available, {'opc-work-request-id': 'job-1'}]) as request:
            helper.install(self.cfg, 'tree')
        self.assertEqual(helper.last_request(), 'job-1')
        self.assertFalse((self.state / 'submission-pending.json').exists())
        self.assertIn('Completion has not been confirmed', self.output.getvalue())
        args = request.call_args.args
        self.assertIn('test-instance', args)
        self.assertEqual(json.loads(args[args.index('--package-names') + 1]), ['tree'])

    def test_active_job_prevents_another_submission(self):
        helper.save('last-request.json', {'work_request_id': 'job-1'})
        with patch.object(helper, 'request', return_value={'data': {'status': 'IN_PROGRESS'}}) as request:
            with self.assertRaisesRegex(ValueError, 'still active'):
                helper.install(self.cfg, 'tree')
            self.assertEqual(request.call_count, 1)

    def test_failed_job_collects_errors_and_exits_unsuccessfully(self):
        helper.save('last-request.json', {'work_request_id': 'job-1'})
        with patch.object(helper, 'request', side_effect=[
            {'data': {'status': 'FAILED'}}, {'data': {'items': [{'message': 'denied'}]}}
        ]):
            self.assertEqual(helper.job_status(self.cfg), 1)
        self.assertTrue((self.state / 'job-errors.json').exists())

    def test_normal_status_uses_api_status_field(self):
        with patch.object(helper, 'load_config', return_value=self.cfg), patch.object(
            helper, 'request', return_value={'data': {'status': 'NORMAL', 'display-name': 'server'}}
        ):
            self.assertEqual(helper.main(['status']), 0)
        self.assertIn('Managed status: NORMAL', self.output.getvalue())

    def test_unreachable_status_is_not_ready(self):
        with patch.object(helper, 'load_config', return_value=self.cfg), patch.object(
            helper, 'request', return_value={'data': {'status': 'UNREACHABLE'}}
        ):
            self.assertEqual(helper.main(['status']), 1)


if __name__ == '__main__':
    unittest.main(verbosity=2)
