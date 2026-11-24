# OLVM Self-Study: Optimization, Events, Logs, and Monitoring

## Introduction

This module contains **100 scored practice questions** for the optimization, events, logs, and monitoring objective, which accounts for **20%** of the exam weighting supplied for this training. Use it after the instructor-led Lab 7 session and while reviewing the supplied course transcripts.

Estimated Time: 100-140 minutes, including explanation review

```quiz-config
passing: 80
badge: images/badge.png
```

### Objectives

- Optimize Clusters, Hosts and VMs
- Configure Highly Available Hosts and VMs
- Optimize VM Performance
- Hot-Plug Devices
- Describe Log Files and Event Management
- Monitoring and Observability

### How to Practice

Choose **one option per question**, including questions whose choices contain a set of items. Click **Check Answer** to see feedback and the explanation. Answer all 100 questions; at least **80 correct answers** unlock this module's badge. The 80% target is a study milestone, not an official exam passing score.

Record your first-attempt score before using **Try Again**. Review the corresponding transcript clip or Lab 7 task, explain the reasoning, and retry after studying. Scores are tracked within this module; keep your own first-attempt record before navigating away.

The recorded course uses older OL8 and OLVM examples than Lab 7. Questions explicitly referring to the recorded course use that context. Match package, identity, and UI procedures to the actual practice environment.

[Study guide and coverage map](../../workshops/olvm-study/index.html?lab=study-guide)

## Task 1: Optimize Clusters, Hosts and VMs

```quiz score
Q: 6.001. What does memory overcommitment in OLVM allow?
* Total memory allocated to VMs can exceed physical memory capacity of the host
- Using only physical memory
- Disabling memory management
- Reducing VM density
> Memory overcommitment allows the total memory assigned to VMs to exceed the host's physical RAM. This relies on the fact that not every VM uses all allocated memory at the same time.
```

```quiz score
Q: 6.002. What is a benefit of memory overcommitment?
- Simpler configuration
- Lower costs only
- Reduced security
* Higher VM densities per host, maximizing resource efficiency
> The benefit of memory overcommitment is higher VM density per host. It lets administrators use physical memory more efficiently when workloads have variable or underused memory demand.
```

```quiz score
Q: 6.003. What can excessive memory overcommitment lead to?
- Increased security
- Faster performance
* Performance degradation if not managed effectively
- Lower VM density
> Excessive overcommitment can degrade performance because VMs may compete for memory the host cannot actually provide. If pressure becomes high, paging, swapping, or ballooning can hurt workload responsiveness.
```

```quiz score
Q: 6.004. What is a risk of more aggressive memory overcommitment?
- Storage capacity becomes unlimited
* Contention or overhead can increase if active guest demand exceeds available resources
- Physical memory is created without adding hardware
- Every guest becomes guaranteed to run faster
> The optimization course treats overcommitment as a tradeoff. Monitor actual demand and contention rather than assuming a larger ratio always helps.
```

```quiz score
Q: 6.005. What does MoM stand for in OLVM?
* Memory Overcommitment Manager
- Monitor of Memory
- Management of Modules
- Mode of Management
> MoM stands for Memory Overcommitment Manager. It helps manage memory overcommit behavior on hosts by coordinating memory optimization mechanisms.
```

```quiz score
Q: 6.006. What is KSM in the context of memory management?
* Kernel Same-page Merging
- Kernel Security Module
- Key Storage Manager
- KVM System Memory
> KSM stands for Kernel Same-page Merging. It is a Linux kernel feature that identifies identical memory pages and consolidates them to save RAM.
```

```quiz score
Q: 6.007. What does KSM do?
- Increases memory allocation
- Encrypts memory pages
* Identifies identical memory pages across VMs and merges them into a single copy
- Backs up memory to storage
> KSM scans for identical memory pages across VMs and merges them into one shared copy. This is especially useful when many VMs run similar operating systems or applications.
```

```quiz score
Q: 6.008. Can OLVM hosts allocate more virtual CPUs to VMs than physical CPU cores available?
* Yes, vCPUs can exceed physical CPU core count
- Only with special licensing
- No, never allowed
- Only in test environments
> OLVM can allocate more vCPUs to VMs than the number of physical CPU cores. This CPU overcommitment works because not every vCPU is busy at the same time, but it must be monitored for contention.
```

```quiz score
Q: 6.009. When several VMs compete for host CPU time, what should an administrator consider?
- Only the disk export filename
* Configured CPU allocation policies and the host's scheduling of vCPU execution
- Only the engine database's table names
- Only the count of user directory groups
> The optimization lesson covers CPU allocation and scheduling. The engine configures management policies while guest execution is scheduled on the host.
```

```quiz score
Q: 6.010. What is CPU pinning?
* Assigning specific vCPUs to dedicated physical CPU cores or threads on the host
- Limiting CPU usage
- Sharing CPUs among VMs
- Disabling CPUs
> CPU pinning maps specific VM vCPUs to dedicated physical CPU cores or threads. It is used when predictable CPU placement matters for performance, latency, or licensing boundaries.
```

```quiz score
Q: 6.011. What is a reasonable reason to evaluate CPU pinning for a workload?
- To move all guest memory into the engine database
- To guarantee every workload always becomes faster
- To eliminate the need for physical CPU capacity
* To control vCPU placement for more predictable scheduling or locality
> Pinning controls placement and can help selected workloads. It also constrains scheduling, so its value should be assessed against the workload.
```

```quiz score
Q: 6.012. Why configure a VM's guaranteed memory appropriately?
- It guarantees every guest application meets its latency target
- It creates additional physical host RAM without hardware
* It establishes memory requirements used when deciding whether the VM can be placed and run
- It replaces every memory balloon driver
> The optimization lesson discusses memory reservation. Reserved or guaranteed memory affects placement and resource planning; it does not promise every application performance outcome.
```

```quiz score
Q: 6.013. What does the memory balloon device allow?
- Blocking memory access
- Increasing physical memory
- Encrypting memory
* VMs to reclaim unused memory and return it to the host for other VMs
> The balloon device lets a guest return unused memory to the host so it can be used by other VMs. This allows the host to rebalance memory without shutting down the VM.
```

```quiz score
Q: 6.014. Why review both cluster memory policy and VM balloon configuration?
- Memory ballooning changes only the portal's displayed color
* The resource policy and guest-side mechanism must be suitable for the intended memory management behavior
- Enabling any cluster option installs every missing guest driver automatically
- The VM's storage VLAN determines balloon driver support
> The optimization lesson discusses cluster-level policy and the balloon device. Effective operation also requires the intended VM device and guest driver.
```

```quiz score
Q: 6.015. What does configuring dedicated I/O threads aim to improve?
- The number of data centers managed by one browser
- The strength of portal passwords
* Handling of disk I/O work separately from other VM processing
- The number of physical disks by changing their MAC addresses
> The optimization lesson discusses VirtIO and dedicated I/O processing. I/O threads are software processing resources; the disk itself is not a CPU core.
```

```quiz score
Q: 6.016. What is ballooning in memory management?
* A technique where a balloon driver requests memory from guest OS to reduce VM memory usage
- Expanding physical memory
- Backing up memory
- Encrypting memory
> Ballooning uses a guest balloon driver to request memory from the guest OS, reducing the VM's effective memory usage. The host can then reclaim that memory for other workloads.
```

```quiz score
Q: 6.017. Before relying on memory ballooning for a VM, what should be checked?
* The VM has the appropriate balloon device and a working guest driver
- That its disk format is named after the engine
- That its user has SuperUser permission
- Only that the VM has a graphical wallpaper
> The recorded course describes a balloon device and guest support. Verify actual VM configuration rather than assuming a default applies in every release.
```

```quiz score
Q: 6.018. What must be installed in the guest OS for the balloon device to work?
- Network drivers
* Relevant balloon drivers
- Video drivers
- Additional storage drivers
> The guest OS needs the relevant balloon drivers for ballooning to work. Without the guest driver, the host cannot coordinate memory reclaim cleanly inside the VM.
```

```quiz score
Q: 6.019. What command is used to check current memory overcommit settings?
- olvm-config
- vm-config
* engine-config
- memory-check
> `engine-config` is used to inspect current engine configuration values, including memory overcommit settings. It is the OLVM configuration utility for these engine parameters.
```

```quiz score
Q: 6.020. Which two parameters define memory overcommit ratios?
* MaxVdsMemOverCommit and MaxVdsMemOverCommitForServers
- MemoryLimit and MemoryReserve
- VMMemory and HostMemory
- MaxMemory and MinMemory
> `MaxVdsMemOverCommit` and `MaxVdsMemOverCommitForServers` define memory overcommit ratios. These parameters control how aggressively hosts can be overcommitted.
```

```quiz score
Q: 6.021. What command is used to change memory overcommit settings?
- vm-memory-config
- set-overcommit
- olvm-set-memory
* engine-config -s
> `engine-config -s` sets engine configuration values. After changing these settings, administrators typically need to apply the configuration through the documented service restart or setup workflow.
```

## Task 2: Configure Highly Available Hosts and VMs

```quiz score
Q: 6.022. Where should a fencing proxy host be located?
- In a different data center
- Outside the OLVM environment
- On the engine host only
* In the same cluster or data center as the host requiring fencing
> A fencing proxy host should be in the same cluster or Data Center as the host that needs fencing. It must be close enough operationally to reach the target's power-management path.
```

```quiz score
Q: 6.023. How does OLVM perform configured host fencing when a proxy host is used?
- The failed host must first successfully log into VM Portal
- A browser writes directly to the host's physical power supply
* The manager coordinates the operation through a suitable healthy host and the configured power-management agent
- Every guest independently powers off all cluster members
> The HA host lesson describes manager coordination and a fencing proxy. Fencing depends on reachable configured power-management infrastructure.
```

```quiz score
Q: 6.024. What does OLVM automatically do to non-responsive hosts with power management enabled?
- Ignores them
- Deletes them
- Reboots all VMs
* Attempts to fence them
> When power management is enabled, OLVM attempts to fence non-responsive hosts. Fencing protects shared resources by isolating or restarting a host that may no longer be safely controlled.
```

```quiz score
Q: 6.025. Why might a host appear non-responsive during boot?
- Storage corruption
- Network attacks
* Temporary delays due to initialization routines or startup tasks
- Hardware failure only
> A host can appear non-responsive during boot because initialization routines or startup tasks take time. Without a delay, OLVM might fence a host that is still legitimately starting.
```

```quiz score
Q: 6.026. What command parameter prevents fencing during host bootup?
- boot-fence-disable
- disable-fencing
* DisableFenceAtStartupInSeconds
- no-fence-boot
> `DisableFenceAtStartupInSeconds` prevents fencing during a host startup window. This reduces false fencing actions while a host is still booting and initializing services.
```

```quiz score
Q: 6.027. What does PMHealthCheckEnabled do when set to true?
* Initiates regular health checks for all configured host power management agents
- Checks only failed hosts
- Disables health checks
- Only checks once
> When `PMHealthCheckEnabled` is true, OLVM regularly checks configured power-management agents. These checks help confirm fencing devices remain reachable before an emergency.
```

```quiz score
Q: 6.028. What does PMHealthCheckIntervalInSeconds specify?
* The interval at which OLVM performs health checks for power management agents
- VM restart time
- Storage check frequency
- Boot timeout
> `PMHealthCheckIntervalInSeconds` defines how often OLVM performs those power-management health checks. The interval controls the cadence of ongoing validation.
```

```quiz score
Q: 6.029. How does recovery from a failed host differ from planned live migration?
- Neither operation requires any destination resources
* An eligible HA VM may restart after failure, while planned live migration transfers a running VM between functioning hosts
- A failed host can always complete a live migration after losing power
- Both operations always preserve a failed host's RAM with no interruption
> Lab 7 practices planned migration. HA restart after failure is a separate recovery action and can interrupt the workload.
```

```quiz score
Q: 6.030. What happens when a host is placed into maintenance mode?
- All VMs are paused
- VMs are deleted
- VMs continue running on that host
* VMs are gracefully shut down or migrated to another host before maintenance
> When a host enters maintenance mode, running VMs are gracefully migrated or shut down before maintenance proceeds. This prevents maintenance work from interrupting active workloads unexpectedly.
```

```quiz score
Q: 6.031. For VM migration, what must both source and destination hosts access? Select the option listing the complete correct set.
- The same data domain where VM disks reside; Different data domains
* The same data domain where VM disks reside; The same virtual networks and VLANs
- Different network segments; The same virtual networks and VLANs
- Different data domains; The same virtual networks and VLANs
> Migration requires both hosts to access the same data domain and the same virtual networks or VLANs. The destination host must be able to reach the VM disks and reproduce its network connectivity.
```

```quiz score
Q: 6.032. Why are two hosts alone insufficient to guarantee VM high availability?
- Any unrelated host can restart every VM without its disks
- Two hosts automatically replicate every disk and memory page
- The second host eliminates all failure detection requirements
* A recovery host also needs capacity, disk access, compatible networking, and the required HA configuration
> The HA lesson requires usable alternate resources and the right configuration. A host count alone does not establish recoverability.
```

## Task 3: Optimize VM Performance

```quiz score
Q: 6.033. What is the typical size range for huge pages?
* 2 MB to 1 GB
- 4 GB to 8 GB
- 4 KB to 8 KB
- 1 KB to 2 KB
> Huge pages typically range from 2 MB to 1 GB. They are larger than standard memory pages and are used to reduce translation overhead for memory-intensive workloads.
```

```quiz score
Q: 6.034. What does using huge pages reduce?
- Memory capacity
* The number of entries required in the system's page table
- Storage space
- CPU cores
> Huge pages reduce the number of page table entries needed to map memory. Fewer entries can reduce address-translation overhead for large memory allocations.
```

```quiz score
Q: 6.035. What is TLB in the context of huge pages?
* Translation Lookaside Buffer - hardware cache for virtual to physical address translations
- Total Logical Buffer
- Time Limit Buffer
- Thread Load Balancer
> TLB means Translation Lookaside Buffer, a hardware cache for virtual-to-physical address translations. Efficient TLB use improves memory-access performance.
```

```quiz score
Q: 6.036. How do larger page sizes affect TLB?
- Disable TLB
- Have no effect
* Reduce TLB misses because fewer translations are needed
- Increase TLB misses
> Larger pages reduce TLB misses because each translation covers more memory. With fewer translations needed, the CPU spends less time resolving memory addresses.
```

```quiz score
Q: 6.037. Why plan huge-page availability before starting a VM configured to use huge pages?
- Guest VLAN tags automatically allocate huge-page pools
* The host must have enough suitable huge-page memory for that configuration
- The browser supplies huge pages over HTTPS
- Huge pages are stored only inside the engine history database
> The course describes huge-page memory allocation at VM startup. Availability and configuration should be checked before relying on the performance feature.
```

```quiz score
Q: 6.038. Which tradeoff does the recorded course highlight for huge-page VM memory?
- Huge pages change a VM's vNIC into a physical switch
- Huge pages automatically remove all guest memory requirements
- Huge pages eliminate every CPU scheduling decision
* The discussed configuration limits dynamic memory hot-plug and hot-unplug workflows
> The recorded lesson contrasts huge-page allocation with dynamic memory resizing. Verify the capabilities of the exact environment before applying runtime changes.
```

## Task 4: Hot-Plug Devices

```quiz score
Q: 6.039. What does hot-plugging mean in VM administration?
- Copying the engine database into a guest
- Restarting every host whenever a vNIC is edited
- Replacing every device only after deleting the VM
* Adding a supported device or resource while the VM is running
> The hot-plug lesson covers runtime resource changes. Availability depends on the device, VM configuration, and guest support.
```

```quiz score
Q: 6.040. Before attempting vCPU hot-unplug, which constraint should be checked?
- Only whether the VM name includes CPU
- Only whether the storage domain has a short name
* Whether the CPUs being removed and the guest configuration support the requested runtime change
- Whether a user can read Grafana without authentication
> The recorded course limits removal to CPUs previously hot-plugged. Runtime removal depends on the VM and guest support, not merely permission to edit a VM.
```

```quiz score
Q: 6.041. A learner wants fewer vCPUs than the VM's current boot-time baseline. What distinction matters?
- The VM's CPU count can never be changed under any circumstances
- Hot-unplug always works for every original boot CPU
* A stopped-VM configuration change differs from supported runtime hot-unplug
- Changing a VLAN automatically changes the boot CPU baseline
> The hot-plug lesson discusses runtime removal constraints. It should not be read as a prohibition on every supported offline CPU reconfiguration.
```

```quiz score
Q: 6.042. Why should guest support be checked before hot-plugging resources into a Windows VM?
- Installing any agent guarantees support for every possible device change
- Windows guests use no virtual devices
- The engine database supplies missing guest drivers automatically
* Required guest drivers or agents and operating-system support must be present for the intended change
> The course highlights guest-side support. An installed agent alone does not guarantee every hot-plug operation is supported.
```

```quiz score
Q: 6.043. What must the VM's operating system support for hot plugging?
* Must explicitly support CPU hot plug functionality
- Any OS works
- Only Windows is supported
- Only Linux is supported
> The guest operating system must explicitly support CPU hotplug. OLVM can expose the operation, but the guest OS must handle CPU addition or removal safely.
```

```quiz score
Q: 6.044. What happens each time you hot plug memory to a VM?
* It appears as a new memory device under VM devices
- Old memory is removed
- VM is paused
- VM restarts
> Each time memory is hot plugged, it appears as a new memory device under the VM's devices. This reflects how the virtualization layer presents additional memory to the guest.
```

```quiz score
Q: 6.045. After the recorded memory hot-plug procedure, what should be verified across a shutdown and restart?
- The engine has become the VM's balloon driver
- The VM's disk contents have been erased
- Every added byte must disappear permanently
* The intended total memory is retained even if temporary device entries are consolidated
> The course distinguishes runtime memory-device entries from the VM's configured total memory. Verify the resulting configuration after restart.
```

```quiz score
Q: 6.046. When is adding memory to a running VM an appropriate option?
- Whenever the portal is reachable, regardless of guest support
- Only after deleting the VM's disks
* When the VM configuration and guest support the intended memory hot-plug operation
- Only when the engine database is renamed
> The course permits supported runtime memory additions. Validate the guest and VM constraints before applying the change.
```

```quiz score
Q: 6.047. What should be checked before combining huge pages with a runtime memory resize?
- Only the number of portal groups
* The exact configuration's compatibility with the intended hot-plug operation
- Only the length of the guest hostname
- Whether all VM disks have identical names
> The recorded course treats huge pages and memory hot-plug as incompatible in its discussed configuration. Check release and configuration constraints when applying that lesson.
```

## Task 5: Describe Log Files and Event Management

```quiz score
Q: 6.048. What are the three main categories of log files in OLVM? Select the option listing the complete correct set.
* Engine installation log files; oVirt engine log files; Host log files
- Network log files; oVirt engine log files; Host log files
- Engine installation log files; oVirt engine log files; Network log files
- Engine installation log files; Network log files; Host log files
> OLVM log files are commonly grouped into engine installation logs, oVirt engine logs, and host logs. These categories separate setup activity, ongoing engine operation, and host-side virtualization behavior.
```

```quiz score
Q: 6.049. Which log category should be examined when engine installation or setup fails?
- Only the physical switch's VLAN table
- Only the SMTP relay's contact list
* Engine setup logs
- Only the guest application's access log
> The events and logs course separates setup logs from engine runtime and host logs. Exact filenames and paths should be verified for the installed release.
```

```quiz score
Q: 6.050. How do detailed setup logs and a setup summary complement each other?
- Both replace the engine's operational database
- The detailed log grants all portal permissions
* The detailed log supports troubleshooting individual actions while the summary records the setup outcome and selected values
- The summary contains every guest disk sector
> The course describes detailed setup information and summary output. Use each for its diagnostic purpose and verify actual log names in the environment.
```

```quiz score
Q: 6.051. What does the engine-setup.log file contain?
* Detailed information about each step and action during setup or upgrade
- Network configurations
- Error messages only
- Summary only
> `engine-setup.log` contains detailed information about each setup or upgrade step. It is the right log to inspect when an install or upgrade fails or behaves unexpectedly.
```

```quiz score
Q: 6.052. Where are oVirt engine log files located?
* /var/log/ovirt-engine
- /var/log/engine
- /etc/ovirt/logs
- /var/log/olvm
> oVirt engine logs are also located under `/var/log/ovirt-engine`. This directory contains both installation-related and runtime engine logs.
```

```quiz score
Q: 6.053. Why distinguish general engine, server, audit, and boot log information?
- Only the filenames matter and the contents can be ignored
* They provide different evidence about operations, server processing, recorded actions, and startup
- Each is a separate physical KVM host
- Each always contains an identical copy of every guest disk
> The recorded course presents several engine log categories. File names and logging arrangements can vary, so choose the evidence by purpose and confirm actual files.
```

```quiz score
Q: 6.054. What does the engine.log file record?
- Boot process only
- Audit trails only
- User authentication only
* General operation information about oVirt engine (starting/stopping services, API requests)
> `engine.log` records general oVirt engine activity such as service start/stop events and API requests. It is often the first engine-side log to inspect for operational problems.
```

```quiz score
Q: 6.055. What does the server.log file contain?
- User activities
* Detailed information about server-side processes
- Boot messages only
- Audit information
> `server.log` contains more detailed information about server-side processes. It helps diagnose problems in the application server and backend services.
```

```quiz score
Q: 6.056. What does the audit.log file contain?
- General operations
* Security events and audit trails (user actions, authentication, permission changes)
- Network events
- Boot process
> `audit.log` records security and audit events such as user actions, authentication events, and permission changes. It is important for accountability and access troubleshooting.
```

```quiz score
Q: 6.057. What does the boot.log file record?
- API calls
- Shutdown events
- User logins
* The booting process of oVirt engine
> `boot.log` records the oVirt engine boot process. It is useful when the engine service fails to start cleanly or has initialization problems.
```

```quiz score
Q: 6.058. When investigating a host-side virtualization problem, why consult system logs as well as VDSM logs?
* System logs can reveal relevant service or kernel events outside the VDSM log
- All libvirt evidence is guaranteed to exist only in one universal file
- The engine browser generates every kernel message
- System logs replace the VM's disks
> The course relates host system and virtualization logs. Depending on logging configuration, system files or the journal may supply additional service evidence.
```

```quiz score
Q: 6.059. What does the SPM lock.log file detail?
- User logins
- VM migrations
* Host ability to obtain a lease on storage pool management
- Network traffic
> `spm-lock.log` details whether a host can obtain the lease for storage pool management. This is useful when troubleshooting SPM election or storage metadata coordination.
```

```quiz score
Q: 6.060. Where is the SPM lock.log file located?
* /var/log/vdsm/spm-lock.log
- /var/log/ovirt
- /var/log/storage
- /var/log/spm
> `spm-lock.log` is located at `/var/log/vdsm/spm-lock.log`. It is a host-side VDSM log focused on SPM lease behavior.
```

```quiz score
Q: 6.061. Where is the VDSM service log file located?
- /var/log/messages
- /var/log/vdsm.log
- /var/log/ovirt/vdsm
* /var/log/vdsm/vdsm.log
> The VDSM service log is `/var/log/vdsm/vdsm.log`. This is the main host-side log for VM, migration, storage, and host-management operations.
```

```quiz score
Q: 6.062. A host fails during engine registration. Which evidence should be included in diagnosis?
- Only the browser's saved passwords
- Only the Grafana dashboard logo
* Host deployment details together with relevant engine and host logs
- Only an unrelated VM's desktop screenshot
> Deployment has its own diagnostic information. Exact temporary or deployment log paths depend on the procedure and environment.
```

```quiz score
Q: 6.063. Where are VM import log files located?
- /tmp/imports
- /var/log/imports
- /var/log/vm-import
* /var/log/vdsm/import/import-<UUID>-<date>
> VM import logs are located under `/var/log/vdsm/import/import-<UUID>-<date>`. The UUID and date identify a specific import operation.
```

```quiz score
Q: 6.064. What does the supervdsm.log file record?
* VDSM tasks executed with superuser permissions
- VM migrations only
- Network configurations
- All VDSM activities
> `supervdsm.log` records VDSM tasks that need superuser privileges. This helps distinguish privileged host operations from ordinary VDSM activity.
```

```quiz score
Q: 6.065. Where is the supervdsm.log file located?
* /var/log/vdsm/supervdsm.log
- /var/log/root
- /var/log/admin
- /var/log/supervdsm
> `supervdsm.log` is located at `/var/log/vdsm/supervdsm.log`. It is part of the host-side VDSM log set.
```

```quiz score
Q: 6.066. What does the mom.log file contain?
- User activities
- Storage usage
- Network bandwidth
* Memory overcommitment information and MoM (Memory Overcommitment Manager) activities
> `mom.log` contains Memory Overcommitment Manager activity and memory overcommit information. It is useful when diagnosing memory optimization or ballooning behavior.
```

```quiz score
Q: 6.067. Where is the upgrade.log file located?
- /var/log/ovirt/upgrade
- /tmp/upgrade
- /var/log/upgrade
* /var/log/vdsm/upgrade.log
> `upgrade.log` is located at `/var/log/vdsm/upgrade.log`. It records host-side upgrade activity related to VDSM or host components.
```

```quiz score
Q: 6.068. What notification methods does OLVM support? Select the option listing the complete correct set.
- Slack integration; SNMP traps
- Email notifications; SMS notifications
- SMS notifications; SNMP traps
* Email notifications; SNMP traps
> OLVM supports email notifications and SNMP traps for events. These methods allow alerts to reach administrators and external monitoring platforms.
```

```quiz score
Q: 6.069. What is the role of event notifier configuration?
- Choosing the host CPU architecture
- Allocating storage extents for VM disks
* Defining notification delivery settings for the events that should be sent
- Assigning every guest its root password
> The notifier lesson configures delivery such as mail and SNMP. Delivery configuration and recipient event subscriptions both matter.
```

```quiz score
Q: 6.070. What parameter specifies the SMTP mail server address?
- notification_server
- smtp_server
- email_server
* mail_server
> `mail_server` specifies the SMTP server address used for email notifications. The notifier needs this value to know where to send mail.
```

```quiz score
Q: 6.071. How should the notifier's SMTP connection be configured?
- By opening every possible mail port regardless of relay configuration
- By using the iSCSI discovery port for mail
* To match the relay's required port, encryption, and authentication settings
- By placing relay credentials inside each VM snapshot
> The course gives common SMTP port examples. Use the relay's actual requirements rather than assuming one universal port and security mode.
```

```quiz score
Q: 6.072. What parameter specifies the username for mail server authentication?
- smtp_user
* mail_user
- email_user
- auth_user
> `mail_user` specifies the username used to authenticate to the mail server. It is paired with the password when SMTP authentication is required.
```

```quiz score
Q: 6.073. What parameter contains the password for mail authentication?
- smtp_password
* mail_password
- email_password
- auth_password
> `mail_password` contains the password for mail server authentication. This credential allows the notifier to send through an authenticated SMTP service.
```

```quiz score
Q: 6.074. Why select the appropriate SNMP version when configuring traps?
- The engine cannot send any notifications without a guest agent
- Every version provides identical authentication and encryption
* The notifier and receiving manager must agree on supported version and security settings
- SNMP version selects the VM disk format
> The recorded SNMP lesson discusses version choices. Compatibility and security settings need to match the receiving system.
```

```quiz score
Q: 6.075. Which SNMPv3 security-level mapping is correct?
- The three names are storage quota modes
- All three levels always encrypt all traffic
* NoAuthNoPriv: neither; AuthNoPriv: authentication only; AuthPriv: authentication and privacy
- NoAuthNoPriv: encryption only; AuthNoPriv: neither; AuthPriv: no security
> The lesson distinguishes authentication and privacy. Choose the required security level and configure corresponding credentials.
```

```quiz score
Q: 6.076. What does NoAuthNoPriv security level provide?
- Full encryption
- Encryption only
* SNMP traps sent without authentication or encryption (clear text)
- Password authentication only
> NoAuthNoPriv sends SNMP traps without authentication or encryption. It is the least secure option because traffic is effectively clear text.
```

```quiz score
Q: 6.077. What does AuthNoPriv security level provide?
* Password authentication but no encryption (clear text transmission)
- Full encryption
- Authentication and encryption
- No security
> AuthNoPriv provides authentication but no encryption. The receiver can verify identity, but the transmitted data is not encrypted.
```

```quiz score
Q: 6.078. What does AuthPriv security level provide?
- No security
- Encryption only
- Password authentication only
* Password authentication and encryption (highest security)
> AuthPriv provides both authentication and encryption, making it the strongest SNMPv3 security level listed here. It protects both identity and privacy of trap data.
```

```quiz score
Q: 6.079. What configuration must align between an SNMP notifier and its receiving manager?
- Only the VM's disk size
- Only the count of engine repositories
* Destination address, supported SNMP version, and the applicable community or security credentials
- Only the name of the user's favorite template
> The destination and protocol settings must match. For SNMPv3, configure the required authentication and privacy settings for the chosen level.
```

```quiz score
Q: 6.080. What is the oVirt log collector diagnostic tool used for?
* Collecting comprehensive logs from manager and attached hosts for troubleshooting
- Creating new logs
- Deleting old logs
- Encrypting logs
> The oVirt log collector gathers comprehensive logs from the manager and attached hosts for troubleshooting. It packages relevant diagnostic data so administrators or support can analyze issues more efficiently.
```

```quiz score
Q: 6.081. What package should be installed on manager and hosts for system diagnostics?
- rsyslog
* sos (sosreport)
- syslog
- logrotate
> The `sos` package provides `sosreport`, a system diagnostic collection tool. It is useful alongside OLVM-specific logs when investigating host or manager problems.
```

```quiz score
Q: 6.082. What command is used to install the ovirt-log-collector package?
- dnf install olvm-logs
* yum install ovirt-log-collector
- dnf install log-collector
- yum install log-diagnostic
> `yum install ovirt-log-collector` installs the oVirt log collector package. The quiz uses the package-management command and package name expected for this tool.
```

```quiz score
Q: 6.083. Which diagnostic collection is relevant to an engine-side incident?
- Only the administrator's browser bookmarks
- Only guest desktop images and unrelated ISO files
- Only a list of VLAN colors
* Engine service logs, relevant database logs, and a system diagnostic report
> The collector lesson assembles multiple engine-side evidence sources. Include affected-host evidence when relevant and available.
```

```quiz score
Q: 6.084. What parameter skips PostgreSQL database logs when collecting?
* --no-postgresql
- --exclude-db
- --skip-database
- --no-database
> `--no-postgresql` skips PostgreSQL database logs during log collection. This is useful when database logs are unnecessary or should be excluded from a support bundle.
```

```quiz score
Q: 6.085. What parameter skips host log collection?
* --no-hypervisors
- --exclude-hosts
- --no-hosts
- --skip-hosts
> `--no-hypervisors` skips host or hypervisor log collection. Use it when you only need manager-side logs or want to reduce collection scope.
```

```quiz score
Q: 6.086. Why might a log collector limit the number of hosts collected per cluster?
* To obtain a manageable representative diagnostic collection for the incident
- To delete all uncollected hosts from OLVM
- To move every VM onto the engine
- To change all cluster CPU models
> The collector can control which host information is gathered. Select scope appropriate to the problem, not just the smallest possible archive.
```

## Task 6: Monitoring and Observability

```quiz score
Q: 6.087. Which database-role mapping helps choose a monitoring data source?
* engine: operational data; ovirt_engine_history: historical reporting data
- engine: all guest disks; ovirt_engine_history: physical NIC firmware
- Both are unrelated copies of every guest filesystem
- Both contain only browser preferences
> The events and monitoring course distinguishes operational and history databases. Lab 7 uses the history database for monitoring.
```

```quiz score
Q: 6.088. What does the engine database contain?
- Archived logs
- Backup data
- Historical data only
* Active operational data
> The `engine` database contains active operational data for OLVM. It is the primary database used by the running engine.
```

```quiz score
Q: 6.089. What does the ovirt_engine_history database contain?
- User accounts
* Historical configuration information and statistical metrics collected over time
- Active data only
- Network configurations
> `ovirt_engine_history` stores historical configuration information and statistical metrics over time. This database supports reporting, monitoring, and Grafana/DWH views.
```

```quiz score
Q: 6.090. What command switches to the postgres user?
* su - postgres
- sudo postgres
- switch postgres
- su postgres
> `su - postgres` switches to the PostgreSQL operating system user with that user's login environment. The dash matters because it loads the target user's environment.
```

```quiz score
Q: 6.091. What command connects to the engine database?
- postgres -d engine
* psql -d engine
- psql engine
- connect engine
> `psql -d engine` connects to the `engine` database using the PostgreSQL command-line client. The `-d` option selects the database.
```

```quiz score
Q: 6.092. What PostgreSQL command lists all databases?
- show databases
* \l
- \databases
- list databases
> In PostgreSQL `psql`, `\l` lists databases. It is one of the built-in psql meta-commands.
```

```quiz score
Q: 6.093. What PostgreSQL command shows connection information?
- \connection
* \conninfo
- \info
- \conn
> `\conninfo` shows the current database connection information. It helps confirm which database and user session you are connected to.
```

```quiz score
Q: 6.094. What PostgreSQL command lists all tables?
- show tables
- \tables
* \dt
- list tables
> `\dt` lists tables in the current database schema. It is the psql meta-command for showing relation tables.
```

```quiz score
Q: 6.095. What PostgreSQL command describes a specific table structure?
- \desc tablename
* \d tablename
- show tablename
- describe tablename
> `\d tablename` describes the structure of a specific PostgreSQL table. It shows columns, types, indexes, and related table metadata.
```

```quiz score
Q: 6.096. What PostgreSQL command quits the database prompt?
- \quit
* \q
- quit
- exit
> `\q` quits the PostgreSQL `psql` prompt. It is the standard psql exit command.
```

```quiz score
Q: 6.097. Before following Grafana-specific diagnostic commands, what environment detail is useful to verify?
- Only the user's preferred portal language
- Only the ISO filename suffix
* The installed Grafana version and service configuration
- Only the number of letters in each VM name
> The Grafana demonstration checks installed software context. Commands and configuration details should be matched to the environment.
```

```quiz score
Q: 6.098. What port does Grafana typically use?
- 8080
- 9090
- 5432
* 3000
> Grafana typically listens on port 3000. Administrators must account for this port when accessing Grafana dashboards or configuring proxies.
```

```quiz score
Q: 6.099. What database does Grafana connect to for OLVM monitoring?
* ovirt_engine_history (DWH database)
- grafana database
- engine database
- admin database
> Grafana connects to the `ovirt_engine_history` DWH database for OLVM monitoring. The history database contains the time-series metrics that dashboards visualize.
```

```quiz score
Q: 6.100. What permission level should be granted to the Grafana database user?
* Read-only (SELECT) permissions on DWH database
- Full admin access
- Root access
- Write access
> The Grafana database user should have read-only SELECT permissions on the DWH database. Grafana needs to query monitoring data, not administer or modify the OLVM databases.
```

## Acknowledgements

- **Author** - OLVM training team
- **Sources** - User-supplied training transcripts, section quiz-bank files, and Lab 7 OLVM content
- **Last Updated** - October 2026
