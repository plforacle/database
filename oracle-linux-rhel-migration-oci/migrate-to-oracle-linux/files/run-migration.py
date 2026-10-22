#!/usr/bin/env python3
"""Run the workshop's pinned conversion only after recovery evidence passes.

Python 3.9 compatible. This launcher never reboots the guest. Saved OCI CLI
records and manual Console confirmations are checkpoint evidence, not a live
lookup of backup availability.
"""

import argparse
from datetime import datetime, timedelta, timezone
import hashlib
import json
import os
from pathlib import Path
import platform
import re
import shlex
import subprocess
import sys
import uuid


VENDOR_NAME = "migrate-to-oracle-linux.sh"
LAUNCHER_NAME = "run-migration.py"
MAX_EVIDENCE_AGE = timedelta(hours=24)
IMDS_URL = "http://169.254.169.254/opc/v2/instance/"


class MigrationError(Exception):
    def __init__(self, message, status=1):
        super().__init__(message)
        self.status = status if status > 0 else 128 - status


def unique_object(pairs):
    result = {}
    for key, value in pairs:
        if key in result:
            raise MigrationError("Duplicate JSON field: " + key)
        result[key] = value
    return result


def parse_json(text, label):
    try:
        return json.loads(text, object_pairs_hook=unique_object)
    except (ValueError, TypeError) as exc:
        raise MigrationError("Invalid {} JSON: {}".format(label, exc)) from exc


def record_data(envelope, label):
    if not isinstance(envelope, dict) or not isinstance(envelope.get("data"), dict):
        raise MigrationError(label + " must be a complete OCI CLI get envelope with a data object")
    return envelope["data"]


def required_text(record, field):
    value = record.get(field)
    if not isinstance(value, str) or not value or value != value.strip():
        raise MigrationError("Missing or malformed field: " + field)
    return value


def required_ocid(record, field, resource=None):
    value = required_text(record, field)
    kind = re.escape(resource) if resource else r"[a-z][a-z0-9]*"
    if not re.fullmatch(r"ocid1\." + kind
                        + r"\.[a-z0-9]+\.[a-z0-9-]+\.[A-Za-z0-9][A-Za-z0-9._:-]*", value):
        raise MigrationError("Malformed OCID field: " + field)
    return value


def timestamp_at(record, field, label, now, require_recent=True):
    value = required_text(record, field)
    try:
        if "T" not in value:
            raise ValueError("date and time required")
        created = datetime.fromisoformat(value.replace("Z", "+00:00"))
        if created.utcoffset() is None:
            raise ValueError("timezone required")
        created = created.astimezone(timezone.utc)
    except ValueError as exc:
        raise MigrationError(label + " has malformed " + field) from exc
    if created > now:
        raise MigrationError(label + " " + field + " is in the future")
    if require_recent and now - created > MAX_EVIDENCE_AGE:
        raise MigrationError(label + " is older than 24 hours; create fresh rehearsal evidence")
    return created


def created_at(record, label, now, require_recent=True):
    return timestamp_at(record, "time-created", label, now, require_recent)


def validate_recovery(backup_envelope, attachment_envelope, guest_instance_id, now=None):
    """Validate saved records against this guest, without requiring OCI API access."""
    now = now or datetime.now(timezone.utc)
    backup = record_data(backup_envelope, "Backup")
    attachment = record_data(attachment_envelope, "Attachment")
    required_ocid(backup, "id", "bootvolumebackup")
    required_ocid(attachment, "id")
    backup_volume = required_ocid(backup, "boot-volume-id", "bootvolume")
    attachment_volume = required_ocid(attachment, "boot-volume-id", "bootvolume")
    attachment_instance = required_ocid(attachment, "instance-id", "instance")
    required_ocid({"id": guest_instance_id}, "id", "instance")
    if required_text(backup, "lifecycle-state") != "AVAILABLE":
        raise MigrationError("Boot-volume backup must be AVAILABLE")
    if required_text(backup, "type") != "FULL":
        raise MigrationError("Boot-volume backup must have type FULL")
    if required_text(attachment, "lifecycle-state") != "ATTACHED":
        raise MigrationError("Boot-volume attachment must be ATTACHED")
    if backup_volume != attachment_volume:
        raise MigrationError("Backup and attachment refer to different boot volumes")
    if attachment_instance != guest_instance_id:
        raise MigrationError("Attachment belongs to a different instance than this guest")
    backup_time = created_at(backup, "Backup", now)
    attachment_time = created_at(attachment, "Attachment", now, require_recent=False)
    if backup_time < attachment_time:
        raise MigrationError("Backup predates this boot-volume attachment")
    return backup, attachment


def validate_console_checkpoint(checkpoint, guest_id, now=None):
    """Check a distinct manual attestation without treating it as an OCI record."""
    now = now or datetime.now(timezone.utc)
    if not isinstance(checkpoint, dict):
        raise MigrationError("Console checkpoint must be a manual attestation object")
    if required_text(checkpoint, "evidence-source") != "oci-console-manual":
        raise MigrationError("Console checkpoint evidence-source must be oci-console-manual")
    required_ocid({"id": guest_id}, "id", "instance")
    if required_ocid(checkpoint, "instance-id", "instance") != guest_id:
        raise MigrationError("Console checkpoint belongs to a different instance than this guest")
    required_ocid(checkpoint, "boot-volume-id", "bootvolume")
    required_ocid(checkpoint, "backup-id", "bootvolumebackup")
    if required_text(checkpoint, "backup-type") != "FULL":
        raise MigrationError("Console checkpoint backup-type must be FULL")
    if required_text(checkpoint, "backup-state") != "AVAILABLE":
        raise MigrationError("Console checkpoint backup-state must be AVAILABLE")
    for field in ("attachment-confirmed", "prepared-source-confirmed"):
        if checkpoint.get(field) is not True:
            raise MigrationError("Console checkpoint requires an exact true value for " + field)
    backup_time = timestamp_at(checkpoint, "backup-time-created", "Console backup", now)
    confirmed_time = timestamp_at(checkpoint, "confirmed-at", "Console confirmation", now)
    if confirmed_time <= backup_time:
        raise MigrationError("Console confirmation must be after the backup")
    return checkpoint


def record_console_checkpoint(path, instance_id, ask=None, now=None):
    """Write one new manual recovery checkpoint. Never invoke conversion."""
    ask = ask or input
    path = path.expanduser()
    if not path.is_absolute():
        raise MigrationError("--record-console-checkpoint must be an absolute path")
    if path.exists() or path.is_symlink():
        raise MigrationError("Console checkpoint already exists; choose a new path")
    if not path.parent.is_dir():
        raise MigrationError("Console checkpoint parent directory must already exist")
    print("Guest instance: " + instance_id, flush=True)
    print("This records manual Console confirmation, not API verification. "
          "No conversion or reboot is performed.", flush=True)
    try:
        backup_id = ask("Full boot-volume backup OCID from OCI Console: ")
        volume_id = ask("Boot-volume OCID currently attached to the displayed guest: ")
        backup_time = ask("Backup creation time, ISO 8601 with timezone "
                          "(for example 2026-10-20T14:00:00-04:00): ")
        confirmed = ask("In OCI Console, verify that this backup is Full and Available, "
                        "its source is the boot volume currently attached to the displayed guest, "
                        "and source preparation finished before this backup. "
                        "Type exactly yes to attest to all of these checks: ")
    except EOFError as exc:
        raise MigrationError("Console confirmation was not completed; no checkpoint saved") from exc
    if confirmed != "yes":
        raise MigrationError("Exact yes confirmation is required; no checkpoint saved")
    confirmed_time = now or datetime.now(timezone.utc)
    checkpoint = {
        "evidence-source": "oci-console-manual",
        "instance-id": instance_id,
        "boot-volume-id": volume_id,
        "backup-id": backup_id,
        "backup-type": "FULL",
        "backup-state": "AVAILABLE",
        "backup-time-created": backup_time,
        "confirmed-at": confirmed_time.isoformat(),
        "attachment-confirmed": True,
        "prepared-source-confirmed": True,
    }
    validate_console_checkpoint(checkpoint, instance_id, confirmed_time)
    # Exclusive creation also prevents an overwrite if a file appears during prompts.
    with path.open("x", encoding="utf-8") as output:
        json.dump(checkpoint, output, indent=2)
        output.write("\n")
    print("Saved manual Console checkpoint: " + str(path), flush=True)
    print("This is manual Console confirmation, not API verification. "
          "No conversion or reboot was performed.", flush=True)
    return checkpoint


def verify_checksums(script_dir):
    """The manifest covers the two executable inputs, not itself."""
    manifest = script_dir / "SHA256SUMS"
    if manifest.is_symlink():
        raise MigrationError("SHA256SUMS must be a regular local file")
    try:
        lines = manifest.read_text(encoding="utf-8").splitlines()
        expected = {}
        for line in lines:
            match = re.fullmatch(r"([a-fA-F0-9]{64}) [ *]([^/\\]+)", line)
            if not match or match.group(2) in expected:
                raise MigrationError("Malformed or duplicate SHA256SUMS entry")
            expected[match.group(2)] = match.group(1).lower()
        if set(expected) != {VENDOR_NAME, LAUNCHER_NAME}:
            raise MigrationError("SHA256SUMS must contain exactly the pinned vendor script and launcher")
        for name, digest in expected.items():
            path = script_dir / name
            if path.is_symlink() or not path.is_file():
                raise MigrationError("Pinned input must be a regular local file: " + name)
            if hashlib.sha256(path.read_bytes()).hexdigest() != digest:
                raise MigrationError("SHA256 mismatch: " + name)
        if b"--verify-boot-only" not in (script_dir / VENDOR_NAME).read_bytes():
            raise MigrationError("Pinned vendor script lacks the required boot verification mode")
    except OSError as exc:
        raise MigrationError("Cannot verify pinned inputs: " + str(exc)) from exc


def require_environment(os_release=Path("/etc/os-release")):
    if not hasattr(os, "geteuid") or os.geteuid() != 0:
        raise MigrationError("Run this launcher as root with sudo")
    try:
        values = {}
        for line in os_release.read_text(encoding="utf-8").splitlines():
            if "=" in line and not line.startswith("#"):
                key, value = line.split("=", 1)
                tokens = shlex.split(value)
                if len(tokens) != 1:
                    raise ValueError("malformed os-release value")
                values[key] = tokens[0]
    except (OSError, ValueError) as exc:
        raise MigrationError("Cannot read the guest operating system: " + str(exc)) from exc
    if values.get("ID") != "rhel" or values.get("VERSION_ID") != "9.8":
        raise MigrationError("This rehearsal requires a fresh RHEL 9.8 guest")
    if platform.machine() != "x86_64":
        raise MigrationError("This rehearsal requires x86_64")


def guest_instance_id():
    command = ["curl", "--fail", "--silent", "--show-error", "--noproxy", "*",
               "--connect-timeout", "3", "--max-time", "10", "-H",
               "Authorization: Bearer Oracle", IMDS_URL]
    try:
        result = subprocess.run(command, capture_output=True, text=True, timeout=12)
    except (OSError, subprocess.TimeoutExpired) as exc:
        raise MigrationError("Cannot query OCI instance metadata: " + str(exc)) from exc
    if result.returncode:
        raise MigrationError("OCI instance metadata lookup failed: " + result.stderr.strip(),
                             result.returncode)
    metadata = parse_json(result.stdout, "OCI instance metadata")
    if not isinstance(metadata, dict):
        raise MigrationError("OCI instance metadata must be an object")
    return required_ocid(metadata, "id", "instance")


def emit(log, message):
    print(message, flush=True)
    log.write(message + "\n")
    log.flush()


def run_command(command, log):
    """Stream merged stdout and stderr while retaining the command's exit status."""
    emit(log, "$ " + shlex.join(command))
    try:
        with subprocess.Popen(command, stdout=subprocess.PIPE, stderr=subprocess.STDOUT,
                              text=True, encoding="utf-8", errors="replace") as process:
            for line in process.stdout:
                print(line, end="", flush=True)
                log.write(line)
                log.flush()
            return process.wait()
    except OSError as exc:
        raise MigrationError("Cannot start {}: {}".format(command[0], exc), 127) from exc


def convert(script, log, runner=run_command):
    steps = [
        ("Conversion", ["bash", str(script), "-y", "--target-version", "9.8", "--kernel", "rhck"]),
        ("Boot verification", ["bash", str(script), "--verify-boot-only", "--kernel", "rhck",
                               "--target-version", "9.8"]),
        ("Package consistency", ["dnf", "check"]),
    ]
    for label, command in steps:
        status = runner(command, log)
        emit(log, "{} exit status: {}".format(label, status))
        if status:
            raise MigrationError(label + " failed. Do not reboot. Keep this evidence.", status)


def main(argv=None):
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--backup-json", type=Path,
                        help="Saved OCI boot-volume-backup get JSON envelope")
    parser.add_argument("--attachment-json", type=Path,
                        help="Saved OCI boot-volume-attachment get JSON envelope")
    parser.add_argument("--console-checkpoint", type=Path,
                        help="Previously recorded manual Console confirmation JSON")
    parser.add_argument("--record-console-checkpoint", type=Path,
                        help="Record manual Console confirmation only, without conversion")
    parser.add_argument("--evidence-dir", type=Path,
                        help="Absolute parent path for a new timestamped evidence directory")
    args = parser.parse_args(argv)
    if args.record_console_checkpoint:
        if any((args.backup_json, args.attachment_json, args.console_checkpoint, args.evidence_dir)):
            parser.error("--record-console-checkpoint cannot be combined with conversion arguments")
    else:
        if not args.evidence_dir:
            parser.error("conversion requires --evidence-dir")
        if bool(args.backup_json) != bool(args.attachment_json):
            parser.error("--backup-json and --attachment-json must be provided together")
        if args.console_checkpoint and args.backup_json:
            parser.error("--console-checkpoint cannot be combined with OCI CLI JSON records")
        if not args.console_checkpoint and not args.backup_json:
            parser.error("conversion requires --console-checkpoint or both OCI CLI JSON records")
    status = 1
    log = None
    try:
        script_dir = Path(__file__).resolve().parent
        verify_checksums(script_dir)
        require_environment()
        if args.record_console_checkpoint:
            record_console_checkpoint(args.record_console_checkpoint, guest_instance_id())
            status = 0
            return status
        evidence_root = args.evidence_dir.expanduser()
        if not evidence_root.is_absolute():
            raise MigrationError("--evidence-dir must be an absolute path")
        evidence_root.mkdir(parents=True, exist_ok=True)
        stamp = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
        evidence = evidence_root / (stamp + "-" + uuid.uuid4().hex[:8])
        evidence.mkdir(mode=0o700)
        log = (evidence / "migration.log").open("w", encoding="utf-8")
        emit(log, "Evidence directory: " + str(evidence))
        emit(log, "SHA256SUMS verified. Never reboot on a failed check.")
        instance_id = guest_instance_id()
        emit(log, "Guest instance: " + instance_id)
        if args.console_checkpoint:
            emit(log, "Evidence source: manual Console confirmation, not API verification. "
                      "Recheck the AVAILABLE full backup and attached boot volume in OCI Console.")
            checkpoint_text = args.console_checkpoint.read_text(encoding="utf-8")
            checkpoint_path = evidence / "console-checkpoint.json"
            checkpoint_path.write_text(checkpoint_text, encoding="utf-8")
            emit(log, "Saved manual Console checkpoint: " + str(checkpoint_path))
            checkpoint = validate_console_checkpoint(parse_json(checkpoint_text, "Console checkpoint"),
                                                       instance_id)
            emit(log, "Manually confirmed boot volume: " + checkpoint["boot-volume-id"])
            emit(log, "Manually confirmed AVAILABLE full backup: " + checkpoint["backup-id"])
        else:
            emit(log, "Saved OCI records are checkpoint evidence and can be stale. Recheck the "
                      "AVAILABLE full backup and attached boot volume in OCI Console before this run.")
            backup_text = args.backup_json.read_text(encoding="utf-8")
            attachment_text = args.attachment_json.read_text(encoding="utf-8")
            (evidence / "backup.json").write_text(backup_text, encoding="utf-8")
            (evidence / "attachment.json").write_text(attachment_text, encoding="utf-8")
            backup, attachment = validate_recovery(parse_json(backup_text, "Backup"),
                                                   parse_json(attachment_text, "Attachment"),
                                                   instance_id)
            emit(log, "Boot volume: " + attachment["boot-volume-id"])
            emit(log, "AVAILABLE full backup: " + backup["id"])
        emit(log, "Recovery evidence gate passed. Starting conversion.")
        convert(script_dir / VENDOR_NAME, log)
        emit(log, "All commands passed. This launcher does not reboot. Complete the lab's "
                  "manual evidence review before any reboot.")
        status = 0
    except MigrationError as exc:
        status = exc.status
        if log:
            emit(log, "STOP: " + str(exc))
        else:
            print("STOP: " + str(exc), file=sys.stderr)
    except (OSError, UnicodeError) as exc:
        if log:
            emit(log, "STOP: Cannot read or save migration evidence: " + str(exc))
        else:
            print("STOP: " + str(exc), file=sys.stderr)
    except KeyboardInterrupt:
        status = 130
        if log:
            emit(log, "STOP: Interrupted. Do not reboot.")
    finally:
        if log:
            emit(log, "FINAL STATUS: " + str(status))
            log.close()
        else:
            print("FINAL STATUS: " + str(status), file=sys.stderr)
    return status


if __name__ == "__main__":
    sys.exit(main())
