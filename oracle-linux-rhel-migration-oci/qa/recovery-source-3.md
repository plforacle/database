# Restored source-3 verification, October 20, 2026

Status: **Workshop.4 conversion failed with final status `1` at 20:34:42 UTC. No reboot performed. Source-3 is partially converted; preserve it and do not rerun conversion.** Recovery, dry run, and the pre-conversion backup/checkpoint passed beforehand. See the [failed-run record](source3-run-20261020.md). Resource identifiers and raw logs remain local.

The operator's Console screenshot shows source-3 Running in availability domain 1 with `VM.Standard.E5.Flex`, 1 OCPU, and 12 GB memory. The agent performed SSH health inspection without changing packages. A Running state and successful SSH do not independently establish which backup supplied this volume.

## Confirmed guest health before the new backup

- RHEL 9.8 source identity, source kernel and readable boot files.
- RPM database and `dnf check` passed.
- Both `libattr` and `libacl` RPM verification and library loading passed.
- Application checksum matched the restored pre-conversion baseline.
- Apache was enabled and active; local and public HTTP returned the workload marker, with public HTTP status 200.
- SELinux was Enforcing, the HTTP firewall rule was present, and no failed units were reported.
- Enabled RHUI IDs included `rhui-rhel-9-for-x86_64-baseos-rhui-rpms` and `rhui-rhel-9-for-x86_64-appstream-rhui-rpms`. No Oracle migration repositories were enabled.
- An explicit live metadata refresh against those BaseOS and AppStream repositories exited `0`. Package inventory, operating-system identity, and source repository-file hashes matched before and after the refresh. This changed the metadata cache without changing source packages or repository configuration.

## Reviewed startup warnings

Cloud-init 24.4 reported status `done` and extended status `degraded done`, with an empty errors list. Recoverable warnings recorded early metadata network-unreachable messages and an initramfs fallback. The later review confirmed that its queried instance ID and current instance-cache directory identify the current guest. Current-boot network, configuration, and final stages finished; the Oracle datasource result contained no stage errors. System time was synchronized.

These are reviewed early-startup warnings, not an unexplained unfinished cloud-init run. The degraded status remains part of the evidence and was not repaired or suppressed. This review does not independently confirm the OCI Console backup or volume lineage.

Evidence: ignored local `qa/live-evidence/source-3-20261020/cloud-init-and-rhui-review.txt`, together with the earlier recovery inspection and health records in that directory.

## Corrected candidate dry run

Workshop.4, SHA-256 `99fff3877ac697e38d28fda047171402c2231709114b49ae6e3056a5775e83a8`, completed the fixed dry-run-only assessment on October 20, 2026 from 20:06:41 to 20:07:37 UTC. Script exit status was `0`; the wrapper reported `passed: true`, unchanged source state, and successful health checks. Package inventory, OS identity, source repository files, boot files, running/default kernel and boot ID matched before and after. The workload marker and baseline checksum still passed.

Independent plan review found:

- Unfiltered Oracle release selection was 9.8; temporary BaseOS latest and AppStream definitions used Oracle HTTPS URLs and enabled GPG verification.
- `libattr` 2.6.0 and `libacl` 2.4.0 were exact Oracle reinstalls. The ordinary nearest-lower list had 0 entries.
- The standard exact solver planned 386 reinstalls plus two SELinux-policy dependency upgrades; the higher-replacement solver planned 123 upgrades. Neither plan contained runtime kernels or deferred shim/GRUB work.
- The isolated kernel plan proposed installing four matching Oracle RHCK packages at `5.14.0-687.58.1.el9_8.x86_64` and upgrading grubby, with no source-kernel removal. It was not executed.
- All seven installed shim/GRUB replacements were deferred and available. Their source-state prediction included the planned Oracle kernel and a deliberate shim downgrade; it did not execute the later bootloader transaction.
- The HTTP branding swap removed only `redhat-logos-httpd`; update exceptions required no changes. Ten unavailable entries retain the [historical package dispositions](readiness-review.md#disposition-of-the-ten-unavailable-entries). Their post-conversion identity, repository, package-consistency, service and boot acceptance conditions remain pending for an actual conversion.

During this dry run, nonempty solver plans were deliberately declined with `Operation aborted.`; no package transaction or scriptlet ran. The later actual conversion failed as recorded below. The source-state predictions did not qualify transaction or boot acceptance.

Evidence: guest state `/var/lib/migrate-to-oracle-linux/20261020T200642Z`, log `/var/log/migrate-to-oracle-linux/20261020T200642Z.log`, and wrapper results under `/home/opc/ol-migration-evidence/source3-dryrun-20261020T2005Z`. The ignored local archive has SHA-256 `e0197b48c0c888044d09189e41466ea19c821b02dee101bae90ebf027b3c921e`.

## Recovery and conversion checkpoint

The operator explicitly confirmed both Console relationships: new volume ending `pm6ub3a` was restored from the saved pre-conversion backup ending `54w6paa`, and that new volume is attached to source-3's instance ending `ko3md7q`. Combined with the observed guest identity, recovered baseline workload and health checks, this accepts the restored-source recovery test. This is operator-confirmed service lineage, not an authenticated API verification.

The operator supplied the new source-3 backup OCID and Console screenshot showing its source volume, **Full** type, **Available** state, and creation time `2026-10-20T20:22:11Z`. The recorder-only launcher exited `0` at 20:26:21 UTC and saved `/home/opc/ol-migration-workshop-4-20261020T2005Z/console-checkpoint-source-3.json`. The manual record identifies source-3, its current volume and new backup, and was validated against live guest identity. No authenticated backup-service lookup was performed. The older source-2 checkpoint and backup remain preserved separately.

Backup preparation completed at `2026-10-20T20:16:53.951586Z`: the source still matched the accepted dry run, writes were synchronized, Apache was stopped, and writes were synchronized again. After backup confirmation, Apache was restarted; enabled/active state, local workload marker and application checksum passed. The readable source boot baseline was saved before conversion: BIOS firmware, VirtIO SCSI root storage with LVM and XFS, and a VirtIO NIC. Preserved source transport and boot arguments provide the comparison reference. Evidence includes ignored `qa/live-evidence/source-3-20261020/backup-preparation.txt` and the current guest checkpoint and boot-baseline records.

## Controlled conversion failed

Unit `ol-source3-conversion-20261020t2028` ran the frozen workshop.4 candidate from 20:28:43 to its failure at 20:34:42 UTC on October 20, 2026. Launcher evidence is `/home/opc/ol-migration-evidence/source3-conversion/20261020T202843Z-eabb5059/migration.log`. Exact/higher batches and both library checks passed. The Oracle RHCK image, initramfs, modules, entry and default passed the initial guard before deferred shim/GRUB work. An earlier `grub2-probe` LVM disk-not-found error was missed by the transaction scanner and remains unresolved.

The final stop was deferred bootloader RPM verification: seven shim-file timestamp differences on vfat. Read-only inspection found exact size/digest matches and one-second timestamp differences; no repair was applied. That evidence does not resolve the earlier GRUB error. Final conversion status was `1`; final independent boot and package-consistency stages were not reached. The [failed-run record](source3-run-20261020.md) contains the evidence and limits.

The running kernel remains RHEL `687.45.1`; the default is Oracle `687.58.1`. Apache and the baseline checksum passed after failure, but bootability is unproven. Preserve source-3, both previous failed instances and their disks, both backups, and all evidence. No rerun or reboot. Workshop.5 local guard tests are recorded as passing, without live acceptance or a resolved GRUB cause. See [current status](current-status.md) for the frozen checksum and the separately verified imported-image result.
