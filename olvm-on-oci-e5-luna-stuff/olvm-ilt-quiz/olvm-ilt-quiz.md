# OLVM Instructor-Led Training Quiz

## Introduction

Use this quiz alongside the Luna HOL workshop during your instructor-led OLVM training. The **100 scored questions** follow the workshop's ten parts, with **ten questions per part**. They check OLVM concepts, practical decisions, and verification steps you have just practiced.

Estimated Time: 90-120 minutes, distributed throughout the training session

```quiz-config
passing: 80
badge: images/badge.png
```

### Objectives

In this quiz, you will:

- Explain the OLVM engine, host, guest, network, and storage relationships
- Apply deployment, migration, backup, monitoring, and snapshot concepts to operational scenarios
- Distinguish identity creation, authentication, and permission assignment
- Use answer explanations to identify topics for discussion and further study

### How to Use This Quiz in Class

Open the Luna HOL workshop and this quiz in separate tabs. After completing a workshop part, answer the matching ten-question group, review the explanations with your instructor, and then return to the workshop. The monitoring group follows Part 8; if the walkthrough is skipped, have the instructor explain the dashboards and Data Warehouse before answering that group.

Choose one option unless the question says **Select all that apply**. For those questions, select every correct option and no incorrect options. Click **Check Answer** to see feedback and the explanation. Use **Try Again** after reviewing a missed answer.

Each question has equal weight. Complete all 100 questions to receive the final score. **80 correct answers (80%)** unlock the downloadable badge. Use the first attempt to identify learning needs; retries provide practice after discussion.

The question groups follow the workshop part sequence and check concepts and procedures described in the hands-on workshop. After class, continue with the separate training materials and self-study quiz bank for broader exam preparation.

### Part Guide

| Workshop part | Question IDs | Review focus |
|---|---|---|
| Part 1: Infrastructure Setup | 1.1 through 1.10 | Architecture, engine sizing and setup, certificates, and management interfaces |
| Part 2: KVM Host | 2.1 through 2.10 | Host components, registration, readiness, troubleshooting, and cluster integration |
| Part 3: Networking, Storage, and Test VM Setup | 3.1 through 3.10 | Logical networking, VLANs, shared storage, templates, and guest configuration |
| Part 4: Application Tier | 4.1 through 4.10 | OVA deployment, vNIC mapping, VM startup, and cross-host workload validation |
| Part 5: VM Live Migration | 5.1 through 5.10 | Migration prerequisites, maintenance, SPM, and workload continuity |
| Part 6: Storage Live Migration | 6.1 through 6.10 | Destination storage readiness, disk moves, and operation validation |
| Part 7: OLVM Engine Backup | 7.1 through 7.10 | Backup scope, verification, scripts, scheduling, and recovery purpose |
| Part 8: Monitoring Portal and Grafana (Optional) | 8.1 through 8.10 | Data Warehouse, resource trends, capacity, health, and diagnostic interfaces |
| Part 9: VM Snapshots | 9.1 through 9.10 | Snapshot types, delta storage, preview, commit, undo, and cleanup |
| Part 10: Manage Users and Roles | 10.1 through 10.10 | Identity sources, passwords, importing users, permissions, and access verification |


## Part 1: Infrastructure Setup

Complete this group after Part 1 of the hands-on workshop. Discuss any missed answers before proceeding.

```quiz score

Q: 1.1. What does the OLVM engine manage in the environment you build?
- Only the operating systems inside the guest VMs
- Only the hardware drivers on each KVM host
- Only the disks attached to the engine server
* Hosts, VMs, logical networks, storage, and permissions
> The engine provides central management of the virtualization environment. You use it to configure resources and request VM operations. The KVM hosts run the VMs, while storage domains hold their disks.

Q: 1.2. This workshop runs the engine on separate management infrastructure. How would a self-hosted engine be deployed differently?
- It would run inside every application VM
- It would replace the KVM hosts that run the application VMs
- It would manage the environment without an engine database
* It would run as a VM on the managed KVM infrastructure
> A standalone engine runs separately from the KVM infrastructure it manages, as in this workshop. A self-hosted engine runs as a VM on that infrastructure. Both provide the same central management role.

Q: 1.3. Why do you install the OLVM release package before installing `ovirt-engine`?
- It configures the engine database and administrator account
- It imports the workshop's application VMs
- It adds the KVM hosts to the cluster
* It configures the repositories that supply the OLVM packages
> The release package sets up the required software repositories. Those repositories supply the engine and related packages. Installing the engine and running its configuration wizard are the next steps.

Q: 1.4. You request a VM operation from the engine. Which sequence passes that request to the host's virtualization software?
- Engine to guest agent to PostgreSQL to KVM/QEMU
- Engine to Grafana to libvirt to KVM/QEMU
* Engine to VDSM to libvirt to KVM/QEMU
- Engine directly to KVM, without using VDSM or libvirt
> The engine sends management requests to VDSM on the KVM host. VDSM uses the libvirt API to manage the KVM/QEMU software that runs the VM. This is the sequence shown in the workshop's architecture diagram.

Q: 1.5. The engine service stops, but the KVM hosts remain healthy. What happens to VMs that are already running?
- They stop because the engine executes their instructions
- They automatically migrate because the engine stopped
- They continue running inside the engine database
* They continue running on their KVM hosts
> The hosts run the VMs independently of the engine's management service. An engine outage limits central management, but it does not by itself stop VMs on healthy hosts.

Q: 1.6. You plan to add more hosts and VMs. Why review the engine's CPU, memory, and disk sizing instead of relying only on the minimum requirements?
- The engine must have the same total CPU and memory as all the VMs combined
- The minimum specification guarantees the same performance at every deployment size
- Shared VM storage automatically provides extra CPU and memory to the engine
* The engine needs capacity for the larger management workload
> The workshop presents different sizing recommendations for small, medium, and large environments. Minimum requirements are a starting point. As the environment grows, the engine needs resources for its own management services, not the combined resources of every guest.

Q: 1.7. After installing `ovirt-engine`, why do you still run `engine-setup`?
* The installed software still needs its management services configured
- The package installs only a VM console viewer
- The command replaces the need to register KVM hosts
- The command imports and starts the application VMs
> Installing the package puts the engine software on the server. `engine-setup` then configures its database, web services, certificates, and initial management objects so the manager is ready to use.

Q: 1.8. The browser reports that it does not trust the engine's HTTPS certificate. Which action follows the workshop's certificate procedure?
* Use the configured engine FQDN and import its CA certificate into the browser's trust store
- Use an unrelated hostname to avoid matching the certificate name
- Rename the VM logical network to match the certificate
- Disable the host management network before reopening the portal
> The engine's fully qualified domain name (FQDN) should match the certificate presented by the portal. The browser must also trust the certificate authority (CA) that issued it. Importing the correct CA certificate establishes that trust; it does not fix a wrong hostname.

Q: 1.9. Which operations are described for `engine-setup` in Part 1? (Select all that apply.)
* Configure PostgreSQL, management web services, and the initial administrator
- Import the application OVAs and start their guest services
* Generate certificates and configure the engine firewall
- Create snapshots of the application VMs
> Part 1 lists database configuration, web services, certificates, firewall configuration, and administrator setup. Importing appliances and creating snapshots are procedures in later workshop parts.

Q: 1.10. Which descriptions match the OLVM portals introduced in the workshop? (Select all that apply.)
- Monitoring Portal: assign system-wide administrator permissions
- VM Portal: give every signed-in user unrestricted cluster administration
* Administration Portal: manage hosts, networks, storage, VMs, and permissions
* VM Portal: provide VM operations allowed by the user's permissions
> The Administration Portal provides infrastructure management. VM Portal provides user-facing VM operations, with access controlled by assigned permissions. The Monitoring Portal shows metrics through Grafana; it does not assign OLVM roles.

```


## Part 2: KVM Host

Complete this group after Part 2 of the hands-on workshop. Discuss any missed answers before proceeding.

```quiz score

Q: 2.1. Which service on a KVM host receives management requests from the OLVM engine?
- The guest agent inside an application VM
* VDSM
- The VM Portal running in the user's browser
- The PostgreSQL database on the engine
> VDSM runs on each KVM host. It receives engine requests and reports information about the host and its VMs. This lets the engine manage the host without running the guest workloads itself.

Q: 2.2. When VDSM needs to manage a VM, which host component does it use?
* The libvirt API, which manages KVM and QEMU
- The guest agent, which replaces the host virtualization software
- The engine database, which executes the guest workload
- Grafana, which sends all VM lifecycle commands
> VDSM uses libvirt to request operations such as starting or stopping a VM. Libvirt manages the host's KVM/QEMU components. VDSM is the host management agent, not the hypervisor.

Q: 2.3. In the host architecture diagram, where do KVM and each VM's QEMU process run?
- KVM runs in user space; QEMU runs in kernel space
* KVM runs in kernel space; QEMU runs in user space
- Both run only inside the guest operating system
- Both run as services on the engine instead of the KVM host
> KVM supplies virtualization support inside the Linux kernel. Each VM also has a QEMU process in user space, where ordinary host applications run. The two components work together at different levels of the host operating system.

Q: 2.4. Why install and run a guest agent inside a VM?
- To register the physical KVM host with the engine
- To replace libvirt on the KVM host
* To report guest information, such as its OS and IP addresses
- To create the engine's Default data center and cluster
> The guest agent reports information from inside the VM. VDSM reports and manages host-side activity. Keeping these roles separate helps you choose the right component when troubleshooting missing guest information or host management problems.

Q: 2.5. When adding a KVM host, which hostname or IP address should you enter so the engine can manage it?
- The address of an application VM running on the host
- The engine's own hostname entered again for each host
- The storage server's address instead of the host's address
* The hostname or IP address of the host's reachable management interface
> Use the KVM host's fully qualified hostname (FQDN) or management IP address so the engine can connect to and configure that host. If it has several network connections, choose the one used for management. The hostname must resolve to the correct address.

Q: 2.6. Why add the engine's SSH public key to the host's root `authorized_keys` file?
- To grant guest VM users access to the Administration Portal
* To let the engine authenticate over SSH for host installation and configuration
- To install VDSM immediately without any engine deployment step
- To give the host a replacement management IP address
> The public key authorizes the engine's SSH connection to the host. The engine can then install software and apply host configuration. This OS-level access is separate from permissions assigned to users in the OLVM portal.

Q: 2.7. A host you just added still shows Installing. When should you use it for the next VM deployment steps?
* After installation and initialization finish and the host shows Up
- As soon as its name appears in the Hosts list
- As soon as the Add Host dialog closes
- After putting it in Maintenance while installation is still running
> Installing and Initializing mean that deployment work is still in progress. Wait for Up before placing workloads on the host. If it becomes Non Operational, investigate the problem rather than treating it as ready.

Q: 2.8. A host fails to register successfully. Which logs help you compare what happened on the engine and on the host?
- Only the web application's log inside a guest
- Only a Grafana CPU graph for the host
* `engine.log` on the engine and `vdsm.log` on the KVM host
- Only an older engine backup archive
> The engine log records the manager's operations, while the VDSM log records host management activity. Comparing their messages can help identify connection, deployment, or service problems. The workshop points to `/var/log/ovirt-engine/engine.log` and `/var/log/vdsm/vdsm.log` for these checks.

Q: 2.9. What must the two hosts share or support so a VM can migrate between them? (Select all that apply.)
- A separate data center for each host
* Access to the VM's storage and the required network configuration
- Identical usernames inside every guest VM
* Compatible CPU capabilities
> The destination must be able to run the VM's CPU workload and reach its disks and networks. These requirements let the VM continue operating after the host changes; guest usernames do not establish host compatibility.

Q: 2.10. Before continuing from Part 2, which checks confirm that both hosts have been added as instructed? (Select all that apply.)
* Both hosts show Up in the Administration Portal
- Only the first host needs to show Up
- The Add Host dialog closing is the only check required
* The engine's SSH public key has been added to both hosts
> Part 2's checklist requires the engine public key on both hosts and Up status for each host. Use those checks before moving on to the networking and storage procedures.

```


## Part 3: Networking, Storage, and Test VM Setup

Complete this group after Part 3 of the hands-on workshop. Discuss any missed answers before proceeding.

```quiz score

Q: 3.1. Which logical network role carries traffic between the workshop's application VMs?
- Management network for engine-to-host requests
- Migration network for transferring VM execution state
* VM network
- Display network for graphical console traffic
> The application VMs communicate through a VM network. Management, migration, and display networks carry other types of traffic. Selecting the right role connects the logical network to the purpose it serves.

Q: 3.2. You created a logical network in OLVM. What must you do before a KVM host can use it?
- Assign it only to a template without changing the host configuration
- Rename a storage domain to match the logical network
* Assign it to the correct host interface through Setup Host Networks
- Assume the definition connects it to every host interface automatically
> Creating a logical network defines it in OLVM. Setup Host Networks connects that definition to the host's actual NIC or bond. Part 3 applies the network to both hosts so VMs can use it on either one.

Q: 3.3. How does the Linux bridge connect a VM to the host's physical network?
* It acts as a virtual switch between the VM's vNIC and the host uplink
- It automatically provides a DHCP address to every guest
- It stores the guest's disk data in the host network configuration
- It replaces the physical uplink with a guest-only connection
> The bridge connects virtual network interfaces, or vNICs, to the host's physical network connection. It provides a switching path for guest traffic. It does not by itself assign guest IP addresses or store VM disks.

Q: 3.4. When a logical network has a VLAN ID, how does the host use that ID as explained in Part 3?
* It tags the network's traffic with the VLAN ID
- It uses the VLAN ID as the guest's IP address
- It uses the VLAN ID to select the storage LUN
- It uses the VLAN ID to assign the user's OLVM role
> Part 3 explains that a configured VLAN ID results in a VLAN interface and tagged traffic on the assigned host interface. The VLAN ID is a network setting, not a guest IP address or storage identifier.

Q: 3.5. A VM may need to run on either KVM host. Why must both hosts have access to its disks?
* So the VM can keep using its disks when it moves to or restarts on the other host
- So the destination host can run the VM without access to its disks
- So disks stored on only one host become available on both hosts automatically
- So the VM network does not need to be configured on the other host
> A VM needs access to its disks wherever it runs. If it moves to the other host, or restarts there after a host failure, that host must be able to access the same disks. Shared storage gives both hosts that access.

Q: 3.6. Which storage setup is ready to hold the VM disks in Part 3?
* A Fibre Channel Data domain attached to the intended data center and showing Active
- An ISO domain used as the destination for writable guest disks
- An Export domain whose name is the only readiness check
- A directory available only on one host with no shared access
> Part 3 selects a Fibre Channel logical unit, or LUN, for a shared Data domain. The domain must be attached to the data center and reach Active before you use it. Data domains hold VM disks; a listed domain name alone does not confirm readiness.

Q: 3.7. Why must the OVA file be available on the host selected for import?
- OLVM reads every appliance directly from the engine database
- OLVM automatically uploads any file in your workstation's Downloads folder
- The file must already exist inside the VM being created
* OLVM reads the file from that host at the path entered in the import dialog
> Host and File Path tell OLVM where to read the OVA. Place the file on the selected host and enter its actual path there. A file on another machine is not available at that host's path automatically.

Q: 3.8. The VM network has no DHCP service. What should you enter in Initial Run for the test guest?
* A valid static IP address, netmask, gateway, and DNS settings
- Only the logical network name, which assigns an IP automatically
- The engine's management address as the guest's own IP
- The storage LUN number as the guest's network address
> Attaching a vNIC to a logical network provides a connection, but the guest still needs IP settings. In Part 3, Initial Run supplies those settings because no DHCP service assigns them. Use values appropriate for the guest's network.

Q: 3.9. Which statements match Part 3's explanation of templates and thin provisioning? (Select all that apply.)
- The template itself runs as every VM created from it
* A template is a starting image for VMs that can be started and stopped independently
* Thin provisioning saves space by storing differences from the template for each VM
- Thin provisioning always stores a full separate copy of the template disk for every VM
> Part 3 uses the term thin provisioning. Its explanation says the template disk is stored once and each VM disk stores differences from that template. A template cannot start directly; a VM created from it has its own lifecycle.

Q: 3.10. After starting the test VM, which checks help confirm its network is configured correctly? (Select all that apply.)
* Inspect the guest's network interface and IP address
* Ping the gateway from inside the guest
- Check only that the VM name appears in OLVM
- Check only that the OVA file exists on the import host
> Look inside the guest to verify its interface and address, then test gateway reachability. These checks exercise the actual guest network connection. A successful import or a visible VM record does not show that networking works.

```


## Part 4: Application Tier

Complete this group after Part 4 of the hands-on workshop. Discuss any missed answers before proceeding.

```quiz score

Q: 4.1. What do you reuse when you import one of the prepared application OVAs?
* The VM's saved disks and configuration, including its OS and installed software
- Only a VM name that must be assigned new empty disks
- Only a user account for the OLVM engine
- The physical host's operating system and hardware drivers
> An Open Virtual Appliance (OVA) contains an exported VM's disks and configuration. Importing it lets you deploy the prepared guest without repeating its OS and software installation. You still need to check its settings in the destination environment.

Q: 4.2. Which statement describes the application deployment in Part 4?
- Both guest operating systems run directly inside the OLVM engine
- Both application services run only inside the Administration Portal
* The database and web application run in separate VMs on the KVM hosts
- The imported templates run the application without creating VMs
> Part 4 imports a database VM and a web application VM, then checks communication between them. The KVM hosts run these guests, while OLVM manages their deployment and configuration.

Q: 4.3. Which import source should you choose for a prepared appliance file?
- An ISO installation in a newly created blank VM
- A new VM with empty disks and no appliance import
- A template selection without importing the appliance file
* Virtual Appliance (OVA)
> Choose Virtual Appliance (OVA) to load the exported VM. This uses the appliance's prepared disks and configuration instead of starting with an empty guest and installing an operating system manually.

Q: 4.4. During an OVA import, why check Network Name and Profile Name for the imported network interface?
- To let the appliance create the physical host's network automatically
* To connect the vNIC to the intended logical network and profile in this environment
- To reuse the source environment's mapping without checking it
- To select a storage domain as the network profile
> The appliance's original network mapping may not match the destination environment. Map its virtual NIC to the intended logical network and vNIC profile before using it. Part 4 checks this so the application VMs can communicate.

Q: 4.5. The database VM and web VM run on different KVM hosts. What provides their network connection in the workshop?
- Their names appear together in the Administration Portal
- Their OVA files were imported from the same directory
* Their vNICs use the intended VM network, which is configured on both hosts and carried between them
- Their application traffic passes through the engine database
> Both VMs need the correct vNIC mapping, and both hosts need the network that carries their traffic. The physical network connects the hosts. The engine configures and manages this arrangement; it is not the device forwarding the application's packets.

Q: 4.6. OLVM shows an imported VM as Up. Why should you still test the application inside it?
- Up means only that the OVA file has been found
- Up guarantees that every guest service is configured correctly
- An application test creates the host's management network
* Up confirms that the VM is running, not that its application services work
> A VM can be running while its database or web service is unavailable. After checking Up, test the guest services and the communication between application tiers, as Part 4 demonstrates.

Q: 4.7. You select Host and File Path in the appliance import dialog. Where must the OVA actually be located?
* At the specified path on the selected host
- Inside the guest VM before it has been created
- In any workstation download folder, regardless of the selected host
- Only in the engine database, with File Path used as a description
> The import dialog tells OLVM which host will read the file and where to find it. Verify that the OVA is accessible at that path on that host. A matching filename elsewhere is not enough.

Q: 4.8. The appliance import finishes and the VM shows Down. What should you do before testing its application?
- Treat Down as proof that the application is already running
- Assign a user role instead of starting the VM
* Click Run, wait for Up, and allow the guest services to start
- Create an engine backup to trigger the VM's startup
> Importing the appliance creates the VM and its disks but may leave it stopped. Start it, confirm Up, and then check service readiness. The VM's power state and the application's availability are separate checks.

Q: 4.9. Which steps belong to the OVA import procedure in Part 4? (Select all that apply.)
* Load and select the appliance from its path on the chosen host
- Delete the cluster's logical network before importing
* Review the imported vNIC's network mapping before completing the import
- Use the OVA file path as a replacement for the VM storage domain
> The procedure reads the appliance from the import host and checks its destination settings. The imported VM still uses OLVM storage and networking; the appliance file's location does not replace those resources.

Q: 4.10. After importing and starting the application VMs, which checks show that the workload is usable? (Select all that apply.)
- The appliance filename appears in the import dialog
- The engine portal opens, without any guest service checks
* The VMs are Up and connected to the intended logical network
* The application responds and its tiers can communicate
> Verify the VM state and network mapping, then test the application itself. These checks confirm both the virtualization setup and the guest services. Reaching the engine portal or finding the OVA file checks only part of the deployment.

```


## Part 5: VM Live Migration

Complete this group after Part 5 of the hands-on workshop. Discuss any missed answers before proceeding.

```quiz score

Q: 5.1. You need to move a VM to another host. How does live migration differ from offline migration?
- Live migration moves only disk data; offline migration moves only memory
- Both require the VM to stay powered off throughout the move
- Live migration creates a new VM from a template instead of moving the existing one
* Live migration moves a running VM; offline migration moves a stopped VM
> Live migration transfers the running VM's execution state to another host. Offline migration starts with the VM stopped. Moving a running VM's disk to another storage domain is storage live migration, which Part 6 covers separately.

Q: 5.2. The destination host cannot reach the VM's shared storage. What should you do before attempting migration?
- Migrate anyway because memory contains everything the VM needs
- Delete the source storage domain before starting the move
* Restore the destination's access to the VM disks
- Change the VM's display name to match the destination host
> After the move, the destination must still read and write the VM's disks. Fix its storage access before migrating; transferring execution does not transfer the disks into host memory.

Q: 5.3. Before migrating a VM, why check the destination host's available CPU and memory?
- Its resources are irrelevant once shared storage is available
- Its CPU and memory only need to match the engine's installation requirements
* It must support the arriving VM alongside the VMs already running there
- The migration automatically removes the destination's existing workloads
> The destination takes over the VM's execution. It needs sufficient resources for that VM and its current workloads. Shared disk access is necessary, but it does not provide extra CPU or memory.

Q: 5.4. Both hosts can access shared storage. Which statement about the Storage Pool Manager (SPM) role matches Part 5?
- Both hosts must always hold the SPM role at the same time
- Each VM has its own SPM role in the same data center
* Only one host holds the SPM role at a time in a data center
- Migrating a VM creates a second SPM role in that data center
> Part 5 states that only one host at a time holds the SPM role per data center. Shared access to storage does not mean that both hosts hold that role simultaneously.

Q: 5.5. A KVM host needs maintenance. What should you do before servicing it while eligible VMs are running?
- Power off the host first and assume the running VMs have migrated
* Request Maintenance and verify that the VMs have moved off the host
- Start the work as soon as the Maintenance request is submitted
- Delete the host's shared storage to force the VMs to move
> Requesting Maintenance lets OLVM move eligible running VMs to suitable hosts. Migration still depends on destination resources, compatible configuration, and the VM's constraints. Check that the evacuation has finished before servicing the host.

Q: 5.6. During live migration, why are some memory pages copied more than once?
- The VM's virtual disks are being deleted page by page
* The running VM can change a page after its first copy
- The destination must recreate the engine database
- The migration replaces the VM with a newly imported appliance
> The VM continues running during the initial memory copy. Pages it changes after copying are called dirty pages. OLVM recopies them so the destination receives the updated memory state before the final switch.

Q: 5.7. What happens during the stop-and-copy phase of live migration?
- The VM is permanently stopped and must be reinstalled
- The engine discards all changes made during the initial copy
* The VM pauses briefly while its final state is transferred, then resumes on the destination
- Only the VM name is copied while execution stays on the source
> Most memory is copied while the VM runs. A brief pause then allows the final changed memory and execution state to be transferred. The destination resumes the VM using that state.

Q: 5.8. Which result confirms that the host migration in Part 5 succeeded?
- The destination host is Up, even if the VM remains on the source
- The migration dialog closes, without checking the result
- The source host's name is changed to match the destination
* The VM is Up on the intended host, Events show completion, and the guest service still works
> Check the VM's Host field, its Up status, and the migration Events. Then test guest reachability and the application. These checks confirm that the VM moved and remains usable, not just that a destination was available.

Q: 5.9. Which conditions should you check before performing Part 5's live migration? (Select all that apply.)
- The guest's IP address must be changed for every host move
* The hosts are in the same cluster and have compatible CPUs
- The VM disks must be available only to the source host
* The destination has enough resources and can access the VM's disks and network
> The destination needs compatible CPU capabilities, sufficient CPU and memory, shared disk access, and the required VM network. These let the existing VM continue on the new host without changing its network identity simply because it moved.

Q: 5.10. How does planned live migration differ from recovery after a host failure? (Select all that apply.)
* A healthy source can transfer a running VM during planned maintenance
- A powered-off source host can always transfer its running memory state
- Both operations guarantee that the application experiences no interruption
* An appropriately configured highly available VM may restart on a suitable host after failure
> During a planned migration, the source is working and can transfer the VM's execution state. After a host failure, recovery may restart the VM on another host if the required HA configuration and resources are available. A restart is different from an uninterrupted live move.

```


## Part 6: Storage Live Migration

Complete this group after Part 6 of the hands-on workshop. Discuss any missed answers before proceeding.

```quiz score

Q: 6.1. In Part 6, you move a running VM's disk to a second storage domain. Which operation are you performing?
- Host live migration
- An engine database restore
* Storage live migration
- A snapshot preview
> Storage live migration transfers a VM disk between storage domains while the VM continues running. It changes the disk's location, rather than which host executes the VM.

Q: 6.2. What changes during storage live migration compared with host live migration?
- The VM's CPU architecture changes, rather than its disk location
* The disk's storage domain changes, rather than the host running the VM
- The guest's login provider changes, rather than its execution host
- The VM's network address must change whenever its disk moves
> Host migration changes where the VM runs. Storage migration changes where its disk data is stored. In Part 6, verify the disk's new domain rather than expecting the VM's Host field to change.

Q: 6.3. Why does Part 6 require another unused Fibre Channel LUN before creating the destination domain?
- It provides CPU and memory for the migration destination
- It replaces the guest's network interface
- It holds the engine's SSH authorization key
* It provides the backing storage for the second Data domain
> A logical unit, or LUN, is a storage resource presented through Fibre Channel. The second domain needs its own available backing storage. You create that destination before moving the existing VM disk.

Q: 6.4. Why select Data as the function of the second storage domain?
- The domain will be an ISO-only installation-media library
* The domain will hold the VM disk that you are moving
- The domain will be an Export domain for legacy appliance transfers
- The domain will supply the VM's network configuration
> A Data domain provides storage for VM disks. The Part 6 destination must support the running guest's disk, rather than serve as an ISO-only or Export domain.

Q: 6.5. After adding the second storage domain, what status should you wait for before moving the VM disk?
- A domain name entered in the dialog, without a status check
- The source domain deleted before the move
- The VM Down and its snapshot In Preview
* Active
> Part 6 instructs you to wait for the new domain to show Active. Then select the intended disk and start the move to that domain while the VM remains running.

Q: 6.6. Before clicking Move, why check the disk's Attached To field?
- To choose the host that will execute the VM
* To confirm that the selected disk belongs to the intended VM
- To determine whether the destination domain is Active
- To select every disk with a similar display name
> Attached To identifies the VM using that disk. Check the association so you move the correct workload's disk. Disk selection and destination readiness are separate checks.

Q: 6.7. After starting the disk move, what should you watch to confirm progress and completion?
* The operation's Tasks and Events
- Only whether the Move dialog has closed
- Only the VM's display name
- Only the list of OLVM users
> Tasks and Events show whether the transfer is progressing, has finished, or has reported an error. Also check that the VM remains running and reachable. Submitting the request is not the same as completing it.

Q: 6.8. Why might an administrator use storage live migration outside the workshop?
- To change which identity provider authenticates a user
* To maintain storage, balance domain usage, or move disks between storage tiers
- To open a guest console without using its Console action
- To make the engine execute the guest's CPU instructions
> Moving a running VM's disk lets you change its storage placement while keeping the workload running. Common reasons include preparing storage for maintenance, redistributing usage, or choosing a different storage tier.

Q: 6.9. Before creating the second domain and moving the disk, which Part 6 prerequisites should you confirm? (Select all that apply.)
- The source domain has been deleted before the transfer
* The hosts are Up, the source domain is Active, and the intended VM is running
* An unused Fibre Channel LUN is available for the destination Data domain
- The VM is Down and its snapshot is In Preview
> Part 6 lists these prerequisites before the disk move. The unused LUN provides storage for the second domain. After adding that domain, wait for Active before starting the transfer.

Q: 6.10. Which checks show that Part 6's disk move succeeded? (Select all that apply.)
* The disk is now on the second storage domain
* The guest remains reachable and its application still works
- The VM must now show a different execution host
- The VM must remain Down after the transfer
> Verify the disk's new storage location and test the guest workload. A successful storage move does not require changing the execution host or shutting down the VM.

```


## Part 7: OLVM Engine Backup

Complete this group after Part 7 of the hands-on workshop. Discuss any missed answers before proceeding.

```quiz score

Q: 7.1. What does Part 7 say the engine backup protects?
* The OLVM Engine configuration and database
- Only the RAM of a running VM
- Only a guest snapshot without engine configuration
- Only the application VM's network settings
> Part 7 identifies the engine configuration and database as the backup contents. The engine stores management information about data centers, clusters, hosts, storage, networks, users, roles, and VMs.

Q: 7.2. What does the `engine-backup --mode=backup --scope=all` command do in Part 7?
* Create a backup of the OLVM Engine configuration and database
- Restore an existing backup over the running engine
- Move a VM disk to another storage domain
- Preview a guest VM snapshot
> The workshop uses this command to create the engine backup file and a separate operation log. It is the backup procedure; Part 7 does not instruct you to perform a restore.

Q: 7.3. Why does the backup command use separate `--file` and `--log` paths?
- The two files are interchangeable copies of the backup
* The backup file holds recovery data; the log records the operation's messages
- The log contains the guest disks and the backup file contains only messages
- The two paths specify SSH keys used to register hosts
> Keep the backup archive for recovery and its log for checking what happened during the run. The log can show completion or errors, but it cannot replace the archive's saved data.

Q: 7.4. A backup file exists after the command finishes. What should you check before treating the operation as successful?
- The filename alone, without reviewing the log
- The VM's snapshot description
- Whether a user has a VM Portal role
* The backup log shows successful completion
> Part 7 checks that the backup and log files exist, reads the last lines of the log, and includes an Events check. Follow those verification steps rather than relying only on the filename.

Q: 7.5. When would you restore an engine backup instead of creating another routine backup?
* When recovering lost management state or rebuilding the engine
- Immediately after every successful scheduled backup
- Whenever you open the Monitoring Portal
- Before starting any application VM
> A backup saves management data; a restore recovers it after a failure or rebuild. Part 7 creates and verifies backups. It does not ask you to restore over the active training engine.

Q: 7.6. Why use the same run timestamp in a backup filename and its log filename?
- To delete older backups automatically
- To prove success without reading the log
* To distinguish each run and match its backup with the correct log
- To make the log interchangeable with the backup archive
> The Part 7 script adds the same TIMESTAMP value to the backup and log filenames. This gives each run matching names, making it easier to find the log for a particular backup.

Q: 7.7. Why make the backup script executable and run it manually before adding the schedule?
- To assume the crontab entry proves the command works
- To avoid needing any later backup log checks
- To make the backup directory alone count as a completed backup
* To check that it runs successfully and creates the expected backup and log
> The manual run tests the script's commands, paths, and execution permissions. Check the archive and log before relying on cron. A schedule can repeat a working command, but it can also repeat a failing one.

Q: 7.8. Why add the tested backup script to root's crontab as shown in Part 7?
- To restore the engine immediately after each backup
- To move the web VM disk every time the schedule runs
- To replace the need to test the script manually
* To run the script automatically every day at midnight
> Part 7 tests the script manually before adding the daily cron entry. The schedule then runs that script at midnight and suppresses console output. The script still writes its own backup and log files.

Q: 7.9. Which uses of engine backups are explained in Part 7? (Select all that apply.)
* A manual backup before a major change
- A restore immediately after every successful backup
* A scheduled script for ongoing engine protection
- A guest snapshot preview instead of the engine backup procedure
> Part 7 describes manual backup before major changes and scheduled backup for ongoing protection of the engine configuration and database. Restore is discussed as a recovery or rebuild action, not part of the routine backup exercise.

Q: 7.10. Which checks establish that the scheduled backup setup is ready? (Select all that apply.)
- Suppressing console output proves that the job succeeded
- An empty backup directory proves that engine data is protected
* The script is executable and a manual run produces an archive with a successful log
* Root's crontab points to that script with the intended recurring schedule
> Verify both the working script and the schedule that invokes it. Because the workshop redirects cron output, review the script's saved backup logs to check later runs. Silence on the console is not a success signal.

```


## Part 8: Monitoring Portal and Grafana (Optional)

Complete this group after Part 8 of the hands-on workshop. Discuss any missed answers before proceeding.

```quiz score

Q: 8.1. After the migration exercises, where can you review historical host and VM resource graphs?
- The VM's interactive Console window
- The Keycloak account-management console
- The engine backup archive
* The Monitoring Portal's Grafana dashboards
> The Monitoring Portal presents resource usage over time using Grafana dashboards. Part 8 uses these graphs to inspect hosts, VMs, and storage after the earlier exercises.

Q: 8.2. What collects the historical performance data that Grafana displays in the workshop?
- The appliance import dialog
- The guest Console's screen output
- The application's database tables
* The oVirt Data Warehouse
> The oVirt Data Warehouse collects historical metrics about the environment. Grafana queries that collected data to show usage and trends. This is different from application records stored inside a guest VM.

Q: 8.3. Which Grafana dashboard should you use to compare CPU usage on the two KVM hosts?
- Virtual Machine dashboard for one guest
- Storage Domains Inventory Dashboard
* Host dashboard
- Only the resource counts in System Dashboard
> The Host dashboard shows resource graphs for individual hosts. Compare the hosts over the same period to see which is busier. A guest graph or inventory count cannot provide that host-to-host CPU comparison.

Q: 8.4. One host's CPU graph is consistently busier than the other's. How can that information help with VM placement?
- It proves the busiest host is the best destination for another VM
* It can help identify a less busy destination, together with other readiness checks
- It means the guest IP address must be changed
- It proves every application query is returning the right data
> CPU usage helps you assess host load before placing or migrating a VM. Also check memory capacity, storage and network access, and compatibility. A CPU graph supports the decision; it does not establish all requirements by itself.

Q: 8.5. What is `ovirt_engine_history` used for in the workshop's monitoring explanation?
- It holds the guest's virtual disk images
* It holds Data Warehouse history that Grafana uses for monitoring
- It is the destination domain selected for a disk move
- It stores the web application's employee records
> The portal overview identifies `ovirt_engine_history` as the Data Warehouse database used by Grafana. Part 8 explains that the Data Warehouse collects metrics for the monitoring dashboards.

Q: 8.6. What can reviewing a VM's CPU and memory graphs help you decide?
- Which Keycloak realm should authenticate its administrator
* Whether its resource allocation fits the workload and whether unusual usage needs investigation
- Which unused Fibre Channel LUN should back a new domain
- Whether the backup archive can replace its operation log
> VM-level graphs help you judge whether the guest has too few or too many resources, often called right-sizing. Unexpected usage can prompt investigation of a busy process or a memory leak. The graph provides evidence rather than proving the cause on its own.

Q: 8.7. After moving the VM disk in Part 6, which dashboard helps compare storage capacity and disk I/O?
- The VM Console connection options
- The list of imported templates
* Storage Domains Inventory Dashboard
- The Keycloak Users page
> The Storage Domains Inventory Dashboard shows domain usage and disk activity. Compare the source and destination around the time of the move to understand capacity changes and migration-related I/O.

Q: 8.8. During an incident, why start with System Dashboard and then inspect host, VM, or storage graphs?
* The overview helps identify what is affected, and the detailed graphs help narrow the problem
- Resource counts alone reveal the exact cause of every incident
- A healthy engine proves that every guest application works
- The system summary replaces all application tests and log review
> System Dashboard provides an overview of engine health and cluster resources. More detailed graphs show CPU, memory, capacity, and I/O behavior for the affected resources. Use application checks and logs to investigate what the graphs suggest.

Q: 8.9. Which measurements help you assess the effect of the workshop's migration operations? (Select all that apply.)
- The spelling of the engine backup archive's filename
* Host and VM CPU, memory, and network usage during the relevant period
- The number of user passwords created that day
* Storage domain capacity and I/O around the disk move
> Compare metrics over the migration's time window. Host migration transfers execution between hosts, while storage migration transfers disk data. Reviewing the relevant resource graphs helps you see the impact of each operation.

Q: 8.10. Which activities require another OLVM interface rather than a Grafana performance dashboard? (Select all that apply.)
* Open the guest's Console from the VM details
- Compare historical CPU usage between hosts
- Review storage capacity trends over time
* Review current management events in Administration Portal Events
> Use Console to interact with the guest's screen and Events to inspect current management activity. Grafana dashboards serve the separate purpose of reviewing collected performance and capacity data.

```


## Part 9: VM Snapshots

Complete this group after Part 9 of the hands-on workshop. Discuss any missed answers before proceeding.

```quiz score

Q: 9.1. Why does Part 9 recommend a snapshot before a patch, upgrade, or configuration change?
- It starts the VM on another KVM host
* It provides a point to return to if the change causes a problem
- It changes the guest's identity provider
- It creates a backup of the engine configuration
> Part 9 describes snapshots as a way to return to an earlier VM state after a risky change. This is the reason for taking the snapshot before the change.

Q: 9.2. After a copy-on-write snapshot, where do later changes to the VM disk go?
- Directly into the saved snapshot state, overwriting it
- Into a full new disk copy for every write
* Into a separate delta layer while the saved disk state stays unchanged
- Only into RAM, without any disk storage
> Part 9 calls this Copy-on-Write (COW). It keeps the snapshot's disk state as a read-only base and records later writes in a thin delta layer. That layer grows as the VM writes data.

Q: 9.3. In Create Snapshot, which settings give you the disk-only snapshot requested for the web VM?
- Select Save Memory and deselect the VM's disk
- Choose another host in the migration dialog
- Stop the engine service instead of selecting snapshot options
* Select its intended disk and leave Save Memory unchecked
> The disk selection determines which disk state is captured. Save Memory adds RAM state, so leave it unchecked for the disk-only exercise. You can then compare this snapshot with the memory-included example.

Q: 9.4. You select Save Memory for the database VM's snapshot. What additional state is saved?
- The engine's management database
* The contents of the guest's RAM at the snapshot point
- Only a description of the VM disk
- The guest's network settings without its memory state
> Part 9 explains that Save Memory includes the guest's in-memory state as well as its disk state. It also notes that capturing memory can briefly pause the VM, which then resumes automatically.

Q: 9.5. Why should you remove snapshot chains that you no longer need?
- Their disk usage stays fixed regardless of guest writes
* Their growing delta layers use storage and can slow disk I/O
- Keeping more layers always improves disk performance
- Deleting a snapshot normally deletes the whole VM
> Each snapshot adds a disk layer that can grow with later guest writes. Long or unnecessary chains consume capacity and can affect disk performance. Part 9 includes cleanup to practice managing this storage impact.

Q: 9.6. In the workshop's conceptual restore procedure, what status must the VM reach before you click Preview?
* Down
- Up while migrating
- Installing its host management agent
- Only the engine must be stopped
> Shut down the VM and wait for Down before previewing the selected snapshot. This is the restore sequence explained in Part 9. The workshop presents it conceptually rather than asking you to restore the active application during the session.

Q: 9.7. You preview a snapshot and find that it is not the state you want. Which action returns to the state from before the preview?
* Undo
- Commit
- Delete the entire VM
- Create another snapshot without canceling the preview
> Undo cancels the preview and returns to the VM's pre-preview active state. It does not choose another older snapshot. This is why you validate a preview before deciding whether to commit it.

Q: 9.8. You validate a snapshot preview and click Commit. What happens to the VM's disk state?
- The VM automatically returns to its pre-preview state
* The previewed state becomes current, and changes made after that snapshot are discarded
- The VM starts running on a different host
- Only the snapshot's description changes
> Commit accepts the previewed state as the current disk state. You cannot then use Undo to recover the pre-preview state in this workflow. Check the guest and application carefully before choosing it.

Q: 9.9. When deleting an unneeded snapshot, which statements about cleanup are correct? (Select all that apply.)
- The operation normally deletes the entire VM
* Its disk layers are merged while the VM's current data is preserved
* A large amount of changed data can take longer to merge and increase storage I/O
- The operation converts a disk-only snapshot into a memory snapshot
> Snapshot deletion removes the saved return point by merging disk layers, rather than deleting the VM. More changed data means more merge work. Watch the operation and its storage activity until cleanup completes.

Q: 9.10. After creating a snapshot, which checks confirm its completion and the intended result? (Select all that apply.)
* The snapshot shows OK and its Memory field matches the requested type
* The guest service remains available and the storage impact is reviewed
- Any listed snapshot record proves completion regardless of status
- Every new snapshot must immediately be previewed and committed
> Check snapshot status and whether memory was included, then test the guest workload and inspect storage use. These checks cover completion, snapshot type, and operational impact. Creating a snapshot does not require restoring it immediately.

```


## Part 10: Manage Users and Roles

Complete this group after Part 10 of the hands-on workshop. Discuss any missed answers before proceeding.

```quiz score

Q: 10.1. Why does Part 10 use different procedures for Keycloak users and local JDBC users?
- They always use the same account-creation command
- Every JDBC account becomes a Keycloak account automatically
* They use different identity stores and can have different portal login behavior
- Their identity source is determined by the VM's storage domain
> Part 10 creates one account through Keycloak and another with `ovirt-aaa-jdbc-tool`. These accounts belong to different identity systems. In the workshop's Keycloak-enabled configuration, creating a local account does not give it the same browser login path as a Keycloak-backed account.

Q: 10.2. Which Keycloak realm should contain the new administrator created in Part 10?
* `ovirt-internal`
- The guest operating system's user list
- The Default cluster's name
- The SuperUser role instead of a realm
> Part 10 tells you to select the `ovirt-internal` realm before adding the Keycloak user. You then import that user into OLVM using the matching authorization provider.

Q: 10.3. The new Keycloak account does not appear in OLVM's Add User results. What should you check first?
* The account's realm and the authorization provider selected for the search
- The storage domain assigned to an application VM
- The guest VM's snapshot description
- Whether the engine backup filename matches the username
> Check that the account is in the intended Keycloak realm and that OLVM is searching the matching provider. In this workshop, the Keycloak search uses `internalsso (internalkeycloak-authz)`. Searching the wrong identity source can omit an otherwise valid account.

Q: 10.4. You have created the Keycloak administrator and added it to OLVM. Which step grants the full administrative access used in Part 10?
- Set the password to match the engine's SSH key
- Create the same username inside every application VM
* Add the SuperUser system permission in the user's Permissions tab
- Create a storage domain with the same name as the user
> Part 10 selects the added user, opens Permissions, and uses Add System Permission to assign SuperUser. Creating the account and adding it to OLVM are earlier steps; the role assignment grants the demonstrated administrative access.

Q: 10.5. Where and how do you create the local internal account demonstrated in Part 10?
- Run the JDBC tool inside the web application guest
- Create it only in OLVM's user list without creating the identity first
* Run `ovirt-aaa-jdbc-tool` on the engine host
- Create it in Keycloak and assume it is also a JDBC account
> The JDBC tool manages the local internal identity store on the engine. Creating that account is different from creating a Keycloak user. Afterward, add the local account to OLVM and assign its role.

Q: 10.6. You created a local user with the JDBC tool. Why must you also add it through the Administration Portal?
- Adding the user imports all application VMs
* OLVM needs the user in its user list before you assign its permissions
- Adding the user creates the host's physical disks
- The local account must first become a KVM host
> The command creates the identity, but it does not automatically complete the portal's user and permission setup. Find and add the local account in OLVM, then assign the required role in Permissions.

Q: 10.7. Which role choices are explicitly described for the local user in Part 10?
- Both roles always grant identical access
- UserRole is a Keycloak realm and SuperUser is an account-creation command
- Setting a password automatically assigns SuperUser
* UserRole for VM Portal access; SuperUser for full administrative rights
> Part 10 lists UserRole for VM Portal access and SuperUser for full administrative rights. Select the required role through the user's Permissions tab after adding the local account to OLVM.

Q: 10.8. A local JDBC user has an OLVM role but cannot use the workshop's normal Keycloak web login. What should you use for administrative browser access?
- Another copy of the same local JDBC account
* A Keycloak-backed account with the required OLVM permissions
- SuperUser alone as a way to convert the local account into Keycloak
- The assumption that every provider accepts any account with a role
> Use the Keycloak-backed identity path recommended for browser administration in this workshop. Adding an OLVM role controls authorization; it does not move an account into another identity store or change the login flow that accepts it.

Q: 10.9. Which checks verify the two user setups demonstrated in Part 10? (Select all that apply.)
* Sign in with the new Keycloak administrator and verify its intended access
- Assume creating either account proves administrative access
* Confirm the local user is added to OLVM and has the expected role in Permissions
- Assume every JDBC account can use the normal Keycloak browser login
> Test the Keycloak administrator's actual portal access. Check the local user's presence and assigned permissions separately, as the workshop instructs. A visible user or role does not remove the login differences between the two identity paths.

Q: 10.10. Which Keycloak settings create an enabled account with the non-temporary password requested in the workshop? (Select all that apply.)
- Leave the account disabled after assigning an OLVM role
- Skip setting credentials because importing the account creates a password
* Enabled is On for the account
* Temporary is Off when setting its password
> Enabled allows the account to authenticate, and Temporary Off means the password is not marked as temporary. Set these in Keycloak, then add the account to OLVM and assign its permissions. The identity settings and OLVM role assignment are separate steps.

```


## Review Your Results

Review missed answers with your instructor and explain the operational reason for each correct choice. Revisit the corresponding workshop procedure or validation checklist, then retry the question. After the session, use your weak areas to select the matching self-study training sections.

## Learn More

- [Deploy and Administer OLVM](../olvm-luna/olvm-luna.md)
- Continue with the OLVM training lessons and the separate study quiz bank provided by your instructor.

## Acknowledgements

- **Source Lab Authors** - Shawn Kelley, John Priest, Perside Foster
- **Source Lab Contributor** - Marvin Kim
- **Quiz Source** - Deploy and Administer OLVM hands-on workshop, using the repository copy in `olvm-luna/olvm-luna.md`
- **Quiz Preparation** - Codex, for Perside Foster
- **Last Updated By/Date** - Codex, October 12, 2026
