# OLVM Self-Study: Storage Domains and Networking

## Introduction

This module contains **95 scored practice questions** for the storage domains and networking objective, which accounts for **19%** of the exam weighting supplied for this training. Use it after the instructor-led Lab 7 session and while reviewing the supplied course transcripts.

Estimated Time: 95-133 minutes, including explanation review

```quiz-config
passing: 80
badge: images/badge.png
```

### Objectives

- Describe OLVM Storage
- Configure Storage Domains
- Explain OLVM Networking Recommendations
- Configure Bonds, VLANs and Logical Networks
- Associate Logical Networks with Virtual Functions
- Configure a VM vNIC with a Specific HW-ADDR

### How to Practice

Choose **one option per question**, including questions whose choices contain a set of items. Click **Check Answer** to see feedback and the explanation. Answer all 95 questions; at least **76 correct answers** unlock this module's badge. The 80% target is a study milestone, not an official exam passing score.

Record your first-attempt score before using **Try Again**. Review the corresponding transcript clip or Lab 7 task, explain the reasoning, and retry after studying. Scores are tracked within this module; keep your own first-attempt record before navigating away.

The recorded course uses older OL8 and OLVM examples than Lab 7. Questions explicitly referring to the recorded course use that context. Match package, identity, and UI procedures to the actual practice environment.

[Study guide and coverage map](../../workshops/olvm-study/index.html?lab=study-guide)

## Task 1: Describe OLVM Storage

```quiz score
Q: 3.001. What does OLVM storage provide centralized storage for? Select the option listing the complete correct set.
- User home directories; ISO files; Snapshots
- Virtual machine disk images; ISO files; User home directories
* Virtual machine disk images; ISO files; Snapshots
- Virtual machine disk images; User home directories; Snapshots
> OLVM storage domains centralize the storage used by virtual machine disk images, ISO media, and snapshots. They are not meant to replace general-purpose user home directories or unrelated application archives.
```

```quiz score
Q: 3.002. Which storage protocol allows OLVM hosts to access block-level storage over TCP/IP networks?
- HTTP
- NFS
- SMB
* iSCSI
> iSCSI provides block-level storage access over TCP/IP networks. Hosts see iSCSI targets as block devices, which makes iSCSI a SAN-style storage option rather than a file-share protocol.
```

```quiz score
Q: 3.003. Which storage protocol allows OLVM hosts to connect to fiber channel SAN storage systems?
- NFS
- iSCSI
- GlusterFS
* FCP (Fiber Channel Protocol)
> FCP, or Fibre Channel Protocol, is used to access Fibre Channel SAN storage. It provides block storage connectivity through Fibre Channel infrastructure rather than IP-based file sharing.
```

```quiz score
Q: 3.004. In the recorded course, which storage protocol is a distributed file system providing scalability and redundancy?
- NFS
- FCP
* GlusterFS
- iSCSI
> GlusterFS is a distributed file system designed to combine storage across servers for scale and redundancy. It is file-based storage, not a block protocol like iSCSI or FCP. These examples describe the recorded training baseline; verify applicable capabilities in the practice environment.
```

```quiz score
Q: 3.005. In the recorded course, what is GlusterFS?
- A block storage protocol
* A distributed file system
- A virtual machine hypervisor
- A backup application
> GlusterFS is a distributed file system. It aggregates storage across nodes and exposes file-based storage rather than acting as a block protocol or hypervisor. These examples describe the recorded training baseline; verify applicable capabilities in the practice environment.
```

```quiz score
Q: 3.006. In the recorded course, what format is used to specify the GlusterFS volume when creating a storage domain?
- //FQDN/volume_name
* FQDN:/volume_name
- volume_name@FQDN
- IP:/volume_name
> The GlusterFS volume format is `FQDN:/volume_name`. Using a fully qualified domain name helps OLVM resolve the Gluster server consistently. These examples describe the recorded training baseline; verify applicable capabilities in the practice environment.
```

## Task 2: Configure Storage Domains

```quiz score
Q: 3.007. Can a Data Center be initialized without a storage domain attached?
* No, at least one storage domain must be attached before initialization
- Yes, storage is optional
- Only in test environments
- Only for Self-Hosted Engine
> A Data Center needs at least one storage domain before it can be initialized because VM disks and storage metadata require a storage backing location. Without a storage domain, the Data Center cannot host normal VM storage operations.
```

```quiz score
Q: 3.008. Which type of storage does OLVM support? Select the option listing the complete correct set.
- Tape storage; File system storage (NAS)
* Block device storage (SAN); File system storage (NAS)
- Cloud object storage; File system storage (NAS)
- Block device storage (SAN); Tape storage
> OLVM supports both block device storage, such as SAN-backed iSCSI or Fibre Channel, and file system storage, such as NAS-backed NFS or GlusterFS. Tape and object storage are not the storage-domain types tested here.
```

```quiz score
Q: 3.009. Which two protocols are examples of block device (SAN) storage? Select the option listing the complete correct set.
- NFS; FCP
* iSCSI; FCP
- iSCSI; NFS
- GlusterFS; FCP
> iSCSI and FCP are block storage protocols. They present logical block devices to hosts rather than mounted file shares.
```

```quiz score
Q: 3.010. Which two protocols are examples of file system (NAS) storage? Select the option listing the complete correct set.
* NFS; GlusterFS
- FCP; GlusterFS
- NFS; iSCSI
- iSCSI; GlusterFS
> NFS and GlusterFS are file-system storage options. Hosts access files and directories through these protocols rather than raw LUN-style block devices.
```

```quiz score
Q: 3.011. A migration fails because the destination cannot reach the source VM's shared disks. What must be fixed?
- The engine's quiz passing percentage
* Destination host access to the relevant active storage domain
- The VM's desktop resolution
- The order of user groups in the portal
> Lab 7's host migration depends on both hosts reaching the VM's shared storage. Network and compute compatibility also matter.
```

```quiz score
Q: 3.012. Before moving a running VM disk to another storage domain in Lab 7, what should be checked?
- That all hosts have been shut down first
- Only that its name is shorter than the source domain's name
- That it belongs simultaneously to two unrelated data centers
* The destination is active, accessible, and has enough usable capacity
> Lab 7 Task 6 validates the destination before storage live migration. The disk move depends on usable storage, not just a listed domain name.
```

```quiz score
Q: 3.013. A disk creation operation waits for storage coordination. Which role should be investigated?
- SMTP relay owner
- Browser certificate importer
* Storage Pool Manager
- Guest desktop user
> SPM coordinates storage metadata operations such as disk and snapshot creation. This is different from the path used for guest application traffic.
```

```quiz score
Q: 3.014. Why should an administrator expect only one active SPM role in a data center?
- A browser can manage only one host at a time
- Every storage LUN must have an independent engine server
- Every running VM needs a separate SPM host
* Metadata coordination is serialized through one selected host
> SPM is a data-center storage coordination role assigned to one host at a time, rather than a separate role per VM or LUN.
```

```quiz score
Q: 3.015. Why check available capacity before a live storage migration?
* The destination needs room for the disk data being moved
- A storage migration always reduces every disk to zero size
- The engine can manufacture physical storage space automatically
- The source domain never contains actual disk data
> Lab 7 Task 6 moves disk data to a destination domain. Capacity checks are necessary even when a disk uses sparse allocation.
```

```quiz score
Q: 3.016. An SPM host fails, and another eligible host can reach the storage. What storage-management recovery is expected?
- All storage domains are automatically reformatted
- The remaining host must create a new independent engine database
* Another host can be selected to take the SPM role
- The engine converts ISO files into VM disks
> SPM can move to an eligible host. Recovery of VMs that ran on the failed host is a separate HA concern.
```

```quiz score
Q: 3.017. What type of cluster is created when configuring local storage?
- High-availability cluster
- Multi-host cluster
* Single-host cluster
- Distributed cluster
> Local storage creates a single-host cluster because the storage is available only to that host. Multi-host clustering requires shared storage that all hosts can reach.
```

```quiz score
Q: 3.018. Why is cross-host live migration unavailable in the course's single-host local-storage data center?
- Local disks cannot hold virtual machines
- The engine cannot manage the local host at all
- Every local-storage VM automatically replicates itself to another cluster
* There is no eligible second host sharing the VM's local disks in that arrangement
> The course describes a single-host local-storage arrangement. This restriction differs from exporting or otherwise relocating a VM through a separate supported workflow.
```

```quiz score
Q: 3.019. What is the RECOMMENDED way to configure local storage?
- Use the root filesystem
- Use network-attached storage
- Create a directory on existing partition
* Use a separate logical volume or disk
> Using a separate logical volume or disk for local storage isolates VM storage from the root filesystem. This avoids filling the OS disk and gives clearer storage management boundaries.
```

```quiz score
Q: 3.020. After creating and mounting a local storage disk, where should it be added to ensure automatic mounting on reboot?
- /etc/storage
* /etc/fstab
- /etc/mount
- /etc/hosts
> `/etc/fstab` controls filesystems mounted automatically at boot. Adding the local storage mount there ensures the storage path returns after a host reboot.
```

```quiz score
Q: 3.021. What must the host's state be before configuring local storage?
- Non-operational
- Up
- Installing
* Maintenance
> The host must be in Maintenance state before configuring local storage. Maintenance mode prevents active workloads from being disrupted while host storage configuration changes are applied.
```

```quiz score
Q: 3.022. Why does identity mapping matter on NFS storage used by OLVM hosts?
- Every portal user must be the NFS export owner
* Host-side services need the expected ownership and access permissions on exported storage
- NFS user IDs determine each VM's VLAN ID
- NFS ownership replaces engine authentication
> The NFS section configures ownership for host services. Matching numeric identities and permissions matters across the systems accessing an export.
```

```quiz score
Q: 3.023. A storage export has the expected user name but wrong numeric group ownership. What issue may result?
* Host services may lack the required access despite matching displayed names
- The physical CPU loses virtualization support
- All VM MAC addresses become identical
- The engine automatically changes every user's role
> Storage access relies on effective ownership and permissions, including numeric IDs. A matching displayed name alone is insufficient.
```

```quiz score
Q: 3.024. Why do virtualization and locking services need appropriate storage access?
- They must disable all NFS export checks
- They must store all passwords in VM snapshots
- They must authenticate as every portal user
* They must perform disk I/O and storage coordination using their service identities
> The storage course describes service identities and permissions. Correct access supports guest disk use and locking; it is not portal authorization.
```

```quiz score
Q: 3.025. What command is used to install NFS utilities on the NFS server?
- yum install nfs
- dnf install nfs-server
* yum install nfs-utils
- yum install nfs-tools
> `yum install nfs-utils` installs the NFS utilities needed on an NFS server. Those tools provide the services and commands required to export NFS shares.
```

```quiz score
Q: 3.026. An NFS storage domain cannot be attached because required server services are unreachable. What should be reviewed?
- Only Grafana's database password
- Only SMTP relay authentication
* The firewall paths for NFS and the related services used by that NFS configuration
- Only the VM's console display protocol
> The course's NFS example permits NFS, mountd, and rpc-bind. Required services depend on the NFS configuration; an open web portal does not prove storage connectivity.
```

```quiz score
Q: 3.027. What command reloads the firewall after adding services?
* firewall-cmd --reload
- systemctl restart firewalld
- service firewall reload
- firewall-cmd restart
> `firewall-cmd --reload` applies firewall service changes without rebooting the server. After adding NFS services, reloading makes the active firewall configuration reflect the new rules.
```

```quiz score
Q: 3.028. What must be the ownership of NFS share directories?
- qemu:sanlock
* vdsm:kvm
- admin:users
- root:root
> NFS share directories must be owned by `vdsm:kvm` so OLVM host processes can create, lock, and manage VM storage files. Incorrect ownership is a common cause of storage-domain activation failures.
```

```quiz score
Q: 3.029. What is the purpose of the /etc/exports file on an NFS server?
- To list exported users
* To define NFS export configurations for sharing directories
- To manage user permissions
- To configure firewall rules
> `/etc/exports` defines which directories are exported over NFS and which clients can access them. It is the core NFS server configuration file for shared directories.
```

```quiz score
Q: 3.030. What command applies NFS export configurations?
- exportfs -a
- systemctl reload nfs iSCSI STORAGE DOMAIN
- nfs-export apply
* exportfs -ra
> `exportfs -ra` re-exports all NFS shares after configuration changes. It applies updates from `/etc/exports` without requiring a full server reboot.
```

```quiz score
Q: 3.031. What must be installed on KVM hosts before using iSCSI storage?
- GlusterFS client
- Fiber channel drivers
- NFS utilities
* iSCSI initiator software
> KVM hosts need iSCSI initiator software before they can connect to iSCSI targets. The initiator is the client-side component that discovers, logs into, and uses iSCSI LUNs.
```

```quiz score
Q: 3.032. What information is required to discover iSCSI targets? Select the option listing the complete correct set.
- DNS hostname only; Port number
* IP address of storage server; Port number
- IP address of storage server; MAC address of storage server
- MAC address of storage server; Port number
> To discover iSCSI targets, the host needs the storage server IP address and port number. Those values identify where the iSCSI target service is listening.
```

```quiz score
Q: 3.033. What is the default port number for iSCSI?
- 22
- 5432
- 8080
* 3260
> The default iSCSI port is 3260. Firewalls and discovery commands must allow or reference this port unless the storage system uses a nondefault configuration.
```

```quiz score
Q: 3.034. What authentication method can optionally be used with iSCSI?
* CHAP
- Kerberos
- OAuth
- LDAP
> CHAP can optionally authenticate iSCSI sessions. It provides a shared-secret authentication mechanism between initiator and target.
```

```quiz score
Q: 3.035. What does LUN stand for in iSCSI terminology?
* Logical Unit Number
- Local Unit Number
- Link Universal Network
- Linux Unified Node
> LUN stands for Logical Unit Number. In SAN terminology, a LUN identifies a logical block device presented by the storage target.
```

```quiz score
Q: 3.036. What is the purpose of iSCSI multipathing?
- To encrypt data transfers
* To prevent host downtime caused by network path failures
- To compress data
- To increase storage capacity
> iSCSI multipathing provides multiple network paths to the same storage target, reducing downtime if one path fails. It is about availability and path resilience, not encryption or compression.
```

```quiz score
Q: 3.037. What is the purpose of an OLVM iSCSI bond as discussed in storage management?
- Creating a hardware RAID array inside the engine database
- Renaming a physical NIC without affecting storage paths
* Associating logical networks and storage connections for iSCSI connectivity
- Combining only guest console passwords
> An iSCSI bond concerns logical networks and storage connections. It is distinct from an Ethernet NIC bond, which combines host interfaces.
```

```quiz score
Q: 3.038. Which path selection supports useful iSCSI multipathing?
- Several copies of the same failed route only
- Only the browser-to-engine HTTPS path
* Valid paths to the same target that provide independent connectivity where available
- Paths to unrelated LUNs treated as one guest disk
> Multipathing provides alternative access paths to the storage. Validate that the chosen paths reach the intended target and provide meaningful path resilience.
```

```quiz score
Q: 3.039. What must be the state of a storage domain before it can be detached from a Data Center?
- Unattached
* Maintenance
- Active
- Locked
> A storage domain must be placed in Maintenance state before it can be detached. Maintenance mode ensures active operations stop before the domain is removed from the Data Center.
```

```quiz score
Q: 3.040. Why must VM leases and storage dependencies be checked before putting a domain into maintenance?
* Active dependencies can prevent the transition and must be resolved appropriately
- Maintenance always ignores active leases
- A lease is merely a browser preference
- A lease means the domain contains no usable data
> The storage lesson discusses leases when changing domain state. Verify the actual VM and domain dependencies before deactivating storage.
```

```quiz score
Q: 3.041. Can you detach a storage domain from one Data Center and attach it to another?
- No, once attached it's permanent
- Yes, it can be attached to multiple Data Centers
* Yes, but not simultaneously to both
- Only ISO domains can be moved
> A storage domain can be detached from one Data Center and later attached to another, but it cannot be attached to both simultaneously. This preserves single Data Center ownership at any given time.
```

```quiz score
Q: 3.042. How should a storage live migration be verified after completion?
* Confirm the disk's new storage location and the workload's continued operation
- Assume success because the destination domain appears in the menu
- Delete the source domain before checking the disk
- Check only that the engine browser refreshed
> Lab 7 Task 6 checks disk placement and workload continuity. An operation request is not proof that the move finished successfully.
```

```quiz score
Q: 3.043. Where do you upload ISO images in the Administration Portal?
- Compute -> VMs
* Storage -> Disks
- Network -> Images
- Administration -> Media
> ISO images are uploaded from Storage -> Disks in the Administration Portal. The upload workflow treats ISO media as disk/image content managed through the storage interface.
```

```quiz score
Q: 3.044. A browser image upload fails because the transfer endpoint certificate is untrusted. What should be checked?
- Whether every host is the SPM simultaneously
- Whether SMTP uses port 25
* Trust in the appropriate engine CA and validation of the transfer endpoint's identity
- Only the VM's guest hostname
> The ISO upload procedure includes browser trust preparation. Certificate-chain trust and endpoint identity both matter for secure transfer connectivity.
```

```quiz score
Q: 3.045. What error might occur if the transfer image client inactivity timeout is too low?
- Connection refused
- Authentication failure
- Disk full error
* Timeout due to transfer inactivity error
> If the transfer image client inactivity timeout is too low, uploads can fail with an inactivity timeout. Large images or slow transfers need enough time to avoid being treated as stalled.
```

```quiz score
Q: 3.046. An iSCSI target is expected to serve writable VM disks. Which backing-device access is needed?
- No access to the backing devices
* Appropriate read-write access for the target's intended disk operations
- Read-only access regardless of workload
- Access only through a portal user's browser
> Writable VM disks require a backing storage path that permits the required writes. This is a storage service requirement, separate from VM Portal permissions.
```

```quiz score
Q: 3.047. What command is used to list newly attached disks on a Linux system?
* fdisk -l
- df -h
- mount
- lsblk
> `fdisk -l` lists disks and partitions visible to the Linux system. It is useful after attaching block devices to verify that the OS sees the new disks.
```

## Task 3: Explain OLVM Networking Recommendations

```quiz score
Q: 3.048. What does a host NIC bond provide?
- A storage domain inside every network adapter
* One logical interface built from multiple physical NICs, with behavior determined by its bond mode
- An automatic guarantee that every single flow uses all link speeds added together
- A replacement for all physical switch configuration
> Bonding combines interfaces. Redundancy and traffic distribution depend on the chosen mode and the network configuration.
```

```quiz score
Q: 3.049. Why is network bonding recommended especially on production hosts?
- To increase security only
- To simplify configuration
* To ensure high availability and fault tolerance
- To reduce costs
> Bonding is recommended on production hosts because it improves availability and fault tolerance. If a cable, NIC, or switch path fails, a properly designed bond can keep traffic flowing through another link.
```

```quiz score
Q: 3.050. What is the purpose of using VLANs in OLVM?
* To segment network traffic logically for enhanced security and performance
- To reduce the number of physical switches needed
- To increase network speed
- To enable wireless connectivity
> VLANs logically separate traffic on shared physical infrastructure. In OLVM, that separation can isolate management, storage, migration, and user or VM traffic for better security and performance.
```

```quiz score
Q: 3.051. Which types of traffic can be separated using VLANs? Select the option listing the complete correct set.
- Management network traffic; Email traffic; User traffic
- Email traffic; Storage network traffic; User traffic
- Management network traffic; Storage network traffic; Email traffic
* Management network traffic; Storage network traffic; User traffic
> VLANs can separate management, storage, and user traffic into different broadcast domains. Email and DNS traffic may traverse networks, but they are not the OLVM traffic categories tested here.
```

```quiz score
Q: 3.052. When mapping a VLAN-backed logical network to a host, what must be consistent?
- The VLAN ID and every user's password length
- The guest kernel name and the engine database name
* The host NIC or bond mapping and the physical network's VLAN configuration
- The VM disk size and the switch port number
> The logical network must be carried through the chosen host uplink and the physical network. A VLAN can be mapped using the appropriate NIC or bond design.
```

```quiz score
Q: 3.053. What does FQDN stand for?
- File Query Domain Network
- Forward Query DNS Name
- Fast Query Domain Name
* Fully Qualified Domain Name
> FQDN means Fully Qualified Domain Name. It is the complete DNS name that identifies a host within its DNS hierarchy, such as `host.example.com`.
```

```quiz score
Q: 3.054. Which two types of name resolution must OLVM support? Select the option listing the complete correct set.
- Broadcast name resolution; Reverse name resolution (IP to domain name)
- Forward name resolution (domain name to IP); Lateral name resolution
- Lateral name resolution; Reverse name resolution (IP to domain name)
* Forward name resolution (domain name to IP); Reverse name resolution (IP to domain name)
> OLVM needs both forward and reverse DNS resolution. Forward resolution maps names to IP addresses, while reverse resolution maps IP addresses back to names, which helps with host identity and service trust.
```

```quiz score
Q: 3.055. What is a useful design goal for DNS used by the engine and hosts?
- Every guest must host a complete copy of the engine database
* DNS remains available when the managed virtualization environment has an outage
- DNS starts only after every VM has been recovered
- The engine browser cache is the sole DNS server
> The networking section warns against dependency loops. Design DNS availability so management and recovery do not depend solely on unavailable managed guests.
```

```quiz score
Q: 3.056. Why is a DNS dependency loop risky during recovery?
- Host fencing requires a guest desktop wallpaper
- DNS automatically deletes every storage domain
* Management services may require name resolution before the VM providing DNS can be started
- A VLAN tag is stored only in DNS
> Name resolution is a management dependency. If its only provider cannot run until management is restored, recovery becomes harder.
```

## Task 4: Configure Bonds, VLANs and Logical Networks

```quiz score
Q: 3.057. What are logical networks in OLVM?
* Representations of network resources that provide connectivity for KVM virtual machines
- Virtual switches only
- Physical network cables
- Network security policies
> Logical networks are OLVM representations of network connectivity used by hosts and VMs. They abstract the physical network into managed networks that can be assigned roles and attached to interfaces.
```

```quiz score
Q: 3.058. At what level are network roles assigned to logical networks?
- Data Center level
- Host level
* Cluster level
- VM level
> Network roles are assigned at the cluster level. This makes the role consistent for hosts in that cluster and aligns network behavior with cluster-wide VM placement and migration decisions.
```

```quiz score
Q: 3.059. What is the purpose of a Management network role?
- Running VMs
- Providing internet access
- Storing backups
* Handling management traffic between OLVM engine and KVM hosts for administrative tasks
> The Management role carries traffic between the OLVM engine and KVM hosts for administrative control. Keeping management traffic reliable is critical because the engine uses it to monitor and control hosts.
```

```quiz score
Q: 3.060. What is the purpose of a VM network role?
- Live migration only
- Managing the engine
* Carrying traffic generated by virtual machines
- Storage access
> A VM network carries traffic generated by virtual machines. This is the network path guest operating systems use for application, user, or service connectivity.
```

```quiz score
Q: 3.061. What is the purpose of a Display network role?
- Managing VMs
- Storing VM images
* Handling display traffic such as graphical display output of virtual machines
- Network monitoring
> The Display network carries graphical console traffic, such as SPICE or VNC display output. Separating display traffic can prevent console sessions from competing with management or migration traffic.
```

```quiz score
Q: 3.062. What is the purpose of a Migration network role?
- Backup operations
- User authentication
- Moving physical servers
* Handling live migration traffic when VMs are moved between hosts without downtime
> The Migration network carries live migration traffic when VMs move between hosts. This traffic can be bandwidth-heavy, so separating it helps protect management and VM networks from migration bursts.
```

```quiz score
Q: 3.063. What is the name of the default logical network automatically created during OLVM setup?
- cluster_network
- management_net
- default_network
* ovirtmgmt
> `ovirtmgmt` is the default logical network created during OLVM setup. It gives the environment an initial management network for engine-to-host communication.
```

```quiz score
Q: 3.064. What role is typically assigned to the ovirtmgmt network?
- Migration role
* Management role
- Storage role
- Display role
> `ovirtmgmt` typically has the Management role. It is the default network used for management communication between the engine and hosts.
```

```quiz score
Q: 3.065. What does the course's default-route network selection control for a host?
* Which logical network provides the host's default-route role in that configuration
- Which VM owns the SPM role
- Which database stores historical metrics
- How many total IP routes Linux can ever contain
> The course permits one default-route logical-network role in its host configuration. That does not mean Linux cannot support more complex routing arrangements.
```

```quiz score
Q: 3.066. A required logical network is missing or not properly attached on a host. What is a likely OLVM consequence?
- The engine deletes all guest disk data automatically
- The host always reboots its physical power controller immediately
- The requirement has no effect on host readiness
* The host may be marked non-operational until the required configuration is restored
> Required network configuration matters to cluster readiness. A general connectivity outage also needs diagnosis; not every network fault has one universal status outcome.
```

```quiz score
Q: 3.067. For VM networks, what is created on the host for each logical network?
- A VLAN tag
* A bridge (virtual switch)
- A firewall rule
- A routing table
> For each VM logical network, OLVM creates a bridge on the host. The bridge acts as the host-side virtual switch for VM network attachment.
```

```quiz score
Q: 3.068. What does a network bridge act as on a KVM host?
* A virtual switch connecting VMs to the physical network
- A load balancer
- A firewall
- A router
> A bridge acts like a virtual switch, connecting VM interfaces to the host's physical network path. It forwards traffic between VM VNICs and the external network.
```

```quiz score
Q: 3.069. In bridge network configuration, what do VMs use to connect to bridges?
- Wireless connections
* VNICs (Virtual Network Interface Cards)
- USB adapters
- Physical NICs directly
> VMs connect to host bridges through VNICs. The VNIC is the virtual network adapter presented to the guest operating system.
```

```quiz score
Q: 3.070. Can a single VM have multiple VNICs connected to different bridges?
- Only on physical hosts
- No, only one VNIC per VM
* Yes, VMs can have multiple VNICs connected to different bridges
- Only in special configurations
> A VM can have multiple VNICs, and each VNIC can connect to a different bridge or logical network. This allows one VM to participate in multiple networks, such as application and backup networks.
```

```quiz score
Q: 3.071. How do VMs on different hosts in the same logical network communicate?
- Only through storage domains
* Through the physical network infrastructure that connects the hosts
- Only through the engine
- They cannot communicate
> VMs on different hosts communicate through the physical network infrastructure that connects those hosts. The engine manages configuration, but it does not forward normal VM traffic.
```

```quiz score
Q: 3.072. What is a VLAN identified by?
* VLAN ID
- MAC address
- Hostname
- IP address
> A VLAN is identified by its VLAN ID. Switches and host interfaces use that ID to tag and separate traffic.
```

```quiz score
Q: 3.073. A VLAN logical network works on one host but not another. Which physical-network check is relevant?
* Whether the intended VLAN is carried on both hosts' uplink paths
- Whether both guests have identical local user IDs
- Whether the engine backup filename includes the VLAN ID
- Whether both browsers use the same default font
> OLVM logical configuration must match the physical network. Verify VLAN availability along the affected host's uplink path.
```

```quiz score
Q: 3.074. If a host does not have enough physical NICs, what can be used instead?
- Virtual NICs only
- Additional virtual machines
* VLAN configurations to create multiple virtual LANs
- Bonded interfaces only
> When there are not enough physical NICs, VLANs can create multiple virtual LANs over the available physical links. This allows traffic separation without requiring a separate NIC for every network.
```

```quiz score
Q: 3.075. Why must VLANs be configured on the physical network infrastructure before use in OLVM?
- To reduce costs
- To increase security only
* To support logical networks that require VLANs for traffic segregation
- To enable wireless access VIRTUAL NICS (VNICs)
> The physical network must be configured for the VLANs before OLVM uses them. If switches and trunks do not carry the VLAN IDs, OLVM logical networks with VLAN tagging will not pass traffic correctly.
```

```quiz score
Q: 3.076. What does a VM use to connect to a logical network?
- Serial port
- USB adapter
- Physical NIC directly
* VNIC (Virtual Network Interface Controller)
> A VM uses a VNIC to connect to a logical network. The guest OS sees the VNIC as its network adapter.
```

```quiz score
Q: 3.077. In ordinary bridged VM networking, how does the vNIC reach the host's logical VM network?
- Only through the SMTP relay
- Only through the engine history database
* Through the corresponding host bridge
- Through a required direct assignment of the whole physical NIC
> The networking course describes vNICs connected to host bridges for ordinary VM networks. Passthrough and SR-IOV are different arrangements.
```

```quiz score
Q: 3.078. What does Oracle Linux Virtualization Manager automatically assign to a VNIC?
- VLAN ID
- Hostname
- IP address
* MAC address
> OLVM automatically assigns a MAC address to a VNIC. The MAC address provides Layer 2 identity for the virtual network adapter.
```

```quiz score
Q: 3.079. What does each MAC address assigned to a VNIC correspond to?
- All VNICs on a host
- Multiple VNICs
* A single VNIC
- All VNICs in a cluster
> Each assigned MAC address corresponds to a single VNIC. Unique MAC addresses prevent Layer 2 identity conflicts on the network.
```

```quiz score
Q: 3.080. What is the purpose of the MAC address on a VNIC?
- Storage access
- Encryption
- Routing only
* Identifying the VNIC on the network and ensuring proper communication
> The VNIC MAC address identifies that virtual adapter on the network and allows Ethernet communication to work correctly. It is not used for encryption or storage access.
```

```quiz score
Q: 3.081. A host bond keeps its logical interface available after one link fails. What benefit does this illustrate?
- Automatic guest authentication
- A guarantee that no network outage is ever possible
- Automatic disk encryption
* Network link redundancy
> A correctly designed bond can provide alternate link connectivity. The remaining path and upstream network still need to function.
```

```quiz score
Q: 3.082. A bridged VM network uses a bonded host uplink. Which relationship is correct?
- The bond becomes an engine database schema
* The bridge connects VM networking to the bond carrying the uplink traffic
- Each guest must be assigned the entire physical bond directly
- The bridge removes all VLAN configuration from the physical switch
> The course describes a bridge over the bonded interface. Host networking and physical switching must remain aligned.
```

```quiz score
Q: 3.083. Which statement about bonding and throughput is accurate?
- Bonding guarantees zero latency
- Bonding removes the need for spare network capacity
* Throughput gains depend on the bond mode, switch support, and traffic pattern
- Every flow always receives the sum of every NIC's line rate
> Bonding can improve resilience and aggregate capacity under suitable conditions. It does not guarantee summed bandwidth for each flow.
```

```quiz score
Q: 3.084. After migrating a VM between hosts, which guest-network check is useful?
- Assign it to an unrelated VLAN without updating the guest
* Confirm connectivity on the intended logical network from the destination host
- Change its MAC address automatically for every migration
- Remove all of its vNICs to test connectivity
> Lab 7 Tasks 3 and 5 map the VM network to both hosts and validate the running workload. The destination must provide the required network connectivity.
```

## Task 5: Associate Logical Networks with Virtual Functions

```quiz score
Q: 3.085. What does pass-through networking allow?
- Faster VM creation
* Network traffic to bypass the software network stack, providing direct access to physical NICs
- Automatic IP assignment
- Encrypted connections only
> Pass-through networking lets VM traffic bypass much of the host software network stack and access a physical NIC more directly. This can improve performance for workloads that need lower latency or higher throughput.
```

```quiz score
Q: 3.086. What is a benefit of pass-through networking?
- Lower cost
- Easier configuration
- More VMs per host
* Reduced latency and improved performance
> The main benefit of pass-through networking is reduced latency and improved performance. Direct hardware access removes some virtualization overhead compared with bridged virtual networking.
```

```quiz score
Q: 3.087. What is a limitation of VMs with pass-through NICs?
* They are less flexible in terms of live migration because specific physical hardware must be available on the target
- They require more CPU cores
- They use more memory
- They cannot connect to networks
> A VM using a pass-through NIC is less flexible for live migration because the destination host must have compatible physical hardware available. That hardware dependency can prevent or constrain migration.
```

```quiz score
Q: 3.088. Which hardware capability is relevant to assigning a network device directly to a VM?
- Only the browser's graphics acceleration
- Only the SMTP AUTH capability
- Only the guest filesystem's sparse-file support
* IOMMU support, such as Intel VT-d or AMD-Vi
> Directed I/O and IOMMU capability support device assignment. Intel VT-d is an example of IOMMU technology, not a separate unrelated requirement.
```

```quiz score
Q: 3.089. How does whole-NIC passthrough differ from SR-IOV virtual function assignment?
- Both methods always assign the same whole device simultaneously to every guest
* Whole-NIC passthrough dedicates that device, while SR-IOV exposes separate VFs for assignment
- Whole-NIC passthrough uses the engine history database as its network path
- SR-IOV removes the physical NIC from the host permanently
> The course distinguishes direct device use from SR-IOV's virtual functions. Dedicated physical-function access and assigning individual VFs have different sharing implications.
```

```quiz score
Q: 3.090. What does SR-IOV allow?
- Faster storage access
- Multiple operating systems on one VM
- Encrypted networking
* A single physical network interface to be divided into multiple virtual interfaces (virtual functions)
> SR-IOV lets one physical NIC expose multiple virtual functions that can be assigned to VMs. This provides more direct hardware-backed networking while allowing a single adapter to serve multiple consumers.
```

```quiz score
Q: 3.091. How do you display virtual functions associated with network interfaces in the Administration Portal?
- Click Enable VF
* Click Show Virtual Functions button
- Edit host properties
- Create new network
> The Administration Portal provides a Show Virtual Functions button to display virtual functions associated with network interfaces. This exposes the SR-IOV resources available for assignment.
```

```quiz score
Q: 3.092. What is the purpose of associating an OLVM logical network with an SR-IOV virtual function arrangement?
- To give the VM a second engine management database
* To make the intended network available through the appropriate VF mapping for VM use
- To grant every portal user access to that VM
- To merge unrelated storage LUNs
> The networking section associates logical networks with virtual functions. Exact UI controls vary; verify the PF, VF availability, and network association in the environment.
```

## Task 6: Configure a VM vNIC with a Specific HW-ADDR

```quiz score
Q: 3.093. Where in the Administration Portal do you configure VM network interfaces?
- Network -> Interfaces
- Storage -> Networks
* Compute -> Virtual Machines -> Network Interfaces tab
- Configuration -> NICs
> VM network interfaces are configured from Compute -> Virtual Machines -> Network Interfaces. That tab is where administrators add, edit, and inspect VNICs for a VM.
```

```quiz score
Q: 3.094. What can you configure for a VM's network interface? Select the option listing the complete correct set.
* Custom MAC address; MAC address from pool
- Custom MAC address; CPU allocation
- Memory size; MAC address from pool
- CPU allocation; MAC address from pool
> A VM interface can use a custom MAC address or a MAC address assigned from the configured pool. These options control the Layer 2 identity of the VM's VNIC.
```

```quiz score
Q: 3.095. In the recorded custom-MAC procedure, what should be checked after applying the change and starting the VM?
- Every VM now shares that MAC address
- The engine database has been renamed
* The vNIC uses the intended unique MAC address and still has network connectivity
- The guest disk automatically moved to another domain
> The course demonstrates a start or restart after changing the MAC. Verify the resulting address and connectivity; do not assume every configuration supports the same runtime change behavior.
```

## Acknowledgements

- **Author** - OLVM training team
- **Sources** - User-supplied training transcripts, section quiz-bank files, and Lab 7 OLVM content
- **Last Updated** - October 2026
