# Student Curriculum Reduction: October 26, 2026

## Changes

The Oracle Linux SMEs requested removing the package-maintenance and Apache-troubleshooting labs. These were Student Labs 8 and 9 before this change.

- Removed their entries from the student manifest and preserved both complete folders under `archive/retired-student-labs`.
- Renumbered OSMH preparation, registration, and patching from Labs 10-12 to Labs 8-10.
- Updated both manifests, introductions, prerequisites, onward references, the shared agent reference, instructor setup, README mapping, and archive index.
- Updated the Ksplice handoff to lead directly to OSMH preparation. An unresolved Ksplice issue remains recorded independently of OSMH results.
- The student workshop now has ten numbered labs and a 360-minute planned agenda, including orientation. Provisioning, synchronization, and job waits can add elapsed time.
- The instructor workshop retains two numbered preparation labs, its OSMH setup guide, and a 150-minute active-time plan.

## Validation

- PASS: Both manifests, their local targets and variables, launcher files, and menu/H1 titles resolve and agree.
- PASS: Numbered student labs are contiguous from 1 through 10; instructor labs remain 1 and 2.
- PASS: All 16 active Markdown files have valid local links, image references, and copy-tag balance. Active lab references stay within the current numbering.
- PASS: Student and instructor schedule totals match their lab durations.
- PASS: All 145 retained Bash blocks are unchanged from the pre-reduction working files. This edit changes curriculum and references, not command behavior.
- PASS: Both newly archived folders match their pre-move SHA-256 snapshots byte for byte.
- PASS: All seven existing image links resolve. No screenshots were edited or fabricated.
- PASS: Active Markdown contains no em dash characters. `git diff --check` passed.
- PASS: LiveLabs Markdown validator checked all 16 active Markdown files and both launcher/manifest pairs with zero errors, including the Python indentation check.

## Context and Remaining Work

Earlier split and OSMH QA reports describe the sequence at the time of those changes. Their previous lab numbers are historical; use the current manifest and README mapping.

The on-premises station/client architecture, instructor prerequisites, agent procedure, and resource-retention rules remain in place. Live registration, patching, and the OSMH screenshots still need the pilot described in the OSMH replacement review, now for Student Labs 8 through 10. No new runtime success is claimed.

No OCI resources, Linux hosts, agents, profiles, keys, or scheduled jobs were changed. The existing migration live test can still resume at Student Lab 5, Task 1.
