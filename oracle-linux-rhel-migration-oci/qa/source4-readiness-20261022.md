# Source-4 readiness for the controlled workshop.5 rehearsal

Date: October 22, 2026. Decision recorded after the source preflight at 14:36:55 UTC and manual checkpoint recorder at 14:37:44 UTC.

**Accepted for one controlled clean-source rehearsal. This is a readiness decision, not a conversion or Oracle-kernel boot pass.** The scope is the existing OCI-provided RHEL image instance, source-4, and its static Apache workshop page. It does not authorize conversion reruns or reboots on failed guests, removal of source kernels, or cleanup of preserved resources.

## Source and candidate

- Source: RHEL 9.8 x86_64, with a 551-entry installed RPM inventory. Running and default kernel are both RHEL `5.14.0-687.59.1.el9_8.x86_64` after the successful source reboot.
- The approved native LVM metadata correction passed before that reboot. The current source retains one PV and two LVs. The correction acceptance is recorded in [source4-correction-20261022.md](source4-correction-20261022.md).
- Candidate: `0.2.1-workshop.5`, SHA256 `bad3bf7487ab2a4b7650e91c56af81ae332e26381a3941b275a33029f05d9df0`.
- Launcher SHA256: `ff545a3a92c10031c53decc1245bcc7489cf55e8a7176cd1eb5bda39921648e1`. Both deployed bundle checksums passed immediately before the current checkpoint.
- Selected target: Oracle Linux 9.8, Oracle RHCK `5.14.0-687.58.1.el9_8.x86_64`. The selected kernel does not share the installed source kernels' NEVRA.

Guest identity was checked against IMDS v2. Exact image and instance identifiers are retained privately. The OCI Console display label of the source image remains unrecorded. This record does not substitute an imported Red Hat image for the OCI-image assignment.

## Source health and boot reference

The fresh preflight returned `SOURCE4_LAB4_SOURCE_PREFLIGHT_PASS`. RPM database verification, `dnf check`, installed `libattr`/`libacl` verification, both library loader checks, and every captured direct and nested root and `/boot` GRUB probe passed. GRUB filesystem UUIDs matched the mounted filesystems. Captured kernel-install hooks were readable and RPM-owned.

The complete Lab 4 `boot-before` capture records BIOS firmware, VirtIO SCSI storage, an LVM root with XFS, and a VirtIO NIC. Observed required modules are `dm_mod`, `sd_mod`, `virtio_net`, `virtio_pci`, `virtio_scsi`, and `xfs`. The observed root path does not use iSCSI. Inherited `netroot=iscsi` and `rd.iscsi.firmware=1` arguments remain recorded in the working source entry and must be preserved when comparing the target entry. Driver requirements follow the observed storage chain.

Apache was active, the local page returned `MIGRATION_WORKLOAD_OK`, and its checksum matched the final RHEL baseline. SELinux was Enforcing, the system state was running, and there were zero failed units. Earlier Lab 2 checks also verified public HTTP. The static application checksum is `11db440c06cd95a31aa4dfe59f97d08389c9235e949f5c0f1f385deab4b04365`.

## Dry run and transaction review

The unchanged candidate's dry run started at 13:58:59 UTC and completed at 14:00:16 UTC with status `0`. Exact reinstalls, nearest-higher and nearest-lower replacements, update exceptions, the HTTP branding swap, explicit Oracle RHCK installation, and deferred bootloader solver checks passed. Solved `--assumeno` plans deliberately reported `Operation aborted.`; no package transaction was applied by the dry run.

The source-state kernel plan installs the matching Oracle `kernel`, `kernel-core`, `kernel-modules`, and `kernel-modules-core` packages and upgrades `grubby`. It proposes no source-kernel removal. Both existing RHEL runtime sets, `687.45.1` and `687.59.1`, are protected during conversion. Their retention does not establish that converted user space can boot with them.

`libattr` remains at `2.6.0-1.el9_8` and `libacl` at `2.4.0-1.el9_8` through exact Oracle package reinstalls. Both blocked library-downgrade lists are empty. The reviewed nearest-lower plan instead contains these seven packages:

| Packages | Source to selected Oracle target |
| --- | --- |
| `glibc`, `glibc-common`, `glibc-gconv-extra`, `glibc-minimal-langpack` | `2.34-283.el9_8` to the coherent `2.34-275.0.1.el9_8` set |
| `kernel-tools`, `kernel-tools-libs`, `python3-perf` | `5.14.0-687.59.1.el9_8` to `5.14.0-687.58.1.el9_8` |

These are solver-approved transitions requiring actual transaction, loader, and dependency checks. They are not claimed safe merely because the solver resolved them.

The deferred group replaces the six installed GRUB packages with Oracle `2.06-126.0.2.el9_8` and changes `shim-x64` from `16.1-7.el9` to `16.1-1.0.2.el9`. The source is BIOS, but installed EFI payloads still receive the candidate's integrity checks. Its source-state solver also predicts the selected Oracle kernel as a dependency. During actual conversion, the explicit Oracle kernel must already be installed and verified before deferred bootloader work; that later transaction must not add or remove runtime kernels.

Required candidate order is general replacements with runtime kernels and the deferred bootloader group excluded, runtime dependency checks, explicit Oracle RHCK installation and initial artifact verification, deferred bootloader replacement, and complete final boot verification. Conversion must stop on a transaction, loader, scriptlet, GRUB, or artifact failure.

## Disposition of the ten unavailable entries

These decisions apply to this controlled rehearsal and static Apache workload. They are conditional on post-conversion package consistency and applicable service checks.

| Current unavailable entries | Disposition and required result |
| --- | --- |
| `redhat-release`, `redhat-release-eula` | Remove source release packages through the candidate. Require installed Oracle release packages and Oracle Linux 9.8 identity. |
| `redhat-logos-httpd` | Use the solved atomic swap to `oracle-logos-httpd`. Require Apache health and an unchanged application checksum. |
| `insights-core`, `insights-core-selinux` | Retain as declared Red Hat exceptions for this rehearsal. Require clean dependencies and no unexplained failed units. This does not establish Red Hat Insights functionality or support on Oracle Linux. |
| `linux-firmware`, `linux-firmware-whence` | Retain the current source firmware as declared exceptions. Require the independent driver inspection and actual Oracle-kernel boot, SSH and network checks. |
| `oci-rhui-client-rhel9-x86_64` | Retain as repository-tooling evidence. Require source RHUI repositories disabled and usable Oracle repositories afterward. Do not erase RHUI configuration manually to bypass an error. |
| `oracle-cloud-agent` | Retain the installed Oracle-vendor agent, currently `1.63.0-5.el9`. Require package consistency and review its service and plugins in the later management lab. This does not qualify OS Management Hub registration. |
| `unified-monitoring-agent` | Retain as a third-party agent exception. Require package consistency and no unexplained service failure. Monitoring functionality is outside this static Apache rehearsal. |

## Prepared-source recovery checkpoint

The operator supplied the backup named `ol-migrate-source-4-ready-for-migration`, created at 14:16:08 UTC on October 22. The screenshot shows Full, Available, and source-4's boot volume. Its supplied source-volume identifier matches the same volume whose current attachment to source-4 the operator previously confirmed. This backup follows the accepted correction, successful RHEL reboot, final baseline capture, and dry run. The earlier backup remains the separate pre-correction recovery point.

The verified launcher recorder completed with status `0` at 14:37:44 UTC. It saved a manual Console attestation tied to the current guest identity. Backup state and attachment are operator-supplied Console facts, not authenticated OCI API results. No restore of this backup has been tested.

Apache quiescence during this backup was not attested. The page is static and its baseline checksum remained unchanged at the current preflight. This rehearsal accepts the backup as a crash-consistent checkpoint: OCI documents boot-volume backups as crash-consistent snapshots that can be created while an instance is running. This does not establish application consistency for a database or other transactional workload. See [OCI boot-volume backup semantics](https://docs.oracle.com/en-us/iaas/Content/Block/Concepts/bootvolumebackups.htm).

## Required acceptance after readiness

Run the verified launcher once with this checkpoint and preserve its actual statuses. A failed or unknown result stops the rehearsal without reboot, rerun, kernel removal, or ad hoc package repairs. A lost SSH session requires checking the existing run rather than starting another.

A conversion pass still requires every launcher stage to return zero, independent Oracle release, repository, RPM, dependency and library checks, the selected Oracle RHCK's boot files and ownership, source-equivalent root arguments and actual storage/network dependencies, healthy Apache, and exported evidence copied locally. Only then may the authorized Oracle-kernel reboot proceed.

Final qualification additionally requires SSH reconnect, an Oracle-owned RHCK both running and default, clean package consistency, zero failed units, SELinux Enforcing, the HTTP firewall rule, unchanged workload checksum, and local and public HTTP checks. Neither this readiness decision nor a final success message substitutes for that boot test.

Private evidence is under ignored `qa/live-evidence/source4-labs-20261022/` and `qa/live-evidence/source4-lab4-20261022/`. The current dry-run reports, complete source boot capture, prepared-source backup record, and successful recorder output are retained there. Preserve all failed instances, their disks and backups, the successful imported-image reference, and the shared network. No commit, push, publication, or resource cleanup is authorized.
