# OLVM Administration Quiz

```quiz-config
passing: 80
badge: images/badge.png
```

## Introduction

Test your understanding of Oracle Linux Virtualization Manager (OLVM) administration with **100 scored questions**. The ten topic groups contain ten questions each and follow the OLVM concepts taught across the ten tasks of Lab 7.

Estimated Time: 90-120 minutes

### Objectives

In this quiz, you will:

- Explain the engine, KVM host, guest, network, and storage roles in OLVM
- Apply virtual appliance deployment and migration concepts
- Review engine backup, monitoring, and snapshot decisions
- Distinguish user creation, authentication sources, and role permissions

### Before You Begin

Review the OLVM administration sections of Lab 7. The identity questions refer to the OLVM 4.5 configuration described in that source, including integrated Keycloak and local internal users. The monitoring topic is included even if you did not perform the optional dashboard walkthrough.

Select one choice for single-answer questions. Questions labeled **Select all that apply** require all correct choices and no incorrect choices. Click **Check Answer** for feedback and an explanation. Use **Try Again** to retry a question.

Each question has equal weight, including multiple-answer questions. Complete all 100 questions for a final result. The passing score is **80%**, or **80 correct answers**. Passing unlocks a downloadable quiz badge.

## Task 1: Engine Deployment and Architecture

Answer the ten questions in this topic group. Review the corresponding OLVM concepts in Lab 7 when needed.

```quiz score
Q: 1.1. What is the main role of the OLVM engine?
* Provide centralized management of hosts, VMs, networks, and storage
- Execute every guest instruction inside the engine process
- Replace all host-side virtualization components
- Store all guest disks in its management database
> The engine manages hosts, VMs, networks, and storage from one place. The KVM hosts run the VMs, so guest workloads do not execute inside the engine.

Q: 1.2. How does a standalone engine differ from a self-hosted engine deployment?
- A standalone engine removes all KVM hosts
- A self-hosted engine runs inside each application guest
- A self-hosted engine needs no management software
* A standalone engine uses dedicated infrastructure; a self-hosted engine runs as a VM on the KVM hosts
> A standalone engine runs on separate management infrastructure. A self-hosted engine runs as a VM on the KVM infrastructure it manages.

Q: 1.3. Why install the OLVM release package before installing engine components?
- It replaces the engine database configuration step
- It turns guest snapshots into engine backups
* It configures the repositories needed for the OLVM software
- It imports every existing VM into the portal
> The release package sets up the software repositories that supply OLVM packages. Once those repositories are ready, you can install the engine software and run its setup.

Q: 1.4. Which statement correctly separates physical infrastructure, KVM hosts, and OLVM?
- KVM hosts only store the engine database
* Physical servers provide resources, KVM hosts execute VMs, and OLVM manages them
- OLVM directly executes every guest instead of the KVM hosts
- The Administration Portal provides physical CPU and memory
> Physical servers supply CPU, memory, network connections, and storage access. KVM hosts use those resources to run VMs, while OLVM provides the tools to manage them.

Q: 1.5. The engine service becomes unavailable while the KVM hosts remain healthy. What happens to running VMs?
* They continue executing on the hosts
- They all stop because execution depends on an open portal
- They automatically migrate solely because the engine stopped
- They move their runtime state into the engine database
> Running VMs continue on healthy KVM hosts even if the engine service stops. You lose central management while the engine is unavailable, but the VMs do not depend on it to keep executing.

Q: 1.6. Before installing the engine, how can an administrator verify that the required repositories are enabled?
- Assume installing the release package removes the need to verify anything
- Use the VM Snapshots tab to list engine packages
- Create a storage domain to configure the package repositories
* Review the enabled repositories with dnf repolist
> Run `dnf repolist` to see which repositories are enabled. Check that the required OLVM repositories are listed, and enable any that are missing before installing the engine.

Q: 1.7. The ovirt-engine package is installed. Why is engine-setup still required?
- Engine-setup replaces the KVM hosts
- Engine-setup imports every appliance automatically
* Software installation and manager service configuration are separate steps
- The package only installs a guest console viewer
> Installing `ovirt-engine` puts the software on the server. Running `engine-setup` configures its database, web services, certificates, and initial data center and cluster so the manager can be used.

Q: 1.8. Why use the engine's fully qualified domain name for its web interface?
- The name matters only after importing a guest
* The engine certificate is bound to that name
- Any unrelated hostname always matches the same certificate
- The VM network name substitutes for the engine certificate name
> Open the portal using the engine's configured fully qualified domain name (FQDN). That name should match the name in its certificate so the browser can verify that it has reached the intended server.

Q: 1.9. Which actions belong to engine-setup? (Select all that apply.)
* Configure the engine database and web services
* Generate certificates and initialize the Default data center and cluster
- Import all application OVAs
- Create snapshots of every running guest
> `engine-setup` configures the database and web services, generates certificates, and creates the initial Default data center and cluster. Importing appliance files and taking VM snapshots are tasks you perform afterward.

Q: 1.10. Which OLVM portal-purpose pairings are correct? (Select all that apply.)
* Monitoring Portal: review Grafana resource metrics
- VM Portal: configure host clusters and shared storage as an administrator
- Monitoring Portal: provide a live guest console
* Administration Portal: manage hosts, networks, storage, VMs, and users
> Use the Administration Portal to manage the OLVM environment and its users. Use the Monitoring Portal to view Grafana metrics. To interact with a guest's screen, open its VM console.

```

## Task 2: KVM Hosts

Answer the ten questions in this topic group. Review the corresponding OLVM concepts in Lab 7 when needed.

```quiz score
Q: 2.1. Which host-side agent receives OLVM engine requests?
* VDSM
- The guest agent running inside a VM
- The engine database
- The VM Portal in the browser
> VDSM runs on the KVM host. It receives requests from the engine and reports information about the host and its VMs, allowing the engine to manage them.

Q: 2.2. How does VDSM manage the virtualization stack?
- It bypasses libvirt and directly executes all guest instructions
- It is the guest-side agent that reports installed applications
- It replaces QEMU with a process running inside the engine database
* It uses libvirt to manage KVM and QEMU
> VDSM uses libvirt to request VM operations on the host. Libvirt manages the KVM and QEMU components that run the VMs; VDSM is the management agent, not the hypervisor.

Q: 2.3. How do KVM and QEMU occupy different layers of the virtualization stack?
- QEMU runs in kernel space while KVM runs in user space
- Both are guest applications installed only inside the VM
* KVM runs in kernel space; each VM has a QEMU process in user space
- Both are engine services with no host-side components
> KVM provides virtualization support inside the host's Linux kernel. Each VM also has a QEMU process running outside the kernel that provides its virtual devices and other VM functions.

Q: 2.4. What is the purpose of the guest agent used with OLVM?
- Receive engine management commands and manage host storage
* Report guest information such as OS details and IP addresses
- Replace libvirt as the host virtualization API
- Configure the Default cluster when the engine is initialized
> The guest agent runs inside the VM and reports information such as its operating system and IP addresses. VDSM runs on the KVM host and manages the host and VMs, so the two agents have different jobs.

Q: 2.5. When registering a physical KVM host, which address should identify it to the engine?
* The FQDN or IP of the interface used for host management
- The address of an unrelated application guest
- The name of a VM template instead of a host address
- The engine's own address repeated for every host
> Use the KVM host's fully qualified hostname (FQDN) or management IP address so the engine can connect to and configure that host. If the host has several network connections, choose the one used for management. Do not use a guest VM's address or the engine's own address.

Q: 2.6. Why copy the engine's SSH public key to the host's root authorized_keys file?
- Install the VDSM packages merely by placing the key file
- Authorize all guest users to administer the portal
- Replace the private management hostname with a guest address
* Enable engine SSH authentication for host installation and configuration
> Putting the engine's SSH public key in the host's root `authorized_keys` file allows the engine to authenticate over SSH. The engine can then install host software and configure the host. Adding the key does not perform the installation by itself.

Q: 2.7. A newly added host shows Installing. What should you do before proceeding?
- Force a migration to finish installation
- Remove its logical network
* Wait until it reaches Up
- Treat Installing as proof of VM readiness
> Installing means OLVM is still deploying the host software and configuration. Wait until the host shows Up before using it for the next lab steps.

Q: 2.8. What does the Maintenance host status mean?
- The host is ready for normal guest placement with no restrictions
* The host is intentionally offline for maintenance or updates
- The host is still installing its management packages
- The host has an unexpected operational failure requiring log review
> Maintenance means an administrator has deliberately taken the host out of normal use for work such as updates. It does not necessarily mean the physical server is powered off or has failed.

Q: 2.9. Which shared characteristics support hosts in a migration-capable cluster? (Select all that apply.)
* Compatible CPU type
* Common storage domains and network configuration
- A different data center for every host
- Identical guest usernames on every VM
> Hosts need compatible CPUs, access to the VM's storage, and the required network configuration to support migration. These let the destination continue running the VM with the resources and connections it needs.

Q: 2.10. Which checks establish that host integration is ready? (Select all that apply.)
* Engine SSH authentication has been configured on both hosts
- Every guest has already completed a snapshot restore
- Each host is registered in an unrelated data center
* Both hosts show Up in the Administration Portal
> In this lab, configure engine SSH access to both hosts and confirm that both reach Up status. These checks show that the engine can connect to the hosts and that registration has completed.

```

## Task 3: Networking, Storage, and Test VMs

Answer the ten questions in this topic group. Review the corresponding OLVM concepts in Lab 7 when needed.

```quiz score
Q: 3.1. What is the purpose of an OLVM logical network designated as a VM Network?
* Carry guest virtual machine traffic
- Carry only engine-to-host management traffic
- Store persistent guest disk images
- Collect historical resource metrics
> A logical network marked as a VM Network carries traffic from guest VMs. The engine's management connection and VM disk storage have different purposes.

Q: 3.2. A logical network exists in the data center. What is still needed for a host to use it?
- Assume the data center definition automatically attaches it to every host interface
- Assign it only to a template without configuring the host
- Rename a storage domain to match the network
* Assign it to the appropriate host interface through Setup Host Networks
> Creating the logical network defines it in OLVM, but the host still needs a connection to it. Use Setup Host Networks to assign it to the appropriate host network interface.

Q: 3.3. What role does the VM network's Linux bridge serve?
- Automatically allocate guest addresses through a DHCP service
- Replace the physical interface with no uplink to the network
* Connect VM virtual interfaces to the physical interface
- Store guest disk data in the host network configuration
> The Linux bridge works like a virtual network switch on the host. It connects VM network interfaces to the host's physical network connection so guests can communicate beyond the host.

Q: 3.4. Why use the OLVM UI or API rather than manually edit host network scripts after assignment?
- Manual edits automatically synchronize with every engine network change
* Manual changes can be overwritten and break connectivity
- Manual editing is required after every Setup Host Networks operation
- OLVM network settings last only until the next browser login
> OLVM and VDSM manage the host's network configuration. Manual edits can conflict with their settings or be overwritten later, so use the OLVM UI or API to make managed network changes.

Q: 3.5. Why use storage accessible to both hosts?
* Both need VM disk access for migration and HA
- The destination can rely on disks that only the source host can access
- Shared storage makes compatible host networking unnecessary
- A local-only disk provides the same migration path as shared storage
> When a VM moves to another host, that host must still be able to read and write its disks. Shared storage gives both hosts access to those disks and supports migration and recovery on another host.

Q: 3.6. A submitted domain is not yet Active. What should you do?
- Import application OVAs immediately
- Treat its name as proof of readiness
- Delete the VM network
* Wait for Active before continuing
> Wait until the storage domain shows Active before using it. Creating or submitting the domain does not by itself confirm that OLVM has finished attaching it and made it available.

Q: 3.7. Why download the OVA to the selected import host?
- The file must be in the guest before creation
- Imports read only the engine database
* The import reads that host's filesystem
- The browser uploads every local download
> OLVM reads the OVA from the host selected in the import dialog. Download it to that host and supply its actual file path; a copy on your workstation or another server will not satisfy that path.

Q: 3.8. Where does OLVM let you supply initial guest authentication and network settings when creating the test VM?
- The storage domain's LUN selection
* Initial Run under the advanced VM options
- The engine backup log
- The host's Permissions tab
> Use Initial Run in the advanced VM options to enter the test VM's initial login and network settings. These settings apply to the guest operating system, not to the engine or storage domain.

Q: 3.9. Which statements distinguish templates and VMs? (Select all that apply.)
* A template is a master image that cannot start directly
* A created VM has its own disk and lifecycle
- Template edits automatically change existing VMs
- A template is a running guest process
> A template is a starting image for creating VMs; it cannot run as a VM itself. Each VM created from it has its own disks and can be started and stopped independently. Later template changes do not automatically update existing VMs.

Q: 3.10. Which checks validate the test VM network? (Select all that apply.)
* Ping the gateway inside the guest console
- Check only the VM name in the portal
- Check only that the OVA exists
* Inspect the guest interface and IP address
> Check the network interface and IP address inside the guest, then ping its gateway. These checks test the guest's actual network configuration and connectivity; seeing the VM in the portal is not enough.

```

## Task 4: Virtual Appliance Deployment

Answer the ten questions in this topic group. Review the corresponding OLVM concepts in Lab 7 when needed.

```quiz score
Q: 4.1. What does importing a prepared OVA let an administrator redeploy?
* A VM containing its configured operating system, software, data, and settings
- Only a VM name without disks or configuration
- Only an engine user account
- A physical host without virtualization software
> An OVA contains an exported VM's disks and configuration. Importing it lets you reuse the prepared operating system and software instead of installing and configuring the guest from scratch.

Q: 4.2. Why can a multi-tier workload demonstrate the value of an OLVM cluster?
- Its guest software replaces the host hypervisor
- Its database becomes the OLVM engine database
- Its VMs no longer need virtual network interfaces
* Its VMs use the cluster's compute, networking, and shared storage together
> A multi-tier application uses several parts of the cluster together: host CPU and memory, VM networking, and shared storage. Testing the application checks that those parts work together to support a real workload.

Q: 4.3. Which import source is used for an exported virtual appliance?
- A template-only workflow instead of the VM import
- An ISO installation performed inside a blank guest
* Virtual Appliance (OVA)
- A new empty VM with no appliance import
> Choose Virtual Appliance (OVA) when importing an exported appliance file. This loads the prepared VM rather than creating a blank VM and installing its operating system yourself.

Q: 4.4. Why review the Network Name and Profile Name during an appliance import?
- Leave the guest disconnected because the OVA supplies host networking
* Map the imported NIC to the intended logical VM network and profile
- Assume the source environment's mapping always matches the destination
- Use a storage domain as the network profile
> The imported VM must connect to a network that exists in the destination environment. Review Network Name and Profile Name to map its virtual network interface to the intended OLVM network.

Q: 4.5. What infrastructure enables application VMs on separate KVM hosts to communicate in the source's cluster design?
* A common VM logical network configured on the hosts
- Only matching VM display names
- Only importing both OVAs from the same file path
- The engine database acting as a guest network bridge
> Configure the common VM logical network on both KVM hosts. This provides the network path for application VMs on different hosts to communicate; matching VM names or import paths does not provide connectivity.

Q: 4.6. Why validate a guest workload after OLVM reports its VM as Up?
- Up guarantees every guest service has valid configuration
- A workload test creates the host's management network
- Workload testing replaces the need to check the VM state
* Up confirms a running VM but does not alone prove the guest service works
> Up tells you that the VM is running. It does not tell you whether the database, web server, or other guest service is working, so test the service after starting the VM.

Q: 4.7. An appliance file is ready for import. How must the selected Host and File Path relate to it?
- Any guest filesystem can supply the file before that guest is created
- The file path is just a VM description and is not used to locate data
* The file must be accessible at that path on the selected host
- The file only needs to exist in an unrelated browser download folder
> The selected Host tells OLVM where to read the appliance, and File Path tells it where the file is on that host. Both must point to the actual OVA location for the import to work.

Q: 4.8. After an appliance import completes with the VM Down, what starts execution?
- Assign a user role instead of running the VM
* Run the VM and wait for its status to become Up
- Assume Down means the guest service is already running
- Create an engine backup to start the guest
> A completed import creates the VM but does not start it when its status is Down. Click Run, wait for Up, and then check that the guest services work.

Q: 4.9. Which actions are part of the appliance import workflow? (Select all that apply.)
* Load and select the appliance from the selected host's file path
* Review the guest's network interface mapping before completing import
- Treat the import file path as a replacement for shared storage
- Delete the cluster logical network before importing
> Select the appliance from its location on the import host and review the imported VM's network mapping. The resulting VM still needs usable storage and network connections in the destination environment.

Q: 4.10. Which checks establish an imported workload is ready? (Select all that apply.)
* The guest services and communication between application tiers respond as expected
- Only the appliance filename appears in the import dialog
- Only the VM name exists, regardless of its runtime state
* The VM is Up with the intended logical network assigned
> Confirm that the VM is Up and connected to the intended network, then test its services and communication with the other application tier. An imported VM record alone does not prove the application is ready.

```

## Task 5: VM Live Migration

Answer the ten questions in this topic group. Review the corresponding OLVM concepts in Lab 7 when needed.

```quiz score
Q: 5.1. What is the purpose of VM live migration in Task 5?
* Move a running VM's execution between KVM hosts
- Move only its disk between storage domains
- Export a VM to an OVA
- Replace the guest OS with engine software
> VM live migration moves a running VM from one KVM host to another. It changes which host executes the VM; moving its disks to another storage domain is a separate operation.

Q: 5.2. The destination cannot access the shared storage domain. What should you do?
- Proceed because host CPU capacity replaces the need for disk access
- Keep the disk accessible only to the source after moving execution
- Change the VM network to bypass unavailable shared storage
* Resolve storage access before migrating
> Fix the destination host's access to the VM's shared storage before migrating. The destination needs those disks to continue running the VM after the move.

Q: 5.3. Why check destination CPU and memory capacity?
- The incoming VM can always displace other guests without a capacity check
- Shared disk access makes destination memory capacity irrelevant
* The destination needs resources to run the arriving VM
- The VM retains its CPU and RAM execution on the source after migration
> The destination needs enough CPU and memory for the arriving VM as well as the VMs already running there. Check its available capacity before choosing it as the migration target.

Q: 5.4. What does automatic destination selection allow OLVM to consider?
- Only the name of an engine backup file
* Available host resources and scheduling policies
- Only the alphabetic order of host names
- Only the source VM's username
> Automatic selection lets OLVM choose a suitable destination using available resources and scheduling policies. You can also select a host manually when you need a particular destination.

Q: 5.5. Which observations show live migration is progressing?
* The migration status or progress indicator and VM Events
- Only opening the Migrate dialog
- Only seeing the source host's name
- Only having a saved VM template
> Watch the VM's migration status, progress indicator, and Events to see whether the move is underway. Opening or submitting the migration dialog alone does not confirm progress or completion.

Q: 5.6. Why recopy dirty memory pages during live migration?
- The virtual disk is deleted page by page
- The destination lacks shared storage
- The engine is replacing the VM with a template
* The running VM can change pages after their first copy
> The VM keeps running while its memory is copied, so it can change pages that have already been transferred. These changed pages are called dirty pages and must be copied again before the final switch.

Q: 5.7. What does the stop-and-copy phase do?
- Reinstall the guest OS
- Copy the engine database into each guest
* Briefly pause execution to transfer final state before destination activation
- Power off both hosts for the migration
> During stop-and-copy, OLVM briefly pauses the VM and transfers the final changed memory and execution state. The VM then resumes on the destination host.

Q: 5.8. Which portal observation confirms execution moved successfully?
- Only the Migrate dialog has closed
* The VM shows the intended destination in Host and its status is Up
- The VM still shows its original host with no successful migration event
- Only the destination host appears in the host list
> Check that the VM's Host field names the intended destination and its status is Up. Then review Events and test the guest workload to confirm that the move completed successfully.

Q: 5.9. Which migration prerequisites are listed? (Select all that apply.)
* Both hosts are in the same cluster
* The VM logical network is available on both
- The VM must change IP after migration
- The source domain must be removed first
> The hosts must belong to the same cluster and provide the VM's required logical network. They also need access to its shared storage, compatible CPUs, and enough destination capacity.

Q: 5.10. Which checks validate workload continuity after a host migration? (Select all that apply.)
* Its guest service still responds correctly
- Only the migration dialog opened successfully
- Only the destination host has a display name
* The migrated VM remains reachable at its expected address
> Check that the migrated VM is reachable at its expected address and that its service still responds correctly. These tests show that the workload continues working after the host changes.

```

## Task 6: Storage Live Migration

Answer the ten questions in this topic group. Review the corresponding OLVM concepts in Lab 7 when needed.

```quiz score
Q: 6.1. A running guest's disk must move to another domain. Which operation fits?
* Storage live migration
- Host live migration without moving the disk domain
- An offline disk relocation requiring the guest to stay stopped
- A snapshot preview that reverts the guest to an earlier disk state
> Use storage live migration to move a running VM's disk to another storage domain. The VM continues running while its disk data is transferred to the new location.

Q: 6.2. How does storage migration differ from host migration?
- It always changes both the host and the storage domain together
- It changes the execution host and leaves disk placement unchanged
- It requires the VM to remain stopped throughout the exercise
* It changes the disk's domain rather than the executing host
> Host migration changes where the VM runs. Storage migration changes where its disks are stored. Moving a disk does not by itself move the VM to another host.

Q: 6.3. Why must a second unused LUN be available?
- It allows the already-used LUN to be reused as a new independent domain
- It is the destination host required for execution migration
* It supplies storage for the second Fibre Channel domain
- It supplies memory capacity for a running guest
> The second Fibre Channel domain needs storage of its own. An unused logical unit (LUN) provides that storage, giving the VM disk a destination for the move.

Q: 6.4. Which configuration describes a Fibre Channel data domain?
- An ISO domain intended only for installation media
* A Fibre Channel Data domain in the Default data center
- An Export domain used only to transfer appliances
- Local-only storage used in place of the shared Fibre Channel destination
> In this lab, create the second domain as a Fibre Channel Data domain in the Default data center. A Data domain holds VM disks, so it can serve as the destination for the disk move.

Q: 6.5. When is the new domain ready for the move?
* When it shows Active
- When its name is typed
- After deleting all guest VMs
- After restoring the engine
> Wait until the new domain shows Active before moving the disk to it. This confirms that OLVM has made the domain available; entering its name is only part of creating it.

Q: 6.6. Why inspect Attached To when selecting a disk to move?
- Select the execution host instead of the VM disk
- Move all disks without checking their workload associations
- Infer storage readiness from a VM display name
* Identify the disk belonging to the intended VM
> Attached To shows which VM uses the disk. Check it before moving anything so you select the intended VM's disk rather than a disk belonging to another workload.

Q: 6.7. What should an administrator monitor after initiating a disk move?
- Only the user list
- Only whether the Move dialog closed
* Tasks and Events until completion
- Only the VM's display name
> Watch Tasks and Events until the disk move finishes, and check for errors. Also verify that the VM stays running and reachable. Closing the Move dialog does not mean the transfer has completed.

Q: 6.8. Which administrative need is addressed by storage live migration?
- Providing guest console access without a VM network
* Storage maintenance, balancing, or moving between storage tiers
- Changing a user's identity source
- Replacing VM execution with an engine process
> Storage live migration lets you move disks for storage maintenance, to balance usage, or to use a different storage tier. It lets the VM keep running while you change its disk location.

Q: 6.9. Which conditions are prerequisites for storage live migration? (Select all that apply.)
* Both hosts are Up and the original domain is Active
* The VM is running and a second unused LUN is available
- The VM is powered off for snapshot preview
- The original domain is deleted
> For this lab, start with both hosts Up, the original storage domain Active, and the VM running. Have an unused LUN available for the second domain so there is a destination for the disk.

Q: 6.10. Which observations support successful storage migration? (Select all that apply.)
* The guest remains reachable and the application check succeeds
- The Host column must change to the other host
- The VM must remain Down afterward
* The disk now resides on the second domain
> Verify that the disk now appears on the second domain and that the guest and application still respond. The VM can stay on the same host because this operation changes disk location, not execution location.

```

## Task 7: Engine Backup

Answer the ten questions in this topic group. Review the corresponding OLVM concepts in Lab 7 when needed.

```quiz score
Q: 7.1. What does an OLVM Engine backup protect?
* Management configuration and the engine database
- Only guest RAM
- All guest VM disks through the engine backup archive
- Only VM snapshots without management configuration
> An engine backup protects OLVM's management configuration and engine database, including information used to manage the environment and its permissions. It does not automatically back up the guest VM disks; those need their own backup plan.

Q: 7.2. Why run engine-backup in backup mode with scope all?
- Restore the engine database using the backup command
- Capture only one guest disk through an engine operation
- Move all running guest disks to a backup storage domain
* Create an engine backup covering the full requested scope
> Backup mode creates a backup rather than restoring one. Scope all requests the full management backup scope supported by the tool for that installation. It does not make the archive a backup of every guest disk.

Q: 7.3. Why does engine-backup specify separate backup and log files?
- The archive configures VM networking while the log starts guests
- Both files are SSH authentication keys
* The archive holds backup data while the log records the operation's outcome
- The log is an interchangeable replacement for the archive
> The backup archive contains the data needed for recovery. The log records what happened during the backup, including errors. Keep both, but do not treat the log as a replacement for the archive.

Q: 7.4. Why review the log and Events after checking the files exist?
- Change the database network profile
* Confirm successful completion of the backup
- Grant the backup VM Portal permissions
- Put all guests in Maintenance
> A file can exist even if a backup failed or stopped early. Review the backup log and relevant Events for errors and completion information before relying on the backup.

Q: 7.5. When is an engine restore normally relevant?
* During disaster recovery or engine rebuild
- After every midnight backup
- Whenever Grafana opens
- Before every application VM starts
> Restore an engine backup when recovering a failed manager or rebuilding it with its saved configuration and database. A successful routine backup does not need to be followed by a restore.

Q: 7.6. Why include a timestamp in backup and log filenames?
- Automatically delete old backups according to a retention policy
- Remove the need to inspect backup logs for successful completion
- Make each backup log interchangeable with its backup archive
* Give successive runs distinct, associated artifact names
> A timestamp gives each backup run a distinct name. Using the same timestamp in its backup and log filenames makes it easy to identify which log belongs to which backup.

Q: 7.7. Why make the script executable and test it before scheduling?
- Adding the crontab entry proves the script can run successfully
- Creating the directory alone proves the engine backup command works
* Verify it can run and produce the expected backup and log
- Scheduling the script removes the need to make it executable
> Make the script executable, then run it manually and check the resulting backup and log. This tests the script before you rely on a schedule to run it automatically.

Q: 7.8. Why schedule a tested engine backup script?
- Migrate running VMs whenever the schedule triggers
* Automate recurring protection of management configuration and the database
- Guarantee a restore succeeds without recovery validation
- Capture all guest VM disks through the engine backup automatically
> Scheduling a tested script makes engine backups run regularly without starting each one manually. It protects management data on a recurring basis, but it does not add guest disk backups or prove that a restore will succeed.

Q: 7.9. Which backup choices fit the described uses? (Select all that apply.)
* Manual backup before a major change
* Scheduled script for ongoing engine protection
- Guest snapshot preview instead of all engine backups
- Immediate restore after each successful backup
> Take a manual engine backup before a major change so you have a recovery point for that change. Use scheduled backups for ongoing protection. Restore is a separate action used when recovery is needed.

Q: 7.10. Which evidence validates automated backup setup? (Select all that apply.)
* The intended recurring schedule is configured
- A crontab entry alone proves the first backup succeeded
- An empty backup directory proves the engine database is protected
* A manual script test creates the expected backup and log
> Run the script manually to check that it produces a successful backup and log, then verify the recurring schedule. A schedule entry shows when a command should run; it does not prove that the command works.

```

## Task 8: Monitoring and Grafana

Answer the ten questions in this topic group. Review the corresponding OLVM concepts in Lab 7 when needed.

```quiz score
Q: 8.1. Which integrated authentication link is used for the OLVM Monitoring Portal?
* Sign in with oVirt Engine Auth
- Use the Keycloak administrative console as the monitoring dashboard
- Use a guest SSH login instead of the portal authentication link
- Assume the portal needs no sign-in because the engine page is reachable
> Click Sign in with oVirt Engine Auth to use the Monitoring Portal's integrated engine authentication. This is the portal sign-in route used in the lab, rather than a guest SSH login.

Q: 8.2. Which component collects the historical metrics queried by Grafana?
- The guest console recording only screen output
- The VM template import dialog
- A guest application's data table
* The oVirt Data Warehouse
> The oVirt Data Warehouse collects historical performance data about the OLVM environment. Grafana reads that data to display resource usage and trends in its dashboards.

Q: 8.3. Which view compares CPU utilization of the two hosts?
- Virtual Machine dashboard for one guest rather than both hosts
- Storage Domains Inventory Dashboard
* Host dashboard
- The System Dashboard resource counts alone
> Use the Host dashboard to compare CPU usage across the hosts. Its host-level graphs show how busy each host is, unlike a dashboard focused on just one VM.

Q: 8.4. How can comparing host CPU graphs inform administration?
- The busiest host is always the best destination for an additional VM
* Identify a less loaded host for placement or migration
- Guest IP addresses should be changed when CPU graphs differ
- CPU graphs alone prove application queries are returning expected data
> Comparing CPU graphs can help you find a less busy host for a VM. Use that information when planning placement or migration, while also checking memory capacity and the other requirements of the destination.

Q: 8.5. Which view examines the database guest's CPU and memory usage?
* Virtual Machine dashboard
- Host CPU totals alone instead of guest-level graphs
- Storage domain capacity graphs instead of guest CPU and memory
- System resource counts alone instead of utilization graphs
> Use the Virtual Machine dashboard to view the database VM's CPU and memory usage. Host totals include other VMs and host activity, so they cannot show that guest's usage on their own.

Q: 8.6. Why observe VM CPU and memory usage?
- Select the Keycloak realm
- Create the second LUN
- Replace the backup log
* Support right-sizing and identify abnormal resource use
> CPU and memory trends help you decide whether a VM has too few or too many resources. Unusual changes can also prompt investigation of problems such as a busy process or a memory leak.

Q: 8.7. Which view compares capacity and I/O after storage migration?
- VM console options
- Template list only
* Storage Domains Inventory Dashboard
- Keycloak Users
> Use the Storage Domains Inventory Dashboard to compare storage capacity and disk activity. After a disk move, these views help you see usage on the source and destination domains.

Q: 8.8. Why use the System Dashboard during incident review?
- Provide a live console for repairing guest software
* Summarize engine health and cluster resource counts
- Replace every host and guest investigation entirely
- Prove all application requests succeed without testing them
> The System Dashboard gives you an overview of engine health and cluster resources. Start there to see the wider picture, then inspect the affected hosts, VMs, or storage for more detail.

Q: 8.9. Which information is tracked in monitoring dashboards? (Select all that apply.)
* Host and VM resource utilization
* Storage capacity, I/O, and migration activity
- Live interactive guest console sessions
- User password values
> Monitoring dashboards show host and VM usage, storage capacity, and activity such as disk I/O and migration. They help you inspect performance and trends; they do not display passwords or provide an interactive guest screen.

Q: 8.10. Which requests use interfaces outside Grafana? (Select all that apply.)
* Open the guest Console from the VM details
- Compare historical host CPU utilization
- Review storage capacity trends
* Review current management events in Administration Portal Events
> Use the VM's Console to interact with its screen and the Administration Portal's Events to review management events. Grafana provides historical performance and capacity dashboards for a different purpose.

```

## Task 9: VM Snapshots

Answer the ten questions in this topic group. Review the corresponding OLVM concepts in Lab 7 when needed.

```quiz score
Q: 9.1. When is a VM snapshot useful?
* Before a risky patch, upgrade, or configuration change
- A snapshot is only useful after the active VM disk is removed
- A snapshot replaces every engine configuration backup
- Snapshots are intended to remain indefinitely without storage impact
> Take a snapshot before a patch, upgrade, or configuration change when you may need to return to the earlier VM state. It gives you a rollback point if the change causes a problem.

Q: 9.2. How does the copy-on-write snapshot preserve disk state?
- Copy the entire disk immediately and never create a delta layer
- Keep writing directly to the unchanged read-only snapshot base
- Overwrite the saved snapshot state on every later guest write
* Keep a read-only base and capture later writes in a delta
> The saved disk state stays unchanged while later writes go into a separate layer called a delta. This avoids copying the whole disk immediately, but the delta uses more storage as the guest writes data.

Q: 9.3. How do you request a disk-only snapshot?
- Select SuperUser
- Choose another migration host
* Leave Save Memory unchecked
- Check Save Memory and clear all disks
> Leave Save Memory unchecked and keep the intended disk selected to create a disk-only snapshot. Checking Save Memory also saves the VM's RAM state.

Q: 9.4. What does including memory add to the snapshot?
- Only a new disk description without any guest RAM
* The guest's in-memory running state
- The engine management database alongside the guest disk
- An independent full backup of all cluster resources
> Including memory saves the contents of the VM's RAM as well as its disks. This captures the running state, and the VM may pause briefly while that memory is saved.

Q: 9.5. Why avoid retaining unnecessary snapshot chains indefinitely?
* Growing deltas consume storage and can slow disk I/O
- Snapshot deltas remain a fixed size regardless of guest writes
- Long chains always improve guest disk I/O
- Deleting unneeded snapshots normally deletes the entire guest
> Snapshot deltas grow as the VM writes more data. Keeping unnecessary snapshot chains uses storage and can slow disk access, so remove snapshots when you no longer need them.

Q: 9.6. Which VM state is required before previewing a snapshot for restore?
- Up and migrating
- Installing a host agent
- Only the engine service must be stopped
* Down
> Shut down the VM and wait for Down before previewing the snapshot in this lab's restore workflow. Stopping the engine instead does not put the guest into the required state.

Q: 9.7. A previewed snapshot has the wrong state. What returns to the pre-preview active state?
- Delete the entire VM
- Create another snapshot without undoing the preview
* Undo
- Commit
> Choose Undo if the previewed snapshot is not the state you want. Undo cancels the preview and returns the VM to the active state it had before previewing.

Q: 9.8. What is the consequence of committing a preview?
- Move execution to another host
* Keep the restored state permanently and discard post-snapshot changes
- Return automatically to the pre-preview state
- Only add a new label
> Commit accepts the previewed snapshot as the VM's current state and discards changes made after that snapshot. Once committed, you cannot use Undo to return to the pre-preview state, so check the preview carefully first.

Q: 9.9. Which statements describe deleting an unneeded snapshot? (Select all that apply.)
* The delta merges into the base image
* A large delta can increase merge time and storage I/O
- It normally deletes the entire VM
- It changes a disk-only snapshot into a memory snapshot
> Deleting an unneeded snapshot merges its disk changes into the remaining disk images instead of deleting the VM. A larger amount of changed data takes more work to merge and can increase disk I/O during cleanup.

Q: 9.10. Which checks validate completed snapshot creation? (Select all that apply.)
* The guest workload remains accessible after capture
- A disk-only snapshot must show memory included
- Every snapshot must be restored and committed immediately
* The snapshot reaches OK and its memory setting matches the intended type
> Check that the snapshot reaches OK and that its details show whether memory was included as intended. Then confirm that the guest service still works. You do not need to restore a snapshot immediately after creating it.

```

## Task 10: Users and Roles

Answer the ten questions in this topic group. Review the corresponding OLVM concepts in Lab 7 when needed.

```quiz score
Q: 10.1. Why distinguish Keycloak-backed and local internal users in OLVM 4.5?
* They have different creation and portal-login behavior
- They always share one creation command
- Every local account automatically becomes a Keycloak account
- The user's storage domain determines its identity source
> In this lab's Keycloak-enabled configuration, users created in Keycloak and local users created with the JDBC tool have different login behavior. Use the identity path that matches the intended access; creating a local account does not make it a Keycloak account.

Q: 10.2. Which realm is used for the new administrator?
- The authorization provider name is a substitute for choosing the realm
- Any Keycloak realm is equivalent for the OLVM user search
- The user must be created as a local JDBC identity rather than in Keycloak
* ovirt-internal
> Create the new administrator in the `ovirt-internal` Keycloak realm used by this lab. OLVM must search the corresponding identity provider to find that account.

Q: 10.3. A Keycloak user is missing from the OLVM Add User search. What should you check first?
- The name of a storage domain
- The disk assigned to a guest
* The user's realm and the selected authorization provider
- The guest VM's snapshot description
> Check that the user was created in the expected Keycloak realm and that Add User is searching the matching authorization provider. Searching a different provider can leave the account out of the results.

Q: 10.4. After creating and importing the account, what grants full administrative access?
- Account creation alone grants full administration
* Assign its SuperUser system permission
- Importing the account automatically grants SuperUser
- Setting the password assigns the required system permission
> Creating and importing the account makes the user available to OLVM. Assign the SuperUser system permission to grant full administration; creating the account or setting its password alone does not grant that access.

Q: 10.5. How is a local internal user created in this task?
* Run ovirt-aaa-jdbc-tool on the engine host
- Create it through Keycloak and assume it becomes a JDBC account
- Create it only in the portal without first creating the local identity
- Run the JDBC user creation operation inside an application guest
> Run `ovirt-aaa-jdbc-tool` on the engine host to create a local internal user. Use Keycloak's user-management interface when you intend to create a Keycloak-backed account instead.

Q: 10.6. Why manually add a JDBC-created user in the portal?
- Account creation imports every guest VM
- Add User creates a physical disk
- The account must become a KVM host
* It must be added to OLVM before assigning portal permissions
> Creating the local account with the JDBC tool does not automatically add it to OLVM's portal user list. Add it through the portal, then assign the required role and permissions.

Q: 10.7. How do UserRole and SuperUser differ?
- UserRole gives full administration while SuperUser only allows VM use
- The two roles always provide identical access
* UserRole supports VM Portal access; SuperUser gives full administration
- UserRole is only for Keycloak and SuperUser only for local JDBC accounts
> UserRole supports basic VM access according to the user's assigned permissions. SuperUser grants full administration of the OLVM environment. Choose the role that matches the access the user needs.

Q: 10.8. A local JDBC user has a role but cannot use normal web login in the Keycloak-enabled environment. What administrative login approach is recommended?
- Move the web VM disk
* Use a Keycloak-backed user for administrative web access
- Recreate the guest network repeatedly
- Delete the engine database
> Use a Keycloak-backed account for administrative browser access in this lab's Keycloak-enabled configuration. Giving a local JDBC user an OLVM role does not change how that account authenticates.

Q: 10.9. Which checks validate the new users? (Select all that apply.)
* Sign in with the new Keycloak user and verify full access
* Inspect the local user's expected role in Permissions
- Assume creation proves administrative access
- Assume every JDBC user can log into the Keycloak portal
> Sign in with the new Keycloak account and check that it has the intended administrative access. For the local user, inspect its assigned role in Permissions. These checks verify the access demonstrated for each identity path in this lab.

Q: 10.10. Which settings create an enabled Keycloak user with a non-temporary password? (Select all that apply.)
* Temporary is Off when setting its password
- Leave the account disabled after assigning SuperUser
- Skip credentials because import creates a password
* Enabled is On when creating the user
> Set Enabled to On so the Keycloak account can authenticate, and set Temporary to Off so its password is not marked as temporary. Adding the account to OLVM and assigning permissions are additional steps.

```

## Review Your Results

After answering every question, check the score tracker. Review the explanations and revisit the relevant OLVM topic in Lab 7 before retrying any missed question.

## Learn More

Review the OLVM administration procedures, architecture diagrams, and validation checklists in Lab 7 through the workshop navigation.

## Acknowledgements

- **Source Lab Authors** - Shawn Kelley, John Priest, Perside Foster
- **Source Lab Contributor** - Marvin Kim
- **Quiz Preparation** - Codex, for Perside Foster
- **Last Updated By/Date** - Codex, October 9, 2026
