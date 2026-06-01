# Section 11: Recovery Management
## Introduction

This lab contains the Section 11 practice questions for **Recovery Management** from the OLVM Exam 1Z0-1170 quiz bank.

Estimated Time: 60 minutes

```quiz-config
passing: 75
badge: images/badge.png
```

### Objectives

In this lab, you will review the OLVM concepts covered by this exam objective and check your understanding with practice questions.

## Practice Questions

```quiz

Q: 1. What tool is used to backup and restore OLVM?
- A. olvm-backup
* B. engine-backup
- C. ovirt-backup
- D. manager-backup
> `engine-backup` is the OLVM tool used for backing up and restoring the engine. It is designed specifically for OLVM engine databases and configuration files.

Q: 2. What does the engine-backup tool backup? (Choose 2)
* A. Engine database
- B. Virtual machine disk images
* C. Configuration files
- D. User home directories
> `engine-backup` backs up the engine database and configuration files. It does not back up VM disk images, so VM data protection must be handled with separate storage or backup processes.

Q: 3. Can the engine-backup tool be used while the engine is active?
- A. No, engine must be stopped
* B. Yes, backups can be performed while engine is active
- C. Only in maintenance mode
- D. Only during off-peak hours
> Backups can be performed while the engine is active. This makes it possible to take management-plane backups without a full OLVM outage.

Q: 4. What user must you be logged in as to run engine-backup?
- A. admin
- B. postgres
* C. root
- D. ovirt
> `engine-backup` must be run as root because it needs access to engine configuration files, database backup operations, and protected system paths.

Q: 5. What parameter specifies whether to perform backup or restore?
- A. --action
* B. --mode
- C. --operation
- D. --task
> `--mode` specifies whether the tool performs a backup or restore. This single option controls the major operation type.

Q: 6. What is the default scope when running engine-backup without parameters?
- A. files
- B. db
* C. all
- D. config
> The default scope is `all` when no scope is specified. That means the tool performs a full backup of supported databases and files by default.

Q: 7. What does scope=all backup?
- A. Database only
- B. Files only
* C. Full backup of all databases and configuration files
- D. Configuration files only
> `scope=all` backs up all supported databases and configuration files. It is the most complete engine backup scope.

Q: 8. What does scope=files backup?
- A. All files and databases
* B. Only the files on the system
- C. Only database files
- D. Only log files
> `scope=files` backs up only system configuration files. It does not include the engine database contents.

Q: 9. What does scope=db backup?
- A. All databases
* B. Only the engine database (active database)
- C. Only data warehouse database
- D. Configuration files
> `scope=db` backs up only the active engine database. This scope is useful when the engine operational database is the target of the backup.

Q: 10. What does scope=dwhdb backup?
- A. Engine database
* B. Only the data warehouse database (historical data)
- C. All databases
- D. Configuration files
> `scope=dwhdb` backs up only the Data Warehouse database, which contains historical data. It is separate from the active engine database.

Q: 11. Which other database scopes are available? (Choose 2)
* A. cinderlib_db
- B. user_db
* C. grafana_db
- D. admin_db
> `cinderlib_db` and `grafana_db` are additional database scopes. They cover optional or related components outside the core engine database.

Q: 12. What parameter specifies the path and name of the backup file?
- A. --backup
* B. --file
- C. --output
- D. --archive
> `--file` specifies the path and filename for the backup archive. This lets administrators place the backup in a controlled location.

Q: 13. What parameter specifies where to write backup/restore operation logs?
- A. --logfile
* B. --log
- C. --output-log
- D. --recording
> `--log` specifies where backup or restore operation logs should be written. Keeping a log is important for audit and troubleshooting.

Q: 14. What is the default file location for engine-backup?
- A. /root
- B. /tmp
* C. /var/lib/ovirt-engine-backup
- D. /home/admin
> The default backup file location is `/var/lib/ovirt-engine-backup`. This directory is the standard location used by the engine backup tooling.

Q: 15. What is the default log file location?
- A. /var/log/ovirt-engine
- B. /tmp/engine-backup.log
* C. /var/log/engine-backup.log
- D. /root/backup.log
> The default log file is `/var/log/engine-backup.log`. It records backup and restore activity for later review.

Q: 16. To restore a backup, what mode must be specified?
- A. --mode=recover
* B. --mode=restore
- C. --mode=import
- D. --mode=recovery
> Restores require `--mode=restore`. This tells `engine-backup` to read an existing backup archive and restore its contents.

Q: 17. Before restoring a backup, what should be done to the engine service?
- A. Nothing required
* B. Stop the engine service
- C. Restart the engine service
- D. Enable debug mode
> The engine service should be stopped before restoring a backup. Restoring while the engine is active could conflict with running services and database access.

Q: 18. After restoring a backup, what command must be run to reconfigure the engine?
- A. engine-config
* B. engine-setup
- C. ovirt-setup
- D. engine-restore
> After restore, `engine-setup` must be run to reconfigure and finalize the restored engine. Restore puts data and files back; setup makes the engine operational with that restored state.

Q: 19. If database credentials are not known during restore, what must you do?
- A. Reinstall the engine
* B. Change the password for the engine database owner using PostgreSQL
- C. Use default credentials
- D. Skip database restore
> If database credentials are not known during restore, the engine database owner's password must be changed using PostgreSQL. The restore process needs valid database access.

Q: 20. What PostgreSQL command changes the database owner password?
- A. UPDATE USER SET PASSWORD
* B. ALTER USER username ENCRYPTED PASSWORD 'newpassword'
- C. CHANGE PASSWORD FOR username
- D. SET PASSWORD username = 'newpassword'
> `ALTER USER username ENCRYPTED PASSWORD 'newpassword'` changes the PostgreSQL user's password. This is the PostgreSQL command pattern used to reset database-owner credentials.

Q: 21. What are two methods to migrate the data warehouse? (Choose 2)
* A. Migrate DWH service only, keep database on engine
- B. Delete and recreate
* C. Migrate both DWH database and service to new machine
- D. Export and import
> DWH can be migrated by moving only the DWH service while keeping the database on the engine, or by moving both the DWH database and service to a new machine. These options allow different separation and scaling designs.

Q: 22. What Oracle Linux version must the new DWH server have?
- A. Oracle Linux 7
* B. Oracle Linux 8
- C. Oracle Linux 9
- D. Any version
> The new DWH server must run Oracle Linux 8 for this migration path. Matching the supported OS version helps ensure package and module compatibility.

Q: 23. Which modules must be enabled on the new DWH server? (Choose 4)
* A. javapackages-tools
* B. postgresql:13 or postgresql:12
* C. mod_auth_openidc:2.3
* D. nodejs:14
- E. python:3.8
> The new DWH server needs modules such as javapackages-tools, postgresql, mod_auth_openidc, and nodejs. These provide runtime, database, authentication, and web component dependencies.

Q: 24. What command enables a module in RHEL/Oracle Linux 8?
- A. yum module enable
* B. dnf module enable
- C. module enable
- D. enable-module
> `dnf module enable` enables module streams in Oracle Linux 8. OL8 uses DNF module streams for versioned application stacks such as PostgreSQL and Node.js.

Q: 25. What command synchronizes packages to latest versions?
- A. dnf update
- B. dnf upgrade
* C. dnf distro-sync
- D. dnf sync
> `dnf distro-sync` synchronizes installed packages to the versions available from enabled repositories. This helps align the system after module or repository changes.

Q: 26. What must be backed up before migrating DWH?
- A. Only the database
* B. DWH database and configuration files
- C. Only configuration files
- D. Virtual machine data
> Before DWH migration, both the DWH database and configuration files must be backed up. The database contains historical data, while files preserve service configuration.

Q: 27. What package must be installed on the new DWH machine?
- A. ovirt-engine
* B. ovirt-engine-tools-backup
- C. postgresql-backup
- D. dwh-tools
> `ovirt-engine-tools-backup` must be installed on the new DWH machine so backup and restore tooling is available there. The new server needs the tools to process the DWH restore.

Q: 28. Which PostgreSQL packages must be installed? (Choose 2)
* A. postgresql-server
- B. postgresql-client
* C. postgresql-contrib
- D. postgresql-backup
> `postgresql-server` and `postgresql-contrib` are required PostgreSQL packages. They provide the database server and additional contributed functionality used by the environment.

Q: 29. After installing PostgreSQL, what must be done before restoring?
- A. Nothing
* B. Enable and start PostgreSQL service
- C. Configure firewall only
- D. Create database manually
> PostgreSQL must be enabled and started before restoring because the restore operation needs an active database service. Installing packages alone is not enough.

Q: 30. What scope is used to restore DWH to the new machine?
- A. --scope=all
- B. --scope=dwhdb
* C. --scope=files,grafana_db,dwhdb
- D. --scope=database
> The DWH restore scope is `files,grafana_db,dwhdb`. That restores the DWH-related files, Grafana database, and Data Warehouse database needed on the new machine.

Q: 31. What tool is used to rename the OLVM engine?
- A. engine-rename
- B. ovirt-rename
* C. ovirt-engine-rename
- D. manager-rename
> `ovirt-engine-rename` is the tool used to rename the OLVM engine. It updates engine configuration for a new hostname.

Q: 32. Where is the ovirt-engine-rename command located?
- A. /usr/bin
- B. /usr/sbin
* C. /usr/share/ovirt-engine/setup/bin
- D. /etc/ovirt-engine
> `ovirt-engine-rename` is located under `/usr/share/ovirt-engine/setup/bin`. This setup tool location is where engine maintenance utilities are stored.

Q: 33. What must be updated before renaming the engine? (Choose 2)
* A. DNS A records
- B. MAC addresses
* C. DNS PTR records
- D. IP addresses
> DNS A and PTR records must be updated before renaming the engine. Forward and reverse DNS must resolve the new engine FQDN consistently.

Q: 34. If DHCP is used, what must be updated?
- A. Nothing
* B. DHCP server configuration to reflect new hostname
- C. Static IP addresses
- D. DNS only
> If DHCP is used, the DHCP server configuration must reflect the new hostname. Otherwise the engine may receive network identity information that conflicts with the renamed host.

Q: 35. What must be updated on the engine server itself?
- A. IP address
* B. Hostname to new FQDN
- C. Network interface
- D. Storage domain
> The engine server's hostname must be updated to the new FQDN. The operating system identity and engine configuration need to agree with DNS.

Q: 36. What configuration is used for active-active disaster recovery?
- A. Two separate environments
* B. Stretched cluster configuration
- C. Master-slave replication
- D. Backup-only site
> Active-active disaster recovery uses a stretched cluster configuration. This design allows both sites to participate actively in the same availability architecture.

Q: 37. In active-active DR, are both sites operational?
- A. No, one is standby
* B. Yes, both sites maintain active operations simultaneously
- C. Only during failover
- D. Depends on load
> In active-active DR, both sites maintain active operations simultaneously. Workloads are not limited to a standby site unless a failure scenario occurs.

Q: 38. What type of network is required for stretched clusters?
- A. Layer 3 only
* B. Layer 2 network with sufficient bandwidth and low latency
- C. VPN only
- D. Satellite links
> Stretched clusters require a Layer 2 network with enough bandwidth and low latency. The sites must behave like one extended cluster network for migration and availability behavior.

Q: 39. What storage type is typically required for stretched clusters?
- A. Local storage only
* B. Shared storage (SAN or NAS) accessible from both sites
- C. Cloud storage
- D. Tape storage
> Stretched clusters typically require shared SAN or NAS storage accessible from both sites. Both sites must be able to reach the VM storage used by the cluster.

Q: 40. What happens if one site fails in active-active DR?
- A. All VMs stop
* B. Cluster migrates all VMs to the other site
- C. Manual intervention required
- D. Data is lost
> If one site fails in active-active DR, the cluster migrates VMs to the other site. The design goal is continued workload availability across site failure.

Q: 41. How is active-passive DR implemented?
- A. Stretched cluster
* B. Two separate OLVM environments (primary and secondary sites)
- C. Single site with backups
- D. Cloud-only deployment
> Active-passive DR uses two separate OLVM environments: a primary site and a secondary site. The secondary is prepared for recovery rather than actively running the same production workload set.

Q: 42. In active-passive DR, which site runs production workloads?
- A. Both sites
* B. Primary site
- C. Secondary site
- D. Alternates between sites
> In active-passive DR, the primary site runs production workloads. The secondary site remains available for failover or recovery.

Q: 43. What is the role of the secondary site in active-passive DR?
- A. Active workload processing
* B. Standby/backup location that replicates data from primary
- C. Development only
- D. Storage only
> The secondary site is a standby or backup location that receives replicated data from the primary. Its purpose is to take over when the primary site is unavailable.

Q: 44. What is a benefit of active-passive DR?
- A. Higher performance
* B. Cost-effective, utilizing standby resources only when needed
- C. Faster than active-active
- D. No replication needed
> Active-passive DR can be cost-effective because standby resources are used mainly when needed. It typically costs less than keeping both sites fully active at all times.

Q: 45. What must be replicated from primary to secondary site?
- A. Only databases
* B. Critical data, OLVM configurations, and VM images
- C. Only configuration files
- D. User accounts only
> Critical data, OLVM configurations, and VM images must be replicated to the secondary site. Without those pieces, the secondary cannot accurately recreate or run the production environment.

Q: 46. What command uninstalls the OLVM engine?
- A. engine-remove
* B. engine-cleanup
- C. engine-uninstall
- D. ovirt-remove
> `engine-cleanup` uninstalls the OLVM engine. It is the cleanup tool used to remove engine components from the system.

Q: 47. What happens when you run engine-cleanup?
- A. Only stops the service
* B. Stops engine service and removes all installed ovirt components
- C. Backs up data
- D. Restarts the engine
> `engine-cleanup` stops the engine service and removes installed oVirt components. It is a destructive cleanup operation and should only be run intentionally.

Q: 48. After running engine-cleanup, what happens to the Administration Portal?
- A. Still accessible
* B. Site cannot be reached
- C. Shows error page
- D. Redirects to new URL
> After `engine-cleanup`, the Administration Portal cannot be reached because the engine service and web application components have been removed or stopped.

Q: 49. What parameter restores file permissions during restore?
- A. --restore-perms
* B. --restore-permissions
- C. --fix-permissions
- D. --permissions
> `--restore-permissions` restores file permissions during restore. Correct permissions are required for engine services to read configuration and run normally.

Q: 50. After restoring from backup, what appears in the Administration Portal?
- A. Empty environment
* B. All previous data, VMs, and configurations are restored
- C. Only database is restored
- D. Only configuration files
> After a successful restore, the Administration Portal shows the previous OLVM data, VMs, and configurations. The goal of the restore is to recover the management environment state from the backup.

```

## Acknowledgements

- **Source** - OLVM_Practice_Tests_Combined.pdf
- **Generated From** - Oracle Linux Virtualization Manager Exam 1Z0-1170 practice test collection
