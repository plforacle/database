# Lab 7 Restored

## Owner Request

The workshop owner requested restoring Lab 7 after the earlier decision to stop at Lab 6. This decision supersedes the scope recorded in `workshop-ends-lab6-2026-10-27.md`.

## Changes

- Restored `osmh-oci-quickstart` from the retired student archive to the workshop root. Both files retained their SHA-256 hashes.
- Restored Lab 7 to the student manifest and the transition from Lab 6.
- Updated introductions, prerequisites, architecture, schedule, README, and archive navigation for seven student labs and 290 minutes including orientation.
- Kept the separate Oracle OSMH tutorial as additional learning.
- Preserved earlier live-test results and scope records. Agent installation passed; IAM setup failed, and registration, inventory, and update execution remain unverified.

Only local documentation changed. No OCI resources or hosts were changed or removed.

## Validation

- Repository LiveLabs formatting checks passed for all 11 active Markdown files and both launcher/manifest pairs.
- Student and instructor menus contain seven and two numbered labs respectively.
- Active local links resolve, and the student schedule totals 290 minutes.
- No stale six-lab completion text or em dash characters remain in active content.
- `git diff --check` passed.
