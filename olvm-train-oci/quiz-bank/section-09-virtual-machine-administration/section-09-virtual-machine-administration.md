# Section 9: Virtual Machine Administration
## Introduction

This lab contains the Section 9 practice questions for **Virtual Machine Administration** from the OLVM Exam 1Z0-1170 quiz bank.

Estimated Time: 70 minutes

```quiz-config
passing: 75
badge: images/badge.png
```

### Objectives

In this lab, you will review the OLVM concepts covered by this exam objective and check your understanding with practice questions.

## Practice Questions

```quiz

Q: 1. What are the two main types of virtual machine storage provisioning in OLVM? (Choose 2)
* A. Pre-allocated (thick provisioning)
- B. Compressed provisioning
* C. Sparse (thin provisioning)
- D. Encrypted provisioning
> OLVM uses pre-allocated, or thick, provisioning and sparse, or thin, provisioning for VM storage. These choices control whether storage is reserved up front or consumed as data is written.

Q: 2. What is pre-allocated storage also known as?
- A. Thin provisioning
* B. Thick provisioning
- C. Sparse provisioning
- D. Dynamic provisioning
> Pre-allocated storage is also called thick provisioning because the disk space is reserved immediately. This can improve predictability at the cost of consuming the full allocation from the start.

Q: 3. How does sparse allocation work?
- A. Allocates all storage upfront
* B. Defines maximum capacity but physical space is allocated only as data is written
- C. Compresses all data
- D. Encrypts all data
> Sparse allocation defines a maximum virtual disk size but only consumes physical storage as the guest writes data. This saves capacity but requires careful monitoring to avoid storage overcommit problems.

Q: 4. Which two disk format types does OLVM use? (Choose 2)
* A. QCOW2
- B. VMDK
* C. Raw
- D. VHD
> OLVM uses QCOW2 and Raw disk formats. QCOW2 provides advanced features, while Raw is simpler and often used for predictable performance.

Q: 5. What does QCOW2 stand for?
- A. Quality Copy On Write 2
* B. QEMU Copy On Write version 2
- C. Quick Copy Overwrite 2
- D. Queue Copy On Write 2
> QCOW2 stands for QEMU Copy On Write version 2. It is the QEMU disk image format that supports copy-on-write behavior and advanced disk features.

Q: 6. Which disk format supports snapshots, encryption, compression, and sparse allocation?
- A. Raw
- B. VMDK
* C. QCOW2
- D. VHD
> QCOW2 supports features such as snapshots, encryption, compression, and sparse allocation. Raw disks are simpler and do not provide the same feature set.

Q: 7. On NFS storage, what format is a QCOW2 disk created as?
- A. A block device
* B. A file with initial size close to 0
- C. A compressed archive
- D. An encrypted container
> On NFS storage, a QCOW2 disk is created as a file and can initially be close to zero in size. It grows as data is written because the file-based storage supports sparse behavior.

Q: 8. On SAN storage, what format is a QCOW2 disk created as?
- A. A file
* B. A block device with initial size smaller than defined virtual disk size
- C. A compressed file
- D. An LVM partition
> On SAN storage, QCOW2 is represented as a block device with an initial size smaller than the defined virtual disk size. The implementation differs because SAN storage presents block devices rather than files.

Q: 9. On NFS storage, can raw disks be sparse?
- A. No, only pre-allocated
* B. Yes, they can be sparse files or pre-allocated
- C. Only with special configuration
- D. Raw disks are not supported on NFS
> On NFS, raw disks can be sparse files or pre-allocated files. File storage gives OLVM flexibility in how raw disk space is consumed.

Q: 10. On SAN storage, are raw disks sparse or pre-allocated?
- A. Only sparse
- B. Both sparse and pre-allocated
* C. Only pre-allocated
- D. Neither, they use compression
> On SAN storage, raw disks are pre-allocated only. Block storage does not use the same sparse-file semantics as NFS-backed file storage.

Q: 11. What does the "wipe after delete" flag do?
- A. Encrypts the disk
* B. Replaces user data with zeros when a virtual disk is deleted
- C. Backs up the disk
- D. Compresses the disk
> The `wipe after delete` flag overwrites deleted virtual disk data with zeros. This reduces the chance that sensitive data remains recoverable after disk deletion.

Q: 12. On file storage systems like NFS, what effect does enabling "wipe after delete" have?
- A. It wipes data thoroughly
* B. It does nothing because the file system ensures no data exists
- C. It encrypts the data
- D. It backs up the data
> On file storage such as NFS, enabling wipe after delete does not provide the same effect because the file system handles deleted file content differently. The behavior is most relevant to SAN/block-backed storage.

Q: 13. When is enabling "wipe_after_delete" recommended?
- A. Always
- B. Never
* C. If the virtual disk contained sensitive data
- D. Only on SAN storage
> Enable `wipe_after_delete` when a disk contained sensitive data and sanitization matters. It is a security choice that should be balanced against the performance cost.

Q: 14. What is the default value for the wipe_after_delete flag?
- A. True
* B. False
- C. Enabled
- D. Auto
> The default value for `wipe_after_delete` is false. OLVM does not zero deleted disk contents by default because doing so can make delete operations much slower.

Q: 15. What command is used to set SANWipeAfterDelete to true?
- A. olvm-config --set SANWipeAfterDelete=true
* B. engine-config --set SANWipeAfterDelete=true
- C. vm-config SANWipeAfterDelete true
- D. set-san-wipe true
> `engine-config --set SANWipeAfterDelete=true` enables wipe-after-delete behavior for SAN-backed storage. This is an engine-level configuration setting.

Q: 16. After changing the SANWipeAfterDelete setting, what must you do?
- A. Reboot all hosts
* B. Restart the ovirt-engine service
- C. Restart all VMs
- D. Nothing, it takes effect immediately
> After changing `SANWipeAfterDelete`, the `ovirt-engine` service must be restarted for the configuration change to take effect. Engine settings are not always applied dynamically.

Q: 17. What is a side effect of enabling wipe_after_delete?
- A. Faster deletion
* B. Performance degradation and prolonged delete times
- C. Increased storage capacity
- D. Better VM performance
> Wiping deleted disks can significantly slow deletion because the system must write zeros across the disk area. That extra I/O causes performance degradation and longer delete times.

Q: 18. What are shareable disks in OLVM?
- A. Disks shared between data centers
* B. Virtual disks that can be accessed simultaneously by multiple VMs
- C. Backup disks
- D. Network disks
> Shareable disks are virtual disks that multiple VMs can access simultaneously. They are used for specialized clustered workloads rather than ordinary single-VM disk use.

Q: 19. What type of VMs should use shareable disks?
- A. Any VMs
* B. Only cluster-aware VMs
- C. Single VMs only
- D. Test VMs only
> Only cluster-aware VMs should use shareable disks because the guest applications and file systems must coordinate concurrent access. Without clustering awareness, multiple writers can corrupt data.

Q: 20. What can happen if you attach a shared disk to VMs that are not cluster-aware?
- A. Performance improvement
* B. Data corruption
- C. Increased security
- D. Nothing
> Attaching a shared disk to non-cluster-aware VMs can cause data corruption. The VMs may write independently without the locking or coordination required for shared storage.

Q: 21. What is an appropriate use case for shareable disks?
- A. General file storage
* B. Clustered database servers or highly available services
- C. Single user workstations
- D. Test environments
> Clustered databases and highly available services are appropriate use cases for shareable disks because they are designed to coordinate shared storage access. General-purpose standalone VMs should not share disks this way.

Q: 22. Where in the Administration Portal do you create a new VM?
- A. Storage -> VMs
* B. Compute -> Virtual Machines
- C. Network -> VMs
- D. Administration -> VMs
> New VMs are created from Compute -> Virtual Machines in the Administration Portal. VMs are compute objects because they run on hosts and consume compute resources.

Q: 23. When creating a VM from scratch (not a template), what template option do you select?
- A. Default
* B. Blank
- C. Empty
- D. None
> When creating a VM from scratch, select the Blank template. This starts with an empty VM definition instead of inheriting disks and configuration from an existing template.

Q: 24. What are the two disk allocation policies? (Choose 2)
* A. Pre-allocated
- B. Compressed
* C. Thin provisioning (sparse)
- D. Encrypted
> The disk allocation policies are pre-allocated and thin provisioning. These correspond to thick reservation and sparse growth behavior.

Q: 25. What is the recommended disk interface for VM disks?
- A. IDE
- B. SATA
* C. VirtIO-SCSI
- D. USB
> VirtIO-SCSI is the recommended disk interface for VM disks because it provides paravirtualized performance and modern SCSI features. Legacy IDE or SATA interfaces are generally less optimal.

Q: 26. In boot options, which device is typically selected for first boot when installing an OS?
- A. Hard disk
* B. CD-ROM
- C. Network
- D. USB
> For a new OS installation, CD-ROM is typically selected as the first boot device so the VM boots from installation media. After installation, the boot order should be changed.

Q: 27. After OS installation is complete, which device should be the first boot device?
- A. CD-ROM
* B. Hard disk
- C. Network
- D. Floppy
> After the OS is installed, the hard disk should be first in the boot order. Otherwise the VM may continue trying to boot from installation media.

Q: 28. What key do you press to access the boot menu in the VM console?
- A. F2
- B. Delete
* C. Escape
- D. F12
> The Escape key opens the VM console boot menu in this practice objective. That lets the user select the desired boot device during startup.

Q: 29. What key combination releases the cursor from the VM console?
- A. Ctrl+Alt
- B. Alt+F4
* C. Shift+F12
- D. Ctrl+Esc
> Shift+F12 releases the cursor from the VM console. This is useful when the console captures mouse or keyboard focus.

Q: 30. What is a VM snapshot?
- A. A backup copy stored externally
* B. A point-in-time capture of VM state including memory, disk, and device state
- C. A template
- D. A compressed VM image
> A VM snapshot is a point-in-time capture of VM state, which can include disk, memory, and device state. It lets administrators return to a known state for short-term change testing.

Q: 31. Should snapshots be used as a primary backup method?
- A. Yes, they are perfect for backups
* B. No, they should not be used as primary backup
- C. Only for production VMs
- D. Only for test VMs
> Snapshots should not be used as the primary backup method. They are operational rollback tools, not a substitute for independent backups stored outside the VM's primary storage dependency.

Q: 32. Why should snapshots NOT be used as primary backup?
- A. They are too slow
* B. They are stored on the same storage as VM disks; if storage fails, snapshots become inaccessible
- C. They use too much CPU
- D. They are encrypted
> Snapshots are stored on the same storage as VM disks, so a storage-domain failure can make both the VM and its snapshots unavailable. A proper backup must survive failures of the primary storage.

Q: 33. When is it appropriate to take a snapshot?
- A. Every day as backup
* B. Before testing updates, configuration changes, or application patches
- C. Never
- D. Only once per VM
> Snapshots are appropriate before updates, configuration changes, or application patches because they provide a rollback point. They are best used for short-term change protection.

Q: 34. What should you do with snapshots after reverting or when no longer needed?
- A. Keep them forever
* B. Delete them promptly to maintain optimal VM performance
- C. Archive them
- D. Encrypt them
> Snapshots should be deleted promptly after they are no longer needed. Long snapshot chains increase storage use and can degrade VM performance.

Q: 35. What can happen if you take several snapshots without cleanup?
- A. Performance improvement
* B. Increased storage usage and performance degradation
- C. Better security
- D. Faster backups
> Keeping many snapshots increases storage consumption and can reduce performance because disk I/O may traverse snapshot layers. Snapshot cleanup is part of good VM maintenance.

Q: 36. What should be installed before taking snapshots?
- A. Antivirus software
* B. Latest guest agent package (qemu-guest-agent)
- C. Backup software
- D. Monitoring tools
> The latest guest agent package, such as `qemu-guest-agent`, should be installed before snapshots. The guest agent helps coordinate consistent snapshot behavior with the guest OS.

Q: 37. Where do you create a VM snapshot in the Administration Portal?
- A. Storage -> Snapshots
* B. Compute -> Virtual Machines -> select VM -> Create Snapshot button
- C. Network -> Snapshots
- D. Configuration -> Snapshots
> Snapshots are created from Compute -> Virtual Machines by selecting a VM and using the Create Snapshot action. Snapshot management is tied to the VM object.

Q: 38. What can you optionally include when creating a snapshot?
- A. Network configuration
* B. Memory state of the VM
- C. User accounts
- D. Firewall rules
> When creating a snapshot, you can optionally include the VM memory state. Including memory captures the running state but adds time and can briefly affect the VM.

Q: 39. What happens to the VM when you select "Save Memory" during snapshot creation?
- A. VM continues running normally
* B. VM will be paused while saving memory
- C. VM is shut down
- D. VM is restarted
> Selecting Save Memory pauses the VM while memory state is saved. This pause is needed to capture a consistent memory image.

Q: 40. What is a VM template in OLVM?
- A. A backup file
* B. A pre-configured VM that captures configuration and state for quick deployment of new VMs
- C. A snapshot
- D. A disk image
> A VM template is a pre-configured VM baseline used to deploy new VMs quickly. It captures configuration and state so new VMs can be created consistently.

Q: 41. How do you create a template from a snapshot?
- A. Copy the VM
* B. Select snapshot -> click "Make Template" button
- C. Export the VM
- D. Clone the snapshot
> To create a template from a snapshot, select the snapshot and click Make Template. This turns the captured VM state into a reusable deployment source.

Q: 42. Where in the Administration Portal do you view templates?
- A. Storage -> Templates
* B. Compute -> Templates
- C. Network -> Templates
- D. VM -> Templates
> Templates are viewed under Compute -> Templates in the Administration Portal. Templates are compute deployment objects rather than storage-only objects.

Q: 43. What option allows all users to access a template?
- A. Public template
* B. Allow all users to access this template
- C. Shared template
- D. Global template
> The option `Allow all users to access this template` makes the template broadly available. Without that permission, only authorized users can deploy from it.

Q: 44. What does "Seal template" do and for which OS is it available?
- A. Encrypts template, Windows only
* B. Removes system-specific details, Linux only
- C. Backs up template, all OS
- D. Compresses template, all OS
> Seal template removes system-specific details, such as identity information, and is available for Linux templates in this objective. Sealing prepares a template for clean cloning.

Q: 45. When creating a VM from a template, what do you NOT need to provide?
- A. VM name
* B. Instance images (disks are already from template)
- C. Network interface
- D. Memory size EXPORTING VMs
> When creating a VM from a template, you do not need to provide instance images because the disks come from the template. You still need VM-specific details such as name and placement.

Q: 46. What file format is used when exporting a VM?
- A. VMDK
* B. OVA
- C. VHD
- D. ISO
> VM export uses the OVA format. OVA is a portable virtual appliance package format used for exporting and importing VM images.

Q: 47. Where must you create a directory for VM export?
- A. On the engine host
* B. On the KVM host where export will happen
- C. On the storage domain
- D. On the client machine
> The export directory must be created on the KVM host where the export operation will run. The host performing the export needs local access to that path.

Q: 48. What permissions should be set on the export directory?
- A. 600 (owner only)
- B. 755 (owner full, others read/execute)
* C. 777 (full permissions for all)
- D. 644 (owner read/write, others read)
> The practice uses `777` permissions for the export directory so the export process can write there without permission issues. In production, permissions should be narrowed to the required service users where possible.

Q: 49. How do you initiate VM export in the Administration Portal?
- A. File -> Export
* B. Select VM -> Options menu (three dots) -> Export as OVA
- C. Right-click -> Export
- D. VM -> Tools -> Export
> VM export is initiated by selecting the VM, opening the options menu, and choosing Export as OVA. This starts the portal-driven export workflow.

Q: 50. Can you export a running VM?
- A. No, it must be shut down first
* B. Yes, VMs can be exported while running
- C. Only if memory is saved
- D. Only test VMs
> OLVM can export VMs while they are running according to this quiz objective. Administrators should still consider consistency and workload behavior when exporting active systems.

Q: 51. To revert a VM to a snapshot, what must be the VM state?
- A. Running
* B. Shut down
- C. Paused
- D. Any state
> A VM must be shut down before reverting to a snapshot. Reversion changes disk and state history, so the VM cannot be actively running through the operation.

Q: 52. What two actions are required to revert to a snapshot? (Choose 2)
* A. Select snapshot -> Preview
- B. Delete the VM
* C. Click Commit button
- D. Restart engine
> Reverting requires selecting the snapshot, previewing it, and then committing the change. Preview lets the administrator verify the selected state before making it permanent.

Q: 53. After reverting to a snapshot, what happens to changes made after the snapshot was taken?
- A. They are preserved
* B. They are lost
- C. They are archived
- D. They are encrypted
> Changes made after the snapshot was taken are lost when the VM is reverted. The VM returns to the earlier captured state, which is the purpose and risk of rollback.

Q: 54. In the practice, what file was created post-snapshot to demonstrate reversion?
- A. test-file
* B. post-snap
- C. snapshot-test
- D. demo-file
> The practice created a file named `post-snap` after the snapshot to demonstrate rollback. It provided a visible change that should disappear after reverting.

Q: 55. After reverting to the snapshot, was the post-snap file still present?
- A. Yes, it remained
* B. No, it was gone because VM reverted to snapshot state
- C. It was moved to backup
- D. It was encrypted
> After reverting to the snapshot, the `post-snap` file was gone because the VM returned to the snapshot state. Anything created after that snapshot was not part of the restored state.

```

## Acknowledgements

- **Author** - Perside Foster
- **Source Material** - OLVM bootcamp training and Oracle Linux Virtualization Manager documentation