# Current migration status and next work

Updated October 22, 2026. This record supersedes earlier proposed next actions. The assignment remains migration of the OCI-provided RHEL image to Oracle Linux. The successful imported-image workshop supplies a comparison, not replacement acceptance for that assignment.

## Folder inspection before manual practice

A local reinspection confirmed the tested workshop.5 candidate and launcher still match their recorded SHA256 values. All required main-lab fixes, the 13 Lab 1 screenshots and naming repair, manifests, validation scripts, tests and sanitized run reports are present.

The required `qa/migration-safety.patch` and ignored private evidence were absent from this checkout. Their saved local hold copy was found and preserved. The exact patch, 574 live-evidence files and eight reference-run files were copied back without changing the saved source. Every copied file matched its saved-source hash; the five latest source-4 indexes verified 191 evidence files, and the final 6,065,474-byte archive and all hash-bound acceptance inputs verified. The folder's ignore rules now allow the required patch while keeping private evidence ignored.

Two instruction gaps were corrected for manual practice: Lab 2 now explicitly requires its boot gate and a RHEL reboot after an approved metadata correction even without a new kernel, and Lab 7 requires independent boot acceptance and the reviewed post-Ksplice wrapper handling before a recommended maintenance reboot. No migration or launcher code changed.

Fresh local workshop checks passed 2,055 checks with zero failures, and scoped whitespace checks passed. The locally available LiveLabs Markdown validator also passed all eleven active inputs: the introduction, eight labs, launch page and tenancy manifest. QA reports and private evidence are outside the published-lab formatting scope. No VM, OCI, commit, push or publication action occurred during this inspection. The recorded Lab 7 stop and unresolved Policy Advisor outcome remain in effect.

## Stopped at Lab 7 by operator request

The operator reports that Policy Advisor setup is stuck and explicitly requested stopping at Lab 7. Setup completion is unconfirmed; some IAM changes may have been applied. Do not retry Setup, enable plugins, resume registration, run further guest commands, or investigate the hang without a new instruction. No cancellation or rollback of the Console operation was attempted. The last confirmed plugin state is not enabled. Source-4 remains running with the previously verified Oracle Linux, maintenance and Ksplice results. Earlier Lab 8 checks were preliminary guest validation before registration; the full lab and cleanup are not complete. The next interaction is a summary and any later operator-directed resumption.

## Verified results

| Workflow | Result |
| --- | --- |
| Standalone workshop at `C:/Users/Perside/Documents/GitHub/oracle-linux-rhel-migration-oci` | User completed the imported-image migration and reboot. Read-only inspection independently verified Oracle Linux 9.8, running/default Oracle RHCK `5.14.0-687.5.3.el9_8.x86_64`, Oracle kernel RPM ownership, no failed units, Apache HTTP 200, and an unchanged application checksum. |
| Database-folder OCI-image workshop, source-2 | Workshop.2 failed during the library transition and kernel installation. No reboot was performed. |
| Database-folder OCI-image workshop, source-3 | Workshop.4 created Oracle boot files but recorded an unresolved GRUB LVM disk lookup error. Its final verification stopped with status 1. No Oracle-kernel reboot was attempted. |
| Existing unconverted source-4, read-only inspection | RHEL 9.8 reproduces the exact same root LVM probe error before conversion. Current VG/LV IDs match; separate `/boot` probes pass. Packages, protected boot files, and boot ID were unchanged. |
| Source-4, Lab 2 preparation on October 21 | RHUI update completed with status zero and reproduced the same GRUB root-volume error during the Red Hat kernel transaction. Apache deployment and local/public HTTP checks passed. The source reboot was paused for the focused correction. |
| Source-4, approved correction on October 22 | One native LVM metadata transaction changed sequence 3 to 4. Every root, boot, and home GRUB probe passed, including the nested root probe. All 327 protected-file hashes and recorded storage, package, filesystem, and kernel invariants matched. |
| Source-4, resumed Lab 2 on October 22 | Rebooted successfully into running/default RHEL `5.14.0-687.59.1.el9_8.x86_64`. SSH, Apache, SELinux, package checks, and GRUB probes passed. Final baseline captured and downloaded, preserving the provisional baseline. |
| Source-4, Lab 3 assessment | Workshop.5 dry run completed with status zero at 14:00:16 UTC. Current-source package exceptions and exact Oracle RHCK `687.58.1` plans were reviewed. Source RPM inventory stayed unchanged. |
| Source-4, Lab 4 controlled rehearsal | Workshop.5 conversion and all launcher stages returned zero. Independent target boot checks and the explained Oracle crash-reservation difference were accepted, and complete evidence was copied locally before reboot. Oracle Linux 9.8 rebooted with running/default Oracle RHCK `5.14.0-687.58.1.el9_8.x86_64`. SSH, settled system state, package checks, SELinux, firewall, Apache and unchanged local/public HTTP workload checks passed by 14:55:28 UTC. |
| Source-4, Lab 5 maintenance | Actual 43-package upgrade returned zero but included UEKR7 `xfsprogs`, which the initial review missed. A separately previewed BaseOS-only one-package restoration passed, and the UEK repository was disabled permanently. Independent boot checks and a maintenance reboot passed; running/default Oracle RHCK, package consistency, security state and workload remained healthy at 15:06:54 UTC. |
| Source-4, Lab 6 Ksplice | Reviewed Uptrack client installation and preview passed. Updates `52t6sh3y` and `ciad9z8t` were applied successfully. Installed-update inventory and a clean remaining preview confirmed application; boot ID, base kernel and workload checksum stayed unchanged. Final checks passed at 15:09:18 UTC. |
| Source-4, Lab 7 guest preparation | Signed OCI Included repository bootstrap passed, Oracle Cloud Agent was updated to `1.64.0-16.el9`, and guest preparation passed at 15:21:04 UTC. The operator reports that the OS Management Hub plugin is not enabled. Console/IAM review, profile selection, plugin eligibility, registration and service jobs remain pending. |
| Source-4, Lab 8 guest validation | All 17 validation-helper checks and unchanged application comparisons passed after the agent update. The frozen verifier stopped on Uptrack's intentional script wrapper for `depmod`; exact payload/runtime review and every other independent check supported separate hash-bound acceptance at 15:28:13 UTC. Original STOP preserved. Final evidence was copied locally and hash-verified, and public HTTP passed at 15:30:35 UTC. No cleanup. |

The successful instance uses `cloud-user`; `opc` authentication was rejected. Its script SHA256 is `5435b59e2f0e672111fd1545b2b428cabe473e7c116a353716c2f7de869d0a42`, matching the standalone workshop's unmodified upstream pin.

## What the successful run explains

The imported-image migration also logged temporary `libattr.so.1` loader errors. Its pre-conversion inventory retained both RHEL `687.5.3` and `687.59.1` kernels. The script replaced the exact-match `687.5.3` kernel with Oracle packages in the early reinstall batch; the new initramfs was created at 20:25:47 UTC, before the library errors at 20:28:44 UTC.

Source-2 had only RHEL `687.45.1`. Its Oracle `687.5.3` kernel was first installed as a dependency inside the later library-downgrade transaction. Kernel scriptlets ran during the loader failure, and the target boot files were absent afterward. This recorded difference strongly supports package ordering as the explanation for the contrasting kernel-file results. It does not establish the complete cause of the original failed VM, which was not examined.

The successful old-script path is not a clean transaction reference: its log contains loader errors and retained Red Hat Insights packages. Do not copy its vulnerable library downgrade or remove current safeguards merely because that instance booted.

Raw records and the detailed comparison are saved locally under ignored `qa/live-evidence/successful-instance-20261021/`. No public IPs, resource OCIDs, key material, or raw subscriber records are needed in the shared QA summary.

## Findings and accepted controlled transition

Source-3 uses LVM for its XFS root; the successful reference uses XFS directly on a partition. Both use BIOS, VirtIO SCSI storage, and a VirtIO NIC. Saved target initramfs listings include VirtIO SCSI on both systems, so simply adding that driver is not an established fix.

The source-3 error occurred in the Oracle kernel transaction while the Red Hat bootloader packages were still installed. Before conversion, source-4 supplied the Red Hat `20-grub.install` and reproduced the identical probe failure. An isolated read-only reproduction of its nested Btrfs condition emitted the error while selecting the ordinary branch with status zero. The full hook was not executed during the earlier read-only inspection. The subsequent Lab 2 RHEL kernel transaction ran installation hooks and reproduced the same error. The exact source-3 invocation was not traced.

Source-4's VG/LV UUID inventory exactly matched the identifier in the error. Its verbose GRUB trace read the home LV but omitted the root LV. Captured metadata formatting matched a specific cursor weakness, supported by a local reproduction and static disassembly of the installed Red Hat executable. The approved native metadata rewrite resolved every failing root probe without changing recorded storage mappings or protected files. The corrected source then passed a RHEL reboot before the separately successful Oracle conversion and reboot. See the [historical findings](source4-readonly-20261021.md), [correction acceptance](source4-correction-20261022.md), and [accepted source-4 run](source4-run-20261022.md).

Source-4's actual conversion contained no GRUB error, shared-library loader failure, failed scriptlet or fatal transaction message. Its explicit Oracle kernel was installed and verified before deferred bootloader work, and the subsequent Lab 4 boot verifications passed. This recorded result qualifies this corrected source and exact candidate; it does not establish universal acceptance across other images or repository states.

The local candidate checks boot-file presence, RPM ownership, and GRUB entries. Manual checks of required storage drivers and direct GRUB root/boot probes remain separately necessary; the narrower script gate must not be described as complete boot acceptance.

## Frozen candidate and local checks

Workshop.5 SHA256: `bad3bf7487ab2a4b7650e91c56af81ae332e26381a3941b275a33029f05d9df0`. It adds the missed GRUB-error detection and a narrowly verified FAT timestamp allowance. It retains both library-downgrade guards, source-kernel protection, and explicit Oracle-kernel verification before deferred bootloader work. These guards do not resolve the GRUB cause.

The previous session recorded 150 migration safety tests and 43 launcher tests passing. Those tests were not rerun for this documentation review. Current local structural results are recorded in [local-results.txt](local-results.txt). Workshop.5 now has an accepted controlled live conversion and Oracle-kernel reboot on the corrected source-4 baseline; see the [complete run and qualification limits](source4-run-20261022.md).

The final October 22 local workshop checker passed 2,053 checks with zero failures, and scoped `git diff --check` passed. The current scope excludes gitignored private transport evidence; historical artifacts were preserved. The official LiveLabs validator remains unavailable.

The frozen verifier has a separately confirmed post-Ksplice limitation: it assumes that `depmod` is ELF, while the installed Oracle Uptrack package intentionally wraps that command. Its later nonzero result was preserved. Exact package/scriptlet provenance, original ELF and interpreter/library checks, supported functional checks and every remaining independent boot check supported a narrow hash-bound manual acceptance. The script and checksums remain unchanged; this is not a blanket wrapper bypass. See the [final guest validation record](source4-final-validation-20261022.md).

## Current next step and boundaries

The operator clarified that source-4 had completed Lab 1 only, then explicitly requested execution of the remaining labs from this database-folder checkout. Normal Lab 2 preparation, its source-kernel reboot, workload checks, and final baseline capture have now passed. See the [Lab 2 execution record](source4-lab2-20261021.md). The exact Console image display label remains unrecorded; the source image OCID and other guest provenance are recorded privately.

The [metadata-format correction](metadata-format-correction-plan.md) was explicitly approved and executed once after the operator supplied source-4's Full, Available backup, created October 22 at 13:43:05 UTC, and confirmed current boot-volume attachment. This is operator-supplied Console evidence, not an authenticated API query or a tested restore. The library/package-order issue remains separate. Do not prescribe another VM or restart Lab 1.

The source-4 correction did not reboot or convert the VM. After its independent acceptance, the separately authorized normal Lab 2 RHEL reboot passed. The original root-volume probe error preceded migration and was reproduced by a normal RHEL kernel transaction; Oracle packages did not introduce it on this baseline.

The operator subsequently supplied the prepared-source backup created at 14:16:08 UTC on October 22. It is Full and Available in the supplied screenshot and identifies source-4's same confirmed boot volume. It captures the corrected source after its RHEL reboot and final baseline. The verified recorder passed at 14:37:44 UTC. This remains manual Console evidence, not a live API query or tested restore. Apache quiescence was not attested; the static immutable workload and crash-consistent backup limitation are recorded in the [current readiness review](source4-readiness-20261022.md).

The controlled conversion is complete and accepted. It ran once from 14:38:49 to 14:46:11 UTC. The initial independent collector stopped on an exact whole-argument comparison because Oracle's verified kdump hook changed only the crash-reservation default. The original STOP was preserved, and a separate narrow review accepted that difference without changing BLS entries. All root and storage/network arguments, drivers, boot artifacts and other required checks passed. The pre-reboot archive was copied locally and hash-verified before the successful Oracle RHCK reboot.

The first reconnect found the system still starting; that result was preserved. After waiting for normal boot completion, the settled check passed at 14:55:27 UTC, followed by public HTTP 200 with the unchanged workload checksum at 14:55:28 UTC. The source-4 [run report](source4-run-20261022.md) records actual firmware replacement, retained Red Hat kernel and Insights exceptions, and the full EVRA-difference review. A successful controlled run does not mean every package kept its source EVRA or that retained Insights and management agents are qualified.

Labs 5 and 6 have now passed their recorded maintenance and live-update checks; see the [maintenance report](source4-maintenance-20261022.md). Lab 5's initial review missed a UEKR7 userspace package in its 43-package plan. The actual installation and focused BaseOS restoration are both preserved in the record, and the RHCK workshop now disables the UEK repository before updates. A successful maintenance reboot followed all checks. Lab 6 applied two named enablement/interface updates without reboot; it does not demonstrate a changed effective kernel version or a particular CVE fix. A combined public HTTP check at 15:10:54 UTC returned 200, the marker and the unchanged checksum.

Lab 7 guest preparation has passed. Its original missing-repository stop was preserved; a reviewed, signed OCI Included release package supplied the repository, followed by a one-package agent update. Separate absence checks confirmed that both example job packages were initially uninstalled. The regional HTTPS 404 response establishes connectivity, not IAM or registration. The operator reports that the OS Management Hub plugin is not enabled. Policy Advisor proposed two user groups, one dynamic group and sixteen policy statements for the lab compartment. The supplied proposal was reviewed against the standard setup. The operator subsequently reported that Setup was stuck and requested stopping at Lab 7. Completion and any partial IAM changes remain unconfirmed; no retry or rollback was attempted. IAM/dynamic-group configuration, profile selection, plugin eligibility and enablement, registration and service jobs remain unverified. A working Oracle Cloud Agent service or Uptrack client does not complete those tasks.

Lab 8 guest validation and the narrowly reviewed post-Uptrack independent acceptance have passed under that pre-registration scope. Its final archive was copied locally and verified at 6,065,474 bytes with matching remote/local SHA256; the public HTTP marker and unchanged checksum passed at 15:30:35 UTC. The [final validation report](source4-final-validation-20261022.md) records the archive digest and qualification limits. Cleanup is excluded. Do not rerun conversion or prescribe another VM. Console-dependent work may require operator participation; do not fabricate OCI service evidence or declare Lab 7 or the entire workshop complete.

Preserve the successful reference instance, all failed instances, their boot volumes, backups, and logs. Do not access, repair, rerun, or reboot the failed instances. No commit, push, or publication is authorized.
