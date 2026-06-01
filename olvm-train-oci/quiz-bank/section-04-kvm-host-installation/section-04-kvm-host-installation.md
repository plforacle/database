# Section 4: KVM Host Installation
## Introduction

This lab contains the Section 4 practice questions for **KVM Host Installation** from the OLVM Exam 1Z0-1170 quiz bank.

Estimated Time: 60 minutes

```quiz-config
passing: 75
badge: images/badge.png
```

### Objectives

In this lab, you will review the OLVM concepts covered by this exam objective and check your understanding with practice questions.

## Practice Questions

```quiz

Q: 1. What is the minimum Oracle Linux version required for a KVM host?
- A. Oracle Linux 7.5
* B. Oracle Linux 8.5 or later
- C. Oracle Linux 9.0
- D. Oracle Linux 8.0
> KVM hosts in this OLVM installation path require Oracle Linux 8.5 or later. That aligns the host operating system with the supported OLVM and oVirt 4.4 package streams used in the workshop.

Q: 2. Which installation type is recommended for the KVM host operating system?
- A. Full installation with GUI
* B. Minimal install
- C. Server with GUI
- D. Workstation installation
> A minimal install is recommended because KVM hosts should run only the services needed for virtualization. Avoiding a full GUI or workstation profile reduces unnecessary packages, attack surface, and maintenance overhead.

Q: 3. Which two Unbreakable Enterprise Kernel releases are supported for KVM hosts? (Choose 2)
- A. UEKR5
* B. UEKR6
* C. UEKR7
- D. UEKR8
> UEKR6 and UEKR7 are the supported Unbreakable Enterprise Kernel releases for the KVM host objective in this quiz. Unsupported kernel streams can create driver, module, or package compatibility issues.

Q: 4. Which additional repository channel is required specifically for VDSM on the KVM host?
- A. UEKR6
* B. UEKR7
- C. Gluster AppStream
- D. KVM AppStream
> The UEKR7 repository channel is specifically required for VDSM-related host configuration in this setup. It provides the supported kernel package stream expected by the OLVM host stack.

Q: 5. What is the MINIMUM CPU requirement for a KVM host?
- A. Single-core 32-bit CPU
* B. 64-bit dual-core CPU
- C. 64-bit quad-core CPU
- D. 64-bit eight-core CPU
> The minimum CPU requirement is a 64-bit dual-core processor. KVM requires a 64-bit-capable host CPU, and at least two cores provide a baseline for the host and virtual machine workload.

Q: 6. What is RECOMMENDED for better performance in a KVM host?
- A. Single CPU with hyper-threading
* B. Multiple CPUs
- C. Higher clock speed only
- D. Overclocked CPUs
> Multiple CPUs are recommended because KVM hosts run the hypervisor, VDSM, host services, and VM workloads. More CPU capacity gives the scheduler room to run multiple VMs and management tasks efficiently.

Q: 7. Which two hardware virtualization technologies must the CPU support? (Choose 2)
* A. Intel VT-x
- B. Intel SGX
* C. AMD-V
- D. ARM TrustZone
> Intel VT-x and AMD-V are the hardware virtualization extensions required for KVM acceleration. They allow the processor to run virtualized workloads efficiently and safely.

Q: 8. What is the MINIMUM RAM required for a KVM host?
- A. 1 GB
* B. 2 GB
- C. 4 GB
- D. 8 GB
> The minimum RAM requirement for a KVM host is 2 GB. In practice, production hosts need much more memory because VM memory demand quickly exceeds the bare minimum host requirement.

Q: 9. What is the MAXIMUM tested RAM capacity for Oracle Linux Virtualization Manager hosts?
- A. 1 terabyte
- B. 2 terabytes
- C. 4 terabytes
* D. 6 terabytes
> The maximum tested RAM capacity for OLVM hosts in this objective is 6 TB. This is a tested scalability figure, not a recommendation that every host should be sized that large.

Q: 10. What is the minimum local storage requirement for a KVM host?
- A. 30 GB
- B. 40 GB
- C. 50 GB
* D. 60 GB
> A KVM host requires at least 60 GB of local storage. This space is for the host OS and OLVM-related components rather than the main shared storage used for VM disks.

Q: 11. What is the purpose of the 60 GB local storage on a KVM host?
- A. Storing all virtual machine disks
* B. Dedicated for host operating system and OLVM components
- C. Backup storage only
- D. ISO image storage
> The local 60 GB requirement is dedicated to the host operating system and OLVM host components. VM disks normally belong on configured storage domains, especially in shared-storage clusters.

Q: 12. What is the MINIMUM network interface requirement for a KVM host?
- A. One NIC with 100 Mbps bandwidth
* B. One NIC with 1 Gbps bandwidth
- C. Two NICs with 1 Gbps bandwidth
- D. Four NICs with 1 Gbps bandwidth
> The minimum network requirement is one 1 Gbps NIC. That is the baseline for host connectivity, but it is usually not enough for production environments with migration, storage, and VM traffic.

Q: 13. What is RECOMMENDED for network configuration on a KVM host?
- A. One NIC with 10 Gbps
* B. Two or more NICs with at least 1 Gbps bandwidth
- C. Four NICs with 100 Mbps
- D. Single bonded interface
> Two or more NICs with at least 1 Gbps bandwidth are recommended so traffic types can be separated or made redundant. This improves operational flexibility and reduces contention.

Q: 14. Why are multiple NICs recommended for KVM hosts?
- A. For load balancing only
- B. For redundancy only
* C. NICs can be dedicated for network-intensive activities like VM migration
- D. To connect to multiple data centers
> Multiple NICs allow administrators to dedicate interfaces to traffic-heavy functions such as VM migration, storage, or VM networks. This can improve performance and isolation compared with pushing everything through one interface.

Q: 15. When are firewall configurations automatically set up on a KVM host?
- A. During OS installation
* B. When the host is registered with the engine
- C. Never, must be done manually
- D. Only for production hosts
> Host firewall rules are automatically configured when the host is registered with the engine, if that option is selected. Registration is when OLVM knows which host services need to be reachable.

Q: 16. What port is used for SSH access between the KVM host and Manager?
- A. 21
* B. 22
- C. 23
- D. 3389
> SSH uses port 22. The engine and administrators rely on SSH during host registration, setup, and troubleshooting workflows.

Q: 17. Which two ports are used for web management and web access? (Choose 2)
* A. 8080
- B. 8000
* C. 8443
- D. 9000
> Ports 8080 and 8443 are used for web management and web access in this host installation objective. These are separate from the engine public UI ports covered elsewhere.

Q: 18. What is the port range for VM Console Access using SPICE or VNC?
- A. 3389 to 3400
- B. 5800 to 5900
* C. 5900 to 6923
- D. 8000 to 9000
> SPICE and VNC console access uses the 5900 to 6923 range. Hosts need this range available for VM graphical console connections.

Q: 19. Which two ports are used for VDSM communication? (Choose 2)
- A. 54320
* B. 54321
* C. 54322
- D. 54323
> VDSM communication uses ports 54321 and 54322. These ports support management communication between the engine and the host-side VDSM agent.

Q: 20. Which three ports are required for high availability with configured cluster management? (Choose 3)
* A. 5405 UDP
* B. 5406 UDP
- C. 5407 TCP
* D. 2224 TCP
- E. 2225 UDP
> High availability with cluster management requires 5405 UDP, 5406 UDP, and 2224 TCP. These ports support cluster communication and management components used for HA behavior.

Q: 21. What is the MAXIMUM number of physical CPU cores tested for a KVM host?
- A. 128
- B. 256
* C. 384
- D. 512
> The tested maximum for physical CPU cores on a KVM host is 384. This is a scalability boundary used for planning and exam purposes.

Q: 22. What is the MAXIMUM tested memory capacity for a KVM host?
- A. 2 terabytes
- B. 4 terabytes
* C. 6 terabytes
- D. 8 terabytes
> The maximum tested memory capacity is 6 TB. Like CPU limits, this is a tested scale value that indicates the supported size of large virtualization hosts.

Q: 23. What is the MAXIMUM number of concurrently running VMs tested on a single KVM host?
- A. 300
- B. 400
- C. 500
* D. Above 600
> The tested maximum number of concurrently running VMs on a single KVM host is above 600. Actual usable density depends on CPU, memory, I/O, network, workload behavior, and operational policy.

Q: 24. Which repository contains the latest version of Oracle Linux 8 base OS packages?
- A. ol8_baseos
* B. ol8_baseos_latest
- C. ol8_latest
- D. baseos_latest
> `ol8_baseos_latest` contains the latest Oracle Linux 8 base operating system packages. It is the foundation repository needed before layering virtualization-specific packages.

Q: 25. Which repository provides additional application packages not included in the base OS?
- A. ol8_applications
* B. ol8_AppStream
- C. ol8_extras
- D. ol8_apps
> `ol8_AppStream` provides additional application packages and module streams that are not in the base OS repository. OLVM setup depends on packages from both base and AppStream channels.

Q: 26. Which repository specifically provides KVM-related packages?
- A. ol8_virtualization
- B. ol8_kvm
* C. ol8_kvm_appstream
- D. kvm_utils
> `ol8_kvm_appstream` provides KVM-related packages. Enabling the correct KVM repository ensures host virtualization packages resolve from the supported Oracle Linux channel.

Q: 27. Which repository contains packages for oVirt 4.4 on Oracle Linux 8?
- A. ol8_ovirt
* B. ol8_ovirt44
- C. ovirt44_ol8
- D. ol8_virtualization44
> `ol8_ovirt44` contains packages for oVirt 4.4 on Oracle Linux 8. OLVM uses the oVirt stack, so this repository is required for the matching versioned components.

Q: 28. Which repository provides supplementary packages for oVirt 4.4?
- A. ol8_ovirt44_additions
* B. ol8_ovirt44_extras
- C. ol8_ovirt_extras
- D. ovirt_supplemental
> `ol8_ovirt44_extras` provides supplemental packages for the oVirt 4.4 stack. Extras repositories often contain supporting dependencies that are not in the primary oVirt channel.

Q: 29. Which repository provides Gluster file system packages?
- A. ol8_gluster
- B. ol8_gluster_appstream
* C. ol8_64_gluster_appstream
- D. gluster_ol8
> `ol8_64_gluster_appstream` provides Gluster filesystem packages. Gluster support is part of the broader storage options OLVM can integrate with.

Q: 30. Which repository contains Unbreakable Enterprise Kernel Release 7 packages?
- A. ol8_UEKR7
* B. ol8_64_UEKR7
- C. UEKR7_ol8
- D. uek_release7
> `ol8_64_UEKR7` contains Unbreakable Enterprise Kernel Release 7 packages for Oracle Linux 8. This repository is needed when the host should use UEKR7.

Q: 31. Which virt module must be DISABLED when preparing a KVM host?
- A. virt:rhel
* B. virt:ol
- C. virt:kvm
- D. virt:enterprise
> The `virt:ol` module must be disabled to avoid pulling packages from the wrong virtualization stream. OLVM host preparation expects the KVM utilities stream instead.

Q: 32. Which virt module must be ENABLED when preparing a KVM host?
- A. virt:kvm
- B. virt:kvm_utils
* C. virt:kvm_utils2
- D. virt:ol8
> The `virt:kvm_utils2` module must be enabled because it provides the supported virtualization utility stream for this OLVM host configuration.

Q: 33. What command is used to install the Oracle oVirt release package on the KVM host?
- A. dnf install ovirt-release-el8
- B. dnf install oracle-ovirt-el8
* C. dnf install oracle-ovirt-release-el8
- D. dnf install olvm-release-el8
> `dnf install oracle-ovirt-release-el8` installs the Oracle oVirt release package. That package configures the oVirt/OLVM repositories needed by the host.

Q: 34. What command cleans the dnf repository cache after configuring repositories?
- A. dnf clear
- B. dnf clean
* C. dnf clean all
- D. dnf cache-clean
> `dnf clean all` clears cached package and repository metadata. This helps ensure DNF sees the current repository configuration after channels are enabled or changed.

Q: 35. Where in the Administration Portal do you add a new KVM host?
- A. Storage -> Hosts
* B. Compute -> Hosts
- C. Network -> Hosts
- D. Configuration -> Hosts
> New KVM hosts are added under Compute -> Hosts in the Administration Portal. Hosts are compute resources because they provide the capacity where VMs run.

Q: 36. What button do you click to begin adding a new host?
- A. Add
* B. New
- C. Create
- D. Register
> The Add Host workflow begins by clicking New. That starts the wizard where host identity, connection, authentication, and configuration options are provided.

Q: 37. Which three pieces of information are required when adding a new host? (Choose 3)
* A. Host name (unique identifier)
- B. Email address
* C. Hostname (FQDN)
* D. SSH port
- E. Physical location
> Adding a host requires a unique host name, the host FQDN, and the SSH port. These values let the engine identify and connect to the correct machine during registration.

Q: 38. Which two authentication methods can be used when adding a KVM host? (Choose 2)
* A. Password authentication
- B. Kerberos
* C. SSH key authentication
- D. Certificate authentication
> The Add Host workflow can authenticate using a password or SSH key. Both methods allow the engine to access the host as the required administrative user during setup.

Q: 39. For which user account must authentication credentials be provided when adding a host?
- A. admin user
* B. root user
- C. ovirt user
- D. vdsm user
> Root credentials are required when adding the host because OLVM must install packages, configure services, and make system-level changes. A normal user or VDSM account is not sufficient for initial registration.

Q: 40. If you select SSH key authentication, where must the SSH public key be placed on the KVM host?
- A. /etc/ssh/authorized_keys
* B. /root/.ssh/authorized_keys
- C. /home/ovirt/.ssh/authorized_keys
- D. /var/lib/ovirt/authorized_keys
> When using SSH key authentication, the engine public key must be placed in `/root/.ssh/authorized_keys`. That authorizes root SSH access for the engine during host registration.

Q: 41. When adding multiple SSH keys to authorized_keys file, where should the engine's SSH key be placed?
- A. At the end of the file
* B. As the first key in the file
- C. Anywhere in the file
- D. In a separate file
> The engine's SSH key should be the first key in `authorized_keys` when multiple keys are present. This avoids key-selection issues during automated host registration.

Q: 42. What option in the Add Host wizard automatically configures firewall rules?
- A. Enable Firewall
- B. Configure Firewall
* C. Automatically Configure Host Firewall
- D. Setup Firewall Rules
> The `Automatically Configure Host Firewall` option lets OLVM configure required firewall rules during host setup. This is safer than opening every port manually and reduces missed-port errors.

Q: 43. After adding a host, what indicates the host is successfully registered and running?
- A. Red arrow icon
- B. Yellow arrow icon
* C. Green arrow icon
- D. Blue arrow icon
> A green arrow icon indicates the host is registered and up. In OLVM, host status icons are quick visual indicators of whether a host is available for workloads.

Q: 44. Which command is used to set SELinux enforcing policies on the KVM host?
- A. selinux
- B. setenforce
* C. semanage
- D. sestatus
> `semanage` is used to manage persistent SELinux policy settings such as booleans and contexts. `setenforce` changes enforcement mode temporarily but does not configure the specific policies asked here.

Q: 45. Which SELinux boolean policy allows fencing to connect to the network?
- A. fenced_network_connect
* B. fenced_can_network_connect
- C. fence_network_enabled
- D. network_fence_allow
> `fenced_can_network_connect` allows fencing components to make network connections. This matters because power management and fencing devices are often reached over the network.

Q: 46. Which SELinux boolean policy allows virtualization to use NFS?
- A. virt_nfs_enabled
* B. virt_use_nfs
- C. nfs_virt_enabled
- D. allow_virt_nfs
> `virt_use_nfs` allows virtualization services to use NFS-backed storage under SELinux policy. Without this boolean, SELinux can block virtualization access to NFS storage even if file permissions look correct.

Q: 47. What is the purpose of setting the OTOPI_PYTHON environment variable?
- A. To enable Python on the host
* B. To specify the Python version for OTOPI operations and avoid package mismatches
- C. To install Python packages
- D. To configure Python security
> The `OTOPI_PYTHON` variable tells OTOPI-based setup operations which Python interpreter to use. This helps avoid package or interpreter mismatches during host deployment tasks.

Q: 48. Where is the OTOPI_PYTHON environment variable typically configured?
- A. /etc/python.env
* B. /etc/otopi.env.d/my-python.env
- C. /var/lib/otopi/python.conf
- D. /root/.python_env
> `/etc/otopi.env.d/my-python.env` is the typical place to set the OTOPI_PYTHON environment override. Files in this directory are read by OTOPI tooling during setup operations.

Q: 49. In a testing/lab environment, what service might be disabled (though NOT recommended for production)?
- A. sshd
- B. NetworkManager
* C. firewalld
- D. vdsmd
> In a lab or test environment, administrators may disable `firewalld` to avoid connectivity issues while learning. That is not recommended for production because it removes an important host security control.

Q: 50. What command is used to disable the firewall daemon?
- A. firewall-cmd --disable
* B. systemctl disable firewalld
- C. service firewalld disable
- D. firewall disable
> `systemctl disable firewalld` disables the firewall daemon from starting automatically. In a real production OLVM environment, the better approach is to configure required rules rather than disabling the firewall.

```

## Acknowledgements

- **Source** - OLVM_Practice_Tests_Combined.pdf
- **Generated From** - Oracle Linux Virtualization Manager Exam 1Z0-1170 practice test collection
