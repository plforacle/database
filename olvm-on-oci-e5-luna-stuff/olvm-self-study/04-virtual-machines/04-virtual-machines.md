# OLVM Self-Study: Virtual Machine Administration

## Introduction

This module contains **50 scored practice questions** for the virtual machine administration objective, which accounts for **10%** of the exam weighting supplied for this training. Use it after the instructor-led Lab 7 session and while reviewing the supplied course transcripts.

Estimated Time: 50-70 minutes, including explanation review

```quiz-config
passing: 80
badge: images/badge.png
```

### Objectives

- Manage Virtual Machines and Their Storage
- Describe Guest Agents and Drivers
- Describe VM Administrator Roles
- Describe VM Portal
- Create and Manage VM Templates

### How to Practice

Choose **one option per question**, including questions whose choices contain a set of items. Click **Check Answer** to see feedback and the explanation. Answer all 50 questions; at least **40 correct answers** unlock this module's badge. The 80% target is a study milestone, not an official exam passing score.

Record your first-attempt score before using **Try Again**. Review the corresponding transcript clip or Lab 7 task, explain the reasoning, and retry after studying. Scores are tracked within this module; keep your own first-attempt record before navigating away.

The recorded course uses older OL8 and OLVM examples than Lab 7. Questions explicitly referring to the recorded course use that context. Match package, identity, and UI procedures to the actual practice environment.

[Study guide and coverage map](../../workshops/olvm-study/index.html?lab=study-guide)

## Task 1: Manage Virtual Machines and Their Storage

```quiz score
Q: 4.001. What are the two main types of virtual machine storage provisioning in OLVM? Select the option listing the complete correct set.
* Pre-allocated (thick provisioning); Sparse (thin provisioning)
- Encrypted provisioning; Sparse (thin provisioning)
- Compressed provisioning; Sparse (thin provisioning)
- Pre-allocated (thick provisioning); Compressed provisioning
> OLVM uses pre-allocated, or thick, provisioning and sparse, or thin, provisioning for VM storage. These choices control whether storage is reserved up front or consumed as data is written.
```

```quiz score
Q: 4.002. When importing an OVA during Lab 7, which configuration needs review before booting the VM?
- Only the archive filename determines every network connection
* Imported resources and network mappings match the intended environment
- The OVA always grants every user administrator access
- The import automatically creates all physical switch VLANs
> Lab 7 Task 4 imports application VMs and checks resource and network settings. An appliance still needs integration with the target environment.
```

```quiz score
Q: 4.003. How does sparse allocation work?
- Compresses all data
* Defines maximum capacity but physical space is allocated only as data is written
- Allocates all storage upfront
- Encrypts all data
> Sparse allocation defines a maximum virtual disk size but only consumes physical storage as the guest writes data. This saves capacity but requires careful monitoring to avoid storage overcommit problems.
```

```quiz score
Q: 4.004. Which two disk format types does OLVM use? Select the option listing the complete correct set.
* QCOW2; Raw
- VMDK; Raw
- VHD; Raw
- QCOW2; VMDK
> OLVM uses QCOW2 and Raw disk formats. QCOW2 provides advanced features, while Raw is simpler and often used for predictable performance.
```

```quiz score
Q: 4.005. Why is QCOW2 relevant when discussing VM snapshot storage?
* Its copy-on-write format supports disk snapshot relationships
- It makes snapshots an independent off-site backup automatically
- It eliminates all storage consumption by snapshots
- It guarantees application consistency without guest coordination
> The VM storage lesson explains copy-on-write disks. Snapshot capability does not establish independent backup or application consistency.
```

```quiz score
Q: 4.006. How is a sparse QCOW2 disk represented on file-based storage such as NFS?
* As a file whose allocated space grows as data is written
- As a VLAN configuration on the host
- As a mandatory fully allocated physical disk in every guest
- As a table in the engine database
> Sparse file-backed storage separates virtual capacity from initially allocated space. Continue monitoring actual storage use as the disk grows.
```

```quiz score
Q: 4.007. What distinction matters for a thin-provisioned virtual disk on block storage?
- Its guest capacity is always equal to the engine's OS disk
- Its backing space can never grow
- It is a browser download with no persistent backing storage
* Its virtual capacity can exceed its initially allocated backing space
> The recorded SAN example distinguishes presented virtual capacity and backing allocation. Thin provisioning still requires sufficient capacity as usage grows.
```

```quiz score
Q: 4.008. On NFS storage, can raw disks be sparse?
- Only with special configuration
* Yes, they can be sparse files or pre-allocated
- Raw disks are not supported on NFS
- No, only pre-allocated
> On NFS, raw disks can be sparse files or pre-allocated files. File storage gives OLVM flexibility in how raw disk space is consumed.
```

```quiz score
Q: 4.009. On SAN storage, are raw disks sparse or pre-allocated?
* Only pre-allocated
- Neither, they use compression
- Only sparse
- Both sparse and pre-allocated
> On SAN storage, raw disks are pre-allocated only. Block storage does not use the same sparse-file semantics as NFS-backed file storage.
```

```quiz score
Q: 4.010. What does the "wipe after delete" flag do?
- Compresses the disk
* Replaces user data with zeros when a virtual disk is deleted
- Encrypts the disk
- Backs up the disk
> The `wipe after delete` flag overwrites deleted virtual disk data with zeros. This reduces the chance that sensitive data remains recoverable after disk deletion.
```

```quiz score
Q: 4.011. When is enabling "wipe_after_delete" recommended?
* If the virtual disk contained sensitive data
- Never
- Only on SAN storage
- Always
> Enable `wipe_after_delete` when a disk contained sensitive data and sanitization matters. It is a security choice that should be balanced against the performance cost.
```

```quiz score
Q: 4.012. Why should the wipe-after-delete policy be verified for sensitive VM disks?
* The configured behavior determines whether deletion includes data wiping
- All deletion operations always securely wipe every block regardless of settings
- The policy controls only browser colors
- A user group name determines disk wiping automatically
> The course discusses the policy and its default in the recorded environment. Verify the actual setting and security requirements before relying on deletion behavior.
```

```quiz score
Q: 4.013. What is a side effect of enabling wipe_after_delete?
- Better VM performance
- Faster deletion
- Increased storage capacity
* Performance degradation and prolonged delete times
> Wiping deleted disks can significantly slow deletion because the system must write zeros across the disk area. That extra I/O causes performance degradation and longer delete times.
```

```quiz score
Q: 4.014. What are shareable disks in OLVM?
- Backup disks
- Disks shared between data centers
- Network disks
* Virtual disks that can be accessed simultaneously by multiple VMs
> Shareable disks are virtual disks that multiple VMs can access simultaneously. They are used for specialized clustered workloads rather than ordinary single-VM disk use.
```

```quiz score
Q: 4.015. What type of VMs should use shareable disks?
- Any VMs
* Only cluster-aware VMs
- Test VMs only
- Single VMs only
> Only cluster-aware VMs should use shareable disks because the guest applications and file systems must coordinate concurrent access. Without clustering awareness, multiple writers can corrupt data.
```

```quiz score
Q: 4.016. What can happen if you attach a shared disk to VMs that are not cluster-aware?
- Increased security
- Nothing
* Data corruption
- Performance improvement
> Attaching a shared disk to non-cluster-aware VMs can cause data corruption. The VMs may write independently without the locking or coordination required for shared storage.
```

```quiz score
Q: 4.017. What is an appropriate use case for shareable disks?
- General file storage
- Test environments
* Clustered database servers or highly available services
- Single user workstations
> Clustered databases and highly available services are appropriate use cases for shareable disks because they are designed to coordinate shared storage access. General-purpose standalone VMs should not share disks this way.
```

```quiz score
Q: 4.018. An imported VM has the wrong logical network attached to its vNIC. What should be corrected before testing its service?
- Rename the engine history database
- Give the VM the SPM role
* Map the vNIC to the intended logical network and verify guest connectivity
- Set every storage domain to maintenance
> Lab 7 Task 4 checks network mappings of imported application VMs. Disk allocation policy alone does not establish application connectivity.
```

```quiz score
Q: 4.019. Which description of a VM snapshot is accurate?
- It always includes all external systems and application dependencies
- It always becomes an off-site engine backup
* It records a point in time for selected VM state, with memory optional in the demonstrated workflow
- It always contains memory regardless of snapshot options
> Lab 7 and the VM training distinguish disk snapshots and optional memory capture. Do not assume every snapshot contains the same state or guarantees application consistency.
```

```quiz score
Q: 4.020. Should snapshots be used as a primary backup method?
- Only for test VMs
- Yes, they are perfect for backups
- Only for production VMs
* No, they should not be used as primary backup
> Snapshots should not be used as the primary backup method. They are operational rollback tools, not a substitute for independent backups stored outside the VM's primary storage dependency.
```

```quiz score
Q: 4.021. Why should snapshots NOT be used as primary backup?
* They are stored on the same storage as VM disks; if storage fails, snapshots become inaccessible
- They are encrypted
- They use too much CPU
- They are too slow
> Snapshots are stored on the same storage as VM disks, so a storage-domain failure can make both the VM and its snapshots unavailable. A proper backup must survive failures of the primary storage.
```

```quiz score
Q: 4.022. When is it appropriate to take a snapshot?
* Before testing updates, configuration changes, or application patches
- Never
- Every day as backup
- Only once per VM
> Snapshots are appropriate before updates, configuration changes, or application patches because they provide a rollback point. They are best used for short-term change protection.
```

```quiz score
Q: 4.023. What should you do with snapshots after reverting or when no longer needed?
- Encrypt them
- Keep them forever
* Delete them promptly to maintain optimal VM performance
- Archive them
> Snapshots should be deleted promptly after they are no longer needed. Long snapshot chains increase storage use and can degrade VM performance.
```

```quiz score
Q: 4.024. What can happen if you take several snapshots without cleanup?
- Faster backups
- Better security
- Performance improvement
* Increased storage usage and performance degradation
> Keeping many snapshots increases storage consumption and can reduce performance because disk I/O may traverse snapshot layers. Snapshot cleanup is part of good VM maintenance.
```

```quiz score
Q: 4.025. What can you optionally include when creating a snapshot?
- Firewall rules
* Memory state of the VM
- User accounts
- Network configuration
> When creating a snapshot, you can optionally include the VM memory state. Including memory captures the running state but adds time and can briefly affect the VM.
```

```quiz score
Q: 4.026. What happens to the VM when you select "Save Memory" during snapshot creation?
* VM will be paused while saving memory
- VM is restarted
- VM continues running normally
- VM is shut down
> Selecting Save Memory pauses the VM while memory state is saved. This pause is needed to capture a consistent memory image.
```

```quiz score
Q: 4.027. Where must you create a directory for VM export?
- On the client machine
- On the engine host
- On the storage domain
* On the KVM host where export will happen
> The export directory must be created on the KVM host where the export operation will run. The host performing the export needs local access to that path.
```

```quiz score
Q: 4.028. How do you initiate VM export in the Administration Portal?
- File -> Export
- VM -> Tools -> Export
* Select VM -> Options menu (three dots) -> Export as OVA
- Right-click -> Export
> VM export is initiated by selecting the VM, opening the options menu, and choosing Export as OVA. This starts the portal-driven export workflow.
```

```quiz score
Q: 4.029. To revert a VM to a snapshot, what must be the VM state?
- Paused
- Running
- Any state
* Shut down
> A VM must be shut down before reverting to a snapshot. Reversion changes disk and state history, so the VM cannot be actively running through the operation.
```

```quiz score
Q: 4.030. What two actions are required to revert to a snapshot? Select the option listing the complete correct set.
- Restart engine; Click Commit button
* Select snapshot -> Preview; Click Commit button
- Select snapshot -> Preview; Delete the VM
- Delete the VM; Click Commit button
> Reverting requires selecting the snapshot, previewing it, and then committing the change. Preview lets the administrator verify the selected state before making it permanent.
```

```quiz score
Q: 4.031. After reverting to a snapshot, what happens to changes made after the snapshot was taken?
* They are lost
- They are encrypted
- They are archived
- They are preserved
> Changes made after the snapshot was taken are lost when the VM is reverted. The VM returns to the earlier captured state, which is the purpose and risk of rollback.
```

## Task 2: Describe Guest Agents and Drivers

```quiz score
Q: 4.032. Why use suitable VirtIO drivers in a guest VM?
- They replace all host virtualization components
* They support efficient access to the configured paravirtualized devices
- They grant the guest user portal administrator permissions
- They automatically replicate VM disks off-site
> The guest agents and drivers lesson describes guest support for virtualized devices. Device drivers and OLVM authorization have different roles.
```

```quiz score
Q: 4.033. What is a useful purpose of a guest agent in VM administration?
- Giving every portal user OS root access
* Providing guest information and supporting configured host-to-guest management operations
- Making all application backups unnecessary
- Replacing the physical host's firmware
> The guest agent complements device drivers by supporting guest integration. It does not replace guest security or independent backup planning.
```

```quiz score
Q: 4.034. OLVM is not receiving expected guest-agent information from a VM. What should be checked?
- Whether every user is a data center administrator
- Only the physical switch's VLAN label color
- Whether the engine backup has been renamed
* The agent is installed, running, and configured correctly inside that guest
> Guest integration depends on a functioning agent in the guest. The engine's own service health alone does not prove guest-agent availability.
```

```quiz score
Q: 4.035. What is the recommended disk interface for VM disks?
* VirtIO-SCSI
- SATA
- USB
- IDE
> VirtIO-SCSI is the recommended disk interface for VM disks because it provides paravirtualized performance and modern SCSI features. Legacy IDE or SATA interfaces are generally less optimal.
```

```quiz score
Q: 4.036. Why should guest-agent availability be checked before using a guest-coordinated snapshot workflow?
- The agent eliminates the need to monitor storage space
- No snapshot of any kind can ever exist without an agent
- The agent moves every snapshot off-site automatically
* Guest coordination requires the intended agent to be installed, running, and supported
> The snapshot lesson prepares the guest agent. Agent readiness supports the intended coordination, but does not turn a snapshot into an independent backup.
```

## Task 3: Describe VM Administrator Roles

```quiz score
Q: 4.037. Why assign a VM administration role at an appropriate scope?
- To bypass user authentication entirely
* To permit the required VM work without unnecessarily granting infrastructure-wide control
- To replace all guest OS permissions
- To grant unlimited storage regardless of quotas
> The VM roles lesson distinguishes VM administration from wider infrastructure control. Choose the role and resource scope for the intended task.
```

```quiz score
Q: 4.038. A user is allowed to operate a VM but must not administer its underlying hosts. What should be reviewed?
- Only whether the host has two NICs
* The role's allowed operations and the scope of its permission assignment
- Only the guest's screen resolution
- Only whether the VM has a sparse disk
> Role privileges and assignment scope control authorized operations. Compute capacity and disk format do not grant infrastructure privileges.
```

```quiz score
Q: 4.039. A permitted user can view a VM but an administrative action is denied. What should be investigated?
* Whether the effective role includes that action on the relevant resource
- Whether a Grafana dashboard has been refreshed
- Only whether the guest has a large disk
- Only whether the VM name ends in a number
> Viewing a resource and performing every operation on it are different privileges. Check role capabilities as well as scope.
```

```quiz score
Q: 4.040. A user needs to administer one VM without managing the whole environment. What should be assigned?
- Only a Grafana dashboard bookmark
* An appropriate VM administration role on the relevant VM or resource scope
- SuperUser for every task regardless of scope
- Only membership in the guest's local Linux group
> The VM administration roles lesson and Lab 7 distinguish VM-level permissions from broad infrastructure administration.
```

## Task 4: Describe VM Portal

```quiz score
Q: 4.041. A user with suitable permissions wants to start and stop assigned VMs. Which access route is intended for basic VM operations?
- The host's power-management controller only
* VM Portal
- The iSCSI server's target editor
- Direct changes to the engine database tables
> The VM Portal lesson describes basic user VM management. Operations are still subject to the permissions assigned in OLVM.
```

```quiz score
Q: 4.042. VM Portal does not show a VM that an administrator expects a user to manage. What should be checked?
* The user's effective access to that VM and the applicable permission scope
- Only the disk's QCOW2 filename
- Only the host's total physical core count
- Only the SNMP trap version
> VM Portal presents resources and operations according to authorization. Confirm the intended permission assignment rather than assuming all VMs are visible.
```

```quiz score
Q: 4.043. Why might a user choose Administration Portal instead of VM Portal for a task?
- VM Portal automatically removes all authentication requirements
* The task requires permitted infrastructure administration beyond basic VM operations
- VM Portal is the only place where physical switches can be configured
- Administration Portal guarantees that every account is SuperUser
> The administration interface lesson distinguishes infrastructure management from user-facing VM operations. Either interface still requires the appropriate permissions.
```

```quiz score
Q: 4.044. A learner needs basic permitted VM operations rather than infrastructure management. Which interface is intended for that use?
- The physical switch's management console
* VM Portal
- The iSCSI target administration screen
- The PostgreSQL system catalog alone
> The VM Portal lesson describes user-facing VM management. Available actions still depend on the learner's permissions.
```

## Task 5: Create and Manage VM Templates

```quiz score
Q: 4.045. What is a VM template in OLVM?
- A disk image
* A pre-configured VM that captures configuration and state for quick deployment of new VMs
- A snapshot
- A backup file
> A VM template is a pre-configured VM baseline used to deploy new VMs quickly. It captures configuration and state so new VMs can be created consistently.
```

```quiz score
Q: 4.046. How do you create a template from a snapshot?
- Clone the snapshot
* Select snapshot -> click "Make Template" button
- Copy the VM
- Export the VM
> To create a template from a snapshot, select the snapshot and click Make Template. This turns the captured VM state into a reusable deployment source.
```

```quiz score
Q: 4.047. Why start and validate a database-tier VM before testing a dependent application-tier VM?
- The engine executes all application SQL itself
- A template removes every service dependency
* The application depends on the database service being available
- Starting the application first always creates the database automatically
> Lab 7 Task 4 tests a multi-tier workload. VM power state and application service readiness are different checks.
```

```quiz score
Q: 4.048. What option allows all users to access a template?
- Public template
- Shared template
* Allow all users to access this template
- Global template
> The option `Allow all users to access this template` makes the template broadly available. Without that permission, only authorized users can deploy from it.
```

```quiz score
Q: 4.049. What is the purpose of sealing a template in the recorded Linux template workflow?
- Delete all template disks
* Remove machine-specific details so new instances can initialize their own identity
- Make every user a template administrator
- Assign every clone the same fixed machine identity deliberately
> The recorded workflow seals a Linux template to remove instance-specific configuration. Other guest types require their own preparation procedures.
```

```quiz score
Q: 4.050. When creating a VM from a template, what do you NOT need to provide?
- Memory size EXPORTING VMs
- VM name
- Network interface
* Instance images (disks are already from template)
> When creating a VM from a template, you do not need to provide instance images because the disks come from the template. You still need VM-specific details such as name and placement.
```

## Acknowledgements

- **Author** - OLVM training team
- **Sources** - User-supplied training transcripts, section quiz-bank files, and Lab 7 OLVM content
- **Last Updated** - October 2026
