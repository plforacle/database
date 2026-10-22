"""Local control-flow tests only. No guest, OCI, DNF, or Bash execution."""

from datetime import datetime, timedelta, timezone
import hashlib
import importlib.util
import io
import json
from pathlib import Path
import subprocess
import tempfile
import unittest
from unittest.mock import Mock, patch


SCRIPT = Path(__file__).resolve().parents[1] / "migrate-to-oracle-linux" / "files" / "run-migration.py"
SPEC = importlib.util.spec_from_file_location("recovery_runner", SCRIPT)
runner = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(runner)
REAL_VALIDATE_RECOVERY = runner.validate_recovery
REAL_VALIDATE_CONSOLE = runner.validate_console_checkpoint
NOW = datetime(2026, 10, 20, 15, tzinfo=timezone.utc)
INSTANCE = "ocid1.instance.oc1.iad.freshguest"
VOLUME = "ocid1.bootvolume.oc1.iad.freshvolume"


def records():
    return (
        {"data": {"id": "ocid1.bootvolumebackup.oc1.iad.freshbackup",
                  "boot-volume-id": VOLUME, "lifecycle-state": "AVAILABLE", "type": "FULL",
                  "time-created": (NOW - timedelta(hours=1)).isoformat()}},
        {"data": {"id": "ocid1.instancebootvolumeattachment.oc1.iad.freshattachment",
                  "boot-volume-id": VOLUME, "instance-id": INSTANCE,
                  "lifecycle-state": "ATTACHED",
                  "time-created": (NOW - timedelta(hours=2)).isoformat()}},
    )


def console_checkpoint():
    return {
        "evidence-source": "oci-console-manual",
        "instance-id": INSTANCE,
        "boot-volume-id": VOLUME,
        "backup-id": "ocid1.bootvolumebackup.oc1.iad.freshbackup",
        "backup-type": "FULL",
        "backup-state": "AVAILABLE",
        "backup-time-created": (NOW - timedelta(hours=1)).isoformat(),
        "confirmed-at": (NOW - timedelta(minutes=10)).isoformat(),
        "attachment-confirmed": True,
        "prepared-source-confirmed": True,
    }


class ConsoleCheckpointTests(unittest.TestCase):
    def setUp(self):
        self.checkpoint = console_checkpoint()

    def check(self):
        return runner.validate_console_checkpoint(self.checkpoint, INSTANCE, NOW)

    def test_recent_manual_confirmation_matches_this_guest(self):
        self.assertEqual(self.check(), self.checkpoint)
        self.assertNotIn("data", self.checkpoint)

    def test_manual_checkpoint_for_failed_guest_cannot_authorize_fresh_guest(self):
        self.checkpoint["instance-id"] = "ocid1.instance.oc1.iad.failedguest"
        with self.assertRaisesRegex(runner.MigrationError, "different instance"):
            self.check()

    def test_wrong_source_type_state_or_ocid_kind_is_rejected(self):
        for field, bad in [("evidence-source", "oci-cli"), ("backup-type", "INCREMENTAL"),
                           ("backup-state", "CREATING"),
                           ("instance-id", VOLUME), ("boot-volume-id", INSTANCE),
                           ("backup-id", VOLUME), ("backup-id", "not-an-ocid"),
                           ("backup-id", "ocid1.bootvolumebackup.short"),
                           ("boot-volume-id", "ocid1.bootvolume.oc1..unique"),
                           ("instance-id", "ocid1.instance.oc1.iad.")]:
            with self.subTest(field=field, value=bad):
                self.checkpoint = console_checkpoint()
                self.checkpoint[field] = bad
                with self.assertRaises(runner.MigrationError):
                    self.check()

    def test_required_text_fields_must_exist_and_have_correct_types(self):
        for field, value in console_checkpoint().items():
            if isinstance(value, bool):
                continue
            for bad in [None, "", " ", 1, True, [], {}]:
                with self.subTest(field=field, value=bad):
                    self.checkpoint = console_checkpoint()
                    self.checkpoint[field] = bad
                    with self.assertRaises(runner.MigrationError):
                        self.check()
            with self.subTest(missing=field):
                self.checkpoint = console_checkpoint()
                del self.checkpoint[field]
                with self.assertRaises(runner.MigrationError):
                    self.check()

    def test_flags_require_exact_true_and_cannot_be_missing(self):
        for field in ["attachment-confirmed", "prepared-source-confirmed"]:
            for bad in [False, None, 1, "true", "yes", [], {}]:
                with self.subTest(field=field, value=bad):
                    self.checkpoint = console_checkpoint()
                    self.checkpoint[field] = bad
                    with self.assertRaisesRegex(runner.MigrationError, "exact true"):
                        self.check()
            self.checkpoint = console_checkpoint()
            del self.checkpoint[field]
            with self.assertRaisesRegex(runner.MigrationError, "exact true"):
                self.check()

    def test_backup_and_confirmation_must_be_recent_and_not_future(self):
        for field in ["backup-time-created", "confirmed-at"]:
            for instant, message in [(NOW - timedelta(hours=24, seconds=1), "older than 24"),
                                     (NOW + timedelta(seconds=1), "future")]:
                with self.subTest(field=field, instant=instant):
                    self.checkpoint = console_checkpoint()
                    self.checkpoint[field] = instant.isoformat()
                    with self.assertRaisesRegex(runner.MigrationError, message):
                        self.check()

    def test_confirmation_must_follow_backup(self):
        for confirmed in [(NOW - timedelta(hours=2)).isoformat(),
                          self.checkpoint["backup-time-created"]]:
            with self.subTest(confirmed=confirmed):
                self.checkpoint["confirmed-at"] = confirmed
                with self.assertRaisesRegex(runner.MigrationError, "must be after"):
                    self.check()

    def test_both_timestamps_require_time_and_timezone(self):
        for field in ["backup-time-created", "confirmed-at"]:
            for bad in ["2026-10-20", "2026-10-20T14:00:00", "yesterday"]:
                with self.subTest(field=field, value=bad):
                    self.checkpoint = console_checkpoint()
                    self.checkpoint[field] = bad
                    with self.assertRaisesRegex(runner.MigrationError, "malformed"):
                        self.check()

    def test_manual_confirmation_cannot_use_cli_envelopes_or_nonobjects(self):
        for value in [*records(), [], None, "confirmed"]:
            with self.subTest(value=value):
                with self.assertRaises(runner.MigrationError):
                    runner.validate_console_checkpoint(value, INSTANCE, NOW)

    def test_recorder_only_saves_valid_attestation_with_exact_confirmation(self):
        with tempfile.TemporaryDirectory() as temp:
            path = Path(temp) / "checkpoint.json"
            replies = [self.checkpoint["backup-id"], VOLUME,
                       self.checkpoint["backup-time-created"], "yes"]
            with patch("sys.stdout", new_callable=io.StringIO) as stdout, \
                    patch.object(runner, "convert") as conversion:
                saved = runner.record_console_checkpoint(path, INSTANCE, Mock(side_effect=replies), NOW)
            conversion.assert_not_called()
            self.assertEqual(json.loads(path.read_text(encoding="utf-8")), saved)
            self.assertEqual(saved["confirmed-at"], NOW.isoformat())
            self.assertEqual(saved["instance-id"], INSTANCE)
            self.assertIn("manual Console confirmation, not API verification", stdout.getvalue())
            self.assertNotIn("data", saved)

    def test_invalid_or_cancelled_recorder_creates_no_file(self):
        for bad_confirmation in ["no", "Yes", "YES", "yes ", ""]:
            with self.subTest(reply=bad_confirmation), tempfile.TemporaryDirectory() as temp:
                path = Path(temp) / "checkpoint.json"
                replies = [self.checkpoint["backup-id"], VOLUME,
                           self.checkpoint["backup-time-created"], bad_confirmation]
                with patch("sys.stdout", new_callable=io.StringIO):
                    with self.assertRaisesRegex(runner.MigrationError, "Exact yes"):
                        runner.record_console_checkpoint(path, INSTANCE, Mock(side_effect=replies), NOW)
                self.assertFalse(path.exists())
        with tempfile.TemporaryDirectory() as temp:
            path = Path(temp) / "checkpoint.json"
            with patch("sys.stdout", new_callable=io.StringIO):
                with self.assertRaises(runner.MigrationError):
                    runner.record_console_checkpoint(path, INSTANCE, Mock(side_effect=EOFError), NOW)
                with self.assertRaises(runner.MigrationError):
                    runner.record_console_checkpoint(path, INSTANCE,
                        Mock(side_effect=["bad", VOLUME, self.checkpoint["backup-time-created"], "yes"]), NOW)
            self.assertFalse(path.exists())

    def test_recorder_never_overwrites_existing_checkpoint(self):
        with tempfile.TemporaryDirectory() as temp:
            path = Path(temp) / "checkpoint.json"
            path.write_text("preserve original", encoding="utf-8")
            ask = Mock()
            with self.assertRaisesRegex(runner.MigrationError, "already exists"):
                runner.record_console_checkpoint(path, INSTANCE, ask, NOW)
            ask.assert_not_called()
            self.assertEqual(path.read_text(encoding="utf-8"), "preserve original")


class RecoveryGateTests(unittest.TestCase):
    def setUp(self):
        self.backup, self.attachment = records()

    def check(self):
        return runner.validate_recovery(self.backup, self.attachment, INSTANCE, NOW)

    def test_current_full_backup_on_this_guest_is_accepted(self):
        backup, attachment = self.check()
        self.assertEqual(backup["boot-volume-id"], attachment["boot-volume-id"])

    def test_preserved_failed_guest_backup_cannot_authorize_fresh_guest(self):
        self.backup["data"]["boot-volume-id"] = "ocid1.bootvolume.oc1.iad.failedvolume"
        self.attachment["data"]["boot-volume-id"] = "ocid1.bootvolume.oc1.iad.failedvolume"
        self.attachment["data"]["instance-id"] = "ocid1.instance.oc1.iad.failedguest"
        with self.assertRaisesRegex(runner.MigrationError, "different instance"):
            self.check()

    def test_different_source_volume_is_rejected(self):
        self.backup["data"]["boot-volume-id"] = "ocid1.bootvolume.oc1.iad.other"
        with self.assertRaisesRegex(runner.MigrationError, "different boot volumes"):
            self.check()

    def test_backup_must_postdate_current_attachment(self):
        self.backup["data"]["time-created"] = (NOW - timedelta(hours=3)).isoformat()
        with self.assertRaisesRegex(runner.MigrationError, "predates"):
            self.check()

    def test_only_full_available_backup_and_attached_volume_pass(self):
        for which, field, value in [(0, "type", "INCREMENTAL"),
                                    (0, "lifecycle-state", "CREATING"),
                                    (1, "lifecycle-state", "DETACHED")]:
            with self.subTest(field=field, value=value):
                self.backup, self.attachment = records()
                (self.backup, self.attachment)[which]["data"][field] = value
                with self.assertRaises(runner.MigrationError):
                    self.check()

    def test_missing_or_wrongly_typed_required_fields_fail_closed(self):
        for which, original in enumerate(records()):
            for field in original["data"]:
                self.backup, self.attachment = records()
                del (self.backup, self.attachment)[which]["data"][field]
                with self.subTest(record=which, missing=field):
                    with self.assertRaises(runner.MigrationError):
                        self.check()
                for bad in [None, "", "  ", 42, [], {}]:
                    with self.subTest(record=which, field=field, value=bad):
                        self.backup, self.attachment = records()
                        (self.backup, self.attachment)[which]["data"][field] = bad
                        with self.assertRaises(runner.MigrationError):
                            self.check()

    def test_incomplete_or_list_envelopes_fail_closed(self):
        for bad in [{}, [], None, {"data": []}, {"data": "value"}]:
            with self.subTest(value=bad):
                with self.assertRaises(runner.MigrationError):
                    runner.validate_recovery(bad, self.attachment, INSTANCE, NOW)
                with self.assertRaises(runner.MigrationError):
                    runner.validate_recovery(self.backup, bad, INSTANCE, NOW)

    def test_future_backup_and_attachment_are_rejected(self):
        for which in [0, 1]:
            with self.subTest(record=which):
                self.backup, self.attachment = records()
                (self.backup, self.attachment)[which]["data"]["time-created"] = (NOW + timedelta(seconds=1)).isoformat()
                with self.assertRaisesRegex(runner.MigrationError, "future"):
                    self.check()

    def test_backup_older_than_24_hours_is_rejected(self):
        self.attachment["data"]["time-created"] = (NOW - timedelta(days=7)).isoformat()
        self.backup["data"]["time-created"] = (NOW - timedelta(hours=24, seconds=1)).isoformat()
        with self.assertRaisesRegex(runner.MigrationError, "older than 24"):
            self.check()

    def test_recent_backup_on_an_older_attachment_is_accepted(self):
        self.attachment["data"]["time-created"] = (NOW - timedelta(days=7)).isoformat()
        self.check()

    def test_timestamp_requires_valid_timezone_and_time(self):
        for timestamp in ["yesterday", "2026-10-20", "2026-10-20T14:00:00", "2026-99-20T14:00:00Z"]:
            with self.subTest(timestamp=timestamp):
                self.backup["data"]["time-created"] = timestamp
                with self.assertRaisesRegex(runner.MigrationError, "malformed time-created"):
                    self.check()

    def test_utc_z_timestamp_is_supported_by_python39(self):
        self.backup["data"]["time-created"] = "2026-10-20T14:00:00Z"
        self.check()

    def test_duplicate_json_fields_are_rejected(self):
        with self.assertRaisesRegex(runner.MigrationError, "Duplicate"):
            runner.parse_json('{"data":{"type":"FULL","type":"INCREMENTAL"}}', "Backup")

    def test_malformed_json_and_ocids_are_rejected(self):
        with self.assertRaisesRegex(runner.MigrationError, "Invalid"):
            runner.parse_json("not json", "Backup")
        self.backup["data"]["boot-volume-id"] = "freshvolume"
        with self.assertRaisesRegex(runner.MigrationError, "Malformed OCID"):
            self.check()


class IntegrityAndGuestTests(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory()
        self.addCleanup(self.temp.cleanup)
        self.root = Path(self.temp.name)
        (self.root / runner.VENDOR_NAME).write_text("# --verify-boot-only\n", encoding="utf-8")
        (self.root / runner.LAUNCHER_NAME).write_text("# launcher\n", encoding="utf-8")
        self.write_manifest()

    def write_manifest(self):
        lines = [hashlib.sha256((self.root / name).read_bytes()).hexdigest() + "  " + name
                 for name in [runner.VENDOR_NAME, runner.LAUNCHER_NAME]]
        (self.root / "SHA256SUMS").write_text("\n".join(lines) + "\n", encoding="utf-8")

    def test_both_execution_inputs_are_checked(self):
        runner.verify_checksums(self.root)
        for name in [runner.VENDOR_NAME, runner.LAUNCHER_NAME]:
            with self.subTest(name=name):
                original = (self.root / name).read_bytes()
                (self.root / name).write_bytes(original + b"changed")
                with self.assertRaisesRegex(runner.MigrationError, "SHA256 mismatch"):
                    runner.verify_checksums(self.root)
                (self.root / name).write_bytes(original)

    def test_unpatched_script_is_rejected_even_with_matching_digest(self):
        (self.root / runner.VENDOR_NAME).write_text("# old script\n", encoding="utf-8")
        self.write_manifest()
        with self.assertRaisesRegex(runner.MigrationError, "lacks the required"):
            runner.verify_checksums(self.root)

    def test_bad_manifest_or_missing_pinned_file_is_rejected(self):
        original = (self.root / "SHA256SUMS").read_text(encoding="utf-8")
        for content in ["", "invalid\n", original.splitlines()[0] + "\n", original + original,
                        original.replace(runner.LAUNCHER_NAME, "../run-migration.py")]:
            with self.subTest(content=content):
                (self.root / "SHA256SUMS").write_text(content, encoding="utf-8")
                with self.assertRaises(runner.MigrationError):
                    runner.verify_checksums(self.root)
        self.write_manifest()
        (self.root / runner.VENDOR_NAME).unlink()
        with self.assertRaises(runner.MigrationError):
            runner.verify_checksums(self.root)

    def test_only_root_rhel98_x86_64_guest_is_accepted(self):
        os_release = self.root / "os-release"
        os_release.write_text('ID="rhel"\nVERSION_ID="9.8"\n', encoding="utf-8")
        with patch.object(runner.os, "geteuid", return_value=0, create=True), \
                patch.object(runner.platform, "machine", return_value="x86_64"):
            runner.require_environment(os_release)
            for content in ['ID="ol"\nVERSION_ID="9.8"', 'ID="rhel"\nVERSION_ID="9.7"']:
                os_release.write_text(content, encoding="utf-8")
                with self.assertRaisesRegex(runner.MigrationError, "RHEL 9.8"):
                    runner.require_environment(os_release)
        with patch.object(runner.os, "geteuid", return_value=1000, create=True):
            with self.assertRaisesRegex(runner.MigrationError, "root"):
                runner.require_environment(os_release)
        with patch.object(runner.os, "geteuid", return_value=0, create=True), \
                patch.object(runner.platform, "machine", return_value="aarch64"):
            os_release.write_text('ID=rhel\nVERSION_ID="9.8"', encoding="utf-8")
            with self.assertRaisesRegex(runner.MigrationError, "x86_64"):
                runner.require_environment(os_release)

    def test_metadata_query_is_local_v2_authenticated_and_bounded(self):
        result = subprocess.CompletedProcess([], 0, json.dumps({"id": INSTANCE}), "")
        with patch.object(runner.subprocess, "run", return_value=result) as query:
            self.assertEqual(runner.guest_instance_id(), INSTANCE)
        args, kwargs = query.call_args
        self.assertIn("Authorization: Bearer Oracle", args[0])
        self.assertIn(runner.IMDS_URL, args[0])
        self.assertIn("--noproxy", args[0])
        self.assertEqual(kwargs["timeout"], 12)

    def test_metadata_failure_preserves_nonzero_status(self):
        result = subprocess.CompletedProcess([], 22, "", "not available")
        with patch.object(runner.subprocess, "run", return_value=result):
            with self.assertRaises(runner.MigrationError) as caught:
                runner.guest_instance_id()
        self.assertEqual(caught.exception.status, 22)


class SequencingTests(unittest.TestCase):
    def test_conversion_boot_validation_and_dnf_are_ordered(self):
        fake = Mock(return_value=0)
        with patch("sys.stdout", new_callable=io.StringIO):
            runner.convert(Path("pinned") / runner.VENDOR_NAME, io.StringIO(), runner=fake)
        commands = [call.args[0] for call in fake.call_args_list]
        self.assertEqual(len(commands), 3)
        self.assertEqual(commands[0][2:], ["-y", "--target-version", "9.8", "--kernel", "rhck"])
        self.assertEqual(commands[1][2:], ["--verify-boot-only", "--kernel", "rhck",
                                            "--target-version", "9.8"])
        self.assertEqual(commands[2], ["dnf", "check"])
        self.assertFalse(any("reboot" in command for command in commands))

    def test_any_failure_stops_remaining_commands_and_retains_status(self):
        for failed_step in range(3):
            with self.subTest(step=failed_step):
                fake = Mock(side_effect=[0] * failed_step + [37])
                with patch("sys.stdout", new_callable=io.StringIO):
                    with self.assertRaises(runner.MigrationError) as caught:
                        runner.convert(Path("pinned") / runner.VENDOR_NAME, io.StringIO(), runner=fake)
                self.assertEqual(caught.exception.status, 37)
                self.assertEqual(fake.call_count, failed_step + 1)

    def test_command_stdout_stderr_and_exit_status_are_retained(self):
        process = Mock()
        process.stdout = io.StringIO("normal output\nerror output\n")
        process.wait.return_value = 23
        context = Mock()
        context.__enter__ = Mock(return_value=process)
        context.__exit__ = Mock(return_value=False)
        log = io.StringIO()
        with patch.object(runner.subprocess, "Popen", return_value=context) as popen, \
                patch("sys.stdout", new_callable=io.StringIO) as stdout:
            self.assertEqual(runner.run_command(["mock-command"], log), 23)
        self.assertEqual(popen.call_args.kwargs["stderr"], subprocess.STDOUT)
        self.assertIn("error output", log.getvalue())
        self.assertIn("normal output", stdout.getvalue())

    def run_main(self, backup, attachment, failure=None):
        with tempfile.TemporaryDirectory() as temp:
            root = Path(temp)
            backup_path, attachment_path = root / "backup.json", root / "attachment.json"
            backup_path.write_text(json.dumps(backup), encoding="utf-8")
            attachment_path.write_text(json.dumps(attachment), encoding="utf-8")
            with patch.object(runner, "verify_checksums") as checksum, \
                    patch.object(runner, "require_environment"), \
                    patch.object(runner, "guest_instance_id", return_value=INSTANCE), \
                    patch.object(runner, "convert", side_effect=failure) as convert, \
                    patch("sys.stdout", new_callable=io.StringIO), \
                    patch.object(runner, "validate_recovery",
                                 side_effect=lambda b, a, i: REAL_VALIDATE_RECOVERY(b, a, i, NOW)):
                status = runner.main(["--backup-json", str(backup_path), "--attachment-json",
                                      str(attachment_path), "--evidence-dir", str(root / "evidence")])
                checksum.assert_called_once()
                logs = list((root / "evidence").glob("*/migration.log"))
                self.assertEqual(len(logs), 1)
                content = logs[0].read_text(encoding="utf-8")
                self.assertTrue((logs[0].parent / "backup.json").is_file())
                return status, content, convert.call_count

    def test_recovery_gate_precedes_every_conversion(self):
        backup, attachment = records()
        attachment["data"]["instance-id"] = "ocid1.instance.oc1.iad.failedguest"
        status, content, calls = self.run_main(backup, attachment)
        self.assertEqual(status, 1)
        self.assertEqual(calls, 0)
        self.assertIn("FINAL STATUS: 1", content)
        self.assertIn("different instance", content)

    def test_main_records_failure_status_and_never_claims_success(self):
        status, content, calls = self.run_main(*records(), failure=runner.MigrationError("failed", 47))
        self.assertEqual(status, 47)
        self.assertEqual(calls, 1)
        self.assertIn("FINAL STATUS: 47", content)
        self.assertNotIn("All commands passed", content)

    def test_main_records_success_and_stale_record_caution(self):
        status, content, calls = self.run_main(*records())
        self.assertEqual((status, calls), (0, 1))
        self.assertIn("FINAL STATUS: 0", content)
        self.assertIn("checkpoint evidence and can be stale", content)

    def test_checksum_failure_stops_before_environment_metadata_or_conversion(self):
        with patch.object(runner, "verify_checksums", side_effect=runner.MigrationError("bad checksum")), \
                patch.object(runner, "require_environment") as environment, \
                patch.object(runner, "guest_instance_id") as metadata, \
                patch.object(runner, "convert") as conversion, \
                patch("sys.stderr", new_callable=io.StringIO):
            status = runner.main(["--backup-json", "unused.json", "--attachment-json", "unused.json",
                                  "--evidence-dir", "unused"])
        self.assertEqual(status, 1)
        environment.assert_not_called()
        metadata.assert_not_called()
        conversion.assert_not_called()

    def run_console_main(self, checkpoint):
        with tempfile.TemporaryDirectory() as temp:
            root = Path(temp)
            checkpoint_path = root / "checkpoint.json"
            checkpoint_path.write_text(json.dumps(checkpoint), encoding="utf-8")
            with patch.object(runner, "verify_checksums") as checksum, \
                    patch.object(runner, "require_environment"), \
                    patch.object(runner, "guest_instance_id", return_value=INSTANCE), \
                    patch.object(runner, "convert") as convert, \
                    patch("sys.stdout", new_callable=io.StringIO), \
                    patch.object(runner, "validate_console_checkpoint",
                                 side_effect=lambda c, i: REAL_VALIDATE_CONSOLE(c, i, NOW)):
                status = runner.main(["--console-checkpoint", str(checkpoint_path),
                                      "--evidence-dir", str(root / "evidence")])
                checksum.assert_called_once()
                logs = list((root / "evidence").glob("*/migration.log"))
                self.assertEqual(len(logs), 1)
                content = logs[0].read_text(encoding="utf-8")
                self.assertEqual(json.loads((logs[0].parent / "console-checkpoint.json").read_text(
                    encoding="utf-8")), checkpoint)
                self.assertFalse((logs[0].parent / "backup.json").exists())
                self.assertFalse((logs[0].parent / "attachment.json").exists())
                return status, content, convert.call_count

    def test_manual_conversion_copies_distinct_checkpoint_and_labels_source(self):
        status, content, calls = self.run_console_main(console_checkpoint())
        self.assertEqual((status, calls), (0, 1))
        self.assertIn("manual Console confirmation, not API verification", content)
        self.assertIn("Saved manual Console checkpoint:", content)
        self.assertIn("FINAL STATUS: 0", content)

    def test_manual_gate_blocks_conversion_for_wrong_guest_or_missing_confirmation(self):
        for field, value in [("instance-id", "ocid1.instance.oc1.iad.failedguest"),
                             ("attachment-confirmed", False),
                             ("prepared-source-confirmed", False)]:
            with self.subTest(field=field):
                checkpoint = console_checkpoint()
                checkpoint[field] = value
                status, content, calls = self.run_console_main(checkpoint)
                self.assertEqual((status, calls), (1, 0))
                self.assertIn("FINAL STATUS: 1", content)

    def test_recorder_main_checks_bundle_environment_and_live_guest_without_conversion(self):
        with patch.object(runner, "verify_checksums") as checksum, \
                patch.object(runner, "require_environment") as environment, \
                patch.object(runner, "guest_instance_id", return_value=INSTANCE) as metadata, \
                patch.object(runner, "record_console_checkpoint") as record, \
                patch.object(runner, "convert") as conversion, \
                patch("sys.stderr", new_callable=io.StringIO):
            self.assertEqual(runner.main(["--record-console-checkpoint", "/var/tmp/checkpoint.json"]), 0)
        checksum.assert_called_once()
        environment.assert_called_once()
        metadata.assert_called_once()
        record.assert_called_once_with(Path("/var/tmp/checkpoint.json"), INSTANCE)
        conversion.assert_not_called()

    def test_conflicting_incomplete_or_missing_arguments_fail_before_actions(self):
        cases = [[], ["--evidence-dir", "evidence"],
                 ["--backup-json", "b.json", "--evidence-dir", "evidence"],
                 ["--attachment-json", "a.json", "--evidence-dir", "evidence"],
                 ["--console-checkpoint", "c.json"],
                 ["--console-checkpoint", "c.json", "--backup-json", "b.json",
                  "--attachment-json", "a.json", "--evidence-dir", "evidence"]]
        for extra in [["--console-checkpoint", "c.json"], ["--backup-json", "b.json"],
                      ["--attachment-json", "a.json"], ["--evidence-dir", "evidence"]]:
            cases.append(["--record-console-checkpoint", "new.json"] + extra)
        with patch.object(runner, "verify_checksums") as checksum, \
                patch.object(runner, "convert") as conversion, \
                patch("sys.stderr", new_callable=io.StringIO):
            for arguments in cases:
                with self.subTest(arguments=arguments), self.assertRaises(SystemExit) as caught:
                    runner.main(arguments)
                self.assertEqual(caught.exception.code, 2)
        checksum.assert_not_called()
        conversion.assert_not_called()


if __name__ == "__main__":
    unittest.main()
