# Removal of the Separate Migration Validation Lab

## Scope

The workshop owner authorized removal of Student Lab 5 following the Linux SME review.

- Removed Validate the Migration from the student manifest and preserved its Markdown and helper script under `archive/retired-student-labs/validate-cleanup`.
- Verified SHA-256 hashes of both archived files before and after the move; contents are unchanged.
- Renumbered kernel choices to Lab 5, Ksplice to Lab 6, and the OSMH labs to Labs 7 through 9.
- Updated student and instructor references, prerequisites, diagrams, menu entries, README mapping, and archive navigation.
- Removed dependencies on the retired manual migration evidence files. The kernel lab now starts from the checks in Lab 4. Ksplice creates and checks its own application checksum in its existing report directory.
- Retained the separate OSMH client's baseline and reports. They do not depend on the retired migration evidence task.
- Reduced the planned student schedule by the removed lab's 25 minutes, from 360 to 335 minutes. Other lab estimates are unchanged.

## Review Boundary

This change removes the validation lab and fixes its dependencies. The remaining SME simplification of the kernel-choice lab is the next review item. Existing historical QA records keep their original lab numbers and test results. The current live-test resume point is report review in Lab 4, Task 2, Step 4, followed by current Lab 5 on the existing migrated VM.

No commands were run on the lab hosts. Existing backups, evidence on the VM, screenshots, and OCI resources were not changed.

## Validation

- Repository LiveLabs Markdown validator checked all 15 active Markdown files and both launch/manifest pairs: zero errors.
- Student lab numbers are contiguous from 1 through 9; instructor lab numbers remain 1 and 2. Menu titles match their lab headings.
- Local Markdown links, variable files, and all seven image references resolve.
- The student schedule totals 335 minutes, matching its introduction and total row.
- No active Markdown depends on `ol-migration-evidence` files or migration archives. Ksplice writes its local checksum before checking it.
- No em dashes or Lab 10 references remain in active Markdown.
- Archived files retain their original hashes, and `git diff --check` passed.

These checks validate the documentation. A fresh live run of the revised workshop remains to be completed.
