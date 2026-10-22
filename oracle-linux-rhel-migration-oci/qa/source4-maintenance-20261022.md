# Source-4 maintenance and live-update results, October 22, 2026

**Labs 5 and 6 passed their recorded maintenance and live-update checks.** Oracle RHCK remained `5.14.0-687.58.1.el9_8.x86_64`. Lab 5 included a successful maintenance reboot after a reviewed package correction; Lab 6 applied two Ksplice updates without another reboot. Apache, the workload checksum, package consistency and security checks passed afterward.

This record follows the accepted [Lab 4 conversion and Oracle boot](source4-run-20261022.md). It applies to source-4 only. All earlier failed resources, saved backups, migration evidence and the imported-image reference remain preserved. These results do not establish OS Management Hub registration or completion of the Console tasks in Lab 7.

## Lab 5 assessment and actual package update

The assessment at 14:58:23 UTC confirmed Oracle Linux 9.8, an Oracle-owned RHCK, the expected kernel inventory, no installed UEK packages, and healthy Apache. `dnf check-update` returned the documented status `100` for available updates. The repository release catalogue still selected Oracle Linux 9.8. Available advisory metadata listed 17 security notices and 19 bugfix notices; these are notice counts, not distinct vulnerabilities or packages fixed.

The preview resolved 43 upgrades. The actual `dnf upgrade` started at 15:02:24 UTC and finished at 15:03:06 UTC with status `0`. It updated userspace packages including systemd, dracut, Apache, Python, SELinux policy and authentication components. The complete transaction log contains no runtime-kernel installation or removal, shared-library loader failure, failed scriptlet, GRUB error, or fatal transaction message.

The initial review missed that `xfsprogs-6.18.0-1.0.4.el9` came from the enabled `ol9_UEKR7` repository. The upgrade was already running when the independent review identified Oracle's [UEK R7 userspace support restriction](https://docs.oracle.com/en/operating-systems/uek/7/relnotes7.0/uek7.0-RelatedUserSpacePackages.html). That package was actually installed. The run must not be described as an uninterrupted supported 43-package RHCK update.

After the transaction finished, the following focused correction was reviewed and performed before reboot:

1. Confirmed that no UEK kernel was installed.
2. Permanently disabled `ol9_UEKR7` and confirmed that only `ol9_baseos_latest` and `ol9_appstream` remained enabled.
3. Queried the BaseOS `xfsprogs` catalogue and previewed a transaction constrained to that repository. The resolved plan changed only `xfsprogs` to `6.4.0-7.0.1.el9.x86_64`.
4. Applied that one-package restoration at 15:04:26 UTC with status `0`. Installed Oracle ownership, `rpm -V xfsprogs`, RPM database verification and `dnf check` passed. A fresh check for remaining updates returned `0` with none listed.

No UEK kernel was installed, no kernel was switched, and no filesystem was reformatted or upgraded. The source XFS feature inventory was inspected. The correction restored the BaseOS userspace package; it was not a root-volume repair. The workshop's [Lab 5 procedure](../manage-patch-oracle-linux/manage-patch-oracle-linux.md) now disables the UEK repository before update assessment and requires transaction review. The original preview, actual upgrade, support finding and correction evidence remain separate and preserved.

## Lab 5 reboot gate and actual reboot

The valid `dnf needs-restarting -r` result was `1`, with an explicit recommendation to reboot because systemd had been updated. That status was interpreted as a reboot hint, not a failed upgrade. Reboot did not proceed until the independent check passed at 15:04:50 UTC.

Every command in that check returned zero, including the pinned candidate's boot-only verification, RPM database and dependency checks, critical-library verification and loading, Oracle kernel and BIOS package integrity, direct and nested GRUB probes, and workload/security checks. The default, root UUID, root mapping, LVM sequence 4 and every boot argument matched the successfully booted Oracle configuration. Both retained RHEL kernel sets remained present. The target archive supplied all observed storage, LVM, filesystem and NIC modules or built-in equivalents, their dependencies, and the required LVM/dm tools. There were no conflicting embedded command-line files. The result was `INDEPENDENT_PRE_REBOOT_PASS`.

The new maintenance check and package records were collected locally before the maintenance reboot. The reboot was scheduled at 15:05:57 UTC. SSH reconnected and the post-maintenance check passed at 15:06:54 UTC:

- A new boot ID established an actual reboot.
- Oracle Linux 9.8 and running/default Oracle-owned RHCK `5.14.0-687.58.1.el9_8.x86_64` matched.
- System state was running, zero units had failed, and cloud-init completed without errors.
- RPM database, dependencies, critical-library verification and loading passed.
- Apache was enabled and active; local HTTP and the saved application checksum passed.
- SELinux was Enforcing, the HTTP firewall rule was enabled, and root/LVM identity was unchanged.
- The installed `xfsprogs` remained Oracle BaseOS `6.4.0-7.0.1.el9`.
- `dnf needs-restarting -r` returned `0` and reported that another reboot was unnecessary.

The collector recorded `LAB5_MAINTENANCE_REBOOT_PASS` with no errors. The upgrade had not installed a new kernel; the maintenance reboot activated updated core userspace.

## Lab 6 client installation and reviewed updates

The initial assessment at 15:07:10 UTC found no Uptrack client. The downloaded `install-uptrack-oc` source passed shell syntax review and was recorded at SHA256 `7a89c8a8323c29a5384dbc0c54a27b421217d23845a91d7b595cfbf7b9656ed8`. That digest was checked again immediately before execution. The installer was run without an automatic-install flag.

Installation ran from 15:07:54 to 15:08:09 UTC and returned status `0`. It installed `uptrack-1.2.80-0.el9.noarch`, `ksplice-uptrack-release-1-5.noarch` and their userspace dependencies. The installation log contains no kernel-package transaction or fatal, loader, scriptlet or GRUB error. The initial installed-update list was empty.

The explicit non-applying preview at 15:08:40 UTC returned status `0` and proposed exactly these two updates:

| Update ID | Reported description |
| --- | --- |
| `52t6sh3y` | Enablement update for live patching. |
| `ciad9z8t` | Provide an interface to freeze tasks. |

The reviewed application at 15:09:18 UTC returned status `0`. `uptrack-show` then listed both update IDs, and a fresh preview reported `Nothing to be done.` The effective kernel version remained `5.14.0-687.58.1.el9_8.x86_64`. The applied updates provide enablement/interface functionality; this run does not demonstrate a changed effective version or a particular CVE fix.

The boot ID before and after application was identical, and the base kernel stayed unchanged. Apache remained enabled and active, local HTTP returned the workload marker, and the baseline application checksum matched. System state was running with zero failed units, SELinux was Enforcing, the HTTP firewall rule remained enabled, and RPM database/dependency checks returned zero. The collector recorded `LAB6_KSPLICE_NO_REBOOT_PASS` with no errors.

The application checksum throughout both labs was `11db440c06cd95a31aa4dfe59f97d08389c9235e949f5c0f1f385deab4b04365`.

The combined public HTTP check at 15:10:54 UTC returned status 200, the workload marker and the same body checksum. This closes the external workload validation after maintenance and live updates.

## Evidence and remaining boundaries

Private evidence is under ignored `qa/live-evidence/source4-labs5-6-20261022/`. It contains the original Lab 5 assessment, complete actual upgrade, BaseOS-only correction plan and transaction, independent boot inspection, maintenance reboot and reconnect checks, reviewed Ksplice installer source and digest, installation, preview and application results, and the combined public HTTP check. Public resource identifiers, IPs and private-key paths are excluded from this report.

Lab 7 guest preparation is incomplete: its first preflight stopped before changes because the expected `ol9_oci_included` repository was absent. The documented repository setup is under review. OS Management Hub plugin eligibility, Console configuration, registration and service jobs still require their own evidence and are not established by a working Uptrack client. Lab 8 final validation remains pending. No cleanup, kernel removal, backup deletion, commit, push or publication is authorized.
