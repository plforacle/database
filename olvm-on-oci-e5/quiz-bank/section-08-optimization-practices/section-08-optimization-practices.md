# Section 8: Optimization Practices
## Introduction

This lab contains the Section 8 practice questions for **Optimization Practices** from the OLVM Exam 1Z0-1170 quiz bank.

Estimated Time: 60 minutes

```quiz-config
passing: 75
badge: images/badge.png
```

### Objectives

In this lab, you will review the OLVM concepts covered by this exam objective and check your understanding with practice questions.

## Practice Questions

```quiz

Q: 1. What does memory overcommitment in OLVM allow?
- A. Using only physical memory
* B. Total memory allocated to VMs can exceed physical memory capacity of the host
- C. Reducing VM density
- D. Disabling memory management
> Memory overcommitment allows the total memory assigned to VMs to exceed the host's physical RAM. This relies on the fact that not every VM uses all allocated memory at the same time.

Q: 2. What is a benefit of memory overcommitment?
- A. Lower costs only
* B. Higher VM densities per host, maximizing resource efficiency
- C. Simpler configuration
- D. Reduced security
> The benefit of memory overcommitment is higher VM density per host. It lets administrators use physical memory more efficiently when workloads have variable or underused memory demand.

Q: 3. What can excessive memory overcommitment lead to?
- A. Faster performance
* B. Performance degradation if not managed effectively
- C. Increased security
- D. Lower VM density
> Excessive overcommitment can degrade performance because VMs may compete for memory the host cannot actually provide. If pressure becomes high, paging, swapping, or ballooning can hurt workload responsiveness.

Q: 4. What happens with higher overcommit threshold values?
- A. They increase memory usage
* B. They conserve physical memory but may increase CPU usage
- C. They disable overcommitment
- D. They reduce CPU usage
> Higher overcommit thresholds conserve physical memory by allowing more aggressive sharing or reclaim behavior, but that management work can increase CPU overhead. Optimization is a tradeoff between density and performance.

Q: 5. What does MoM stand for in OLVM?
- A. Mode of Management
* B. Memory Overcommitment Manager
- C. Monitor of Memory
- D. Management of Modules
> MoM stands for Memory Overcommitment Manager. It helps manage memory overcommit behavior on hosts by coordinating memory optimization mechanisms.

Q: 6. What is KSM in the context of memory management?
- A. Key Storage Manager
* B. Kernel Same-page Merging
- C. KVM System Memory
- D. Kernel Security Module
> KSM stands for Kernel Same-page Merging. It is a Linux kernel feature that identifies identical memory pages and consolidates them to save RAM.

Q: 7. What does KSM do?
- A. Encrypts memory pages
* B. Identifies identical memory pages across VMs and merges them into a single copy
- C. Increases memory allocation
- D. Backs up memory to storage
> KSM scans for identical memory pages across VMs and merges them into one shared copy. This is especially useful when many VMs run similar operating systems or applications.

Q: 8. Can OLVM hosts allocate more virtual CPUs to VMs than physical CPU cores available?
- A. No, never allowed
* B. Yes, vCPUs can exceed physical CPU core count
- C. Only with special licensing
- D. Only in test environments
> OLVM can allocate more vCPUs to VMs than the number of physical CPU cores. This CPU overcommitment works because not every vCPU is busy at the same time, but it must be monitored for contention.

Q: 9. What ensures fair CPU allocation among VMs on the same host?
- A. Manual intervention
* B. OLVM CPU schedulers
- C. Guest OS schedulers only
- D. Hardware only
> OLVM and the underlying virtualization scheduler help distribute CPU time fairly among VMs on the same host. Guest OS schedulers manage work inside a VM, but the hypervisor controls host CPU allocation between VMs.

Q: 10. How can administrators set CPU limits for VMs?
- A. Only by rebooting
* B. In terms of percentage of total host CPU resources or specific CPU cycles
- C. Through guest OS only
- D. Cannot be set
> Administrators can set CPU limits as a percentage of host CPU resources or in specific CPU-cycle terms. These limits prevent one VM from consuming more CPU than intended.

Q: 11. What is CPU pinning?
- A. Limiting CPU usage
* B. Assigning specific vCPUs to dedicated physical CPU cores or threads on the host
- C. Disabling CPUs
- D. Sharing CPUs among VMs
> CPU pinning maps specific VM vCPUs to dedicated physical CPU cores or threads. It is used when predictable CPU placement matters for performance, latency, or licensing boundaries.

Q: 12. What is a benefit of CPU pinning?
- A. Lower costs
* B. Optimizes performance by reducing cache misses and ensuring consistent access
- C. Simpler configuration
- D. Higher VM density
> CPU pinning can reduce cache misses because a workload runs more consistently on the same physical CPU resources. This is useful for latency-sensitive or performance-critical workloads.

Q: 13. What is memory reservation?
- A. Blocking memory access
* B. Guaranteeing a minimum amount of memory always available to a VM
- C. Encrypting memory
- D. Reducing memory allocation
> Memory reservation guarantees a minimum amount of memory for a VM. Even in an overcommitted environment, the reserved memory is protected for that VM's operation.

Q: 14. What does the memory balloon device allow?
- A. Increasing physical memory
* B. VMs to reclaim unused memory and return it to the host for other VMs
- C. Encrypting memory
- D. Blocking memory access
> The balloon device lets a guest return unused memory to the host so it can be used by other VMs. This allows the host to rebalance memory without shutting down the VM.

Q: 15. Where is memory balloon optimization configured?
- A. VM level only
* B. Cluster level
- C. Data Center level
- D. Host level only
> Memory balloon optimization is configured at the cluster level. That makes the policy consistent across the hosts and VMs governed by the cluster.

Q: 16. What does VirtIO disk optimization do?
- A. Encrypts disks
* B. Pins VirtIO disks to dedicated threads or CPU cores to reduce I/O contention
- C. Increases disk size
- D. Backs up disks
> VirtIO disk optimization pins VirtIO disk processing to dedicated threads or CPU cores. This can reduce I/O contention and improve predictability for disk-heavy workloads.

Q: 17. What is ballooning in memory management?
- A. Expanding physical memory
* B. A technique where a balloon driver requests memory from guest OS to reduce VM memory usage
- C. Encrypting memory
- D. Backing up memory
> Ballooning uses a guest balloon driver to request memory from the guest OS, reducing the VM's effective memory usage. The host can then reclaim that memory for other workloads.

Q: 18. Is the balloon device included in VMs by default?
- A. No, must be manually added
* B. Yes, unless explicitly removed during VM configuration
- C. Only in Linux VMs
- D. Only in Windows VMs
> The balloon device is included by default unless it is explicitly removed during VM configuration. This makes memory reclaim available without needing to add the device manually in most cases.

Q: 19. What must be installed in the guest OS for the balloon device to work?
- A. Additional storage drivers
* B. Relevant balloon drivers
- C. Network drivers
- D. Video drivers
> The guest OS needs the relevant balloon drivers for ballooning to work. Without the guest driver, the host cannot coordinate memory reclaim cleanly inside the VM.

Q: 20. What command is used to check current memory overcommit settings?
- A. olvm-config
* B. engine-config
- C. memory-check
- D. vm-config
> `engine-config` is used to inspect current engine configuration values, including memory overcommit settings. It is the OLVM configuration utility for these engine parameters.

Q: 21. What parameter can you grep to view memory overcommit settings?
- A. maxmem
* B. maxvdsmem
- C. memorymax
- D. vdsmemory
> `maxvdsmem` is the parameter pattern used to view memory overcommit-related settings. Grepping for it helps locate the relevant engine configuration entries.

Q: 22. Which two parameters define memory overcommit ratios?
- A. MaxMemory and MinMemory
* B. MaxVdsMemOverCommit and MaxVdsMemOverCommitForServers
- C. MemoryLimit and MemoryReserve
- D. VMMemory and HostMemory
> `MaxVdsMemOverCommit` and `MaxVdsMemOverCommitForServers` define memory overcommit ratios. These parameters control how aggressively hosts can be overcommitted.

Q: 23. What command is used to change memory overcommit settings?
* A. engine-config -s
- B. olvm-set-memory
- C. vm-memory-config
- D. set-overcommit
> `engine-config -s` sets engine configuration values. After changing these settings, administrators typically need to apply the configuration through the documented service restart or setup workflow.

Q: 24. Where should a fencing proxy host be located?
- A. In a different data center
* B. In the same cluster or data center as the host requiring fencing
- C. On the engine host only
- D. Outside the OLVM environment
> A fencing proxy host should be in the same cluster or Data Center as the host that needs fencing. It must be close enough operationally to reach the target's power-management path.

Q: 25. Which three methods can perform power management operations? (Choose 3)
* A. By the manager after it reboots
* B. By the proxy host
* C. Manually in the administration portal
- D. By the guest OS
- E. By the storage system
> Power management operations can be performed by the manager after it reboots, by a proxy host, or manually in the Administration Portal. These options provide automated and manual recovery paths.

Q: 26. What does OLVM automatically do to non-responsive hosts with power management enabled?
- A. Deletes them
* B. Attempts to fence them
- C. Ignores them
- D. Reboots all VMs
> When power management is enabled, OLVM attempts to fence non-responsive hosts. Fencing protects shared resources by isolating or restarting a host that may no longer be safely controlled.

Q: 27. Why might a host appear non-responsive during boot?
- A. Hardware failure only
* B. Temporary delays due to initialization routines or startup tasks
- C. Network attacks
- D. Storage corruption
> A host can appear non-responsive during boot because initialization routines or startup tasks take time. Without a delay, OLVM might fence a host that is still legitimately starting.

Q: 28. What command parameter prevents fencing during host bootup?
- A. disable-fencing
* B. DisableFenceAtStartupInSeconds
- C. no-fence-boot
- D. boot-fence-disable
> `DisableFenceAtStartupInSeconds` prevents fencing during a host startup window. This reduces false fencing actions while a host is still booting and initializing services.

Q: 29. What does PMHealthCheckEnabled do when set to true?
- A. Disables health checks
* B. Initiates regular health checks for all configured host power management agents
- C. Only checks once
- D. Checks only failed hosts
> When `PMHealthCheckEnabled` is true, OLVM regularly checks configured power-management agents. These checks help confirm fencing devices remain reachable before an emergency.

Q: 30. What does PMHealthCheckIntervalInSeconds specify?
- A. Boot timeout
* B. The interval at which OLVM performs health checks for power management agents
- C. VM restart time
- D. Storage check frequency
> `PMHealthCheckIntervalInSeconds` defines how often OLVM performs those power-management health checks. The interval controls the cadence of ongoing validation.

Q: 31. When does a highly available VM automatically restart or migrate? (Choose 2)
- A. During normal operation
* B. When its original host experiences failure
* C. When scheduled for maintenance
- D. When VM is idle
- E. When storage is full
> A highly available VM can restart or migrate when its host fails or when the host is placed into scheduled maintenance. HA behavior is designed to keep workloads available during host disruption or planned service.

Q: 32. What happens when a host is placed into maintenance mode?
- A. VMs are deleted
* B. VMs are gracefully shut down or migrated to another host before maintenance
- C. VMs continue running on that host
- D. All VMs are paused
> When a host enters maintenance mode, running VMs are gracefully migrated or shut down before maintenance proceeds. This prevents maintenance work from interrupting active workloads unexpectedly.

Q: 33. For VM migration, what must both source and destination hosts access? (Choose 2)
* A. The same data domain where VM disks reside
- B. Different data domains
* C. The same virtual networks and VLANs
- D. Different network segments
> Migration requires both hosts to access the same data domain and the same virtual networks or VLANs. The destination host must be able to reach the VM disks and reproduce its network connectivity.

Q: 34. What is required for highly available VMs?
- A. Single host only
* B. Host must be part of a cluster of two or more available hosts
- C. Local storage only
- D. Shared CPU only
> Highly available VMs require a cluster with at least two available hosts. HA cannot restart a VM elsewhere if there is no other suitable host to run it.

Q: 35. What is the typical size range for huge pages?
- A. 4 KB to 8 KB
* B. 2 MB to 1 GB
- C. 1 KB to 2 KB
- D. 4 GB to 8 GB
> Huge pages typically range from 2 MB to 1 GB. They are larger than standard memory pages and are used to reduce translation overhead for memory-intensive workloads.

Q: 36. What does using huge pages reduce?
- A. Memory capacity
* B. The number of entries required in the system's page table
- C. CPU cores
- D. Storage space
> Huge pages reduce the number of page table entries needed to map memory. Fewer entries can reduce address-translation overhead for large memory allocations.

Q: 37. What is TLB in the context of huge pages?
- A. Total Logical Buffer
* B. Translation Lookaside Buffer - hardware cache for virtual to physical address translations
- C. Thread Load Balancer
- D. Time Limit Buffer
> TLB means Translation Lookaside Buffer, a hardware cache for virtual-to-physical address translations. Efficient TLB use improves memory-access performance.

Q: 38. How do larger page sizes affect TLB?
- A. Increase TLB misses
* B. Reduce TLB misses because fewer translations are needed
- C. Disable TLB
- D. Have no effect
> Larger pages reduce TLB misses because each translation covers more memory. With fewer translations needed, the CPU spends less time resolving memory addresses.

Q: 39. When are huge pages allocated?
- A. Dynamically during runtime
* B. Pre-allocated when a VM starts
- C. Only when needed
- D. After VM is running for 1 hour
> Huge pages are pre-allocated when a VM starts. The memory must be reserved up front because huge pages cannot always be assembled dynamically from fragmented memory.

Q: 40. What is a limitation of huge pages?
- A. Cannot use with Linux VMs
* B. Do not support hotplug or hot unplug operations for memory
- C. Reduce performance
- D. Increase memory usage HOT PLUGGING vCPUs
> Huge pages do not support memory hotplug or hot unplug operations. Because huge pages are pre-allocated, they are incompatible with dynamic memory resizing workflows.

Q: 41. What is hot plugging?
- A. Restarting VMs
* B. Adding or removing vCPUs from a running VM without downtime
- C. Increasing temperature
- D. Adding physical CPUs
> Hot plugging lets administrators add or remove vCPUs from a running VM without downtime. It avoids a full VM shutdown when supported by the guest OS and VM configuration.

Q: 42. Can you hot unplug a vCPU that was part of the original VM configuration?
- A. Yes, always
* B. No, only vCPUs that were previously hot plugged can be hot unplugged
- C. Only on Linux VMs
- D. Only with engine restart
> A vCPU that was part of the original VM configuration cannot be hot unplugged. Only vCPUs added later through hotplug can be removed while the VM is running.

Q: 43. What is the minimum number of vCPUs a VM cannot go below?
- A. 1 vCPU
* B. The number it was originally created with
- C. 2 vCPUs
- D. 4 vCPUs
> A VM cannot be reduced below the number of vCPUs it originally had. The original vCPU count is the baseline configuration that hot unplug cannot go under.

Q: 44. What is the MINIMUM number of vCPUs required for hot plugging capability?
- A. 1 vCPU
- B. 2 vCPUs
* C. 4 vCPUs
- D. 8 vCPUs
> The VM needs at least 4 vCPUs for vCPU hot plugging capability in this objective. Below that threshold, the hotplug feature is not available.

Q: 45. What must Windows VMs have installed for hot plugging to work?
- A. Additional drivers only
* B. Guest agent
- C. Service packs
- D. .NET Framework
> Windows VMs need the guest agent for hot plugging to work correctly. The guest agent helps the virtualization platform coordinate changes with the Windows guest OS.

Q: 46. What must the VM's operating system support for hot plugging?
- A. Any OS works
* B. Must explicitly support CPU hot plug functionality
- C. Only Windows is supported
- D. Only Linux is supported
> The guest operating system must explicitly support CPU hotplug. OLVM can expose the operation, but the guest OS must handle CPU addition or removal safely.

Q: 47. What happens each time you hot plug memory to a VM?
- A. VM restarts
* B. It appears as a new memory device under VM devices
- C. Old memory is removed
- D. VM is paused
> Each time memory is hot plugged, it appears as a new memory device under the VM's devices. This reflects how the virtualization layer presents additional memory to the guest.

Q: 48. What happens to hot plugged memory devices when you shut down and restart a VM?
- A. They become permanent
* B. They are cleared from VM devices without reducing the virtual memory space
- C. Memory is lost
- D. VM cannot restart
> After a shutdown and restart, hot-plugged memory devices are cleared from the VM devices list without reducing the configured virtual memory space. The effective memory configuration remains, but the transient device entries are cleaned up.

Q: 49. Can memory be hot plugged while a VM is running?
- A. No, VM must be stopped
* B. Yes, memory can be adjusted on the fly
- C. Only during maintenance mode
- D. Only once per day
> Memory can be hot plugged while the VM is running when the VM and guest OS support it. This lets administrators adjust memory without a downtime event.

Q: 50. What is incompatible with memory hot plug/unplug operations?
- A. VirtIO disks
* B. Huge pages
- C. Balloon devices
- D. Network interfaces
> Huge pages are incompatible with memory hot plug and hot unplug operations. Huge-page memory must be pre-allocated, while hotplug relies on dynamic memory device changes.

```

## Acknowledgements

- **Author** - Perside Foster
- **Source Material** - OLVM bootcamp training and Oracle Linux Virtualization Manager documentation
