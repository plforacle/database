# Luna OLVM Administration Quiz

```quiz-config
passing: 80
badge: images/badge.png
```

## Introduction

Review the OLVM administration concepts taught in **Lab 7: Optional Luna OLVM Administration**. This quiz contains **100 scored questions**, organized into ten groups of ten questions that match the ten tasks in Lab 7.

Estimated Time: 90-120 minutes

### Objectives

In this quiz, you will:

- Check your understanding of deployment, host architecture, networking, and shared storage
- Apply application validation and migration concepts to practical scenarios
- Review engine backup, monitoring, snapshot, and user management decisions
- Use explanations to identify topics to revisit in Lab 7

### Before You Begin

Review all ten tasks in Lab 7. Task 8's monitoring exercise is optional in the source lab, but its ten questions are included in this quiz. Reading that task is sufficient to answer them.

For single-answer questions, select one choice. Questions labeled **Select all that apply** require every correct choice and no incorrect choices. Click **Check Answer** for feedback and an explanation. Use **Try Again** to retry a question.

Each question carries equal weight. Complete all 100 questions for a final result. The passing score is **80%**, or **80 correct answers**. A multiple-answer question counts as one question. Passing unlocks a downloadable quiz badge.

The scenarios follow the **Luna environment in Lab 7**, including its application placement and network access paths. The hands-on lab uses values from your own Luna session; these questions use the lab's example values.

## Task 1: Infrastructure Setup

Review Task 1 in Lab 7 if you need to revisit a concept. Answer all ten questions in this group.

```quiz score
Q: 1.1. The playbook pauses after creating the lab servers. How should you preserve the environment?
* Press Ctrl+C and then A at the pause
- Press Enter to continue
- Run cleanup before recording IPs
- Let the paused playbook continue automatically
> The lab says Enter continues cleanup and deletes the environment. Aborting at the pause preserves the deployed servers.

Q: 1.2. Why record both public and private IP addresses during provisioning?
- They can always be used interchangeably
- Only public addresses are used in this lab
- Private addresses are used only for OVA downloads
* They support different external access and internal management paths
> Public access supports SSH and VNC tunneling; private addresses support engine-to-host management. Choose the address appropriate to the documented connection path.

Q: 1.3. Why install the OCI and community Ansible collections before provisioning?
- The collections replace the inventory and instance configuration files
- The collections configure the engine database before package installation
* The playbook depends on their modules to create OCI resources
- The playbook can create OCI resources without its required collections
> The collections provide reusable automation modules that the playbook uses to provision compute, networking, and storage through OCI APIs.

Q: 1.4. A KVM host check reports AMD-V was not found. What does the lab direct you to do?
- Choose a different logical network
* Stop and wait for the instructor
- Register the host and ignore the result
- Install the web application to enable virtualization
> Missing virtualization support is a prerequisite failure. Continuing host configuration does not resolve the failed hardware check.

Q: 1.5. The engine goes offline while the KVM hosts remain healthy. What happens to running VMs according to the lab?
* They keep running on the hosts
- They all stop because management and execution are the same service
- They automatically migrate solely because the engine went offline
- They require the Administration Portal to stay open to execute
> The engine provides centralized management. KVM handles workload execution independently, so loss of management alone does not stop the VMs.

Q: 1.6. Why use an SSH tunnel for the manager's VNC session?
- It copies VM disks into the engine database
- It removes the need for a VNC server
- It opens VNC directly to the public internet
* It provides encrypted access to a port not directly reachable from the internet
> The local viewer connects through SSH to the manager's VNC server. The lab uses this path because direct internet access to VNC is firewalled.

Q: 1.7. The ovirt-engine package is installed. Why still run engine-setup?
- Engine-setup imports the application VMs
- Engine-setup replaces the two KVM hosts
* Installing supplies software; engine-setup configures the manager services
- The package contains only the VNC viewer
> The lab separates package installation from configuration of the database, web services, certificates, and initial management objects.

Q: 1.8. Why obtain the manager's FQDN before opening its web interface?
- Any public IP always matches the engine certificate equally well
* The engine's SSL certificate is bound to that name
- The certificate is bound only to the guest VM network name
- The FQDN is needed only after importing the application VMs
> Using the fully qualified name aligns the URL with the certificate name. It is part of the documented portal access path.

Q: 1.9. Which actions does engine-setup perform? (Select all that apply.)
* Configure the engine database and web services
* Generate certificates and initialize the Default data center and cluster
- Import the MySQL and web application OVAs
- Create snapshots of every guest VM
> Engine setup initializes the management platform. Application imports and guest snapshots are separate tasks later in the lab.

Q: 1.10. Which portal-purpose pairings match the lab? (Select all that apply.)
* Monitoring Portal: review Grafana resource metrics
- VM Portal: run the OCI provisioning playbook
- Monitoring Portal: open a live guest console
* Administration Portal: manage hosts, networks, storage, VMs, and users
> The Administration Portal manages infrastructure and the Monitoring Portal presents metrics. Provisioning and live console access use other workflows.

```

## Task 2: KVM Hosts

Review Task 2 in Lab 7 if you need to revisit a concept. Answer all ten questions in this group.

```quiz score
Q: 2.1. Which host component receives engine requests and acts as the engine-to-host intermediary?
* VDSM
- The guest agent inside MySQL
- The engine's PostgreSQL database
- The browser's VNC viewer
> VDSM is the host-side management agent. It translates engine requests into host operations and reports host and VM status back.

Q: 2.2. How does VDSM manage the virtualization stack?
- It bypasses libvirt and directly executes all guest instructions
- It is the guest-side agent that reports installed applications
- It replaces QEMU with a process running inside the engine database
* It uses libvirt to manage KVM and QEMU
> The lab separates the host agent from the virtualization API. VDSM uses libvirt rather than acting as the hypervisor itself.

Q: 2.3. How does Task 2 distinguish KVM from QEMU?
- QEMU runs in kernel space while KVM runs in user space
- Both are guest applications installed only inside the VM
* KVM runs in kernel space; each VM has a QEMU process in user space
- Both are engine services with no host-side components
> KVM supplies kernel virtualization support. QEMU represents each VM as a user-space process, illustrating two different layers of the stack.

Q: 2.4. What is the purpose of the guest agent described in the lab?
- Receive engine management commands and manage host storage
* Report guest information such as OS details and IP addresses
- Replace libvirt as the host virtualization API
- Configure the Default cluster when the engine is initialized
> The guest agent supplies information from inside the guest. It is distinct from the host-side VDSM agent and the engine's management services.

Q: 2.5. Why register the host using its private VDSM hostname?
* Direct engine communication to the private management network
- It routes management over the primary public-side access path
- It makes the management hostname interchangeable with every guest address
- It removes the need for the engine to reach VDSM on the host
> The private name identifies the documented management interface. It keeps engine-to-host and related traffic on the private access path.

Q: 2.6. Why copy the engine's SSH public key to the host's root authorized_keys file?
- Install the VDSM packages merely by placing the key file
- Authorize all guest users to administer the portal
- Replace the private management hostname with a guest address
* Enable engine SSH authentication for host installation and configuration
> The engine needs SSH key authentication to install VDSM and deploy host configuration. This bootstrap access differs from OLVM user permissions.

Q: 2.7. A newly added host shows Installing. What should you do before proceeding?
- Force a migration to finish installation
- Remove its logical network
* Wait until it reaches Up
- Treat Installing as proof of VM readiness
> Installing indicates ongoing package and agent deployment. The lab's readiness checkpoint is Up, after integration and verification complete.

Q: 2.8. What does the Maintenance host status mean in the lab?
- The host is ready for normal guest placement with no restrictions
* The host is intentionally offline for maintenance or updates
- The host is still installing its management packages
- The host has an unexpected operational failure requiring log review
> Maintenance is a deliberate administrative state. It differs from Up, Installing, and Non Operational.

Q: 2.9. Which cluster host characteristics are listed in Task 2? (Select all that apply.)
* Compatible CPU type
* Common storage domains and network configuration
- Identical guest usernames
- A shared public browser session
> Compatible CPUs and common storage and networking support migration and resource pooling. Guest usernames and browser sessions do not define the cluster.

Q: 2.10. Which checks belong before continuing to Task 3? (Select all that apply.)
* The engine SSH public key has been copied to both hosts
- Both application VMs are already imported
- The web VM disk has moved to the second domain
* Both KVM hosts show Up
> Task 2 checks host integration and authentication. Application deployment and storage migration occur in later tasks.

```

## Task 3: Networking, Storage, and Test VM Setup

Review Task 3 in Lab 7 if you need to revisit a concept. Answer all ten questions in this group.

```quiz score
Q: 3.1. What is the main purpose of l2-vm-network?
* Carry virtual machine traffic
- Carry only engine-to-VDSM management traffic
- Provide the persistent storage location for VM disks
- Collect the guests historical resource metrics
> The logical VM network defines guest communication. Storage, authentication, and host file paths serve other purposes.

Q: 3.2. You created the logical network in the data center. What is still needed on each host?
- Create a local user with that name
- Download the OVA into the browser folder
- Restart MySQL in the guest
* Assign it through Setup Host Networks
> Defining the network does not complete host attachment. The lab assigns it to each host through the network setup dialog.

Q: 3.3. What role does the VM network's Linux bridge serve?
- Automatically allocate guest addresses through a DHCP service
- Replace the physical interface with no uplink to the network
* Connect VM virtual interfaces to the physical interface
- Store guest disk data in the host network configuration
> The bridge acts as a virtual switch between guests and the physical network.

Q: 3.4. Why use the OLVM UI or API rather than manually edit host network scripts after assignment?
- Manual edits automatically synchronize with every engine network change
* Manual changes can be overwritten and break connectivity
- Manual editing is required after every Setup Host Networks operation
- OLVM network settings last only until the next browser login
> The engine and VDSM manage host networking. External edits can conflict with that configuration.

Q: 3.5. Why use storage accessible to both hosts?
* Both need VM disk access for migration and HA
- The destination can rely on disks that only the source host can access
- Shared storage makes compatible host networking unnecessary
- A local-only disk provides the same migration path as shared storage
> Shared disk access supports a VM when execution moves between hosts. A disk accessible only to its source host cannot support this workflow.

Q: 3.6. A submitted domain is not yet Active. What should you do?
- Import application OVAs immediately
- Treat its name as proof of readiness
- Delete the VM network
* Wait for Active before continuing
> Active is the storage readiness checkpoint. Submission alone does not establish completed attachment.

Q: 3.7. Why download the OVA to the selected import host?
- The file must be in the guest before creation
- Imports read only the engine database
* The import reads that host's filesystem
- The browser uploads every local download
> Host and File Path identify where the OVA is read. A file on another machine does not satisfy that path.

Q: 3.8. Why configure static IPv4 settings for the test VM?
- The portal assigns a public IP each boot
* The OCI VLAN has no default DHCP server
- Static IPs create Keycloak users
- The template has no virtual NIC
> The lab sets the guest address, netmask, and gateway because default DHCP is unavailable on the VLAN.

Q: 3.9. Which statements distinguish templates and VMs? (Select all that apply.)
* A template is a master image that cannot start directly
* A created VM has its own disk and lifecycle
- Template edits automatically change existing VMs
- A template is a running guest process
> Templates supply the source for new VMs. Created VMs are independent, so later template changes do not modify them.

Q: 3.10. Which checks validate the test VM network? (Select all that apply.)
* Ping the gateway inside the guest console
- Check only the VM name in the portal
- Check only that the OVA exists
* Inspect the guest interface and IP address
> The lab checks actual guest configuration and gateway reachability. A visible VM or downloaded file does not prove working networking.

```

## Task 4: Application Tier

Review Task 4 in Lab 7 if you need to revisit a concept. Answer all ten questions in this group.

```quiz score
Q: 4.1. Why use prebuilt application OVAs?
* They package configured OS, software, data, and settings
- They create an engine in every application VM
- They eliminate networking between tiers
- They contain only empty disks
> Prepared VMs reduce manual setup while still relying on cluster storage, networking, and VM lifecycle operations.

Q: 4.2. Which division describes the Employee Directory tiers?
- Tomcat provides the employee database and MySQL hosts the web pages
- Both VMs provide database storage without an application tier
- The employee data is served directly from the engine management database
* MySQL provides the database and Tomcat hosts the web application
> Separate database and application VMs form the workload tiers on top of OLVM.

Q: 4.3. Which import source is used for the application files?
- A new blank VM created without importing its configured appliance
- A template-only import used instead of the application VM import workflow
* Virtual Appliance (OVA)
- An ISO installation followed by manual database and application setup
> The files contain exported VMs and are read as virtual appliances rather than identity or dashboard records.

Q: 4.4. Which interface mapping connects the imported VM to the guest network?
- Keep the imported network mapping without checking it
* Set Network Name and Profile Name to l2-vm-network
- Map the VM interface to a profile unrelated to l2-vm-network
- Leave the NIC disconnected because importing an OVA establishes connectivity
> The explicit network mapping connects the imported NIC to the network used by the application tiers.

Q: 4.5. What initial application placement does this Luna task specify?
* Database on olkvm01 and web VM on olkvm02
- Both VMs must initially be placed on olkvm01 as in the E5 beginner path
- The database is initially on olkvm02 and the web VM on olkvm01
- Both application VMs run directly on the OLVM engine host
> The Luna lab demonstrates separate-host application tiers. It differs from the E5 beginner same-host application path.

Q: 4.6. Why query the employee row count after starting MySQL?
- Prove that Up status alone guarantees the database has valid data
- Configure database connectivity solely through the row-count query
- Replace the need to verify whether MySQL responds
* Verify database response and expected sample data
> A successful query returning eight records tests the database workload. VM Up alone does not prove MySQL and its data are available.

Q: 4.7. What does a successful web-to-database ping demonstrate?
- A populated employee page
- A successful backup
* Network reachability between the VMs
- Valid database credentials
> Ping checks connectivity. Queries and application requests are still needed to validate credentials, services, and data.

Q: 4.8. Why view the employee table after individual VM checks?
- A successful guest ping already proves the employee page loads
* Verify database-backed results through the web tier
- The browser check is the step that imports the application OVA
- The page opens successfully whenever the engine portal is reachable
> The browser test exercises the deployed application beyond guest status and basic reachability.

Q: 4.9. Which checks precede migration? (Select all that apply.)
* Both application VMs are Up on l2-vm-network
* The query and Employee Directory show expected records
- An engine restore has completed
- The database disk is deleted
> Task 4 checks guest readiness, services, and data. Restore and disk deletion are not readiness checks.

Q: 4.10. Which checks serve different purposes on the web VM? (Select all that apply.)
* Ping to MySQL checks network reachability
- The OVA filename proves service response
- Host login proves the table is populated
* Local curl checks web application response
> Local response and inter-VM connectivity complement the database query and browser validation.

```

## Task 5: VM Live Migration

Review Task 5 in Lab 7 if you need to revisit a concept. Answer all ten questions in this group.

```quiz score
Q: 5.1. What is the purpose of VM live migration in Task 5?
* Move a running VM's execution between KVM hosts
- Move only its disk between storage domains
- Export a VM to an OVA
- Replace the guest OS with engine software
> Host migration changes where the VM executes. Task 6 covers the distinct operation of changing disk placement.

Q: 5.2. The destination cannot access the shared storage domain. What should you do?
- Proceed because host CPU capacity replaces the need for disk access
- Keep the disk accessible only to the source after moving execution
- Change the VM network to bypass unavailable shared storage
* Resolve storage access before migrating
> Both hosts must access the VM's storage. Moving execution does not allow the VM to abandon its disks.

Q: 5.3. Why check destination CPU and memory capacity?
- The incoming VM can always displace other guests without a capacity check
- Shared disk access makes destination memory capacity irrelevant
* The destination needs resources to run the arriving VM
- The VM retains its CPU and RAM execution on the source after migration
> The destination must support the incoming workload alongside its existing VMs.

Q: 5.4. Why restart the engine after extensive infrastructure changes before migrating in this lab?
- Move every disk to the second domain
* Refresh cached host, network, and storage information
- Shut down all application VMs
- Discard the templates
> The lab identifies stale engine state as a possible migration error source. Restarting management rebuilds its view without stopping the VMs.

Q: 5.5. A long browser session produces a migration UI exception. What initial recovery does the lab describe?
* Close Firefox, reopen it, sign in, and retry
- Immediately reimport the database OVA and discard the running workload
- Switch the guest to local-only storage before retrying
- Assume the UI exception alone proves the VM has been shut down
> The lab recommends refreshing a stale browser session first. It suggests restarting the engine if the issue persists.

Q: 5.6. Why recopy dirty memory pages during live migration?
- The virtual disk is deleted page by page
- The destination lacks shared storage
- The engine is replacing the VM with a template
* The running VM can change pages after their first copy
> The guest keeps modifying memory during pre-copy. Iterative copying transfers changed pages before the final switch.

Q: 5.7. What does the stop-and-copy phase do?
- Reinstall the guest OS
- Copy the engine database into each guest
* Briefly pause execution to transfer final state before destination activation
- Power off both hosts for the lab
> The taught sequence includes a brief final synchronization followed by resuming on the destination. It is not a permanent shutdown or reinstall.

Q: 5.8. Which portal observation confirms the specified database VM move?
- Grafana opens in a new tab
* ol9-mysql shows Host olkvm02 and Status Up
- It still shows olkvm01 with no migration event
- A domain name appears in the host list
> The changed Host column and running status show the destination and VM readiness. The task then validates connectivity and workload response.

Q: 5.9. Which migration prerequisites are listed? (Select all that apply.)
* Both hosts are in the same cluster
* The VM logical network is available on both
- The VM must change IP after migration
- The source domain must be removed first
> The same cluster and common network preserve the operating environment. Shared storage and destination capacity are additional prerequisites.

Q: 5.10. Which post-migration checks validate workload continuity? (Select all that apply.)
* Check that the web application still returns its employee table
- Check only that the Migrate dialog opened
- Check only that the destination has a name
* Reach the database VM and query the eight employee records
> Moving hosts should also preserve the workload. The lab checks reachability, database data, and the application path.

```

## Task 6: Storage Live Migration

Review Task 6 in Lab 7 if you need to revisit a concept. Answer all ten questions in this group.

```quiz score
Q: 6.1. A running guest's disk must move to another domain. Which operation fits?
* Storage live migration
- Host live migration without moving the disk domain
- An offline disk relocation requiring the guest to stay stopped
- A snapshot preview that reverts the guest to an earlier disk state
> Storage live migration changes the disk location while the VM continues running. It serves a storage placement goal.

Q: 6.2. How does storage migration differ from host migration?
- It always changes both the host and the storage domain together
- It changes the execution host and leaves disk placement unchanged
- It requires the VM to remain stopped throughout the exercise
* It changes the disk's domain rather than the executing host
> Execution placement and disk placement are distinct operations, even when both occur on running VMs.

Q: 6.3. Why must a second unused LUN be available?
- It allows the already-used LUN to be reused as a new independent domain
- It is the destination host required for execution migration
* It supplies storage for the second Fibre Channel domain
- It supplies memory capacity for a running guest
> The second domain requires an available storage resource. This is created before moving the existing disk.

Q: 6.4. Which configuration matches the second domain?
- An ISO domain intended only for installation media
* A Fibre Channel Data domain in the Default data center
- An Export domain used only to transfer appliances
- Local-only storage used in place of the shared Fibre Channel destination
> Task 6 creates another Fibre Channel Data domain as the running web VM disk's destination.

Q: 6.5. When is the new domain ready for the move?
* When it shows Active
- When its name is typed
- After deleting all guest VMs
- After restoring the engine
> Domain submission is not the readiness checkpoint. The task waits for Active before using it.

Q: 6.6. Why use Attached To when selecting a disk in the original domain?
- Select the execution host instead of the attached VM disk
- Move every disk without identifying which workload it belongs to
- Determine the destination domain readiness from the VM name alone
* Identify the disk attached to ol9-webapp
> Identifying the attached VM helps choose the intended disk rather than an arbitrary storage object.

Q: 6.7. What should you monitor after clicking Move?
- Only the certificate filename
- Only the Luna wallpaper
* Tasks and Events until completion
- Only the Keycloak user list
> Tasks and Events show progress and completion. The lab also verifies that the guest stays running and reachable.

Q: 6.8. The post-move web check counts nine table rows. What does that represent?
- One row per KVM host
* One header and eight employee data rows
- Nine employees without a header
- Nine storage domains
> The check counts HTML table rows including the header. The expected total validates the application path after the disk move.

Q: 6.9. Which conditions precede Task 6? (Select all that apply.)
* Both hosts are Up and the original domain is Active
* The VM is running and a second unused LUN is available
- The VM is powered off for snapshot preview
- The original domain is deleted
> Storage migration starts from a healthy running workload and available destination storage. Snapshot restore prerequisites concern a different operation.

Q: 6.10. Which observations support successful storage migration? (Select all that apply.)
* The guest remains reachable and the application check succeeds
- The Host column must change to the other host
- The VM must remain Down afterward
* The disk now resides on the second domain
> Success requires disk relocation and workload continuity. Changing disk placement does not itself require a host move or shutdown.

```

## Task 7: OLVM Engine Backup

Review Task 7 in Lab 7 if you need to revisit a concept. Answer all ten questions in this group.

```quiz score
Q: 7.1. What does the engine backup exercise protect?
* The management configuration and database
- Every guest VM disk through the same engine backup archive
- Only guest snapshots without management configuration
- All OCI resources through an infrastructure export
> The engine stores managed infrastructure and permission configuration. Task 7 protects that management information with engine-backup.

Q: 7.2. Why does the command use backup mode with scope all?
- Restore the engine database using the backup command
- Capture only one guest disk through an engine operation
- Move all running guest disks to a backup storage domain
* Create an engine backup covering the full requested scope
> The command creates a full-scope engine backup for this exercise. It does not perform recovery, guest preview, or migration.

Q: 7.3. Which file is the backup artifact rather than its log?
- /root/backup.sh
- /etc/pki/ovirt-engine/keys/engine_id_rsa
* /tmp/olvm-engine-backup
- /tmp/olvm-engine-backup.log
> The file argument identifies the backup; the log argument identifies diagnostics. The script and SSH key have other roles.

Q: 7.4. Why review the log and Events after checking the files exist?
- Change the database network profile
* Confirm successful completion of the backup
- Grant the backup VM Portal permissions
- Put all guests in Maintenance
> File existence alone does not demonstrate success. The lab checks the log and events for the operation's outcome.

Q: 7.5. When does the task say a restore is normally performed?
* During disaster recovery or engine rebuild
- After every midnight backup
- Whenever Grafana opens
- Before every application VM starts
> The exercise creates backups only. Restore is reserved for recovery scenarios or instructor direction.

Q: 7.6. Why include a timestamp in backup and log filenames?
- Automatically delete old backups according to a retention policy
- Remove the need to inspect backup logs for successful completion
- Make each backup log interchangeable with its backup archive
* Give successive runs distinct, associated artifact names
> Each execution builds distinct filenames with the same run timestamp, pairing each backup with its diagnostic log.

Q: 7.7. Why make the script executable and test it before scheduling?
- Adding the crontab entry proves the script can run successfully
- Creating the directory alone proves the engine backup command works
* Verify it can run and produce the expected backup and log
- Scheduling the script removes the need to make it executable
> The lab tests the script before relying on cron. A schedule does not prove the underlying backup command works.

Q: 7.8. What schedule does the root crontab entry configure?
- Only when a user signs in
* Daily at midnight
- Every minute
- Only after a VM migration
> The entry specifies minute zero and hour zero on every day, providing recurring protection independent of VM and login events.

Q: 7.9. Which backup choices fit the described uses? (Select all that apply.)
* Manual backup before a major change
* Scheduled script for ongoing engine protection
- Guest snapshot preview instead of all engine backups
- Immediate restore after each successful backup
> Manual and recurring backups address different protection needs. Restore remains a separate recovery workflow.

Q: 7.10. Which evidence validates scheduled setup? (Select all that apply.)
* Root crontab contains the daily midnight entry
- All guest disks move to local storage
- All Keycloak users are deleted
* The tested script creates a backup and log in the directory
> The checklist establishes both a working script and its schedule. Storage relocation and identity deletion do not validate backups.

```

## Task 8: Monitoring Portal and Grafana

Review Task 8 in Lab 7 if you need to revisit a concept. Answer all ten questions in this group.

```quiz score
Q: 8.1. Which login path does the lab use for monitoring?
* Sign in with oVirt Engine Auth
- Use the Keycloak administrative console as the monitoring dashboard
- Use a guest SSH login instead of the portal authentication link
- Assume the portal needs no sign-in because the engine page is reachable
> The task uses integrated engine authentication. Guest SSH and template operations are separate workflows.

Q: 8.2. What collects historical metrics for Grafana?
- The guest console records all historical host metrics
- Grafana uses only current browser observations without a data warehouse
- The application employee table supplies the cluster resource metrics
* The oVirt Data Warehouse
> The warehouse records engine and host metrics in a separate PostgreSQL schema. Grafana queries that data for its dashboards.

Q: 8.3. Which view compares CPU utilization of the two hosts?
- Virtual Machine dashboard for one guest rather than both hosts
- Storage Domains Inventory Dashboard
* Host dashboard
- The System Dashboard resource counts alone
> Per-host resource graphs allow comparison of cluster load. Identity and VM creation dialogs do not supply that comparison.

Q: 8.4. How can comparing host CPU graphs inform administration?
- The busiest host is always the best destination for an additional VM
* Identify a less loaded host for placement or migration
- Guest IP addresses should be changed when CPU graphs differ
- CPU graphs alone prove application queries are returning expected data
> The lab connects observed load to manual migration or load-balancing policy decisions. CPU graphs do not validate application data or credentials.

Q: 8.5. Which view examines the database guest's CPU and memory usage?
* Virtual Machine dashboard
- Host CPU totals alone instead of guest-level graphs
- Storage domain capacity graphs instead of guest CPU and memory
- System resource counts alone instead of utilization graphs
> Guest metrics isolate the database VM's usage from total host load, helping assess its behavior and resource allocation.

Q: 8.6. Why observe VM CPU and memory usage?
- Select the Keycloak realm
- Create the second LUN
- Replace the backup log
* Support right-sizing and identify abnormal resource use
> The task links metrics to sizing decisions and detecting runaway processes or memory leaks.

Q: 8.7. Which view compares capacity and I/O after storage migration?
- VM console options
- Template list only
* Storage Domains Inventory Dashboard
- Keycloak Users
> This dashboard presents both domains' utilization and activity, including possible migration-related I/O.

Q: 8.8. Why use the System Dashboard during incident review?
- It replaces individual host and guest resource investigation entirely
* Summarize engine health and cluster resource counts
- It proves the application responds without any workload check
- It supplies live console access to repair the guest OS
> The overview precedes resource-specific investigation. At this point the lab expects two hosts, three running VMs, and two active storage domains.

Q: 8.9. Which information is tracked in monitoring dashboards? (Select all that apply.)
* Host and VM resource utilization
* Storage capacity, I/O, and migration activity
- Live interactive guest console sessions
- User password values
> The lab lists health, performance, capacity, and migration metrics. Grafana is not a guest console or a password viewer.

Q: 8.10. Which requests should use interfaces outside Grafana? (Select all that apply.)
* Open the guest Console in the VM detail view
- Compare historical host CPU
- Review storage capacity trends
* Inspect real-time logs in Administration Portal Events
> Events and Console are separate management functions. Historical resource and capacity metrics belong in monitoring dashboards.

```

## Task 9: VM Snapshots

Review Task 9 in Lab 7 if you need to revisit a concept. Answer all ten questions in this group.

```quiz score
Q: 9.1. When does the lab describe a snapshot as useful?
* Before a risky patch, upgrade, or configuration change
- A snapshot is only useful after the active VM disk is removed
- A snapshot replaces every engine configuration backup
- Snapshots are intended to remain indefinitely without storage impact
> A snapshot captures a point-in-time state to support rollback if a later change causes a problem.

Q: 9.2. How does the copy-on-write snapshot preserve disk state?
- Copy the entire disk immediately and never create a delta layer
- Keep writing directly to the unchanged read-only snapshot base
- Overwrite the saved snapshot state on every later guest write
* Keep a read-only base and capture later writes in a delta
> The base and growing delta avoid an immediate full copy. This explains initial efficiency and later storage growth.

Q: 9.3. How do you request a disk-only snapshot?
- Select SuperUser
- Choose another migration host
* Leave Save Memory unchecked
- Check Save Memory and clear all disks
> Save Memory controls inclusion of RAM. The task leaves it unchecked and keeps the VM disk selected.

Q: 9.4. What does including memory add to the snapshot?
- Only a new disk description without any guest RAM
* The guest's in-memory running state
- The engine management database alongside the guest disk
- An independent full backup of all cluster resources
> A memory snapshot saves guest RAM as well as disk state and can briefly pause the guest during capture.

Q: 9.5. Why avoid retaining unnecessary snapshot chains indefinitely?
* Growing deltas consume storage and can slow disk I/O
- Snapshot deltas remain a fixed size regardless of guest writes
- Long chains always improve guest disk I/O
- Deleting unneeded snapshots normally deletes the entire guest
> The lab warns about capacity growth and I/O effects as deltas accumulate, and recommends cleaning up unneeded snapshots.

Q: 9.6. Which VM state is required before snapshot preview for restore?
- Up and migrating
- Installing a host agent
- Only the engine must be in Maintenance
* Down
> The taught restore workflow begins with guest shutdown and Down status. The lab asks you to understand it conceptually rather than execute it here.

Q: 9.7. A previewed snapshot has the wrong state. What returns to the pre-preview active state?
- Delete the entire VM
- Create a Keycloak realm
* Undo
- Commit
> Preview is reversible. Undo discards it and returns to the state before preview, rather than selecting another older snapshot.

Q: 9.8. What is the consequence of committing a preview?
- Move execution to another host
* Keep the restored state permanently and discard post-snapshot changes
- Return automatically to the pre-preview state
- Only add a new label
> Commit makes the restored state permanent and cannot be undone according to the lab. It differs from reversible Preview and Undo.

Q: 9.9. Which statements describe deleting an unneeded snapshot? (Select all that apply.)
* The delta merges into the base image
* A large delta can increase merge time and storage I/O
- It normally deletes the entire VM
- It changes the user's authorization provider
> Snapshot cleanup preserves the VM while merging layers. The amount of delta data affects the work and I/O required.

Q: 9.10. Which checks validate workloads after snapshot creation? (Select all that apply.)
* The database query still returns eight employee records
- The disk-only snapshot must show Memory Yes
- A restore must be committed during this exercise
* The web check still returns nine table rows
> The checklist tests service continuity. A disk-only snapshot excludes RAM, and restore remains a conceptual exercise here.

```

## Task 10: Manage Users and Roles

Review Task 10 in Lab 7 if you need to revisit a concept. Answer all ten questions in this group.

```quiz score
Q: 10.1. Why distinguish Keycloak-backed and local internal users in this OLVM 4.5 lab?
* They have different creation and portal-login behavior
- They always share one creation command
- Every local account automatically becomes a Keycloak account
- The user's storage domain determines its identity source
> Keycloak creates browser-based administrators; the JDBC tool creates local identities. The lab warns that local users may not support normal portal login in this environment.

Q: 10.2. Which realm is used for the new administrator?
- The authorization provider name is a substitute for choosing the realm
- Any Keycloak realm is equivalent for the OLVM user search
- The user must be created as a local JDBC identity rather than in Keycloak
* ovirt-internal
> The account must be in the specified Keycloak realm before OLVM searches for it through the corresponding authorization provider.

Q: 10.3. A Keycloak user does not appear in the Add User search. What should you check first?
- The employee row count
- The second domain's name
* The account realm and selected authorization provider
- The web VM's snapshot
> The troubleshooting tip links discovery to realm and provider selection. Guest snapshots and application data do not determine user search results.

Q: 10.4. After creating and importing the account, what grants full administrative access?
- Account creation alone grants full administration
* Assign its SuperUser system permission
- Importing the account automatically grants SuperUser
- Setting the password assigns the required system permission
> Creation and import identify the user; permissions control access. The lab grants SuperUser for full administration.

Q: 10.5. How is a local internal user created in this task?
* Run ovirt-aaa-jdbc-tool on the engine host
- Create it through Keycloak and assume it becomes a JDBC account
- Create it only in the portal without first creating the local identity
- Run the JDBC user creation operation inside an application guest
> The JDBC tool manages local identities on the engine. This differs from creating a user through the Keycloak console.

Q: 10.6. Why manually add a JDBC-created user in the portal?
- Account creation imports every guest VM
- Add User creates a physical disk
- The account must become a KVM host
* It must be added to OLVM before assigning portal permissions
> The lab says a newly created local account does not appear in the portal until manually added. Role assignment follows that step.

Q: 10.7. How does the lab distinguish UserRole and SuperUser?
- UserRole gives full administration while SuperUser only allows VM use
- The two roles always provide identical access
* UserRole supports VM Portal access; SuperUser gives full administration
- UserRole is only for Keycloak and SuperUser only for local JDBC accounts
> The roles illustrate different permission scopes. They are not infrastructure or monitoring object types.

Q: 10.8. A local JDBC user has a role but cannot use normal web login in the Keycloak-enabled environment. What does the lab advise?
- Move the web VM disk
* Use a Keycloak-backed user for administrative web access
- Recreate the guest network repeatedly
- Delete the engine database
> Visibility and permissions do not remove the documented identity-source difference. Keycloak-backed users are recommended for browser administration.

Q: 10.9. Which checks validate the new users? (Select all that apply.)
* Sign in with the new Keycloak user and verify full access
* Inspect the local user's expected role in Permissions
- Assume creation proves administrative access
- Assume every JDBC user can log into the Keycloak portal
> The task tests actual Keycloak login and inspects the local user's role. It does not promise local identities normal browser login here.

Q: 10.10. Which account settings does the lab specify? (Select all that apply.)
* Temporary is Off when setting its password
- Leave the account disabled after assigning SuperUser
- Skip credentials because import creates a password
* Enabled is On when creating the user
> Enabling the account and setting its non-temporary password are explicit identity steps. Import and role assignment are separate.

```

## Review Your Results

After answering every question, check the score tracker. If your score is below 80%, review the explanations, revisit the relevant task in Lab 7, and retry the questions you missed. You can also retry individual questions after passing to reinforce a topic.

## Learn More

Return to **Lab 7: Optional Luna OLVM Administration** in the workshop navigation for the source procedures, diagrams, and validation checklists.

## Acknowledgements

- **Source Lab Authors** - Shawn Kelley, John Priest, Perside Foster
- **Source Lab Contributor** - Marvin Kim
- **Quiz Preparation** - Codex, for Perside Foster
- **Last Updated By/Date** - Codex, October 9, 2026
