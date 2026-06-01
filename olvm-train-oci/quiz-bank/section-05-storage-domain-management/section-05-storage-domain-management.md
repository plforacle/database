# Section 5: Storage Domain Management
## Introduction

This lab contains the Section 5 practice questions for **Storage Domain Management** from the OLVM Exam 1Z0-1170 quiz bank.

Estimated Time: 70 minutes

```quiz-config
passing: 75
badge: images/badge.png
```

### Objectives

In this lab, you will review the OLVM concepts covered by this exam objective and check your understanding with practice questions.

## Practice Questions

```quiz

Q: 1. What does OLVM storage provide centralized storage for? (Choose 3)
* A. Virtual machine disk images
- B. User home directories
* C. ISO files
* D. Snapshots
- E. Email archives
> OLVM storage domains centralize the storage used by virtual machine disk images, ISO media, and snapshots. They are not meant to replace general-purpose user home directories or unrelated application archives.

Q: 2. Which storage protocol allows OLVM hosts to access block-level storage over TCP/IP networks?
- A. NFS
* B. iSCSI
- C. SMB
- D. HTTP
> iSCSI provides block-level storage access over TCP/IP networks. Hosts see iSCSI targets as block devices, which makes iSCSI a SAN-style storage option rather than a file-share protocol.

Q: 3. Which storage protocol allows OLVM hosts to connect to fiber channel SAN storage systems?
- A. NFS
- B. iSCSI
* C. FCP (Fiber Channel Protocol)
- D. GlusterFS
> FCP, or Fibre Channel Protocol, is used to access Fibre Channel SAN storage. It provides block storage connectivity through Fibre Channel infrastructure rather than IP-based file sharing.

Q: 4. Which storage protocol is a distributed file system providing scalability and redundancy?
- A. NFS
- B. iSCSI
- C. FCP
* D. GlusterFS
> GlusterFS is a distributed file system designed to combine storage across servers for scale and redundancy. It is file-based storage, not a block protocol like iSCSI or FCP.

Q: 5. Can a Data Center be initialized without a storage domain attached?
- A. Yes, storage is optional
* B. No, at least one storage domain must be attached before initialization
- C. Only in test environments
- D. Only for Self-Hosted Engine
> A Data Center needs at least one storage domain before it can be initialized because VM disks and storage metadata require a storage backing location. Without a storage domain, the Data Center cannot host normal VM storage operations.

Q: 6. Which type of storage does OLVM support? (Choose 2)
* A. Block device storage (SAN)
- B. Tape storage
* C. File system storage (NAS)
- D. Cloud object storage
> OLVM supports both block device storage, such as SAN-backed iSCSI or Fibre Channel, and file system storage, such as NAS-backed NFS or GlusterFS. Tape and object storage are not the storage-domain types tested here.

Q: 7. Which two protocols are examples of block device (SAN) storage? (Choose 2)
- A. NFS
* B. iSCSI
* C. FCP
- D. GlusterFS
> iSCSI and FCP are block storage protocols. They present logical block devices to hosts rather than mounted file shares.

Q: 8. Which two protocols are examples of file system (NAS) storage? (Choose 2)
- A. iSCSI
* B. NFS
- C. FCP
* D. GlusterFS
> NFS and GlusterFS are file-system storage options. Hosts access files and directories through these protocols rather than raw LUN-style block devices.

Q: 9. For VMs to be migrated between hosts, what storage requirement must be met?
- A. Each host needs local storage
* B. VMs must share the same storage domain
- C. Storage must be SSD-based
- D. VMs must use iSCSI only
> VM migration requires the source and destination hosts to access the same VM disks, so the VM must reside on storage shared by those hosts. Local-only storage prevents normal cross-host migration.

Q: 10. Can a storage domain be shared between multiple Data Centers?
- A. Yes, freely shared
* B. No, storage domains cannot be shared between different OLVM Data Centers
- C. Only ISO domains can be shared
- D. Only with special configuration
> A storage domain can belong to only one Data Center at a time. This avoids conflicting metadata ownership and inconsistent assumptions between separate OLVM Data Centers.

Q: 11. What is the Storage Pool Manager (SPM)?
- A. A physical storage device
* B. A management role assigned to one host in the Data Center
- C. A type of storage domain
- D. A backup application
> The Storage Pool Manager is a role assigned to one host in a Data Center. It coordinates storage metadata operations for that Data Center rather than representing a physical storage device.

Q: 12. How many hosts can have the SPM role in a single Data Center at one time?
- A. All hosts share the SPM role equally
* B. Only one host at a time
- C. Two hosts for redundancy
- D. Up to five hosts
> Only one host can hold the SPM role at a time in a Data Center. A single SPM prevents simultaneous, conflicting storage metadata updates.

Q: 13. What is the primary responsibility of the SPM?
- A. Running virtual machines
* B. Coordinating storage metadata operations like creating disks or snapshots
- C. Managing network traffic
- D. User authentication
> The SPM coordinates storage metadata changes such as disk creation and snapshot operations. VM execution happens on hosts generally, but storage metadata coordination is the SPM's special responsibility.

Q: 14. If the host with the SPM role fails, what happens?
- A. All storage operations stop permanently
* B. OLVM automatically assigns the SPM role to another host
- C. The Data Center becomes unavailable
- D. All VMs must be restarted
> If the SPM host fails, OLVM can assign the SPM role to another eligible host. Running VMs do not all need to restart simply because the SPM role changes.

Q: 15. What type of cluster is created when configuring local storage?
- A. Multi-host cluster
* B. Single-host cluster
- C. High-availability cluster
- D. Distributed cluster
> Local storage creates a single-host cluster because the storage is available only to that host. Multi-host clustering requires shared storage that all hosts can reach.

Q: 16. Can VMs in a local storage domain be migrated to other hosts?
- A. Yes, freely migrated
* B. No, migration is not possible
- C. Only with special configuration
- D. Only during maintenance windows
> VMs on local storage cannot be migrated to other hosts because their disks are not shared. Migration depends on the destination host being able to access the same VM storage.

Q: 17. Can VMs in a local storage domain be fenced?
- A. Yes, fencing works normally
* B. No, fencing is not available
- C. Only manual fencing
- D. Only with power management
> Fencing is not available for local storage Data Centers in the way it is for clustered shared-storage environments. Local storage lacks the multi-host HA model that fencing protects.

Q: 18. What happens when you configure a KVM host for local storage if it was previously in a clustered Data Center?
- A. It remains in the cluster
* B. It is automatically removed from the previous Data Center and cluster
- C. Managing network traffic
- C. It joins both clusters
- D. It requires manual removal first
> When a host is configured for local storage, it is removed from its previous clustered Data Center and cluster. Local storage changes the host into a single-host storage design, so it cannot remain in the prior shared cluster.

Q: 19. What is the RECOMMENDED way to configure local storage?
- A. Use the root filesystem
- B. Create a directory on existing partition
* C. Use a separate logical volume or disk
- D. Use network-attached storage
> Using a separate logical volume or disk for local storage isolates VM storage from the root filesystem. This avoids filling the OS disk and gives clearer storage management boundaries.

Q: 20. After creating and mounting a local storage disk, where should it be added to ensure automatic mounting on reboot?
- A. /etc/hosts
* B. /etc/fstab
- C. /etc/mount
- D. /etc/storage
> `/etc/fstab` controls filesystems mounted automatically at boot. Adding the local storage mount there ensures the storage path returns after a host reboot.

Q: 21. What must the host's state be before configuring local storage?
- A. Up
* B. Maintenance
- C. Non-operational
- D. Installing
> The host must be in Maintenance state before configuring local storage. Maintenance mode prevents active workloads from being disrupted while host storage configuration changes are applied.

Q: 22. What is the recommended operating system for OLVM manager and KVM hosts when using NFS storage?
- A. Any Linux distribution
* B. Oracle Linux
- C. Windows Server
- D. FreeBSD
> Oracle Linux is the recommended operating system for OLVM manager and KVM hosts when using NFS storage in this context. Using the supported OS reduces UID/GID, package, and compatibility issues.

Q: 23. What is the default user ID for the VDSM user?
- A. 0
* B. 36
- C. 100
- D. 1000
> The default VDSM user ID is 36. Matching this ID matters for NFS ownership and permissions because hosts must access shared storage consistently.

Q: 24. What is the default group ID for the KVM group?
- A. 0
* B. 36
- C. 100
- D. 1000
> The default KVM group ID is also 36. Consistent group ownership lets QEMU, sanlock, and related virtualization processes access storage correctly.

Q: 25. Which users are members of the KVM group? (Choose 2)
- A. root
* B. QEMU
* C. sanlock
- D. vdsm
> QEMU and sanlock are members of the KVM group because they need access to VM disk files and locking mechanisms. These memberships support safe VM execution on shared storage.

Q: 26. What command is used to install NFS utilities on the NFS server?
- A. yum install nfs
* B. yum install nfs-utils
- C. dnf install nfs-server
- D. yum install nfs-tools
> `yum install nfs-utils` installs the NFS utilities needed on an NFS server. Those tools provide the services and commands required to export NFS shares.

Q: 27. Which services must be added to the firewall for NFS? (Choose 3)
* A. nfs
- B. ssh
- C. nfs3
* D. mountd
* E. rpc-bind
> The firewall must allow `nfs`, `mountd`, and `rpc-bind` services for NFS access. NFS relies on RPC-related services, so opening only one port is often insufficient.

Q: 28. What command reloads the firewall after adding services?
- A. firewall-cmd restart
* B. firewall-cmd --reload
- C. systemctl restart firewalld
- D. service firewall reload
> `firewall-cmd --reload` applies firewall service changes without rebooting the server. After adding NFS services, reloading makes the active firewall configuration reflect the new rules.

Q: 29. What must be the ownership of NFS share directories?
- A. root:root
* B. vdsm:kvm
- C. qemu:sanlock
- D. admin:users
> NFS share directories must be owned by `vdsm:kvm` so OLVM host processes can create, lock, and manage VM storage files. Incorrect ownership is a common cause of storage-domain activation failures.

Q: 30. What is the purpose of the /etc/exports file on an NFS server?
- A. To list exported users
* B. To define NFS export configurations for sharing directories
- C. To configure firewall rules
- D. To manage user permissions
> `/etc/exports` defines which directories are exported over NFS and which clients can access them. It is the core NFS server configuration file for shared directories.

Q: 31. What command applies NFS export configurations?
- A. exportfs -a
* B. exportfs -ra
- C. nfs-export apply
- D. systemctl reload nfs iSCSI STORAGE DOMAIN
> `exportfs -ra` re-exports all NFS shares after configuration changes. It applies updates from `/etc/exports` without requiring a full server reboot.

Q: 32. What must be installed on KVM hosts before using iSCSI storage?
- A. NFS utilities
* B. iSCSI initiator software
- C. Fiber channel drivers
- D. GlusterFS client
> KVM hosts need iSCSI initiator software before they can connect to iSCSI targets. The initiator is the client-side component that discovers, logs into, and uses iSCSI LUNs.

Q: 33. What information is required to discover iSCSI targets? (Choose 2)
* A. IP address of storage server
- B. MAC address of storage server
* C. Port number
- D. DNS hostname only
> To discover iSCSI targets, the host needs the storage server IP address and port number. Those values identify where the iSCSI target service is listening.

Q: 34. What is the default port number for iSCSI?
- A. 22
* B. 3260
- C. 5432
- D. 8080
> The default iSCSI port is 3260. Firewalls and discovery commands must allow or reference this port unless the storage system uses a nondefault configuration.

Q: 35. What authentication method can optionally be used with iSCSI?
- A. Kerberos
- B. LDAP
* C. CHAP
- D. OAuth
> CHAP can optionally authenticate iSCSI sessions. It provides a shared-secret authentication mechanism between initiator and target.

Q: 36. What does LUN stand for in iSCSI terminology?
- A. Local Unit Number
* B. Logical Unit Number
- C. Linux Unified Node
- D. Link Universal Network
> LUN stands for Logical Unit Number. In SAN terminology, a LUN identifies a logical block device presented by the storage target.

Q: 37. What is the purpose of iSCSI multipathing?
- A. To increase storage capacity
* B. To prevent host downtime caused by network path failures
- C. To encrypt data transfers
- D. To compress data
> iSCSI multipathing provides multiple network paths to the same storage target, reducing downtime if one path fails. It is about availability and path resilience, not encryption or compression.

Q: 38. What is an iSCSI bond?
- A. A physical cable connection
* B. A logical connection that aggregates multiple physical and logical connections to iSCSI targets
- C. A storage encryption method
- D. A user authentication mechanism
> An iSCSI bond is a logical aggregation of physical and logical paths to iSCSI targets. It helps OLVM manage multipath connectivity for storage traffic.

Q: 39. How many paths to the same iSCSI target should be selected when configuring multipathing?
- A. Only one path
* B. All paths to the same target
- C. Maximum of two paths
- D. Paths from different targets
> When configuring multipathing, all paths to the same target should be selected so failover and path redundancy work correctly. Selecting paths from different targets would not represent alternate paths to the same storage device.

Q: 40. What is GlusterFS?
- A. A block storage protocol
* B. A distributed file system
- C. A backup application
- D. A virtual machine hypervisor
> GlusterFS is a distributed file system. It aggregates storage across nodes and exposes file-based storage rather than acting as a block protocol or hypervisor.

Q: 41. What format is used to specify the GlusterFS volume when creating a storage domain?
- A. IP:/volume_name
* B. FQDN:/volume_name
- C. volume_name@FQDN
- D. //FQDN/volume_name
> The GlusterFS volume format is `FQDN:/volume_name`. Using a fully qualified domain name helps OLVM resolve the Gluster server consistently.

Q: 42. What port is typically used for GlusterFS management?
- A. 22
- B. 3260
- C. 24007
* D. The transcript does not specify
> Although GlusterFS commonly uses management port 24007, the quiz source notes that the transcript did not specify the port. For this practice question, the correct response is based on what was explicitly taught in the source.

Q: 43. What must be the state of a storage domain before it can be detached from a Data Center?
- A. Active
* B. Maintenance
- C. Locked
- D. Unattached
> A storage domain must be placed in Maintenance state before it can be detached. Maintenance mode ensures active operations stop before the domain is removed from the Data Center.

Q: 44. Can you move a storage domain to maintenance mode if a VM has a lease on it?
- A. Yes, always
* B. No, you cannot
- C. Only if the VM is powered off
- D. Only with admin privileges
> A storage domain with an active VM lease cannot be moved to maintenance. The lease indicates active ownership or dependency, and detaching or maintaining the domain could disrupt the VM.

Q: 45. Can you detach a storage domain from one Data Center and attach it to another?
* A. Yes, but not simultaneously to both
- B. No, once attached it's permanent
- C. Yes, it can be attached to multiple Data Centers
- D. Only ISO domains can be moved
> A storage domain can be detached from one Data Center and later attached to another, but it cannot be attached to both simultaneously. This preserves single Data Center ownership at any given time.

Q: 46. What is the MINIMUM number of storage domains a Data Center must have to remain active?
- A. Zero
* B. One (at least one storage domain)
- C. Two
- D. Three
> A Data Center must have at least one storage domain to remain active. Storage is fundamental to VM disks and Data Center initialization, so zero storage domains is not a valid active state.

Q: 47. Where do you upload ISO images in the Administration Portal?
- A. Compute -> VMs
* B. Storage -> Disks
- C. Network -> Images
- D. Administration -> Media
> ISO images are uploaded from Storage -> Disks in the Administration Portal. The upload workflow treats ISO media as disk/image content managed through the storage interface.

Q: 48. What button is used to begin uploading an ISO image?
- A. New
- B. Import
* C. Upload
- D. Add
> The Upload button starts the image upload workflow. New and Import are different actions and do not begin the browser-based ISO upload in this context.

Q: 49. What must be installed in the browser to successfully upload images without connection errors?
- A. Java plugin
- B. Flash player
* C. Engine CA certificate
- D. ActiveX controls
> The Engine CA certificate must be installed in the browser so the upload client can trust the engine connection. Without that trust, secure image upload operations can fail with connection or certificate errors.

Q: 50. What error might occur if the transfer image client inactivity timeout is too low?
- A. Connection refused
* B. Timeout due to transfer inactivity error
- C. Disk full error
- D. Authentication failure
> If the transfer image client inactivity timeout is too low, uploads can fail with an inactivity timeout. Large images or slow transfers need enough time to avoid being treated as stalled.

Q: 51. In the practice labs using Oracle Cloud Infrastructure, what type of attachment is used for block volumes?
- A. NFS
* B. iSCSI
- C. Paravirtualized
- D. SMB
> In the OCI practice labs, block volumes are attached using iSCSI. This matches the SAN-style block storage workflow taught for OLVM storage domains.

Q: 52. What access mode is typically configured for block volumes attached to storage servers?
- A. Read-only
- B. Write-only
* C. Read-write
- D. No access
> Block volumes attached to storage servers are typically configured read-write so hosts can create and update storage content. Read-only would not support normal VM disk writes.

Q: 53. What command is used to list newly attached disks on a Linux system?
- A. lsblk
* B. fdisk -l
- C. df -h
- D. mount
> `fdisk -l` lists disks and partitions visible to the Linux system. It is useful after attaching block devices to verify that the OS sees the new disks.

Q: 54. After attaching block devices via iSCSI commands, what tool can partition the devices?
- A. parted
- B. fdisk
- C. gdisk
* D. All of the above
> `parted`, `fdisk`, and `gdisk` can all partition block devices, depending on the partition table and administrator preference. The key point is that standard Linux partitioning tools can prepare newly attached devices.

Q: 55. Where are pre-created block volumes located in Oracle Cloud Infrastructure?
- A. Compute -> Instances
* B. Storage -> Block Volumes
- C. Network -> Storage
- D. Database -> Volumes
> In Oracle Cloud Infrastructure, pre-created block volumes are found under Storage -> Block Volumes. That is where OCI exposes block storage resources before they are attached to compute instances.

```

## Acknowledgements

- **Source** - OLVM_Practice_Tests_Combined.pdf
- **Generated From** - Oracle Linux Virtualization Manager Exam 1Z0-1170 practice test collection
