# Next instructor rehearsal for WMS 12229

Status: **Workshop.4 failed on source-3 with final status `1`; no reboot performed. Learner migration remains on hold.** The tested candidate SHA-256 was `99fff3877ac697e38d28fda047171402c2231709114b49ae6e3056a5775e83a8`. Its 178 historical local tests and 1,618 historical local checks did not qualify the live conversion. Preserve source-3 as partially converted; do not rerun or reboot it. See the [failed-run record](source3-run-20261020.md).

The workshop.2 attempt left `ol-migrate-rhel-source-2` partially converted. It cannot be reused as a clean RHEL source. Preserve that instance, its disk, its backup, and evidence. Preserve the original failed instance and boot volume separately. These historical records do not prescribe another test or any change to a failed instance.

Workshop.4 used coherent BaseOS latest and AppStream repositories, retained the release/library guards, and installed the Oracle kernel before deferred shim/GRUB work. The library and initial kernel-artifact checks passed, but a GRUB LVM disk-not-found error occurred before the final timestamp-verification stop. Workshop.5 is frozen with local guard tests recorded, without live acceptance. The underlying GRUB issue and OCI-image migration path remain unqualified. The [current status](current-status.md) supersedes this historical rehearsal record.

## Current action: local review, no new Console task

The former source-4 restore-and-launch proposal is withdrawn. Do not create another VM, restore another volume, take another backup, or repeat conversion from this record. The operator stopped that cycle.

The imported-image migration and reboot have now been verified independently. Compare that successful reference with the preserved OCI-image failure evidence and review the unresolved LVM/GRUB issue locally. See [current status and next work](current-status.md) for the findings, evidence gaps, and unchanged qualification hold. Any future live evidence plan must be agreed separately.

The operator subsequently supplied an already available source-4 for read-only checks. Those checks reproduce the same root LVM probe error before conversion and identify a metadata-formatting compatibility lead. See [source-4 findings](source4-readonly-20261021.md). This does not reinstate the withdrawn resource-creation plan or authorize corrective writes, conversion, or reboot.

## Historical source-3 Console restore and launch record

Source-3 has already been launched, and the operator explicitly confirmed the backup-to-new-volume and new-volume-to-instance relationships in OCI Console. Its later Full backup and manual checkpoint are also recorded. Do not repeat the resource-creation steps below; they document the completed restore-and-launch path. See the [current verification record](recovery-source-3.md). No authenticated API lineage lookup was performed.

The operator uses OCI Console for these checks. Cloud Shell and an authenticated local OCI CLI are not required; the agent has neither authenticated Console control nor API access.

1. Open **Boot Volume Backups** in the workshop compartment. Find the operator-confirmed Full, Available backup created on **October 20, 2026 at 14:28:46 UTC**, before the source-2 conversion. Match its OCID to the saved checkpoint. Keep this backup.

2. From that backup's **Actions** menu, select **Restore Boot Volume**. Create a **new** volume named `ol-migrate-rhel-source-3-boot` in the workshop compartment and the availability domain intended for the new instance. Wait for the restored volume to become Available, and record its new OCID. [Oracle: Restoring a Boot Volume](https://docs.oracle.com/en-us/iaas/Content/Block/Tasks/create-restore-bv-boot-volume-backup.htm).

3. Open **Compute > Instances > Create instance**. Name it `ol-migrate-rhel-source-3`. Under the image selection, choose **Change image > My images > Boot volumes**, then select the newly restored volume by name and OCID. Select the same availability domain as that volume. Reuse the workshop shape, OCPU and memory settings, existing VCN, public subnet, security settings, and matching public SSH key. Assign a new public IP; retain the system-assigned private IP. Review the selected boot-volume OCID before creating the instance. [Oracle: Creating an Instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/launchinginstance.htm).

4. Record the new instance OCID, boot-volume OCID, public IP, and SSH account. The operator has confirmed that the new volume was restored from the identified pre-conversion backup and is attached to source-3. Preserve those records and continue with the remaining instructor gates below.

## Instructor gates after launch

1. Verify the restored guest through SSH before changing packages: live instance identity, RHEL 9.8, original source kernel and boot files, RPM database and DNF consistency, both `libattr` and `libacl` payload integrity and loading, Apache marker, and baseline application checksum. Confirm expected RHUI BaseOS/AppStream IDs, repository access, absence of Oracle migration repositories, public HTTP, and security state. Inspect cloud-init's full status: `done` or exit `0` can coexist with degraded completion and recoverable warnings. Classify those warnings against the current boot and instance identity before acceptance. Record this separately as the backup restoration and recovery test; a Running state alone does not pass it. See [source-3 verification status](recovery-source-3.md).

2. The corrected candidate, launcher, and matching `SHA256SUMS` were staged from the local checkout. The fixed dry-run-only assessment and its solver review have passed on source-3; see the recovery record for evidence and the remaining package acceptance conditions. Retain the exact staged bundle and reports. A dry run does not execute package scriptlets or prove boot.

3. Completed on October 20, 2026: source-3's new Full backup was confirmed Available with creation time 20:22:11 UTC. Apache restart, enabled/active state, marker and baseline checksum passed. The recorder-only launcher exited `0` at 20:26:21 UTC and saved `console-checkpoint-source-3.json` in the verified workshop.4 bundle directory. The source boot baseline was also captured. These are manual Console service confirmations plus guest checks; no backup API query was made. Retain both backups and the separate source-2 checkpoint.

4. The controlled conversion failed at 20:34:42 UTC under unit `ol-source3-conversion-20261020t2028`, with final status `1`. Do not launch another run or reboot. Preserve the actual BIOS/VirtIO SCSI/LVM/XFS baseline and failure evidence. The unresolved GRUB error must not be dismissed because later kernel files exist or shim payload bytes match. A future accepted migration still requires completed conversion, independent boot/package/workload checks and an actual Oracle-kernel reboot. No next live attempt is established by this record.
