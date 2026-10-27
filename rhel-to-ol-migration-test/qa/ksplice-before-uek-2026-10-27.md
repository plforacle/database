# Ksplice Before Kernel Choices

## Authorized Sequence

The workshop owner agreed to run Ksplice before Kernel Choices to avoid switching to a new UEK with no available patches and then returning to RHCK.

- Lab 4: migrate to Oracle Linux RHCK and verify the result.
- Lab 5: apply actual Ksplice updates on that RHCK and verify the boot identity and application response.
- Lab 6: install and boot UEK as a separate kernel-choice exercise.
- Labs 7 through 9: prepare, register, and update the separate on-premises OSMH client.

## Changes

- Reordered the two student manifest entries and matched lab headings, prerequisites, next-lab references, schedule, introduction, diagram, and README mapping.
- Removed the Ksplice lab's UEK-to-RHCK return procedure. Its starting state is now the Oracle RHCK from Lab 4.
- Kept the simplified application checks and actual-patch requirement. No-updates results remain incomplete for the patch-application test.
- Added instructor pre-class verification of RHCK patch availability.
- Explained that the later UEK reboot occurs after the Ksplice test and does not invalidate its recorded no-reboot result.
- Kept nine labs and the 335-minute schedule. Lab directories retain their names.

## Live Test Boundary

The earlier October 27 results retain the lab numbers used during testing. That run demonstrated UEK installation and 135 actual Ksplice patches on Oracle RHCK, with unchanged boot identity and a working application afterward. The revised order has not yet been run end to end on a fresh VM. Existing successful results need not be repeated merely because the documentation order changed.

No commands were executed on the VM by the assistant. No OCI resources, backups, screenshots, archives, or earlier test results were changed.

## Validation

- Repository LiveLabs Markdown validator checked all 15 active Markdown files and both launch/manifest pairs: zero errors.
- Both menus resolve. Student lab numbers remain 1 through 9, with Ksplice fifth and Kernel Choices sixth; lab headings match the menu titles.
- The introduction's schedule matches the student manifest order and still totals 335 minutes.
- All local links, variable files, and seven image references resolve. No em dashes are present in active Markdown.
- Confirmed the Lab 4 to Ksplice to Kernel Choices to OSMH prerequisite chain and transitions. Ksplice contains no kernel-switch or reboot procedure.
- All nine remaining Ksplice Bash blocks pass `bash -n`. `git diff --check` passed.
