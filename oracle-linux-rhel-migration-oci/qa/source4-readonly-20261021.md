# Source-4 read-only LVM and GRUB inspection

Result: **The unconverted RHEL source reproduces the exact GRUB LVM disk lookup error observed during source-3 conversion.** The identifier matches the current root VG and LV. This establishes pre-existing probe behavior on this baseline; it does not establish Oracle-kernel bootability or authorize a migration.

The operator supplied the already available source-4 for read-only inspection. No instance or volume was created. No packages, boot configuration, or LVM metadata were changed. No kernel-install hook was executed. No conversion or reboot was performed. Failed instances were not accessed.

## Verified source state

- RHEL 9.8, running and default kernel `5.14.0-687.45.1.el9_8.x86_64`.
- Red Hat release RPM installed; Oracle Linux release RPM absent. No completed migration state was found.
- BIOS, XFS root on LVM `rootvg/rootlv`, separate XFS `/boot`, and XFS home on `rootvg/homelv`.
- System state `running`, zero failed units.
- Before/after package-inventory hash, protected boot/configuration/hook hashes, and boot ID matched during inspection.
- LVM package verification passed. GRUB verification reported only a mode difference on `/boot/grub2/grub.cfg`; no hook or executable payload differences were reported.

## Probe observations

| Read-only probe | Result |
| --- | --- |
| Root Linux device and GRUB drive | Pass; identify the mounted root LV and its current VG/LV IDs |
| Root filesystem, filesystem UUID, and abstraction | Fail with the exact source-3 `lvmid` disk-not-found error |
| Nested root device-to-filesystem probe | Same failure |
| All six separate `/boot` probes | Pass |
| Home LV filesystem probe | Pass, XFS |
| Current kernel and initramfs paths relative to `/boot` | Pass |

Verbose output discovers the PV and `rootvg`, then opens `homelv` successfully. It does not expose `rootlv` before failing to open its ID. This is not a general failure to find the physical disk or volume group. Matching `lvs` IDs also rules out describing this particular identifier as stale.

The Red Hat `20-grub.install` belongs to `grub2-common 2.06-126.el9_8.noarch`, SHA256 `f85c0fe68120d3eb71a8fe96266c2490453b529e5611b2ef2dcbf6ebb46f579f`. Its nested root filesystem probe is inside a Btrfs-specific conditional. An isolated reproduction of only that read-only condition prints the same error, selects the ordinary branch, and returns zero. The full hook was not run. This explains a mechanism by which the message can coexist with later boot-file creation; it does not establish the exact source-3 invocation or permit global error suppression.

## Metadata-formatting lead

Both LVs are ordinary linear volumes on one PV. Read-only metadata headers show offset 3072 plus text size 1403 is below metadata-area size 1044480, so the captured current metadata does not wrap. The circular-metadata hypothesis does not match these headers.

The committed text has a blank line between the first segment's closing brace and its LV's closing brace:

```text
stripes = ["pv0", 0]
}

}

rootlv {
```

The reviewed [GRUB LVM parser](https://github.com/rhboot/grub2/blob/master/grub-core/disk/lvm.c) finds the next closing brace and advances three characters. In this exact text, that lands on the first LV's closing brace, which its next iteration treats as the end of the LV list. A local string-only cursor reproduction confirms that behavior. Removing one blank line in a local in-memory copy allows that cursor to reach `rootlv`.

This fits the observed home-volume success and root-volume omission. The formatting also matches the current [OSBuild LVM metadata writer](https://github.com/osbuild/osbuild/blob/main/osbuild/util/lvm2.py). The captured metadata includes OSBuild image-construction hints, but the exact builder version has not been established. The [LVM metadata dump implementation](https://github.com/lvmteam/lvm2/blob/main/tools/pvck.c) prints the stored text directly; these blank lines are not a pretty-printing effect.

Follow-up static disassembly of the exact installed Red Hat `grub2-probe` confirms the implicated behavior. Executable SHA256 is `8cb96b2218a4310f1c65eed4f98f290a121ce91511651148089abaf4840b383b`, owned by `grub2-tools-minimal-2.06-126.el9_8.x86_64`; package verification returns zero. The function containing the LVM parsing strings searches for `}` at `0x10b02`, adds three at `0x10b25`, and returns to a loop that tests for `}` at `0xf5b2`. This directly verifies the relevant installed-binary control flow. It is static analysis, not a runtime trace or an execution test against altered metadata.

No live metadata was normalized, restored, or rewritten. A [focused correction plan](metadata-format-correction-plan.md) is prepared for review; it requires a verified backup and separate approval.

## Disposition

Preserve source-4 as an unconverted reference. The current source preflight correctly stops on these unresolved probe failures. Keep the separate library/package-order correction and the LVM/GRUB compatibility work distinct. Do not weaken global GRUB-error handling or classify all such errors as harmless.

The next technical step is the proposed metadata-format test using LVM's own writer, with before/after invariants and the exact failed probes. Changes to LVM metadata are outside this read-only inspection; the prepared plan requires separate approval and a recovery checkpoint. No new VM, migration, or reboot is prescribed here.

Raw records, source hook contents, local cursor reproduction, and checksums remain under ignored `qa/live-evidence/source4-readonly-20261021/`. See [current status](current-status.md).
