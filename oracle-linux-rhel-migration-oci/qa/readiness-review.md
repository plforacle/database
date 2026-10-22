# Lab 3 readiness review for the fresh rehearsal VM

Date: October 20, 2026. Scope: `ol-migrate-rhel-source-2`, RHEL 9.8 x86_64, with the operator-confirmed Full, Available backup created at `2026-10-20T14:28:46Z`.

Historical Lab 3, Task 6 result: **passed for the controlled workshop.2 attempt; that conversion subsequently failed**. The migration path remains unqualified. Do not repeat Lab 4 or rerun conversion on the partially converted source.

This review records checks performed before conversion. Candidate `0.2.1-workshop.2` was then run at `2026-10-20T15:56:18Z` and stopped with conversion and final status `1`. No reboot was performed. The original failed instance and boot volume were not accessed. The [failed-run record](fresh-run-20261020.md) supersedes any inference that the readiness pass established a working migration.

## Source and recovery checks before conversion

- Guest identity matched the fresh instance supplied by the operator.
- Candidate bundle checksums passed.
- The saved manual Console checkpoint passed its field, timestamp, and guest-identity checks. Full type, Available state, and source-volume attachment remain operator-confirmed facts, not an authenticated API lookup.
- Source RPM database, `dnf check`, `libattr` loading, and installed `libattr` and `dracut` file verification passed.
- Apache was active, its marker responded, and the application checksum matched the saved RHEL baseline.
- Source runtime kernel: RHEL `5.14.0-687.45.1.el9_8.x86_64`.
- Oracle kernel available in the selected target repositories: `5.14.0-687.5.3.el9_8.x86_64`.

## Two issues found before conversion

A name-only `dnf install kernel grubby` considered the newer RHEL kernel already installed and proposed only a grubby upgrade. It did not install an Oracle kernel. The explicit Oracle kernel version produced a plan to install `kernel`, `kernel-core`, `kernel-modules`, and `kernel-modules-core` together, without removing a source kernel.

The old fallback also marked lower-version replacements for `rpm -e --nodeps` before installing their replacements. Combined with `--skip-broken` in the earlier downgrade pass, that could leave a required library or tool temporarily absent. This is a confirmed code-path risk; it does not establish what happened on the original failed VM.

Workshop.2 resolved its exact target kernel and checked the proposed transaction before release or package changes. It rejected unresolved replacement transactions and kept same-name downgrades inside DNF, without a preliminary RPM erase. Branding replacement used an atomic DNF swap. The live failure showed that these changes were insufficient: DNF added the target kernel to the nearest-lower transaction, where its scriptlets ran during the library downgrade.

## Non-changing DNF solver evidence

The commands used `LC_ALL=C`, the candidate's isolated Oracle repository definitions, `--assumeno`, and `--setopt=assumeyes=False`. No transaction was applied. DNF returned `1` with `Dependencies resolved.`, a transaction summary, and `Operation aborted.` because the answer was deliberately no. That combination was reviewed as a solved but declined transaction, not a completed installation. Package-inventory hashes were unchanged after the checks.

| Proposed operation | Observed result |
| --- | --- |
| Name-only kernel installation | Blocker: only grubby would change; newer installed RHEL kernel satisfied the name |
| Explicit Oracle kernel installation | Install four matching Oracle kernel packages; upgrade grubby; no source-kernel removal |
| 102 nearest higher replacements | Dependencies resolved; install 6 and upgrade 102 |
| 81 nearest lower replacements | Dependencies resolved; install 10, upgrade 8, downgrade 81; no skipped packages |
| 333 standard exact reinstalls | Dependencies resolved; Oracle reinstalls and two dependency upgrades planned |
| Apache branding swap | Install `oracle-logos-httpd`; remove only `redhat-logos-httpd`; retain Apache |

Fresh-guest evidence is under `~/ol-migration-evidence/readiness-solver/`: `kernel.log`, `kernel-5.14.0-687.5.3.el9_8.x86_64.log`, `nearest-higher.log`, `nearest-lower.log`, `exact-reinstall.log`, and `branding-swap.log`. These are source-state transaction predictions. The later controlled conversion exercised scriptlets and failed; boot was not attempted.

## Disposition of the ten unavailable entries

These historical decisions applied to this controlled rehearsal and its Apache workload. The conversion failed before their post-conversion conditions could be accepted. They do not authorize another run, grant blanket acceptance for production systems, or qualify the later management labs.

| Entries | Disposition and acceptance condition |
| --- | --- |
| `redhat-release`, `redhat-release-eula` | Expected source-release removal. Require installed Oracle release packages and Oracle Linux 9.8 identity afterward. |
| `redhat-logos-httpd` | Replace through the solved atomic swap with `oracle-logos-httpd`. Require unchanged application checksum and working Apache afterward. |
| `insights-core`, `insights-core-selinux` | Retain and report as source-vendor exceptions for this rehearsal. Their policy requirement is at least 38.1.66; the selected Oracle policy is 38.1.75. Package consistency must pass after conversion. No claim is made that Red Hat Insights works or is supported on Oracle Linux. |
| `linux-firmware`, `linux-firmware-whence` | Retain the source firmware as a declared rehearsal exception. The explicit kernel solver required no firmware removal or replacement. This does not establish runtime driver compatibility; actual reboot, SSH, and networking must pass before acceptance. |
| `oci-rhui-client-rhel9-x86_64` | Retain the package as a declared repository-tooling exception. Its recorded requirements are shell, curl, systemd, and yum, without a Red Hat release-package dependency. Require source RHUI repositories disabled and Oracle repositories usable after conversion. Do not remove RHUI configuration manually to bypass a problem. |
| `oracle-cloud-agent` | Retain the installed Oracle-vendor package. Require package consistency and review its service and plugins during later management validation; this readiness review does not qualify OS Management Hub. |
| `unified-monitoring-agent` | Retain as third-party agent evidence. Require package consistency and no unexplained service failures. Monitoring behavior is outside this Apache migration rehearsal. |

## Actual conversion result

Candidate `0.2.1-workshop.2`, SHA-256 `477ca307d9c1709fef420d7594a8be7870d82acf26b70c4aa999f3fe19345289`, passed 85 kernel/control-flow regression tests before the live attempt. The unchanged launcher had passed 43 tests. These historical local results did not predict the scriptlet failure during the actual package transaction.

Its final complete dry run returned `0`. Kernel, exact-reinstall, nearest-higher, nearest-lower, and HTTP-branding solver checks passed; the update-exception solver required no changes. The source package inventory, operating-system identity, and source repository hashes matched before and after the run. Source kernel image, readable initramfs, Apache, and workload checks passed afterward. Evidence: `~/ol-migration-evidence/readiness-v2-final.log`, `/var/lib/migrate-to-oracle-linux/20261020T155005Z`, and `/var/log/migrate-to-oracle-linux/20261020T155005Z.log`.

The nearest-lower transaction installed Oracle kernel `5.14.0-687.5.3.el9_8.x86_64` as a dependency while downgrading `libattr` from 2.6.0 to 2.5.1. The library link temporarily referred to the removed `.2600` target, and tools used by kernel scriptlets could not load `libattr.so.1`. Initramfs creation failed. At transaction completion the library link repaired automatically, so a later runtime check passed despite the failed earlier scriptlets. The explicit kernel installation was then a no-op because RPM already considered the package installed. The artifact guard rejected the missing Oracle kernel image and the launcher stopped with status `1`.

Launcher evidence is `/home/opc/ol-migration-evidence/conversion/20261020T155618Z-7fc88b04/migration.log`; candidate state is `/var/lib/migrate-to-oracle-linux/20261020T155618Z`. Captured metadata confirms both target boot files absent, source kernel image and initramfs present, and the source RHEL kernel still selected by GRUB. No reboot or recovery boot was performed. Retained source files do not prove the partially converted guest can boot.

## Remaining work

The intermediate workshop.3 candidate, SHA-256 `360864fcc526dd164b68bda401289a77fae90fb637eb050323fc7654dee74a10`, added conservative rejection of the observed `libattr` downgrade before operating-system mutation and protected selected replacement phases. It passed 99 migration safety tests, 43 launcher tests, and 1,603 local structural checks. Those historical results establish a safer refusal, not a successful migration.

Tested workshop.4, SHA-256 `99fff3877ac697e38d28fda047171402c2231709114b49ae6e3056a5775e83a8`, used coherent repositories and deferred bootloader ordering. Its 135 migration safety tests, 43 launcher tests, 1,618 local checks and source-3 dry run passed. Actual conversion then failed with final status `1` at 20:34:42 UTC on October 20, 2026. Both library checks and initial Oracle RHCK artifact checks passed, but a `grub2-probe` LVM disk-not-found error was missed during kernel installation. The final stop involved seven shim-file timestamp differences on vfat. Read-only matching size/digest and one-second mtime evidence supports a timestamp-precision explanation for that stop; it does not resolve the GRUB error or qualify migration. No reboot occurred.

Source-3 recovery and its pre-conversion checkpoint remain valid historical results: operator-confirmed Full/Available backup at 20:22:11 UTC, manual recorder status `0` at 20:26:21 UTC, and saved BIOS/VirtIO SCSI/LVM/XFS baseline. No authenticated backup API lookup was performed. The [source-3 failed-run record](source3-run-20261020.md) preserves the actual failure evidence. Source-3 is now partially converted, with the RHEL kernel running and Oracle kernel selected as default. Apache and the application checksum passed after failure; those checks do not establish bootability or full package consistency. Preserve its disk and backup; no rerun or reboot.

Workshop.5 is frozen at SHA256 `bad3bf7487ab2a4b7650e91c56af81ae332e26381a3941b275a33029f05d9df0`, with 150 migration safety tests and 43 launcher tests recorded as passing in the prior session. No live pass is claimed. The GRUB problem remains unresolved; a changed guard is not a completed migration fix. The [current status](current-status.md) records the separate imported-image success and supersedes the withdrawn source-4 proposal.

Preserve `ol-migrate-rhel-source-2`, its disk, backup, and failed-run evidence. Do not rerun the conversion or reboot this guest. Preserve the original failed instance and boot volume as before. Any future successful migration claim still requires a supported transition on the separately verified clean source, independent target boot-file and package checks, and an actual Oracle-kernel reboot with SSH and application validation. A solved DNF plan alone does not satisfy any of those results.
