# Source-4 LVM metadata-format correction

Status: **Approved, executed once, and accepted on October 22, 2026.** The operator approved the exact metadata-only command conditional on a source-4 backup, supplied a Full, Available backup created at 13:43:05 UTC, and explicitly confirmed the source boot volume's current attachment to source-4. The checkpoint is based on operator-supplied OCI Console evidence, not authenticated API verification. Execution and independent comparison completed at 13:53:42 UTC with `METADATA_FORMAT_CORRECTION_PASS`. See the [acceptance record](source4-correction-20261022.md).

The proposal and safeguards below are retained as the record of the reviewed procedure. This correction performed no reboot or operating-system conversion. The separately authorized remaining labs may resume normal Lab 2 work, including its pending RHEL reboot after a fresh boot gate. Acceptance of this correction does not qualify an Oracle Linux migration.

Before this correction, the normal Lab 2 RHEL update reproduced the same root-volume probe error during installation of RHEL kernel `5.14.0-687.59.1.el9_8`. DNF returned zero, the new boot artifacts passed inspection, and the previous RHEL kernel remained available. At the correction checkpoint, source-4 still ran its original `687.45.1` kernel and selected `687.59.1` for the next boot. Those selections and all 327 protected-file hashes were unchanged by the metadata transaction.

## Evidence and proposed correction

Source-4 is unconverted RHEL 9.8. Its installed Red Hat `grub2-probe` recognizes `homelv` but skips `rootlv` because the captured metadata has an extra blank line between closing braces. Static disassembly of the exact installed binary confirms the character-search, three-byte cursor advance, and subsequent closing-brace test implicated by the local reproduction. RPM verification of the executable package passes. See [source-4 findings](source4-readonly-20261021.md).

Installed LVM is `2.03.33-4.el9_8.1`. Its help explicitly supports `vgck --updatemetadata VG` and the common `--devices PV` option. The matching [upstream LVM version's implementation](https://gitlab.com/lvmteam/lvm2/-/raw/v2_03_33/tools/vgck.c) writes and commits the VG through LVM's own metadata exporter. Its [text exporter](https://gitlab.com/lvmteam/lvm2/-/raw/v2_03_33/lib/format_text/export.c) does not insert the extra blank line between segment and LV closing braces. The proposal required a live test of the installed writer's output. That test has now passed on source-4.

The proposed and executed scope was one standard LVM metadata transaction on the existing source-4:

```bash
sudo vgck --updatemetadata --devices /dev/sda4 rootvg
```

This requests serialization and commit of the existing volume-group description. It does not request activation, UUID generation, resizing, filesystem conversion, package changes, kernel installation, bootloader regeneration, migration, or reboot. Metadata sequence number, checksums, formatting, description, and device hints may change. PV/VG/LV IDs, sizes, extent mappings, tags, filesystem UUIDs, and active device-mapper tables must remain unchanged.

## Required checkpoint and final read-only checks

Before any write, record a current **Full, Available boot-volume backup of source-4's attached boot volume** from OCI Console. Record the backup OCID, source boot-volume OCID, creation time, and operator confirmation of attachment to source-4. Use a backup less than 24 hours old that covers this prepared source. No Cloud Shell or OCI CLI is required. An LVM text backup alone is not the full recovery point.

Immediately before execution, match source-4's live instance identity to the saved identity record. Recheck RHEL identity, running/default kernel, package inventory, mounts, and the existing failure. Confirm unrestricted LVM reports still show exactly one PV for `rootvg`, that it is `/dev/sda4`, and that restricted reports show the identical complete VG. `--devices` hides omitted PVs, so a changed or incomplete PV list is a stop condition.

Normal read-only `vgck --devices /dev/sda4 rootvg` returned zero without warnings before the correction. The pre-correction headers were valid; the VG had one PV, two ordinary linear LVs, no tags, and metadata sequence 3. The approved procedure required these checks to be repeated and metadata, headers, complete LV records, device-mapper tables, filesystem UUIDs, and protected-file hashes to be captured locally before the correction. Missing or duplicate PVs, damaged or inconsistent metadata, unknown or historical LVs, pending LVM operations, or changed topology were stop conditions. No force or locking override was authorized.

## Execution and acceptance

The following safeguards were applied to the single execution. They are not instructions to repeat the completed command.

After approval and checkpoint confirmation, execute only the proposed metadata command once and capture its complete output and status. Do not retry a failed command speculatively.

Read the committed metadata again and compare the recorded invariants. Rerun root device, drive, filesystem, filesystem UUID, abstraction, and nested device-to-filesystem probes, together with the six `/boot` probes and the home-volume probe. Require zero statuses without GRUB errors and the correct mounted filesystem UUIDs. Recheck normal `vgck`, active mappings, mounts, existing kernel/initramfs hashes, running/default kernel, package inventory, and accessible application data.

If invariants change unexpectedly, any probe remains unresolved, or the command fails, preserve the evidence and stop. Do not run additional metadata repair, `vgcfgrestore`, bootloader installation, migration, or reboot. Any recovery from the boot-volume backup is a separate action.

If the correction passes, record only acceptance of this source metadata-format correction. It does not qualify the Oracle Linux conversion. A migration plan and its prepared-source recovery checkpoint remain separate work.

## Approval record and scope

The operator explicitly approved this metadata-only source-4 correction after its backup. The supplied Console screenshot showed Full and Available, and the operator separately confirmed that the identified source boot volume remained attached to source-4. Private checkpoint records retain the resource identifiers, approval, and attachment confirmation under ignored `qa/live-evidence/source4-correction-20261022/`.

That approval did not extend to failed instances or the successful imported-image reference. No new instance, volume replacement, package transaction, conversion, or reboot was part of this correction test. Execution of the remaining workshop labs is separately authorized and remains subject to each lab's checks. No commit, push, or publication is authorized.
