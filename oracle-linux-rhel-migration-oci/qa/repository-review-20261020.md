# Updated repository and bootloader review

Date: October 20, 2026. Scope: the next controlled RHEL 9.8 x86_64 rehearsal for WMS 12229. **No successful conversion or reboot is established by this review.**

## Repository correction

The failed candidate called its temporary repository `ol9_baseos_latest` but pointed it at `OL9/8/baseos/base`, the original 9.8 BaseOS snapshot. AppStream pointed at the rolling repository. Its saved plan therefore downgraded newer source libraries even though matching Oracle builds exist in the updated BaseOS repository.

The proposed correction uses [Oracle BaseOS latest](https://yum.oracle.com/repo/OracleLinux/OL9/baseos/latest/x86_64/index.html) with rolling AppStream. It keeps the workshop target at 9.8 by checking the highest available Oracle release package before source changes. That check must inspect the unfiltered catalogue and reject a newer minor release. Normal package queries also exclude later minor-release builds. The library-downgrade guards remain mandatory.

The official current BaseOS metadata was downloaded over HTTPS and its compressed primary XML checksum was verified against `repomd.xml`:

| Record | SHA-256 |
| --- | --- |
| `repomd.xml` | `0eb479787d46c432a251d9a2d84f163b91cb292d79197006dadb2d0c66c90b28` |
| `primary.xml.gz` | `8a36fd0d2c4f8ef78734715e2aeb21c567be37b1fb47ca91082c477a6c95189e` |

The highest published Oracle release version in this captured catalogue is 9.8. These exact source-library versions are present with vendor `Oracle America`:

| Source library | Oracle build in updated BaseOS |
| --- | --- |
| `libattr-2.6.0-1.el9_8.x86_64` | Same NEVRA |
| `libacl-2.4.0-1.el9_8.x86_64` | Same NEVRA |

This supports replacing those packages with Oracle payloads without the observed 2.6-to-2.5 library transition. It does not establish transaction success. The captured metadata is retained locally under ignored `qa/live-evidence/catalogue-20261020`. Package signatures were not checked by this offline metadata inspection; actual DNF transactions must retain Oracle GPG verification.

## Bootloader ordering

Exact Oracle RPM headers explain the unexpected kernel addition: [shim](https://yum.oracle.com/repo/OracleLinux/OL9/baseos/latest/x86_64/getPackage/shim-x64-16.1-1.0.2.el9.x86_64.rpm) requires `oracle(kernel-sig-key) >= 202204`, which the [Oracle kernel](https://yum.oracle.com/repo/OracleLinux/OL9/baseos/latest/x86_64/getPackage/kernel-5.14.0-687.5.3.el9_8.x86_64.rpm) provides. Shim also requires the Oracle GRUB signing capability. GRUB components have dependencies on matching component versions.

The next candidate therefore defers installed shim and GRUB packages as one planned group until the explicit Oracle kernel stage passes. Earlier package transactions must exclude both runtime kernels and the deferred bootloader group. A source-state prediction may include the explicitly planned Oracle kernel; the actual later bootloader transaction must reject any runtime-kernel change. Final Oracle ownership and boot checks remain necessary after bootloader work.

The failed run's kernel-tools-libs scriptlets invoked `ldconfig` before old libattr cleanup, and kernel post-transaction work ran afterward. This supports the transient-link explanation. The exact link-changing operation was not traced, so this review does not attribute the original incident to a proven writer.

## Live result and next evidence required

Workshop.4 passed its local checks and the source-3 dry run, then failed the controlled conversion with status `1`. The target Oracle kernel and initramfs were created. The final stop involved timestamp-only differences on vfat EFI files, while an earlier GRUB disk-probe error was missed by the scanner. See the [source-3 failure record](source3-run-20261020.md) for the actual results and evidence limits. The repository and library correction did not establish a complete migration or boot.

The local workshop.5 candidate adds detection of the observed GRUB error and a strictly bounded FAT timestamp allowance. Those guards do not resolve the underlying probe error. The missing evidence is the source-state direct/nested GRUB probes, matching VG/LV identity, and actual kernel-install hooks. No new VM or live inspection is prescribed by this record. Follow the [current status and next work](current-status.md); the source-4 proposal is withdrawn. Preserve all failed instances, their disks, both backups and evidence. Do not rerun or repair a partially converted guest from this record.
