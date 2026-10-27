# Lab 4 SME Review

## Scope

Implemented the Lab 4 review after the workshop owner accepted the revised Lab 3 and requested Lab 4.

- Removed the repeated starting migration gate. Kept the working-directory command with the migration command so a new SSH session can start correctly.
- Preserved the tested migration options: `-y --target-version 9.8 --kernel rhck`.
- Reworked the phase and error guidance using the script documentation, distinguishing same-release package replacement from a general distribution synchronization.
- Preserved the OS, repository, installed-kernel, and default-kernel checks before reboot.
- Reworked report review to use the completed migration's printed State path and explain the four HTML reports printed by the pinned script. Removed the partial 40-row preview and dependency on Lab 5 for interpreting exceptions.
- Preserved reboot, SSH reconnection, and running-kernel verification.
- Removed the separate health-check and changed/unchanged review tasks. Kept a single webpage check with the existing user-provided screenshot under the reboot task, as proposed during discussion.
- Renumbered the remaining tasks 1 through 3. Lab 5 and later labs remain pending their separate review.

## Sources

- SME feedback: `C:\Users\Perside\main\_01_hands-on-labs\rhel-ol-hol\PLAB_RS_COMMENTS.docx`; POINT means Step.
- [Migration script documentation](https://github.com/oracle/migrate-to-ol/blob/main/README-migrate-to-oracle-linux.md), reviewed October 27, 2026: migration modes, operations, reports, and reboot guidance. Current documentation can describe a newer script version.
- The owner's October 25 successful migration output confirms the pinned version's completion message, four HTML report filenames, selected RHCK, and differing source and Oracle kernel builds.

## Validation

- Repository LiveLabs Markdown validator: zero errors for Lab 4.
- Task and step numbering is contiguous; the retained screenshot path resolves.
- No em dashes or obsolete task, migration-gate, or Lab 5 references remain in Lab 4.
- `git diff --check` passed.

No migration was rerun and no OCI resources, backup files, or screenshot contents were changed.
