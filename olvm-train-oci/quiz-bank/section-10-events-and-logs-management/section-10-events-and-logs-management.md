# Section 10: Events & Logs Management
## Introduction

This lab contains the Section 10 practice questions for **Events & Logs Management** from the OLVM Exam 1Z0-1170 quiz bank.

Estimated Time: 70 minutes

```quiz-config
passing: 75
badge: images/badge.png
```

### Objectives

In this lab, you will review the OLVM concepts covered by this exam objective and check your understanding with practice questions.

## Practice Questions

```quiz

Q: 1. What are the three main categories of log files in OLVM? (Choose 3)
* A. Engine installation log files
- B. Network log files
* C. oVirt engine log files
* D. Host log files
- E. User activity log files
> OLVM log files are commonly grouped into engine installation logs, oVirt engine logs, and host logs. These categories separate setup activity, ongoing engine operation, and host-side virtualization behavior.

Q: 2. Where are engine installation log files located?
- A. /var/log/ovirt
* B. /var/log/ovirt-engine
- C. /var/log/engine
- D. /etc/ovirt-engine/logs
> Engine installation logs are located under `/var/log/ovirt-engine`. This is where setup and engine-related logging is collected on the manager system.

Q: 3. Which two key log files are in the engine installation logs? (Choose 2)
* A. engine-setup.log
- B. engine-install.log
* C. engine-setup-summary.log
- D. installation.log
> `engine-setup.log` and `engine-setup-summary.log` are key setup logs. The first captures detailed setup activity, while the summary records the resulting setup choices and output.

Q: 4. What does the engine-setup.log file contain?
- A. Summary only
* B. Detailed information about each step and action during setup or upgrade
- C. Error messages only
- D. Network configurations
> `engine-setup.log` contains detailed information about each setup or upgrade step. It is the right log to inspect when an install or upgrade fails or behaves unexpectedly.

Q: 5. Where are oVirt engine log files located?
- A. /var/log/engine
* B. /var/log/ovirt-engine
- C. /var/log/olvm
- D. /etc/ovirt/logs
> oVirt engine logs are also located under `/var/log/ovirt-engine`. This directory contains both installation-related and runtime engine logs.

Q: 6. Which four key log files are part of oVirt engine logs? (Choose 4)
* A. engine.log
* B. server.log
* C. audit.log
* D. boot.log
- E. network.log
- F. storage.log
> `engine.log`, `server.log`, `audit.log`, and `boot.log` are key oVirt engine logs. They cover general operations, server internals, audit/security events, and engine boot activity.

Q: 7. What does the engine.log file record?
- A. User authentication only
* B. General operation information about oVirt engine (starting/stopping services, API requests)
- C. Boot process only
- D. Audit trails only
> `engine.log` records general oVirt engine activity such as service start/stop events and API requests. It is often the first engine-side log to inspect for operational problems.

Q: 8. What does the server.log file contain?
- A. Audit information
* B. Detailed information about server-side processes
- C. Boot messages only
- D. User activities
> `server.log` contains more detailed information about server-side processes. It helps diagnose problems in the application server and backend services.

Q: 9. What does the audit.log file contain?
- A. General operations
* B. Security events and audit trails (user actions, authentication, permission changes)
- C. Boot process
- D. Network events
> `audit.log` records security and audit events such as user actions, authentication events, and permission changes. It is important for accountability and access troubleshooting.

Q: 10. What does the boot.log file record?
- A. User logins
* B. The booting process of oVirt engine
- C. Shutdown events
- D. API calls
> `boot.log` records the oVirt engine boot process. It is useful when the engine service fails to start cleanly or has initialization problems.

Q: 11. Where is the libvirt log file located?
- A. /var/log/libvirt
* B. /var/log/messages
- C. /var/log/vdsm
- D. /var/log/hosts
> The libvirt log information referenced in this quiz is found in `/var/log/messages`. libvirt-related host events can appear in system logging rather than a dedicated OLVM engine directory.

Q: 12. What does the SPM lock.log file detail?
- A. User logins
* B. Host ability to obtain a lease on storage pool management
- C. Network traffic
- D. VM migrations
> `spm-lock.log` details whether a host can obtain the lease for storage pool management. This is useful when troubleshooting SPM election or storage metadata coordination.

Q: 13. Where is the SPM lock.log file located?
- A. /var/log/spm
* B. /var/log/vdsm/spm-lock.log
- C. /var/log/ovirt
- D. /var/log/storage
> `spm-lock.log` is located at `/var/log/vdsm/spm-lock.log`. It is a host-side VDSM log focused on SPM lease behavior.

Q: 14. Where is the VDSM service log file located?
* A. /var/log/vdsm/vdsm.log
- B. /var/log/vdsm.log
- C. /var/log/messages
- D. /var/log/ovirt/vdsm
> The VDSM service log is `/var/log/vdsm/vdsm.log`. This is the main host-side log for VM, migration, storage, and host-management operations.

Q: 15. Where is the host deployment log file located?
- A. /var/log/deploy
* B. /tmp/ovirt-host-deploy-<date>
- C. /var/log/ovirt-deploy
- D. /var/log/vdsm/deploy
> Host deployment logs are written under `/tmp/ovirt-host-deploy-<date>`. These logs capture the host registration and deployment process.

Q: 16. Where are VM import log files located?
- A. /var/log/imports
* B. /var/log/vdsm/import/import-<UUID>-<date>
- C. /var/log/vm-import
- D. /tmp/imports
> VM import logs are located under `/var/log/vdsm/import/import-<UUID>-<date>`. The UUID and date identify a specific import operation.

Q: 17. What does the supervdsm.log file record?
- A. All VDSM activities
* B. VDSM tasks executed with superuser permissions
- C. VM migrations only
- D. Network configurations
> `supervdsm.log` records VDSM tasks that need superuser privileges. This helps distinguish privileged host operations from ordinary VDSM activity.

Q: 18. Where is the supervdsm.log file located?
- A. /var/log/supervdsm
* B. /var/log/vdsm/supervdsm.log
- C. /var/log/root
- D. /var/log/admin
> `supervdsm.log` is located at `/var/log/vdsm/supervdsm.log`. It is part of the host-side VDSM log set.

Q: 19. What does the mom.log file contain?
- A. Network bandwidth
* B. Memory overcommitment information and MoM (Memory Overcommitment Manager) activities
- C. User activities
- D. Storage usage
> `mom.log` contains Memory Overcommitment Manager activity and memory overcommit information. It is useful when diagnosing memory optimization or ballooning behavior.

Q: 20. Where is the upgrade.log file located?
- A. /var/log/upgrade
* B. /var/log/vdsm/upgrade.log
- C. /var/log/ovirt/upgrade
- D. /tmp/upgrade
> `upgrade.log` is located at `/var/log/vdsm/upgrade.log`. It records host-side upgrade activity related to VDSM or host components.

Q: 21. What notification methods does OLVM support? (Choose 2)
* A. Email notifications
- B. SMS notifications
* C. SNMP traps
- D. Slack integration
> OLVM supports email notifications and SNMP traps for events. These methods allow alerts to reach administrators and external monitoring platforms.

Q: 22. Where is the event notifier configuration file located?
- A. /etc/ovirt-engine/notifier.conf
- B. /etc/ovirt-engine-notifier/ovirt-engine-notifier.conf
* C. /etc/ovirt-engine/services/ovirt-engine-notifier/ovirt-engine-notifier.conf
- D. /var/lib/ovirt/notifier.conf
> The event notifier configuration file is `/etc/ovirt-engine/services/ovirt-engine-notifier/ovirt-engine-notifier.conf`. This file stores notifier settings such as mail server and trap configuration.

Q: 23. What parameter specifies the SMTP mail server address?
- A. smtp_server
* B. mail_server
- C. email_server
- D. notification_server
> `mail_server` specifies the SMTP server address used for email notifications. The notifier needs this value to know where to send mail.

Q: 24. Which three port numbers are commonly used for mail servers? (Choose 3)
* A. 25 (plain SMTP)
* B. 465 (SMTP with SSL)
* C. 587 (SMTP with TLS)
- D. 8080 (HTTP)
- E. 3306 (MySQL)
> Common mail ports are 25 for plain SMTP, 465 for SMTP over SSL, and 587 for SMTP with TLS/submission. The correct port depends on the organization's mail server configuration.

Q: 25. What parameter specifies the username for mail server authentication?
- A. smtp_user
* B. mail_user
- C. email_user
- D. auth_user
> `mail_user` specifies the username used to authenticate to the mail server. It is paired with the password when SMTP authentication is required.

Q: 26. What parameter contains the password for mail authentication?
- A. smtp_password
* B. mail_password
- C. email_password
- D. auth_password
> `mail_password` contains the password for mail server authentication. This credential allows the notifier to send through an authenticated SMTP service.

Q: 27. What parameter specifies the engine's process ID?
- A. engine_id
* B. engine_pid
- C. process_id
- D. ovirt_pid
> `engine_pid` specifies the engine process ID parameter in the notifier configuration context. It identifies the engine process used by the service.

Q: 28. Which two SNMP versions does oVirt support? (Choose 2)
- A. SNMP version 1
* B. SNMP version 2
* C. SNMP version 3
- D. SNMP version 4
> oVirt supports SNMP version 2 and version 3 in this objective. SNMPv3 adds stronger security options than SNMPv2.

Q: 29. What are the three SNMP security levels? (Choose 3)
* A. NoAuthNoPriv
- B. BasicAuth
* C. AuthNoPriv
* D. AuthPriv
- E. FullAuth
> The SNMP security levels are NoAuthNoPriv, AuthNoPriv, and AuthPriv. They represent no authentication/no privacy, authentication without encryption, and authentication with encryption.

Q: 30. What does NoAuthNoPriv security level provide?
- A. Full encryption
* B. SNMP traps sent without authentication or encryption (clear text)
- C. Password authentication only
- D. Encryption only
> NoAuthNoPriv sends SNMP traps without authentication or encryption. It is the least secure option because traffic is effectively clear text.

Q: 31. What does AuthNoPriv security level provide?
- A. No security
* B. Password authentication but no encryption (clear text transmission)
- C. Full encryption
- D. Authentication and encryption
> AuthNoPriv provides authentication but no encryption. The receiver can verify identity, but the transmitted data is not encrypted.

Q: 32. What does AuthPriv security level provide?
- A. No security
- B. Password authentication only
* C. Password authentication and encryption (highest security)
- D. Encryption only
> AuthPriv provides both authentication and encryption, making it the strongest SNMPv3 security level listed here. It protects both identity and privacy of trap data.

Q: 33. What information is required to configure SNMP managers? (Choose 3)
* A. IP address or FQDN of SNMP manager
- B. MAC address
* C. SNMP version supported
* D. Community string or SNMP v3 credentials
- E. Operating system version
> Configuring SNMP managers requires the manager IP or FQDN, supported SNMP version, and the community string or SNMPv3 credentials. These values tell OLVM where and how to send traps securely.

Q: 34. What is the oVirt log collector diagnostic tool used for?
- A. Creating new logs
* B. Collecting comprehensive logs from manager and attached hosts for troubleshooting
- C. Deleting old logs
- D. Encrypting logs
> The oVirt log collector gathers comprehensive logs from the manager and attached hosts for troubleshooting. It packages relevant diagnostic data so administrators or support can analyze issues more efficiently.

Q: 35. What package should be installed on manager and hosts for system diagnostics?
- A. syslog
* B. sos (sosreport)
- C. logrotate
- D. rsyslog
> The `sos` package provides `sosreport`, a system diagnostic collection tool. It is useful alongside OLVM-specific logs when investigating host or manager problems.

Q: 36. What command is used to install the ovirt-log-collector package?
- A. dnf install log-collector
* B. yum install ovirt-log-collector
- C. dnf install olvm-logs
- D. yum install log-diagnostic
> `yum install ovirt-log-collector` installs the oVirt log collector package. The quiz uses the package-management command and package name expected for this tool.

Q: 37. What does the ovirt-log-collector tool collect? (Choose 3)
* A. SOS report from engine
* B. Engine log files
- C. Browser history
* D. Engine PostgreSQL database logs
- E. User passwords
> The log collector gathers the engine SOS report, engine log files, and engine PostgreSQL database logs. It does not collect browser history or user passwords.

Q: 38. What parameter skips PostgreSQL database logs when collecting?
- A. --skip-database
* B. --no-postgresql
- C. --exclude-db
- D. --no-database
> `--no-postgresql` skips PostgreSQL database logs during log collection. This is useful when database logs are unnecessary or should be excluded from a support bundle.

Q: 39. What parameter skips host log collection?
- A. --skip-hosts
- B. --no-hosts
* C. --no-hypervisors
- D. --exclude-hosts
> `--no-hypervisors` skips host or hypervisor log collection. Use it when you only need manager-side logs or want to reduce collection scope.

Q: 40. What parameter collects logs per hypervisor per cluster?
- A. --cluster-mode
- B. --per-cluster
* C. --hypervisors-per-cluster
- D. --cluster-hypervisors
> `--hypervisors-per-cluster` collects logs per hypervisor per cluster. This helps structure collection across clustered hosts.

Q: 41. What are the two PostgreSQL databases in OLVM? (Choose 2)
* A. engine
- B. admin
* C. ovirt_engine_history
- D. manager
- E. olvm_data
> The two PostgreSQL databases are `engine` and `ovirt_engine_history`. The engine database stores current operational data; the history database stores DWH historical data.

Q: 42. What does the engine database contain?
- A. Historical data only
* B. Active operational data
- C. Backup data
- D. Archived logs
> The `engine` database contains active operational data for OLVM. It is the primary database used by the running engine.

Q: 43. What does the ovirt_engine_history database contain?
- A. Active data only
* B. Historical configuration information and statistical metrics collected over time
- C. User accounts
- D. Network configurations
> `ovirt_engine_history` stores historical configuration information and statistical metrics over time. This database supports reporting, monitoring, and Grafana/DWH views.

Q: 44. What command switches to the postgres user?
- A. su postgres
* B. su - postgres
- C. sudo postgres
- D. switch postgres
> `su - postgres` switches to the PostgreSQL operating system user with that user's login environment. The dash matters because it loads the target user's environment.

Q: 45. What command connects to the engine database?
- A. psql engine
* B. psql -d engine
- C. postgres -d engine
- D. connect engine
> `psql -d engine` connects to the `engine` database using the PostgreSQL command-line client. The `-d` option selects the database.

Q: 46. What PostgreSQL command lists all databases?
- A. show databases
- B. list databases
* C. \l
- D. \databases
> In PostgreSQL `psql`, `\l` lists databases. It is one of the built-in psql meta-commands.

Q: 47. What PostgreSQL command shows connection information?
- A. \info
- B. \connection
* C. \conninfo
- D. \conn
> `\conninfo` shows the current database connection information. It helps confirm which database and user session you are connected to.

Q: 48. What PostgreSQL command lists all tables?
- A. show tables
- B. \tables
* C. \dt
- D. list tables
> `\dt` lists tables in the current database schema. It is the psql meta-command for showing relation tables.

Q: 49. What PostgreSQL command describes a specific table structure?
- A. describe tablename
* B. \d tablename
- C. show tablename
- D. \desc tablename
> `\d tablename` describes the structure of a specific PostgreSQL table. It shows columns, types, indexes, and related table metadata.

Q: 50. What PostgreSQL command quits the database prompt?
- A. exit
- B. quit
* C. \q
- D. \quit
> `\q` quits the PostgreSQL `psql` prompt. It is the standard psql exit command.

Q: 51. What command shows the Grafana server version?
- A. grafana --version
* B. grafana-server -v
- C. grafana version
- D. grafana-server --version
> `grafana-server -v` shows the Grafana server version. This verifies the installed Grafana server binary and version.

Q: 52. What port does Grafana typically use?
- A. 8080
* B. 3000
- C. 5432
- D. 9090
> Grafana typically listens on port 3000. Administrators must account for this port when accessing Grafana dashboards or configuring proxies.

Q: 53. What configuration must be enabled to avoid "origin not allowed" errors in Grafana?
- A. cors_enabled
* B. proxy_preserve_host
- C. allow_origin
- D. enable_proxy
> `proxy_preserve_host` must be enabled to avoid origin-related Grafana errors in this context. Preserving the host header helps Grafana validate proxied requests correctly.

Q: 54. What database does Grafana connect to for OLVM monitoring?
- A. engine database
* B. ovirt_engine_history (DWH database)
- C. admin database
- D. grafana database
> Grafana connects to the `ovirt_engine_history` DWH database for OLVM monitoring. The history database contains the time-series metrics that dashboards visualize.

Q: 55. What permission level should be granted to the Grafana database user?
- A. Full admin access
* B. Read-only (SELECT) permissions on DWH database
- C. Write access
- D. Root access
> The Grafana database user should have read-only SELECT permissions on the DWH database. Grafana needs to query monitoring data, not administer or modify the OLVM databases.

```

## Acknowledgements

- **Author** - Perside Foster
- **Source Material** - OLVM bootcamp training and Oracle Linux Virtualization Manager documentation