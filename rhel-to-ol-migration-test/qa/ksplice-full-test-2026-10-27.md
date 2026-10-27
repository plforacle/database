# Ksplice Full Test Preparation

## Requested Changes

The workshop owner confirmed Ksplice and OSMH are required. After the UEK 8 attempt found no patches, they requested simpler application checks and an actual patch application before moving on.

- Removed the background HTTP monitor, monitor lifecycle commands, Apache PID comparison, and application checksum checks from Lab 6.
- Kept a simple HTTP/service check before and after patching and the boot-ID and boot-kernel comparisons.
- Retained patch preview, apply, and installed-patch inspection. Added the apply exit status and made actual patch identifiers part of a successful full test.
- Assigned each new attempt a separate timestamped report directory so a new baseline does not overwrite the earlier no-updates result.
- Clarified that a no-updates check leaves actual patch application untested.
- Added instructor guidance for checking the retained Oracle RHCK as another candidate on the disposable workshop VM. Kernel selection and reboot happen before a fresh patch-test baseline. No patches are removed to create a demonstration.
- Updated the student menu description. Ksplice remains required.

## Live Test Evidence

The supplied UEK 8 test used `6.12.0-207.111.5.6.el9uek.x86_64`. Uptrack installed and reported no updates; installed patches remained None. The earlier boot, checksum, process, and 189 HTTP samples passed. Those observations remain in the live-test record and do not demonstrate patch application.

The owner verified the retained Oracle RHCK `5.14.0-687.5.3.el9_8.x86_64`, selected its boot entry, rebooted, and queried patch availability. The preview listed 135 patches. They captured a fresh post-reboot baseline and successfully applied all 135 patches with exit status 0. The installed patch-identifier set matched the preview.

Final comparisons showed an unchanged boot ID and boot-kernel version. The effective RHCK advanced to `5.14.0-687.48.1.el9_8.x86_64`. Apache returned `active` and the workload marker before and after patching. The full RHCK patch test passed. Continuous application availability was not measured in this simplified attempt.

The lab now includes the verified RHCK selection path in its no-updates branch. Exact builds and available patches must be checked before each class. The assistant did not execute commands on the host.

## Sources

- [Ksplice maintained kernels](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-MaintainedKernels.html), reviewed October 27, 2026: Oracle Linux 9 RHCK and UEK 8 are maintained families; new kernels can lack available patches.
- [Uptrack commands](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-UsingtheuptrackupgradeCommandtoManageKspliceUpdates.html), reviewed October 27, 2026: preview, apply, installed update list, and effective kernel inspection.
- [Changing the default kernel](https://docs.oracle.com/en/operating-systems/oracle-linux/9/boot/boot-UsinggrubbyToManageKernels_change_the_default_kernel.html), reviewed October 27, 2026: selecting an installed kernel and rebooting.
- Owner-provided terminal results in `live-test-2026-10-27.md`.

## Validation

- Repository LiveLabs Markdown validator: zero errors for the revised Ksplice lab and student manifest.
- Confirmed five sequential tasks and sequential steps, with no remaining monitor, process-ID, or checksum-file dependencies in the active lab.
- All 12 final Bash command blocks passed `bash -n`, including the added RHCK instructions. The kernel-lab reference link resolves and task numbers remain 1 through 5.
- Student manifest parses and its local tutorial paths resolve. `git diff --check` passed.

The user-provided live results verify actual patch application on this OCI test VM. They do not establish on-premises entitlement or Ksplice through OSMH. No OCI resources were cleaned up, and no backups, screenshots, or earlier test records were changed.
