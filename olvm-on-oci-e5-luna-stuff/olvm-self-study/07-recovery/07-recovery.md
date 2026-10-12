# OLVM Self-Study: Recovery Management

## Introduction

This module contains **45 scored practice questions** for the recovery management objective, which accounts for **9%** of the exam weighting supplied for this training. Use it after the instructor-led Lab 7 session and while reviewing the supplied course transcripts.

Estimated Time: 45-63 minutes, including explanation review

```quiz-config
passing: 80
badge: images/badge.png
```

### Objectives

- Back Up the Manager
- Restore a Manager Backup
- Rename the Engine
- Explain Disaster Recovery Solutions

### How to Practice

Choose **one option per question**, including questions whose choices contain a set of items. Click **Check Answer** to see feedback and the explanation. Answer all 45 questions; at least **36 correct answers** unlock this module's badge. The 80% target is a study milestone, not an official exam passing score.

Record your first-attempt score before using **Try Again**. Review the corresponding transcript clip or Lab 7 task, explain the reasoning, and retry after studying. Scores are tracked within this module; keep your own first-attempt record before navigating away.

The recorded course uses older OL8 and OLVM examples than Lab 7. Questions explicitly referring to the recorded course use that context. Match package, identity, and UI procedures to the actual practice environment.

[Study guide and coverage map](../../workshops/olvm-study/index.html?lab=study-guide)

## Task 1: Back Up the Manager

```quiz score
Q: 7.001. What tool is used to backup and restore OLVM?
- manager-backup
* engine-backup
- olvm-backup
- ovirt-backup
> `engine-backup` is the OLVM tool used for backing up and restoring the engine. It is designed specifically for OLVM engine databases and configuration files.
```

```quiz score
Q: 7.002. Which content belongs to an engine management backup?
- All guest disk images automatically regardless of backup scope
- Every administrator workstation's local files
- Every physical switch's firmware automatically
* Engine configuration and the selected management databases
> The recovery course and Lab 7 use engine-backup for management state. Guest disk protection requires a separate backup design.
```

```quiz score
Q: 7.003. Lab 7 schedules engine backups while management services remain active. What should still be verified?
- That no backup log exists
- That every guest disk is automatically included
* Backup completion, the resulting artifact, and the operation log
- That the engine has been powered off before every run
> The training supports online engine backup. Lab 7 Task 7 verifies artifacts and logs rather than assuming a scheduled invocation succeeded.
```

```quiz score
Q: 7.004. Why must a scheduled engine backup use an appropriate privileged execution context?
- A Grafana dashboard supplies database write credentials
- The guest agent grants root access to the manager automatically
* It needs authorized access to the engine configuration and databases being backed up
- Every ordinary portal user automatically has OS root access
> The recovery lesson runs engine-backup as root. Lab 7 Task 7 automates that operation, so the execution context must support the required access.
```

```quiz score
Q: 7.005. What parameter specifies whether to perform backup or restore?
- --task
- --action
* --mode
- --operation
> `--mode` specifies whether the tool performs a backup or restore. This single option controls the major operation type.
```

```quiz score
Q: 7.006. In an otherwise valid engine-backup invocation, what does omitting an explicit scope mean in the recorded course?
- The tool needs no mode or file parameters at all
- Only guest disks are backed up
* The default scope is all
- Nothing is backed up until a VM is shut down
> The recorded course describes all as the default scope. A usable invocation still needs the required mode, file, and appropriate logging parameters.
```

```quiz score
Q: 7.007. What is the important boundary of an all-scope engine backup?
- It guarantees an off-site copy of every guest disk
- It replaces every VM snapshot with a tape backup
- It includes all physical network devices without configuration
* It covers supported management configuration and databases, not the VM disk payloads automatically
> All refers to the tool's management backup scope. Lab 7 reinforces that engine-backup is not a complete VM workload backup.
```

```quiz score
Q: 7.008. What does the files scope of engine-backup protect?
- Only all guest virtual disks
* Selected engine configuration and related files handled by the tool
- Every file on every host and guest
- Only the browser's downloads directory
> The recovery lesson distinguishes files from database scopes. Files is not a general full-filesystem backup of the entire environment.
```

```quiz score
Q: 7.009. What does scope=db backup?
- Only data warehouse database
- All databases
- Configuration files
* Only the engine database (active database)
> `scope=db` backs up only the active engine database. This scope is useful when the engine operational database is the target of the backup.
```

```quiz score
Q: 7.010. What does scope=dwhdb backup?
- Configuration files
- Engine database
* Only the data warehouse database (historical data)
- All databases
> `scope=dwhdb` backs up only the Data Warehouse database, which contains historical data. It is separate from the active engine database.
```

```quiz score
Q: 7.011. Why check the installed engine-backup tool's supported scopes before planning a backup?
- An unsupported scope automatically creates the missing database
- Scope selection determines the VM's CPU model
- Every scope is available and populated on every installation
* Optional management components and their available database scopes depend on the installation
> The recorded course mentions additional component scopes. Match the plan to the actual tool and enabled components rather than assuming every optional database exists.
```

```quiz score
Q: 7.012. Why give automated backup files distinguishable names or timestamps?
* To identify backup runs and avoid unintentionally replacing the only recovery copy
- To make the backup include every guest disk automatically
- To grant the backup file a VM administrator role
- To eliminate the need for restore testing
> Lab 7 Task 7 uses a dated backup workflow. Naming helps identify recovery points, while retention and independent storage remain separate planning concerns.
```

```quiz score
Q: 7.013. A scheduled backup script ran, but no valid artifact can be found. What evidence should be reviewed?
- Only the engine's portal theme
* The operation's exit status, log, and expected output location
- Only the VM template description
- Only the user's quiz badge
> Lab 7 Task 7 verifies the backup result. A schedule entry or script invocation alone does not establish a usable recovery artifact.
```

## Task 2: Restore a Manager Backup

```quiz score
Q: 7.014. To restore a backup, what mode must be specified?
- --mode=import
- --mode=recover
* --mode=restore
- --mode=recovery
> Restores require `--mode=restore`. This tells `engine-backup` to read an existing backup archive and restore its contents.
```

```quiz score
Q: 7.015. Before restoring a backup, what should be done to the engine service?
* Stop the engine service
- Enable debug mode
- Nothing required
- Restart the engine service
> The engine service should be stopped before restoring a backup. Restoring while the engine is active could conflict with running services and database access.
```

```quiz score
Q: 7.016. After restoring a backup, what command must be run to reconfigure the engine?
- ovirt-setup
- engine-restore
- engine-config
* engine-setup
> After restore, `engine-setup` must be run to reconfigure and finalize the restored engine. Restore puts data and files back; setup makes the engine operational with that restored state.
```

```quiz score
Q: 7.017. Before a restore that reconnects to management databases, what information should be prepared?
- Only the browser's default font
- Only a list of storage VLAN colors
* Required database connection details and authorized credentials or provisioning choices
- Only the last guest's desktop screenshot
> The restore course discusses database access. Prepare and validate the recovery inputs rather than changing database passwords blindly.
```

```quiz score
Q: 7.018. Why should recovery-related database credential changes be coordinated with engine configuration?
- Engine authentication requires every guest to use the same database password
- Changing any PostgreSQL password automatically updates every engine configuration file
- The VM's MAC address determines database authentication
* The restored services must use connection details that match the database
> The recovery lesson reconnects services to databases. Mismatched credentials can prevent restored services from starting successfully.
```

```quiz score
Q: 7.019. Which distinction matters when planning a Data Warehouse migration?
- DWH migration always renames all KVM hosts
- DWH migration always moves every guest disk
- The history database can exist only in the browser
* Moving only the DWH service differs from moving both its service and database
> The recovery course presents service-only and service-plus-database choices. Plan dependencies and connectivity for the selected arrangement.
```

```quiz score
Q: 7.020. What should be verified when choosing an OS for a new DWH server?
- That it contains every guest application's database
- That the OS hostname equals the engine database password
* Compatibility with the intended engine and DWH release baseline
- That any OS accepts the same packages and module commands
> The recorded DWH migration uses an Oracle Linux 8 context. Treat that as the training baseline and verify the intended environment's compatible stack.
```

```quiz score
Q: 7.021. What command enables a module in RHEL/Oracle Linux 8?
- yum module enable
- module enable
* dnf module enable
- enable-module
> `dnf module enable` enables module streams in Oracle Linux 8. OL8 uses DNF module streams for versioned application stacks such as PostgreSQL and Node.js.
```

```quiz score
Q: 7.022. Why align installed packages before restoring DWH onto another server?
- To make all backups independent of software versions
* To prepare a coherent compatible software stack for the restored service and database
- To replace the engine's PKI with a VLAN
- To grant every user database write permissions
> The recorded preparation includes repository and package alignment. Compatibility is the purpose; exact commands depend on the release.
```

```quiz score
Q: 7.023. What must be backed up before migrating DWH?
- Virtual machine data
- Only the database
* DWH database and configuration files
- Only configuration files
> Before DWH migration, both the DWH database and configuration files must be backed up. The database contains historical data, while files preserve service configuration.
```

```quiz score
Q: 7.024. In the recorded course, what package must be installed on the new DWH machine?
- ovirt-engine
- postgresql-backup
* ovirt-engine-tools-backup
- dwh-tools
> `ovirt-engine-tools-backup` must be installed on the new DWH machine so backup and restore tooling is available there. The new server needs the tools to process the DWH restore. These examples describe the recorded training baseline; verify applicable capabilities in the practice environment.
```

```quiz score
Q: 7.025. Why prepare PostgreSQL server components on a destination hosting the restored DWH database?
- PostgreSQL packages replace host NIC drivers
- Installing them grants all portal permissions
- The server components automatically restore all guest disks
* The destination must provide the database services and supporting components required by the recovery plan
> The migration lesson prepares database software before restore. Installing packages alone does not complete database recovery or service configuration.
```

```quiz score
Q: 7.026. Before restoring a DWH database onto a destination, what must be ready?
- Only the packages are downloaded, with no database service available
- Only the browser's certificate store is cleared
* The compatible database service and required access are prepared according to the recovery procedure
- The destination has no knowledge of any database connection details
> The DWH migration lesson prepares PostgreSQL before restore. Package installation alone does not provide a usable restored database service.
```

```quiz score
Q: 7.027. When restoring DWH and an associated enabled Grafana database, how should scopes be selected?
* Include the applicable configuration and database scopes supported by the destination's recovery plan
- Select db alone and assume it contains every optional component
- Use a storage VLAN number as the scope value
- Select all guest disk images through dwhdb
> The recorded migration includes configuration, DWH, and Grafana scopes. Include the actual components being moved and verify supported tool options.
```

```quiz score
Q: 7.028. Which tool is demonstrated for removing engine configuration before the recovery exercise?
* engine-cleanup
- The VM console viewer
- A Grafana dashboard refresh
- engine-config as a full VM disk backup
> The recorded recovery exercise uses engine-cleanup before restore. This is a destructive management configuration operation and belongs only in an appropriate recovery exercise.
```

```quiz score
Q: 7.029. What important effect does the recorded engine-cleanup exercise demonstrate?
* It stops engine services and removes engine configuration or data selected by the cleanup workflow
- It renames the engine without changing configuration
- It produces a verified independent copy of every guest disk
- It proves every installed RPM has been removed from the OS
> The demonstration clears the engine's configured state for recovery. Do not confuse configuration cleanup with package uninstallation or workload backup.
```

```quiz score
Q: 7.030. After running engine-cleanup, what happens to the Administration Portal?
- Shows error page
* Site cannot be reached
- Redirects to new URL
- Still accessible
> After `engine-cleanup`, the Administration Portal cannot be reached because the engine service and web application components have been removed or stopped.
```

## Task 3: Rename the Engine

```quiz score
Q: 7.031. What tool is used to rename the OLVM engine?
- ovirt-rename
- engine-rename
* ovirt-engine-rename
- manager-rename
> `ovirt-engine-rename` is the tool used to rename the OLVM engine. It updates engine configuration for a new hostname.
```

```quiz score
Q: 7.032. Where is the ovirt-engine-rename command located?
* /usr/share/ovirt-engine/setup/bin
- /usr/bin
- /etc/ovirt-engine
- /usr/sbin
> `ovirt-engine-rename` is located under `/usr/share/ovirt-engine/setup/bin`. This setup tool location is where engine maintenance utilities are stored.
```

```quiz score
Q: 7.033. What must be updated before renaming the engine? Select the option listing the complete correct set.
- IP addresses; DNS PTR records
- MAC addresses; DNS PTR records
- DNS A records; MAC addresses
* DNS A records; DNS PTR records
> DNS A and PTR records must be updated before renaming the engine. Forward and reverse DNS must resolve the new engine FQDN consistently.
```

```quiz score
Q: 7.034. If DHCP is used, what must be updated?
- Static IP addresses
- Nothing
* DHCP server configuration to reflect new hostname
- DNS only
> If DHCP is used, the DHCP server configuration must reflect the new hostname. Otherwise the engine may receive network identity information that conflicts with the renamed host.
```

```quiz score
Q: 7.035. What must be updated on the engine server itself?
- IP address
- Network interface
* Hostname to new FQDN
- Storage domain
> The engine server's hostname must be updated to the new FQDN. The operating system identity and engine configuration need to agree with DNS.
```

## Task 4: Explain Disaster Recovery Solutions

```quiz score
Q: 7.036. What configuration is used for active-active disaster recovery?
- Two separate environments
- Backup-only site
* Stretched cluster configuration
- Master-slave replication
> Active-active disaster recovery uses a stretched cluster configuration. This design allows both sites to participate actively in the same availability architecture.
```

```quiz score
Q: 7.037. In active-active DR, are both sites operational?
- Depends on load
- Only during failover
* Yes, both sites maintain active operations simultaneously
- No, one is standby
> In active-active DR, both sites maintain active operations simultaneously. Workloads are not limited to a standby site unless a failure scenario occurs.
```

```quiz score
Q: 7.038. Why do stretched-cluster DR plans need suitable inter-site network connectivity?
- DNS names automatically provide unlimited bandwidth
* Cluster and workload operations depend on the required connectivity, bandwidth, and latency
- A stretched cluster eliminates all network requirements
- The management browser carries every storage packet between sites
> The DR lesson discusses network requirements for stretched designs. A two-site label alone does not establish reliable failover.
```

```quiz score
Q: 7.039. What storage type is typically required for stretched clusters?
- Cloud storage
- Local storage only
- Tape storage
* Shared storage (SAN or NAS) accessible from both sites
> Stretched clusters typically require shared SAN or NAS storage accessible from both sites. Both sites must be able to reach the VM storage used by the cluster.
```

```quiz score
Q: 7.040. If an active site fails, what can happen to eligible HA workloads in a properly prepared stretched design?
- The surviving browser reconstructs missing VM disks automatically
- Every workload has guaranteed uninterrupted RAM state
* They may restart on surviving resources if required storage, capacity, and recovery conditions are met
- All VMs always live-migrate from hosts that have already lost power
> Site failure is different from planned migration. Recovery depends on the design and surviving resources, and cannot assume live migration from a failed source.
```

```quiz score
Q: 7.041. How is active-passive DR implemented?
- Single site with backups
* Two separate OLVM environments (primary and secondary sites)
- Cloud-only deployment
- Stretched cluster
> Active-passive DR uses two separate OLVM environments: a primary site and a secondary site. The secondary is prepared for recovery rather than actively running the same production workload set.
```

```quiz score
Q: 7.042. In active-passive DR, which site runs production workloads?
- Both sites
- Secondary site
- Alternates between sites
* Primary site
> In active-passive DR, the primary site runs production workloads. The secondary site remains available for failover or recovery.
```

```quiz score
Q: 7.043. What is the role of the secondary site in active-passive DR?
* Standby/backup location that replicates data from primary
- Development only
- Active workload processing
- Storage only
> The secondary site is a standby or backup location that receives replicated data from the primary. Its purpose is to take over when the primary site is unavailable.
```

```quiz score
Q: 7.044. What is the purpose of the secondary environment in an active-passive DR design?
- Guarantee zero recovery time without testing
- Remove the need to replicate critical data
- Run every production workload at both sites by definition
* Provide prepared recovery resources when the primary cannot serve the workloads
> The secondary site is prepared for recovery. Its cost, capacity, and recovery timing depend on the chosen design.
```

```quiz score
Q: 7.045. What must be replicated from primary to secondary site?
* Critical data, OLVM configurations, and VM images
- User accounts only
- Only databases
- Only configuration files
> Critical data, OLVM configurations, and VM images must be replicated to the secondary site. Without those pieces, the secondary cannot accurately recreate or run the production environment.
```

## Acknowledgements

- **Author** - OLVM training team
- **Sources** - User-supplied training transcripts, section quiz-bank files, and Lab 7 OLVM content
- **Last Updated** - October 2026
