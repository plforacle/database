# Lab 3 SME Review

## Scope

Implemented the Lab 3 simplification discussed with the workshop owner after they confirmed a backup at `C:\Users\Perside\Documents\GitHub\hold\rhel-to-ol-migration-test-10272026`.

- Removed the manual baseline evidence bundle and repeated final migration gate.
- Retained the five source-readiness steps.
- Retained the tested script commit, checksum, and version check; removed the help pager command.
- Retained the dry-run command and completion review. Replaced the extra report-discovery commands and checklist with guidance on exact, nearest-release, and unavailable package results.
- Retained the optional recovery task and renumbered the remaining tasks 1 through 4.
- Updated the Lab 3 menu description and introduction references.

## Sources

- SME feedback: `C:\Users\Perside\main\_01_hands-on-labs\rhel-ol-hol\PLAB_RS_COMMENTS.docx`. POINT was interpreted as Step.
- [Migration script documentation](https://github.com/oracle/migrate-to-ol/blob/main/README-migrate-to-oracle-linux.md), reviewed October 27, 2026. The currently indexed documentation describes newer script behavior, so the lab distinguishes it from the pinned workshop version.
- Existing workshop commands and the owner's October 25 live dry-run output establish the pinned command, completion message, counts, and report names.

## Remaining Review

This is the Lab 3 portion of the review. Labs 4 through 6 have not been rewritten in this change. Lab 5 and the kernel and Ksplice labs still reference the old manual evidence files. Those dependencies must be removed or revised with the agreed Lab 5 removal and later lab edits before testing the revised workshop from a fresh environment. Existing live-test evidence is preserved.

No script version was changed. No commands were run on the lab VM. The backup and all OCI resources were left untouched.

## Validation

- Repository LiveLabs Markdown validator: zero errors for the revised lab, student introduction, and student manifest.
- Student manifest parses, and every local tutorial path resolves.
- Lab 3 task numbers are contiguous from 1 through 4; no em dashes are present.
- `git diff --check` passed. This is documentation validation, not a fresh migration run.
