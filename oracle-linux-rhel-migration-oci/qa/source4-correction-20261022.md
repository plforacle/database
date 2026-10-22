# Source-4 metadata-format correction acceptance

Result: **`METADATA_FORMAT_CORRECTION_PASS`**, completed October 22, 2026, at 13:53:42 UTC. One native LVM metadata transaction resolved the pre-existing root GRUB lookup failures on the prepared RHEL source. This correction performed no reboot or operating-system conversion. Oracle Linux migration remains unqualified.

## Authorization and recovery checkpoint

The operator explicitly approved the [focused metadata-only correction](metadata-format-correction-plan.md), conditional on a backup. The operator then supplied an OCI Console screenshot showing a **Full, Available** boot-volume backup created October 22, 2026, at **13:43:05 UTC**, with source `ol-migrate-rhel-source-4 (Boot volume)`, and supplied both backup and source boot-volume identifiers. The operator separately confirmed that this boot volume was currently attached to source-4.

This is operator-supplied Console evidence and attachment confirmation, not authenticated OCI API verification. The checkpoint was less than 24 hours old at execution. Full identifiers and private records remain under ignored `qa/live-evidence/source4-correction-20261022/`; they are omitted here.

## Preflight and one-time execution

Read-only checks matched source-4's live instance identity and RHEL 9.8 x86_64 state to the saved record. Unrestricted and device-restricted LVM reports agreed on the complete VG: one PV at `/dev/sda4`, two known linear LVs, no tags, no historical LVs, and metadata sequence 3. Metadata and headers were healthy, and ordinary `vgck` returned zero without warnings. The existing root GRUB failures were reproduced before the write.

The original RHEL kernel `5.14.0-687.45.1.el9_8.x86_64` was running, and the newly prepared RHEL kernel `5.14.0-687.59.1.el9_8.x86_64` was the default. Apache was active and enabled, SELinux was Enforcing, and the workshop page returned its expected content.

After a fresh snapshot comparison and an exclusive attempt marker, the approved command ran exactly once:

```bash
sudo vgck --updatemetadata --devices /dev/sda4 rootvg
```

It returned zero with empty stdout and stderr. No force, locking override, restore operation, bootloader regeneration, or speculative retry was used.

## Acceptance checks

Independent review reran the saved preflight and comparison guards against the captured before/after records. All checks passed:

- Metadata sequence changed exactly from 3 to 4. The committed text now has consecutive segment and LV closing braces. Device hints and descriptive fields were rewritten by the native LVM exporter as expected.
- PV, VG, and LV IDs; sizes; tags; extent geometry and mappings; active device-mapper tables; mounted filesystem UUIDs and sources; and immutable PV/header/metadata-area geometry remained unchanged. The metadata precommit slot was clear after commit.
- All **327 protected-file SHA256 hashes** matched, including the kernel images, initramfs files, boot configuration, probe executable, kernel-install hook, and workshop page.
- Package inventory, RPM and dependency checks, library integrity and loading, running/default kernel selections, and boot ID remained unchanged. Apache and the expected workload content remained available.
- All **18 direct root, `/boot`, and home GRUB probes**, plus the **nested root device-to-filesystem probe**, returned zero without errors. Reported filesystem types and UUIDs matched the mounted filesystems. The root abstraction was `lvm`, and its partition map was `gpt`.

The same probe executable and protected boot files were used before and after the metadata transaction. Together with the unchanged storage mappings and the corrected brace formatting, the result supports the identified metadata/GRUB compatibility cause for these source-4 probe failures. It does not establish the cause of every earlier failed migration or prove Oracle kernel bootability.

## Disposition

Accept this source-4 metadata-format correction. Resume the separately authorized Lab 2 procedure with a fresh source boot gate before its pending RHEL reboot, then recapture the final prepared-source baseline and continue the ordered readiness checks. The correction checkpoint and acceptance do not waive Lab 3 or Lab 4 migration requirements.

No failed instance or successful imported-image reference was accessed. No instance or volume was replaced, and no reboot or operating-system conversion occurred within this correction step. No commit, push, publication, or resource cleanup was performed.
