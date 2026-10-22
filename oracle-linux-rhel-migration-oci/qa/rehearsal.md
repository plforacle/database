# Migration safety rehearsal for WMS 12229

Status: **Workshop.2 and workshop.4 conversions failed. Workshop.4 ended with final status `1` at 20:34:42 UTC; no reboot performed. Learner migration remains unqualified.** Preserve source-3 as partially converted alongside both earlier failed instances. Do not rerun or reboot them.

This status applies to the OCI platform-image procedure. The separate imported-image workshop now has independently verified migration and reboot evidence. See [current status and next work](current-status.md), which supersedes earlier proposed next actions. No new VM or live attempt is prescribed.

This record concerns **Migrate Red Hat Enterprise Linux to Oracle Linux on OCI**, in `oracle-linux-rhel-migration-oci`. It is an instructor maintenance record, not another learner lab. Changes remain local until publication is explicitly authorized.

## Incident and confirmed procedure gaps

The operator reported a kernel installation failure involving unavailable `libattr.so.1`, missing kernel image and initramfs files, removal of RHEL kernels, a success message, and an SSH timeout after reboot. The backup step was skipped. No further inspection or repair of that failed VM is part of this work.

Source inspection independently confirms that upstream commit `82947c960535cfbb632977c70b0fea67f8e0de7a` warns rather than fails when the kernel image is absent, skips its default-kernel verification when that image is missing, and removes source runtime kernels before final verification. That establishes a false-success path. It does not establish why the library became unavailable on this particular VM. No complete incident log or disk inspection is available, and neither is required for this fresh-run procedure.

## Controlled fresh-VM result, October 20, 2026

Candidate `0.2.1-workshop.2`, SHA-256 `477ca307d9c1709fef420d7594a8be7870d82acf26b70c4aa999f3fe19345289`, passed the source readiness review and complete dry run, then failed during the controlled conversion on `ol-migrate-rhel-source-2`. The launcher recorded conversion exit status `1` and final status `1`. No reboot was performed. See the [sanitized failed-run record](fresh-run-20261020.md) for exact evidence paths and limits.

The nearest-lower package transaction added the Oracle kernel as a dependency while downgrading `libattr`. During that transaction, `libattr.so.1` temporarily pointed to the absent `.2600` library while the replacement `.2501` library existed. Kernel scriptlets reported loader failures and initramfs creation failed. The link healed automatically at transaction completion. The later dependency probe therefore passed, and the explicit kernel installation reported the package already installed. The boot-artifact guard then rejected the missing target kernel image. This was a failed migration caught before reboot, not a successful conversion or immediate detection of the first scriptlet error.

The source kernel image and initramfs remain present, and GRUB still selects that RHEL source kernel. These observations do not prove that the partially converted guest can boot. Preserve this fresh run's evidence, instance, disk, and backup. Keep the original failed instance and boot volume untouched.

The intermediate candidate, `0.2.1-workshop.3`, added conservative rejection of the observed `libattr` downgrade before operating-system mutation, protected exact, nearest, update-exception, and fallback replacement phases against kernel changes, and scanned those transactions for scriptlet and loader failures. Its SHA-256 was `360864fcc526dd164b68bda401289a77fae90fb637eb050323fc7654dee74a10`; 99 migration safety and 43 launcher tests passed, together with 1,603 local structural checks. That safety rejection was not a tested migration solution.

## Candidate history and pending correction

Tested candidate `0.2.1-workshop.4`, SHA-256 `99fff3877ac697e38d28fda047171402c2231709114b49ae6e3056a5775e83a8`, used coherent repositories and deferred bootloader ordering. Its 178 local tests, 1,618 local checks, dry run, library checks and initial Oracle kernel-artifact guard passed. The subsequent actual conversion failed; those earlier results do not qualify migration.

Source-3's operator-confirmed Full/Available backup was created at 20:22:11 UTC on October 20, 2026; its manual checkpoint recorder returned `0` at 20:26:21 UTC. No authenticated backup API lookup was performed. The actual run lasted 20:28:43 to 20:34:42 UTC and ended with status `1`. A `grub2-probe` LVM disk-not-found error during kernel installation was missed by the scanner. The final stop was seven shim timestamp differences on vfat; read-only checks found matching sizes/digests and one-second mtime differences, without repairs. That timestamp evidence does not resolve the GRUB error. See the [source-3 failed-run record](source3-run-20261020.md).

Workshop.5 is frozen at SHA256 `bad3bf7487ab2a4b7650e91c56af81ae332e26381a3941b275a33029f05d9df0`, with 150 migration safety tests and 43 launcher tests recorded as passing in the prior session. No live success is claimed. A stronger guard does not fix the underlying GRUB issue. Preserve all instances and disks, both backups and run evidence. No new live attempt is established here.

## Candidate and source traceability

- Upstream source: [pinned script](https://github.com/oracle/migrate-to-ol/blob/82947c960535cfbb632977c70b0fea67f8e0de7a/migrate-to-oracle-linux.sh).
- Original upstream SHA-256: `5435b59e2f0e672111fd1545b2b428cabe473e7c116a353716c2f7de869d0a42`.
- Candidate: [workshop migration script](../migrate-to-oracle-linux/files/migrate-to-oracle-linux.sh), with the upstream license and attribution retained.
- Reviewable changes: [unified safety patch](migration-safety.patch). The checksum-verified upstream copy is retained here for reproducible comparison.
- Execution checkpoint: [launcher](../migrate-to-oracle-linux/files/run-migration.py) and [exact bundle checksums](../migrate-to-oracle-linux/files/SHA256SUMS).
- Boot configuration reference: [Oracle Linux 9 kernel and boot guide](https://docs.oracle.com/en/operating-systems/oracle-linux/9/boot/index.html) and [default-kernel management](https://docs.oracle.com/en/operating-systems/oracle-linux/9/boot/boot-UsinggrubbyToManageKernels_change_the_default_kernel.html).
- Recovery reference: [boot-volume backups](https://docs.oracle.com/en-us/iaas/Content/Block/Concepts/bootvolumebackups.htm) and [backup creation](https://docs.oracle.com/en-us/iaas/Content/Block/Tasks/create-bv-boot-volume-backup.htm).
- The normal checkpoint path uses OCI Console confirmation and the launcher's interactive recorder. It requires no Cloud Shell or local CLI. Optional service-record exports are documented by OCI CLI [backup get](https://docs.oracle.com/en-us/iaas/tools/oci-cli/latest/oci_cli_docs/cmdref/bv/boot-volume-backup/get.html) and [attachment get](https://docs.oracle.com/en-us/iaas/tools/oci-cli/latest/oci_cli_docs/cmdref/compute/boot-volume-attachment/get.html).

The candidate is a workshop modification to community code. It is not an upstream fix or an Oracle-supported migration release. A checksum identifies its bytes; local mocked tests establish the behavior of specific guards. Neither proves a successful OCI conversion or guarantees boot. The actual workshop.2 failure demonstrates that a solved package plan and a later healthy loader cannot establish that earlier kernel scriptlets succeeded.

## Preserve list and new source

Record these values locally before doing any OCI work. Do not put private keys or credentials in this repository.

| Resource | Recorded OCID or result |
| --- | --- |
| Original failed instance, preserve | Pending operator entry |
| Original failed boot volume, preserve | Pending operator entry |
| Existing VCN and public subnet, reuse and preserve | Pending operator entry |
| Fresh rehearsal instance, now partially converted; preserve | `ol-migrate-rhel-source-2`; identity matched to the operator-provided OCID over IMDS; no reboot after conversion failed |
| Fresh RHEL image provenance | Image OCID recorded from live guest metadata in the baseline; guest verified as RHEL 9.8 x86_64 |
| Fresh boot volume, confirmed attached in Console | Operator confirmed for `ol-migrate-rhel-source-2`; OCID retained in the guest checkpoint |
| Full backup for the fresh prepared source | Operator confirmed Full and Available; created October 20, 2026 at 14:28:46 UTC; OCID retained in the guest checkpoint |
| Tested failed candidate SHA-256 | `477ca307d9c1709fef420d7594a8be7870d82acf26b70c4aa999f3fe19345289` (`0.2.1-workshop.2`) |
| Intermediate safety candidate SHA-256 | `360864fcc526dd164b68bda401289a77fae90fb637eb050323fc7654dee74a10` (`0.2.1-workshop.3`); 142 historical local tests and 1,603 historical local checks passed |
| Tested failed workshop.4 candidate | SHA-256 `99fff3877ac697e38d28fda047171402c2231709114b49ae6e3056a5775e83a8`; actual final status `1` |
| Frozen workshop.5 guard revision | SHA256 `bad3bf7487ab2a4b7650e91c56af81ae332e26381a3941b275a33029f05d9df0`; 193 prior-session local tests passed; no live acceptance |
| Separate source-3 and new boot volume, preserve | Partially converted after workshop.4 failed; new backup and checkpoint retained; no reboot |

Any later conversion qualification would need a clean RHEL 9.8 x86_64 source with its own recorded identity and recovery point. The partially converted failed guests are not clean sources. The earlier source-4 proposal is withdrawn; follow [current-status.md](current-status.md) for local next work. Do not replace any preserved instance's volume or rerun a failed guest.

## Historical rehearsal sequence

The learner repeat remains on hold. The sequence below records the process attempted with workshop.4; it does not start another attempt. Workshop.5 has local guard results, but the unresolved GRUB issue still requires review before any later live plan.

1. Follow the [Console restore and launch procedure](next-rehearsal.md). Record source-3's new instance and volume identities and reused network. Preserve both previous instances and disks.
2. Verify recovery through read-only RHEL identity, kernel and boot-file, RHUI, RPM/DNF, library, SSH, baseline checksum, and local/public workload checks. Record the backup restoration result separately; a Running state alone does not pass it.
3. Stage the exact workshop.4 bundle from this checkout. Require checksum and full dry-run success, including the unfiltered 9.8 release gate, both library guards, ordinary replacement plans and deferred bootloader plan. Review every warning. A dry run cannot prove a scriptlet will complete.
4. After readiness passes, take a **Full** backup of source-3's currently attached boot volume. Wait for Available and record a new Console checkpoint using Lab 3, Task 5. The older backup's source-volume OCID and restored source-2 checkpoint do not identify this new disk and instance. No attachment OCID or CLI export is required. Guest metadata identifies the current instance; backup existence, state and volume attachment remain operator-confirmed Console facts.
5. Keep backup restoration and migration qualification as separate results. The successful restore of source-2's checkpoint does not test restoration of the later source-3 backup. Preserve both backups until their respective acceptance points.
6. Reconfirm the new checkpoint immediately before a controlled instructor run of Lab 4. If more than 24 hours have elapsed, create a new checkpoint. Save actual statuses and logs for general replacement, explicit Oracle kernel, deferred shim/GRUB, final boot verification, and package checks.
7. Require the repeated read-only boot verification, Oracle identity and RHCK ownership, nonempty kernel/initramfs, readable initramfs, modules and dependency file, correct GRUB entry and default, healthy RPM database and package dependencies, and healthy Apache before reboot. Copy the evidence locally. If any result fails or is unknown, stop without rebooting or rerunning conversion.
8. Reboot only the fresh migration VM. Reconnect by SSH. Require the running Oracle RHCK to equal the selected default. Verify workload marker, baseline checksum, public HTTP, SELinux, firewall, and no failed services. Save post-reboot evidence.
9. Record migration acceptance below only after all required results pass on this image and exact bundle. If a conversion-stage check fails, preserve the run's evidence and recovery point. Restore results remain specific to the backup tested: source-2's saved checkpoint was recovered to source-3; source-3's new conversion backup exists but has not been restore-tested. Do not debug the original failed VM. Do not describe retained source kernels as a tested fallback.

## Qualification evidence

| Required result | Status | Evidence location |
| --- | --- | --- |
| Local regression tests | See local-results.txt | Local test output only |
| Local workshop structure, images, checksums, syntax | See local-results.txt | Not the official LiveLabs validator |
| Fresh source and RHUI baseline | Passed October 20, 2026 | Fresh guest: `~/ol-migration-evidence/before` and `rhel-before.tar.gz`; source boot files, RPM/DNF, dependencies, local and public HTTP passed |
| Tested workshop.2 final dry run, with unchanged source package/repository/identity hashes | Passed before the failed conversion; not migration qualification | `~/ol-migration-evidence/readiness-v2-final.log`; state `/var/lib/migrate-to-oracle-linux/20261020T155005Z` |
| Available full backup matches fresh VM and disk | Operator confirmed October 20, 2026; manual checkpoint recorder passed on the fresh guest | `/home/opc/ol-migration/console-checkpoint.json`; no backup API verification was performed |
| Lab 3, Task 6 historical readiness | Passed for the controlled workshop.2 attempt; subsequent conversion failed | [Readiness review and package-exception dispositions](readiness-review.md) |
| Separate source-3 recovery and SSH/workload test | Accepted; guest checks passed and operator explicitly confirmed Console restore lineage | [Recovery record](recovery-source-3.md); no authenticated API lineage lookup |
| Source-3 Full backup and conversion checkpoint | Operator confirmed Full/Available backup created 20:22:11 UTC; manual recorder status `0` at 20:26:21 UTC | [Source-3 record](recovery-source-3.md); no authenticated backup API lookup |
| Workshop.4 local checks and source-3 dry run | 178 historical local tests, 1,618 historical checks and dry run passed; later conversion failed | [Source-3 record](recovery-source-3.md) |
| Historical workshop.2 conversion and kernel transaction results | Failed; launcher conversion and final status `1`; nearest-lower transaction had kernel scriptlet/loader failures | [Failed-run record](fresh-run-20261020.md) |
| Historical workshop.2 target boot artifacts | Failed; Oracle kernel image and initramfs missing | Artifact guard and captured failure metadata |
| Workshop.4 final conversion and independent pre-reboot verification | Failed final status `1`; final independent stages not reached | [Failed-run record](source3-run-20261020.md); unresolved GRUB error |
| RPM and DNF consistency after completed workshop.4 conversion | Not reached | Library checks passed; full final consistency not accepted |
| Reboot and SSH reconnect | Not performed | Fresh VM left running without reboot |
| Running Oracle RHCK equals selected default | Not performed | Source-3 runs RHEL `687.45.1`; default is Oracle `687.58.1`; no reboot |
| Post-reboot workload, checksum, public HTTP and security | Not performed | No post-reboot acceptance |
| Instructor acceptance, date, exact image and bundle | Rejected for workshop.2 and workshop.4 | Learner repeat remains on hold; no rerun or reboot |

Do not mark pending OCI results as passed from a source-code review, a mocked test, a Running instance state, or a changed `/etc/os-release`.

Console checkpoint validation is deliberately separate from authenticated API verification. It checks the operator's required fields and confirmations, backup and confirmation timestamps, and equality with the guest's live instance ID. It never synthesizes OCI CLI records or claims it queried the backup service. The 24-hour checkpoint window is a workshop rehearsal rule. A recent Full, Available backup for this prepared source can be reused; lack of CLI access alone does not require another backup.

The historical readiness review produced candidate `0.2.1-workshop.2`. It resolved an explicit Oracle kernel version, ran the kernel and package transaction solvers before source changes, rejected skipped or unresolved replacement operations, and used dependency-checked DNF downgrade or swap for fallback replacement. Its complete live dry run passed with source package, repository, and OS identity hashes unchanged. The subsequent failed conversion disproved the assumption that these checks were sufficient to protect kernel scriptlets in an earlier package transaction.

The workshop.2 dry run reported 335 exact candidates, 183 nearest replacements, and 10 packages without a direct replacement. Its fixed BaseOS base catalogue downgraded `libattr` from 2.6.0 to 2.5.1 while AppStream remained rolling. The run directly demonstrated loader failure during that transition on the fresh source. It does not prove the cause of the original failed VM. The coherent BaseOS latest catalogue includes Oracle payloads matching source `libattr` 2.6.0 and `libacl` 2.4.0. The source-3 workshop.4 dry run selected exact Oracle replacements for both libraries and zero standard nearest-lower replacements. Its actual library and initial kernel transactions passed, but final conversion failed with an unresolved GRUB error and timestamp-verification stop; no Oracle-kernel boot was attempted.

Historical source checks also corrected two assumptions in the first local candidate: this RHEL image owns `dracut-install` through `dracut`, without a separate `dracut-core`, and `dracut --version` returns 1 despite printing a version. The candidate verifies the actual helper owner and uses a successful, nonempty `dracut --list-modules` probe. Workshop.2 passed its historical dry run with those corrections; workshop.4 also passed a non-changing source-3 dry run. Neither result establishes a successful conversion or Oracle-kernel boot.

## Local verification

Run from the repository root with Python 3. Git Bash is used for Bash syntax and mocked regression checks on Windows. Linux runs may use `/bin/bash`.

```text
python -m unittest discover -s oracle-linux-rhel-migration-oci/qa -p "test_*.py" -v
python oracle-linux-rhel-migration-oci/qa/check-workshop.py
```

The local checks preserve the 13 Lab 1 image references and check that image paths resolve, including the filename repaired to avoid LiveLabs image loading problems. No new screenshots or FreeSQL content are needed for this change. Workshop.3 passed 142 historical local regression tests and 1,603 local structural checks. Workshop.4 passed 178 local regression tests and 1,618 local workshop checks. Workshop.5 has 193 prior-session local regression passes. Failed-run evidence is retained locally. No successful OCI platform-image conversion and boot is established; the separate imported-image migration and reboot were independently verified on October 21, 2026.

The skill's official-validator wrapper requires an external `validate_workshop.py` that is not bundled in this repository. The local structural checker is a documented substitute for local QA, not an official validation result.
