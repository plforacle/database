# OLVM Self-Study: Engine and KVM Host Installation

## Introduction

This module contains **80 scored practice questions** for the engine and kvm host installation objective, which accounts for **16%** of the exam weighting supplied for this training. Use it after the instructor-led Lab 7 session and while reviewing the supplied course transcripts.

Estimated Time: 80-112 minutes, including explanation review

```quiz-config
passing: 80
badge: images/badge.png
```

### Objectives

- Explain Engine and KVM Host Requirements
- Explore Engine and KVM Host Firewall Requirements
- Perform Engine Setup
- Add a KVM Host
- Perform Self-Hosted Engine Setup

### How to Practice

Choose **one option per question**, including questions whose choices contain a set of items. Click **Check Answer** to see feedback and the explanation. Answer all 80 questions; at least **64 correct answers** unlock this module's badge. The 80% target is a study milestone, not an official exam passing score.

Record your first-attempt score before using **Try Again**. Review the corresponding transcript clip or Lab 7 task, explain the reasoning, and retry after studying. Scores are tracked within this module; keep your own first-attempt record before navigating away.

The recorded course uses older OL8 and OLVM examples than Lab 7. Questions explicitly referring to the recorded course use that context. Match package, identity, and UI procedures to the actual practice environment.

[Study guide and coverage map](../../workshops/olvm-study/index.html?lab=study-guide)

The self-hosted-engine setup objective has **partial source coverage**: architecture, shared-storage planning, and HA-agent concepts. The supplied materials do not provide a complete deployment walkthrough. These questions do not establish readiness to perform that setup.

## Task 1: Explain Engine and KVM Host Requirements

```quiz score
Q: 2.001. Before installing an engine using recorded course commands, what should be verified?
- The engine requires the same disk image as every guest VM
- Any Linux release accepts the same repository names and packages
- The browser version determines the engine's kernel version
* The operating system and OLVM release match the intended training environment
> The recorded course uses an older OL8 and OLVM baseline than Lab 7. Prerequisites and repositories must match the environment being practiced.
```

```quiz score
Q: 2.002. What type of CPU architecture is required for the Engine host?
- 32-bit x86
- PowerPC
- ARM64
* 64-bit x86
> The OLVM Engine host requires a 64-bit x86 architecture. The installation stack and supported Oracle Linux platform for this release are not based on 32-bit x86, ARM64, or PowerPC.
```

```quiz score
Q: 2.003. What is the purpose of an engine's minimum hardware specification?
- To make recommended sizing irrelevant
- To guarantee capacity for every number of hosts and VMs
- To eliminate the need for monitoring after installation
* To establish a starting requirement, while workload sizing still determines suitable capacity
> Minimum requirements do not guarantee comfortable operation at every workload scale. The training distinguishes minimum and recommended sizing.
```

```quiz score
Q: 2.004. Why might an engine be sized above the minimum specification?
* To allow headroom for its expected management workload and growth
- To avoid installing a KVM host
- To store all guest RAM permanently in the engine database
- To replace the need for shared storage
> Recommended sizing provides room beyond the minimum. The engine manages infrastructure but does not execute all guest workloads.
```

```quiz score
Q: 2.005. What change should trigger a review of engine sizing?
* Growth in the number of managed hosts
- A change to a guest's desktop wallpaper
- The order of choices in a quiz
- Changing a VM's descriptive tag alone
> The engine sizing discussion relates capacity to deployment scale. Adding managed hosts increases the management workload.
```

```quiz score
Q: 2.006. Why include expected VM count in engine capacity planning?
- The engine allocates a physical core to every guest vCPU
- VM count controls the VLAN ID range
- Every VM disk must fit inside the engine database
* More managed VMs increase the amount of operational information and management work
> VM count helps estimate management scale. Guest compute and storage sizing remain separate concerns.
```

```quiz score
Q: 2.007. An environment grows beyond its original small deployment plan. What is the appropriate sizing response?
- Assume the initial minimum remains sufficient indefinitely
* Reassess engine CPU, memory, and disk capacity against the larger workload
- Change only the engine's browser bookmark
- Move all VM disks into the engine installation directory
> The deployment sizing tables illustrate different scales. Reassess resources as the managed estate grows rather than memorizing a universal host-count limit.
```

```quiz score
Q: 2.008. A planned VM rollout substantially increases management load. Which approach is sound?
* Use expected VM and host totals when selecting the engine sizing tier
- Select a tier using only the number of users' keyboards
- Use the smallest tier because VMs run on hosts
- Disable historical collection to guarantee capacity
> Although VMs run on KVM hosts, the engine still manages them. Its sizing should reflect the managed estate.
```

```quiz score
Q: 2.009. Why does a large deployment need a deliberate engine sizing plan?
- Every host must run a separate independent engine database
- Large deployments cannot use a REST API
* Its central management workload is larger than that of a small deployment
- A large deployment makes host virtualization extensions unnecessary
> Sizing scales with managed infrastructure and operational demands. A larger estate requires planning for the management services.
```

```quiz score
Q: 2.010. Which statement correctly separates engine and host capacity planning?
- Give the engine the sum of every guest disk's capacity as database space
* Size the engine for management scale and the KVM hosts for guest execution workloads
- Size only the hosts because the engine has no management workload
- Size only the engine because hosts use no CPU or RAM
> Engine and host roles differ. Both need suitable capacity, but they serve different workloads.
```

```quiz score
Q: 2.011. Before preparing a KVM host, what should be checked about its OS release?
- Any Linux distribution works with identical repository commands
- It must equal the browser's version number
- It is determined only by the VM's guest OS
* It matches the supported baseline of the intended OLVM training environment
> The recorded course and Lab 7 use different release contexts. Validate host prerequisites for the environment being practiced.
```

```quiz score
Q: 2.012. In the recorded course, which installation type is recommended for the KVM host operating system?
* Minimal install
- Server with GUI
- Workstation installation
- Full installation with GUI
> A minimal install is recommended because KVM hosts should run only the services needed for virtualization. Avoiding a full GUI or workstation profile reduces unnecessary packages, attack surface, and maintenance overhead.
```

```quiz score
Q: 2.013. Why verify the host's kernel release before installation?
- The kernel name must equal the storage domain name
- The kernel chooses every user's portal role
* The virtualization packages and kernel must match the intended supported baseline
- All guest OS kernels must be identical to the host kernel
> Host preparation includes kernel and package compatibility. The recorded UEK examples should not be treated as universal requirements for every release.
```

```quiz score
Q: 2.014. A required host package is unavailable from the enabled repositories. What should be investigated?
- Whether every user has SuperUser permission
- Whether the VM Portal has a different color theme
- Whether the guest's disk label matches the package name
* Whether the appropriate kernel and OLVM repository channels are enabled for that baseline
> The host repository preparation step supplies compatible packages. Missing channels can prevent installation even before the host is registered.
```

```quiz score
Q: 2.015. In the recorded course, what is the MINIMUM CPU requirement for a KVM host?
- 64-bit quad-core CPU
- Single-core 32-bit CPU
- 64-bit eight-core CPU
* 64-bit dual-core CPU
> The minimum CPU requirement is a 64-bit dual-core processor. KVM requires a 64-bit-capable host CPU, and at least two cores provide a baseline for the host and virtual machine workload.
```

```quiz score
Q: 2.016. In the recorded course, what is RECOMMENDED for better performance in a KVM host?
- Single CPU with hyper-threading
- Overclocked CPUs
* Multiple CPUs
- Higher clock speed only
> Multiple CPUs are recommended because KVM hosts run the hypervisor, VDSM, host services, and VM workloads. More CPU capacity gives the scheduler room to run multiple VMs and management tasks efficiently.
```

```quiz score
Q: 2.017. Which two hardware virtualization technologies must the CPU support? Select the option listing the complete correct set.
- ARM TrustZone; AMD-V
- Intel SGX; AMD-V
* Intel VT-x; AMD-V
- Intel VT-x; Intel SGX
> Intel VT-x and AMD-V are the hardware virtualization extensions required for KVM acceleration. They allow the processor to run virtualized workloads efficiently and safely.
```

```quiz score
Q: 2.018. In the recorded course, what is the MINIMUM RAM required for a KVM host?
- 8 GB
- 4 GB
* 2 GB
- 1 GB
> The minimum RAM requirement for a KVM host is 2 GB. In practice, production hosts need much more memory because VM memory demand quickly exceeds the bare minimum host requirement.
```

```quiz score
Q: 2.019. Why avoid using a published tested maximum RAM value as a workload sizing target?
* Actual safe capacity depends on workload demand, host overhead, and supported deployment limits
- Host memory never limits virtualization
- Tested maxima guarantee the same performance for every workload
- The engine automatically supplies additional physical RAM
> Tested maxima and workload sizing answer different questions. Plan physical resources for actual demand and validate the release context.
```

```quiz score
Q: 2.020. In the recorded course, what is the minimum local storage requirement for a KVM host?
- 50 GB
- 40 GB
- 30 GB
* 60 GB
> A KVM host requires at least 60 GB of local storage. This space is for the host OS and OLVM-related components rather than the main shared storage used for VM disks.
```

```quiz score
Q: 2.021. Why reserve local host disk capacity separately from VM storage capacity?
- All shared VM disks are always duplicated into the host OS partition
* The host OS and host-side components need local space even when VM disks use shared storage
- The host's boot disk replaces storage domain permissions
- The engine database lives on every host's boot disk
> The course separates local host OS requirements from storage domains that hold VM disks.
```

```quiz score
Q: 2.022. Why can a single host NIC be a poor production design even if it provides basic connectivity?
* It can create a shared traffic bottleneck and a single network failure point
- It automatically disables hardware virtualization
- It removes all authentication requirements
- It prevents creation of any guest vCPU
> Basic connectivity does not provide redundancy or traffic separation. Network design must consider availability and load.
```

```quiz score
Q: 2.023. What is a practical reason to provide multiple NICs on a KVM host?
* To support redundancy and separation of intensive network traffic
- To guarantee every flow uses the sum of all link speeds
- To make VLAN configuration on switches unnecessary
- To store engine database backups inside NIC firmware
> Multiple NICs allow more flexible traffic and availability designs. The benefit depends on bond mode and the physical network.
```

```quiz score
Q: 2.024. In the recorded course, why are multiple NICs recommended for KVM hosts?
* NICs can be dedicated for network-intensive activities like VM migration
- For redundancy only
- For load balancing only
- To connect to multiple data centers
> Multiple NICs allow administrators to dedicate interfaces to traffic-heavy functions such as VM migration, storage, or VM networks. This can improve performance and isolation compared with pushing everything through one interface.
```

```quiz score
Q: 2.025. In the recorded course, which repository contains the latest version of Oracle Linux 8 base OS packages?
- ol8_baseos
- baseos_latest
- ol8_latest
* ol8_baseos_latest
> `ol8_baseos_latest` contains the latest Oracle Linux 8 base operating system packages. It is the foundation repository needed before layering virtualization-specific packages.
```

```quiz score
Q: 2.026. In the recorded course, which repository provides additional application packages not included in the base OS?
- ol8_applications
- ol8_extras
- ol8_apps
* ol8_AppStream
> `ol8_AppStream` provides additional application packages and module streams that are not in the base OS repository. OLVM setup depends on packages from both base and AppStream channels.
```

```quiz score
Q: 2.027. In the recorded course, which repository specifically provides KVM-related packages?
- ol8_virtualization
- ol8_kvm
* ol8_kvm_appstream
- kvm_utils
> `ol8_kvm_appstream` provides KVM-related packages. Enabling the correct KVM repository ensures host virtualization packages resolve from the supported Oracle Linux channel.
```

```quiz score
Q: 2.028. Why should OLVM repository versions be aligned when preparing a host?
- To give the host automatic SuperUser privileges
- To copy all VM disks into the repository cache
- To make NFS permissions unnecessary
* To obtain compatible host and management components for the intended release
> The recorded course uses oVirt 4.4 repository examples, while Lab 7 uses a newer baseline. Align packages with the environment rather than mixing commands blindly.
```

```quiz score
Q: 2.029. What is the role of supplementary OLVM repository channels in host preparation?
- Replacing every storage domain with a package cache
- Creating a second independent SPM in the same data center
* Providing additional packages required by the chosen release's host stack
- Providing guest user passwords to the engine
> Additional repository channels supply dependencies. Their names and applicability depend on the release baseline.
```

```quiz score
Q: 2.030. When using Gluster storage in the recorded course, why prepare the relevant package sources?
* To make the required storage-related packages available
- To assign all guest disk MAC addresses
- To replace the engine's management database
- To enable every VM to ignore storage permissions
> The course's host preparation includes storage-related repositories. Apply package names and channels appropriate to the release being used.
```

```quiz score
Q: 2.031. Why should a kernel repository channel match the chosen host baseline?
- To select the VM Portal's default user group
* To obtain compatible kernel packages for the host stack
- To set the iSCSI CHAP password
- To make the physical switch create every VLAN automatically
> Kernel repository configuration belongs to host software preparation. It must be evaluated with the OS and OLVM release.
```

```quiz score
Q: 2.032. Why does the recorded host procedure disable an incompatible virtualization module stream?
- To remove the need for VDSM
* To avoid selecting packages from a conflicting stream
- To force all guests to use physical disk passthrough
- To disable CPU virtualization permanently
> Module stream selection controls available package versions. The recorded command is release-specific; understand its dependency purpose.
```

```quiz score
Q: 2.033. What should enabling the intended virtualization module stream accomplish?
* Make the compatible virtualization package set available for installation
- Make the firewall allow every incoming connection
- Start all templates as VMs automatically
- Create a new engine database on every guest
> The intended stream supplies compatible virtualization packages. It does not perform guest deployment or replace firewall configuration.
```

```quiz score
Q: 2.034. In the recorded course, what command is used to install the Oracle oVirt release package on the KVM host?
- dnf install oracle-ovirt-el8
- dnf install ovirt-release-el8
- dnf install olvm-release-el8
* dnf install oracle-ovirt-release-el8
> `dnf install oracle-ovirt-release-el8` installs the Oracle oVirt release package. That package configures the oVirt/OLVM repositories needed by the host.
```

```quiz score
Q: 2.035. In the recorded course, what command cleans the dnf repository cache after configuring repositories?
- dnf clear
- dnf cache-clean
- dnf clean
* dnf clean all
> `dnf clean all` clears cached package and repository metadata. This helps ensure DNF sees the current repository configuration after channels are enabled or changed.
```

## Task 2: Explore Engine and KVM Host Firewall Requirements

```quiz score
Q: 2.036. In the recorded course, what is the default port number for SSH access to the Manager?
* 22
- 21
- 3389
- 23
> SSH uses TCP port 22 by default. In OLVM installation and administration, SSH is commonly needed for host access, initial setup, and troubleshooting.
```

```quiz score
Q: 2.037. Which two ports are used for web interface and REST API access? Select the option listing the complete correct set.
- 8443 (TCP); 443 (TCP)
- 80 (TCP); 8080 (TCP)
- 8080 (TCP); 443 (TCP)
* 80 (TCP); 443 (TCP)
> The OLVM web interface and REST API use standard web ports: HTTP on 80 and HTTPS on 443. Ports 8080 and 8443 are common application-server ports, but they are not the expected public access ports in this question.
```

```quiz score
Q: 2.038. In the recorded course, what is the default port number for PostgreSQL database communication?
* 5432
- 3306
- 5433
- 27017
> PostgreSQL uses TCP port 5432 by default. OLVM uses PostgreSQL for engine data, so this port matters when a remote database or database communication path is involved.
```

```quiz score
Q: 2.039. What port is used for iSCSI storage domain access?
- 111
* 3260
- 32803
- 2049
> iSCSI uses TCP port 3260 by default. When OLVM uses iSCSI storage domains, hosts must be able to reach the iSCSI target on this port.
```

```quiz score
Q: 2.040. Which port is used by the portmapper service for NFS?
- 32803 (TCP)
- 2049 (TCP and UDP)
* 111 (TCP and UDP)
- 3260 (TCP)
> The portmapper/rpcbind service uses port 111 for TCP and UDP. NFS environments often rely on RPC-related services, so firewall planning must account for portmapper along with NFS-specific ports.
```

```quiz score
Q: 2.041. What is a practical purpose of the engine's public key infrastructure?
- Allocating VM disk extents
- Assigning storage quota limits
- Scheduling vCPUs on physical cores
* Supporting certificates and trusted secure connections
> PKI supports certificate-based trust. It is separate from storage allocation, CPU scheduling, and quota enforcement.
```

```quiz score
Q: 2.042. When are firewall configurations automatically set up on a KVM host?
- Only for production hosts
* When the host is registered with the engine
- During OS installation
- Never, must be done manually
> Host firewall rules are automatically configured when the host is registered with the engine, if that option is selected. Registration is when OLVM knows which host services need to be reachable.
```

```quiz score
Q: 2.043. What port is used for SSH access between the KVM host and Manager?
- 21
- 3389
- 23
* 22
> SSH uses port 22. The engine and administrators rely on SSH during host registration, setup, and troubleshooting workflows.
```

```quiz score
Q: 2.044. A user reaches VM Portal but cannot open a graphical VM console. Which firewall path should be checked?
- Only SMTP from the engine to the relay
* The path required for the configured console service to the host
- Only the DWH PostgreSQL connection
- Only the iSCSI target discovery path
> Web portal connectivity and graphical console connectivity are different paths. The recorded host firewall section discusses console access separately.
```

```quiz score
Q: 2.045. What should be considered when estimating a host's usable CPU capacity?
* Guest demand, host overhead, scheduling, and any CPU allocation constraints
- Only the length of the hostname
- Only the number of storage domains
- Only the largest core count mentioned in a slide
> A tested CPU maximum does not replace workload planning. Overcommitment and host overhead affect usable capacity.
```

```quiz score
Q: 2.046. A host has enough nominal RAM for assigned VMs but begins swapping. What planning issue should be reviewed?
- The number of VLAN digits
- The portal's language setting
* Actual guest demand and memory overcommitment, including host overhead
- The length of each user's password alone
> RAM totals must be considered together with active demand and overhead. A large tested maximum does not guarantee a particular host's workload fits.
```

```quiz score
Q: 2.047. Why is a tested maximum VM count insufficient to choose a host's production workload?
- VM count determines the NFS protocol version
- The engine always runs the guest workload instead of the host
* VM sizes and activity differ, so resource demand matters as well as count
- Every VM consumes an identical amount of CPU and RAM
> A host running many small idle VMs is different from one running intensive workloads. Size for resources and expected activity.
```

## Task 3: Perform Engine Setup

```quiz score
Q: 2.048. Why enable the appropriate base OS repository during engine preparation?
- To disable certificate verification
* To provide operating system packages compatible with the chosen environment
- To create an external directory user
- To import every VM template automatically
> Repository preparation supplies the right package sources. Repository names depend on the operating system and release baseline.
```

```quiz score
Q: 2.049. What is the purpose of the Oracle oVirt release package during engine preparation?
- To restore VM disks from an engine backup
- To assign permissions to every directory user
* To configure access to the relevant OLVM package repositories
- To install each guest operating system
> The release package prepares OLVM repository configuration. Installing engine components and running setup are later steps.
```

```quiz score
Q: 2.050. In the recorded course, what command is used to install the OLVM Engine package?
- yum install oracle-engine
* dnf install ovirt-engine
- dnf install olvm-manager
- yum install olvm-engine
> The OLVM Engine package is installed with `dnf install ovirt-engine`. The package name follows the upstream oVirt naming used by the OLVM engine component.
```

```quiz score
Q: 2.051. In the recorded course, what command is used to configure the OLVM Engine after package installation?
* engine-setup
- olvm-configure
- ovirt-setup
- manager-setup
> `engine-setup` is the configuration command that initializes and configures the OLVM Engine after package installation. Installing the RPMs alone does not create the working engine configuration.
```

```quiz score
Q: 2.052. In the recorded course, what type of interface does the engine-setup command provide?
- Web-based interface
* Interactive command-line with questions
- Graphical user interface
- Silent installation with config file
> `engine-setup` is an interactive command-line installer that asks configuration questions. It is not a graphical or browser-based installer in this workflow.
```

```quiz score
Q: 2.053. In the recorded course, what does the engine-setup command display after all questions are answered?
* Summary of entered values
- Database schema
- Installation progress bar
- Error log
> After collecting answers, `engine-setup` displays a summary so the administrator can review the selected values before applying configuration. This checkpoint helps catch hostname, database, or service choices before setup proceeds.
```

```quiz score
Q: 2.054. What information is displayed after the engine-setup configuration is complete?
- System resource usage
* Details about how to log in to the Administration Portal
- Network topology diagram
- List of available VMs
> When setup completes, it prints login information for the Administration Portal, including the URL and relevant access details. At that point the engine is ready for browser-based administration.
```

```quiz score
Q: 2.055. Which five configuration groupings are part of the engine-setup process? Select the option listing the complete correct set.
- Database configuration; Network configuration; Hardware configuration; Certificates and security; Service configurations
- Hardware configuration; Network configuration; Administration user setup; Certificates and security; Service configurations
- Database configuration; Hardware configuration; Administration user setup; Certificates and security; Service configurations
* Database configuration; Network configuration; Administration user setup; Certificates and security; Service configurations
> The setup process covers database, network, administrative user, certificate/security, and service configuration. Hardware sizing is a prerequisite planning activity, not a configuration grouping inside `engine-setup`.
```

```quiz score
Q: 2.056. What can the database configuration in engine-setup allow you to configure? Select the option listing the complete correct set.
* A local database instance; A remote database
- A local database instance; MySQL database
- Oracle Database; A remote database
- MySQL database; A remote database
> `engine-setup` can configure the engine to use a local PostgreSQL database or connect to a remote PostgreSQL database. MySQL and Oracle Database are not the supported database choices for the OLVM engine in this workflow.
```

```quiz score
Q: 2.057. In the recorded course's legacy internal authentication setup, which default administrative username is created?
- root as the portal username automatically
* admin
- postgres
- vdsm
> The recorded engine setup creates the admin account in the internal profile. Lab 7 also covers a Keycloak path; this legacy account question is not a universal identity configuration rule.
```

```quiz score
Q: 2.058. In the recorded course's legacy authentication setup, which profile contains the default admin user?
- The host's KVM group
* internal
- The history database
- The guest's local user database
> The recorded course uses admin in the internal profile. Identity mechanisms can differ in Lab 7, so verify the environment's actual profile.
```

```quiz score
Q: 2.059. If you enable automatic firewall configuration during engine-setup, what happens?
- All ports are opened
* Necessary ports for OLVM are automatically allowed
- Only SSH port is opened
- The firewall is disabled
> When automatic firewall configuration is enabled, setup opens the ports required by OLVM services. It does not indiscriminately open all ports or disable the firewall.
```

```quiz score
Q: 2.060. After adding an alternate engine hostname, what needs to remain consistent for reliable access?
- Only the order of hosts in the dashboard
- Only the storage LUN number
- Only the VM's memory quota
* Name resolution, the engine's allowed access configuration, and certificate identity
> The course demonstrates alternate-hostname configuration. A working alias also needs coherent name resolution, access configuration, and certificate validation.
```

```quiz score
Q: 2.061. In the recorded course, what command is used to restart the OLVM Engine service after configuring an alternate hostname?
- service ovirt-engine restart
* systemctl restart ovirt-engine
- systemctl reload ovirt-engine
- engine-restart
> After changing engine hostname-related configuration, the engine service must be restarted with `systemctl restart ovirt-engine` for the change to take effect. Reloading or using older service syntax is not the expected command here.
```

```quiz score
Q: 2.062. Why import the appropriate engine CA certificate into the browser's trust store?
- To accept every certificate from every website
* To trust the engine certificate chain while still validating the accessed hostname
- To bypass portal authentication permanently
- To turn HTTP into shared storage
> CA trust helps validate the certificate chain. It does not excuse a mismatched hostname or grant portal permissions.
```

```quiz score
Q: 2.063. In the recorded course, what command is used to enable a repository using dnf config-manager?
- dnf repository enable <repo-name>
- dnf enable-repo <repo-name>
- dnf config-manager enable <repo-name>
* dnf config-manager --set-enabled <repo-name>
> `dnf config-manager --set-enabled <repo-name>` enables a repository using DNF configuration tools. The exact option matters because DNF does not use the made-up commands shown in the distractors.
```

```quiz score
Q: 2.064. In the recorded course, what command cleans up the dnf repository cache?
- dnf cache-clean
- dnf clean
- dnf clear
* dnf clean all
> `dnf clean all` clears cached repository metadata and packages. This is useful after repository changes so DNF refreshes metadata from the enabled repositories.
```

## Task 4: Add a KVM Host

```quiz score
Q: 2.065. Where in the Administration Portal do you add a new KVM host?
- Network -> Hosts
- Configuration -> Hosts
- Storage -> Hosts
* Compute -> Hosts
> New KVM hosts are added under Compute -> Hosts in the Administration Portal. Hosts are compute resources because they provide the capacity where VMs run.
```

```quiz score
Q: 2.066. What button do you click to begin adding a new host?
* New
- Register
- Create
- Add
> The Add Host workflow begins by clicking New. That starts the wizard where host identity, connection, authentication, and configuration options are provided.
```

```quiz score
Q: 2.067. Which three pieces of information are required when adding a new host? Select the option listing the complete correct set.
- Host name (unique identifier); Hostname (FQDN); Email address
- Host name (unique identifier); Email address; SSH port
- Email address; Hostname (FQDN); SSH port
* Host name (unique identifier); Hostname (FQDN); SSH port
> Adding a host requires a unique host name, the host FQDN, and the SSH port. These values let the engine identify and connect to the correct machine during registration.
```

```quiz score
Q: 2.068. Which two authentication methods can be used when adding a KVM host? Select the option listing the complete correct set.
* Password authentication; SSH key authentication
- Kerberos; SSH key authentication
- Certificate authentication; SSH key authentication
- Password authentication; Kerberos
> The Add Host workflow can authenticate using a password or SSH key. Both methods allow the engine to access the host as the required administrative user during setup.
```

```quiz score
Q: 2.069. For which user account must authentication credentials be provided when adding a host?
- admin user
* root user
- ovirt user
- vdsm user
> Root credentials are required when adding the host because OLVM must install packages, configure services, and make system-level changes. A normal user or VDSM account is not sufficient for initial registration.
```

```quiz score
Q: 2.070. If you select SSH key authentication, where must the SSH public key be placed on the KVM host?
* /root/.ssh/authorized_keys
- /home/ovirt/.ssh/authorized_keys
- /var/lib/ovirt/authorized_keys
- /etc/ssh/authorized_keys
> When using SSH key authentication, the engine public key must be placed in `/root/.ssh/authorized_keys`. That authorizes root SSH access for the engine during host registration.
```

```quiz score
Q: 2.071. How should an engine SSH public key be added to the host's authorized_keys file?
- By placing it only in a guest VM's home directory
* As its own complete key entry, retaining any other authorized entries as appropriate
- By joining all keys into one continuous line
- By placing the engine's private key in the file
> Lab 7 authorizes the engine's public key for host registration. Each authorized public key is a separate entry; the engine's private key remains private.
```

```quiz score
Q: 2.072. What option in the Add Host wizard automatically configures firewall rules?
- Setup Firewall Rules
* Automatically Configure Host Firewall
- Configure Firewall
- Enable Firewall
> The `Automatically Configure Host Firewall` option lets OLVM configure required firewall rules during host setup. This is safer than opening every port manually and reduces missed-port errors.
```

```quiz score
Q: 2.073. What should be verified after host registration completes?
* The host reaches Up status and its deployment events show successful completion
- Only a browser bookmark was created
- Only the host's name contains the word KVM
- The host appears anywhere in the list regardless of status
> A listed host can still be deploying or non-operational. Lab 7 verifies Up status before using the host for workloads.
```

```quiz score
Q: 2.074. A host service is blocked by SELinux policy. What is the appropriate troubleshooting direction?
- Grant every portal user SuperUser
- Delete all storage domains
* Investigate the required policy or boolean for that service while maintaining the intended security configuration
- Disable SELinux permanently as the only possible fix
> The recorded host procedure configures SELinux policies for particular services. Service-specific policy configuration is different from simply disabling enforcement.
```

```quiz score
Q: 2.075. Which SELinux boolean policy allows fencing to connect to the network?
- network_fence_allow
* fenced_can_network_connect
- fence_network_enabled
- fenced_network_connect
> `fenced_can_network_connect` allows fencing components to make network connections. This matters because power management and fencing devices are often reached over the network.
```

```quiz score
Q: 2.076. Which SELinux boolean policy allows virtualization to use NFS?
- allow_virt_nfs
* virt_use_nfs
- virt_nfs_enabled
- nfs_virt_enabled
> `virt_use_nfs` allows virtualization services to use NFS-backed storage under SELinux policy. Without this boolean, SELinux can block virtualization access to NFS storage even if file permissions look correct.
```

```quiz score
Q: 2.077. A training demo temporarily disables the firewall to diagnose connectivity. What should a learner take into production planning?
- Every guest firewall can be deleted automatically
* Configure the required service paths rather than treating an unrestricted host as the target configuration
- All production firewalls should remain disabled
- Disabling a firewall establishes certificate trust
> The demo workaround does not replace the engine and host firewall requirements taught in the course. Required paths should be configured deliberately.
```

```quiz score
Q: 2.078. What should a host firewall validation check after registration?
- Whether all ports are open to every source
- Whether the portal displays a green background
* Required management, storage, migration, and console paths for the actual configuration
- Whether the guest wallpaper was updated
> Host registration can configure firewall rules, but the required paths still need to work in the actual network and service design.
```

## Task 5: Perform Self-Hosted Engine Setup

```quiz score
Q: 2.079. Before planning a self-hosted engine, which storage characteristic is essential in the architecture taught here?
* The engine VM storage is accessible to eligible hosting hosts
- Every application VM owns an independent engine disk
- The management database is stored in the browser cache
- The engine VM exists only on an administrator's laptop
> The architecture places the engine VM on shared storage. This question covers deployment planning, not the full deployment command sequence, which is absent from the supplied material.
```

```quiz score
Q: 2.080. Which host-side component supports availability of the self-hosted engine VM?
- The storage server's browser certificate store
- The guest desktop manager alone
* The hosted-engine HA agent
- The SMTP relay alone
> The architecture describes HA agents managing the engine VM. The supplied material does not provide a complete self-hosted deployment walkthrough.
```

## Acknowledgements

- **Author** - OLVM training team
- **Sources** - User-supplied training transcripts, section quiz-bank files, and Lab 7 OLVM content
- **Last Updated** - October 2026
