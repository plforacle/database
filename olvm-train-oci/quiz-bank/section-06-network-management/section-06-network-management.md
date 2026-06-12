# Section 6: Network Management
## Introduction

This lab contains the Section 6 practice questions for **Network Management** from the OLVM Exam 1Z0-1170 quiz bank.

Estimated Time: 60 minutes

```quiz-config
passing: 75
badge: images/badge.png
```

### Objectives

In this lab, you will review the OLVM concepts covered by this exam objective and check your understanding with practice questions.

## Practice Questions

```quiz

Q: 1. What is network bonding?
- A. Connecting VMs to the network
* B. Combining multiple network interfaces into a single logical interface to increase throughput and provide redundancy
- C. Encrypting network traffic
- D. Creating virtual networks
> Network bonding combines multiple physical NICs into one logical interface. This can increase available throughput and provide redundancy if one physical link fails.

Q: 2. Why is network bonding recommended especially on production hosts?
- A. To reduce costs
* B. To ensure high availability and fault tolerance
- C. To simplify configuration
- D. To increase security only
> Bonding is recommended on production hosts because it improves availability and fault tolerance. If a cable, NIC, or switch path fails, a properly designed bond can keep traffic flowing through another link.

Q: 3. What is the purpose of using VLANs in OLVM?
- A. To increase network speed
* B. To segment network traffic logically for enhanced security and performance
- C. To reduce the number of physical switches needed
- D. To enable wireless connectivity
> VLANs logically separate traffic on shared physical infrastructure. In OLVM, that separation can isolate management, storage, migration, and user or VM traffic for better security and performance.

Q: 4. Which types of traffic can be separated using VLANs? (Choose 3)
* A. Management network traffic
- B. Email traffic
* C. Storage network traffic
* D. User traffic
- E. DNS queries
> VLANs can separate management, storage, and user traffic into different broadcast domains. Email and DNS traffic may traverse networks, but they are not the OLVM traffic categories tested here.

Q: 5. When adding a physical NIC with VLAN support, where should the VLAN be assigned?
- A. To the virtual machine
* B. Directly to the physical interface
- C. To the logical network only
- D. To the cluster
> When using a physical NIC with VLAN support, the VLAN is assigned directly to the physical interface. OLVM then maps logical networks to the VLAN-aware host networking configuration.

Q: 6. What does FQDN stand for?
- A. Fast Query Domain Name
* B. Fully Qualified Domain Name
- C. Forward Query DNS Name
- D. File Query Domain Network
> FQDN means Fully Qualified Domain Name. It is the complete DNS name that identifies a host within its DNS hierarchy, such as `host.example.com`.

Q: 7. Which two types of name resolution must OLVM support? (Choose 2)
* A. Forward name resolution (domain name to IP)
- B. Lateral name resolution
* C. Reverse name resolution (IP to domain name)
- D. Broadcast name resolution
> OLVM needs both forward and reverse DNS resolution. Forward resolution maps names to IP addresses, while reverse resolution maps IP addresses back to names, which helps with host identity and service trust.

Q: 8. Where should DNS servers used for OLVM name resolution be hosted?
- A. As VMs inside the OLVM environment
- B. On the engine host
* C. Outside of the OLVM environment
- D. On each KVM host
> DNS servers should be hosted outside the OLVM environment. If DNS runs only inside the virtual environment it supports, an OLVM outage could also break the name resolution needed to recover or manage that environment.

Q: 9. Why should DNS servers be hosted outside the OLVM environment?
- A. To reduce costs
* B. To prevent dependency loops and ensure DNS availability even if the virtual environment encounters issues
- C. To increase DNS performance
- D. Because VMs cannot run DNS services
> External DNS avoids dependency loops. Management components, hosts, and services can still resolve names even when the OLVM virtual environment has problems.

Q: 10. What are logical networks in OLVM?
- A. Physical network cables
* B. Representations of network resources that provide connectivity for KVM virtual machines
- C. Virtual switches only
- D. Network security policies
> Logical networks are OLVM representations of network connectivity used by hosts and VMs. They abstract the physical network into managed networks that can be assigned roles and attached to interfaces.

Q: 11. At what level are network roles assigned to logical networks?
- A. Data Center level
* B. Cluster level
- C. Host level
- D. VM level
> Network roles are assigned at the cluster level. This makes the role consistent for hosts in that cluster and aligns network behavior with cluster-wide VM placement and migration decisions.

Q: 12. What is the purpose of a Management network role?
- A. Running VMs
* B. Handling management traffic between OLVM engine and KVM hosts for administrative tasks
- C. Providing internet access
- D. Storing backups
> The Management role carries traffic between the OLVM engine and KVM hosts for administrative control. Keeping management traffic reliable is critical because the engine uses it to monitor and control hosts.

Q: 13. What is the purpose of a VM network role?
- A. Managing the engine
* B. Carrying traffic generated by virtual machines
- C. Live migration only
- D. Storage access
> A VM network carries traffic generated by virtual machines. This is the network path guest operating systems use for application, user, or service connectivity.

Q: 14. What is the purpose of a Display network role?
- A. Managing VMs
* B. Handling display traffic such as graphical display output of virtual machines
- C. Storing VM images
- D. Network monitoring
> The Display network carries graphical console traffic, such as SPICE or VNC display output. Separating display traffic can prevent console sessions from competing with management or migration traffic.

Q: 15. What is the purpose of a Migration network role?
- A. Moving physical servers
* B. Handling live migration traffic when VMs are moved between hosts without downtime
- C. Backup operations
- D. User authentication
> The Migration network carries live migration traffic when VMs move between hosts. This traffic can be bandwidth-heavy, so separating it helps protect management and VM networks from migration bursts.

Q: 16. What is the name of the default logical network automatically created during OLVM setup?
- A. default_network
* B. ovirtmgmt
- C. management_net
- D. cluster_network
> `ovirtmgmt` is the default logical network created during OLVM setup. It gives the environment an initial management network for engine-to-host communication.

Q: 17. What role is typically assigned to the ovirtmgmt network?
- A. Display role
* B. Management role
- C. Storage role
- D. Migration role
> `ovirtmgmt` typically has the Management role. It is the default network used for management communication between the engine and hosts.

Q: 18. How many logical networks can be configured as the default route for a KVM host?
- A. Zero
* B. One
- C. Two for redundancy
- D. As many as needed
> A KVM host can have only one logical network configured as its default route. Multiple default routes would create ambiguous outbound routing behavior.

Q: 19. What happens if a KVM host loses connectivity to a network marked as "required"?
- A. Nothing, it continues normally
* B. The host will be considered non-operational
- C. Only VMs on that network stop
- D. The host reboots automatically
> If a host loses connectivity to a required network, OLVM marks the host non-operational. Required networks are considered mandatory for the host to safely participate in the cluster.

Q: 20. For VM networks, what is created on the host for each logical network?
- A. A VLAN tag
* B. A bridge (virtual switch)
- C. A firewall rule
- D. A routing table
> For each VM logical network, OLVM creates a bridge on the host. The bridge acts as the host-side virtual switch for VM network attachment.

Q: 21. What does a network bridge act as on a KVM host?
- A. A router
* B. A virtual switch connecting VMs to the physical network
- C. A firewall
- D. A load balancer
> A bridge acts like a virtual switch, connecting VM interfaces to the host's physical network path. It forwards traffic between VM VNICs and the external network.

Q: 22. In bridge network configuration, what do VMs use to connect to bridges?
- A. Physical NICs directly
* B. VNICs (Virtual Network Interface Cards)
- C. USB adapters
- D. Wireless connections
> VMs connect to host bridges through VNICs. The VNIC is the virtual network adapter presented to the guest operating system.

Q: 23. Can a single VM have multiple VNICs connected to different bridges?
- A. No, only one VNIC per VM
* B. Yes, VMs can have multiple VNICs connected to different bridges
- C. Only in special configurations
- D. Only on physical hosts
> A VM can have multiple VNICs, and each VNIC can connect to a different bridge or logical network. This allows one VM to participate in multiple networks, such as application and backup networks.

Q: 24. How do VMs on different hosts in the same logical network communicate?
- A. They cannot communicate
* B. Through the physical network infrastructure that connects the hosts
- C. Only through the engine
- D. Only through storage domains
> VMs on different hosts communicate through the physical network infrastructure that connects those hosts. The engine manages configuration, but it does not forward normal VM traffic.

Q: 25. What does VLAN stand for?
- A. Virtual LAN
* B. Virtual Local Area Network
- C. Verified LAN
- D. Variable Link Access Network
> VLAN stands for Virtual Local Area Network. VLANs create separate logical Layer 2 networks over shared physical switching infrastructure.

Q: 26. What is a VLAN identified by?
- A. MAC address
- B. IP address
* C. VLAN ID
- D. Hostname
> A VLAN is identified by its VLAN ID. Switches and host interfaces use that ID to tag and separate traffic.

Q: 27. What device uses the VLAN ID to segregate traffic?
- A. The router
* B. The switch
- C. The firewall
- D. The DNS server
> Switches use VLAN IDs to segregate traffic at Layer 2. The switch decides which ports and trunks carry each VLAN.

Q: 28. If a host does not have enough physical NICs, what can be used instead?
- A. Virtual NICs only
* B. VLAN configurations to create multiple virtual LANs
- C. Bonded interfaces only
- D. Additional virtual machines
> When there are not enough physical NICs, VLANs can create multiple virtual LANs over the available physical links. This allows traffic separation without requiring a separate NIC for every network.

Q: 29. Why must VLANs be configured on the physical network infrastructure before use in OLVM?
- A. To reduce costs
* B. To support logical networks that require VLANs for traffic segregation
- C. To increase security only
- D. To enable wireless access VIRTUAL NICS (VNICs)
> The physical network must be configured for the VLANs before OLVM uses them. If switches and trunks do not carry the VLAN IDs, OLVM logical networks with VLAN tagging will not pass traffic correctly.

Q: 30. What does a VM use to connect to a logical network?
- A. Physical NIC directly
* B. VNIC (Virtual Network Interface Controller)
- C. USB adapter
- D. Serial port
> A VM uses a VNIC to connect to a logical network. The guest OS sees the VNIC as its network adapter.

Q: 31. What are VNICs always attached to on the KVM host?
- A. A VLAN
* B. A bridge
- C. A router
- D. A firewall
> VNICs attach to bridges on the KVM host. The bridge is the virtual switching layer that connects VM network traffic to the host's physical or bonded interface.

Q: 32. What does Oracle Linux Virtualization Manager automatically assign to a VNIC?
- A. IP address
* B. MAC address
- C. VLAN ID
- D. Hostname
> OLVM automatically assigns a MAC address to a VNIC. The MAC address provides Layer 2 identity for the virtual network adapter.

Q: 33. What does each MAC address assigned to a VNIC correspond to?
- A. Multiple VNICs
* B. A single VNIC
- C. All VNICs on a host
- D. All VNICs in a cluster
> Each assigned MAC address corresponds to a single VNIC. Unique MAC addresses prevent Layer 2 identity conflicts on the network.

Q: 34. What is the purpose of the MAC address on a VNIC?
- A. Encryption
* B. Identifying the VNIC on the network and ensuring proper communication
- C. Routing only
- D. Storage access
> The VNIC MAC address identifies that virtual adapter on the network and allows Ethernet communication to work correctly. It is not used for encryption or storage access.

Q: 35. In a bonded network configuration, what are multiple NICs combined into?
- A. Multiple separate networks
* B. A single logical interface (bond)
- C. Virtual switches
- D. VLAN tags
> In a bonded configuration, multiple physical NICs are combined into a single logical bond. The bond is then used as one network interface from the host configuration perspective.

Q: 36. After creating a bond, what is created over the bonded interface?
- A. A firewall
* B. A bridge
- C. A VM
- D. A storage domain
> After creating a bond, a bridge is created over the bonded interface for VM network traffic. VMs attach to the bridge while the bond provides the physical uplink redundancy or bandwidth.

Q: 37. What benefit does bonding provide for network transmission?
- A. Reduces costs
* B. Combines transmission capabilities of all NICs for increased bandwidth and redundancy
- C. Simplifies configuration only
- D. Reduces power consumption
> Bonding can combine NIC transmission capacity and provide redundancy depending on the bonding mode and switch configuration. The goal is better throughput, resilience, or both.

Q: 38. What does pass-through networking allow?
- A. Encrypted connections only
* B. Network traffic to bypass the software network stack, providing direct access to physical NICs
- C. Faster VM creation
- D. Automatic IP assignment
> Pass-through networking lets VM traffic bypass much of the host software network stack and access a physical NIC more directly. This can improve performance for workloads that need lower latency or higher throughput.

Q: 39. What is a benefit of pass-through networking?
- A. Easier configuration
* B. Reduced latency and improved performance
- C. Lower cost
- D. More VMs per host
> The main benefit of pass-through networking is reduced latency and improved performance. Direct hardware access removes some virtualization overhead compared with bridged virtual networking.

Q: 40. What is a limitation of VMs with pass-through NICs?
- A. They use more memory
* B. They are less flexible in terms of live migration because specific physical hardware must be available on the target
- C. They cannot connect to networks
- D. They require more CPU cores
> A VM using a pass-through NIC is less flexible for live migration because the destination host must have compatible physical hardware available. That hardware dependency can prevent or constrain migration.

Q: 41. Which CPU features must be supported for pass-through networking? (Choose 2)
- A. Intel VTX
* B. Intel VT-d
- C. AMD-V
* D. IOMMU support (Intel VT-d or AMD-Vi)
> Pass-through networking requires I/O virtualization support such as Intel VT-d or AMD-Vi/IOMMU. These features allow safe assignment of physical devices to virtual machines.

Q: 42. Can a physical NIC in pass-through mode be shared with other VMs on the same host?
- A. Yes, freely shared
* B. No, it cannot be shared while in pass-through mode
- C. Only with the same operating system
- D. Only in high-availability mode
> A physical NIC in pass-through mode is assigned directly and cannot be shared with other VMs on the same host. Sharing would defeat the dedicated hardware access model.

Q: 43. What does SR-IOV stand for?
- A. Standard Root IO Virtualization
* B. Single Root IO Virtualization
- C. Shared Resource IO Virtualization
- D. Secure Root IO Virtualization
> SR-IOV stands for Single Root I/O Virtualization. It is a hardware virtualization feature that exposes multiple virtual functions from one physical PCIe device.

Q: 44. What does SR-IOV allow?
- A. Multiple operating systems on one VM
* B. A single physical network interface to be divided into multiple virtual interfaces (virtual functions)
- C. Faster storage access
- D. Encrypted networking
> SR-IOV lets one physical NIC expose multiple virtual functions that can be assigned to VMs. This provides more direct hardware-backed networking while allowing a single adapter to serve multiple consumers.

Q: 45. How do you display virtual functions associated with network interfaces in the Administration Portal?
- A. Click Enable VF
* B. Click Show Virtual Functions button
- C. Edit host properties
- D. Create new network
> The Administration Portal provides a Show Virtual Functions button to display virtual functions associated with network interfaces. This exposes the SR-IOV resources available for assignment.

Q: 46. How do you associate a logical network with a virtual function?
- A. Edit the VM
* B. Drag and drop the logical network from unassigned to the virtual function
- C. Run a command line tool
- D. Restart the host
> A logical network can be associated with a virtual function by dragging it from the unassigned area to the virtual function. This maps the OLVM logical network to the SR-IOV VF resource.

Q: 47. Where in the Administration Portal do you configure VM network interfaces?
- A. Storage -> Networks
* B. Compute -> Virtual Machines -> Network Interfaces tab
- C. Network -> Interfaces
- D. Configuration -> NICs
> VM network interfaces are configured from Compute -> Virtual Machines -> Network Interfaces. That tab is where administrators add, edit, and inspect VNICs for a VM.

Q: 48. What can you configure for a VM's network interface? (Choose 2)
* A. Custom MAC address
- B. CPU allocation
* C. MAC address from pool
- D. Memory size
> A VM interface can use a custom MAC address or a MAC address assigned from the configured pool. These options control the Layer 2 identity of the VM's VNIC.

Q: 49. After configuring a custom MAC address for a VM's VNIC, what must you do for the changes to take effect?
- A. Reboot the host
- B. Restart the engine
* C. Start or restart the virtual machine
- D. Reboot all hosts
> After changing a custom MAC address, the VM must be started or restarted for the change to take effect. The guest needs the VNIC to be reinitialized with the new MAC value.

Q: 50. In the VM details page, which tab shows network interface information?
- A. General
* B. Network Interfaces
- C. Hardware
- D. Settings
> The Network Interfaces tab on the VM details page shows VNIC information. It is the primary place to inspect and manage a VM's network adapters.

```

## Acknowledgements

- **Author** - Perside Foster
- **Source Material** - OLVM bootcamp training and Oracle Linux Virtualization Manager documentation
