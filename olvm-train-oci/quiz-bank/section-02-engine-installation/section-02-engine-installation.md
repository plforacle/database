# Section 2: Engine Installation
## Introduction

This lab contains the Section 2 practice questions for **Engine Installation** from the OLVM Exam 1Z0-1170 quiz bank.

Estimated Time: 60 minutes

```quiz-config
passing: 75
badge: images/badge.png
```

### Objectives

In this lab, you will review the OLVM concepts covered by this exam objective and check your understanding with practice questions.

## Practice Questions

```quiz

Q: 1. What is the minimum Oracle Linux version required for installing the OLVM Manager (Engine)?
- A. Oracle Linux 7.5
* B. Oracle Linux 8.5
- C. Oracle Linux 9.0
- D. Oracle Linux 6.8
> OLVM Manager installation for this exam objective requires Oracle Linux 8.5 or later in the Oracle Linux 8 family. Older Oracle Linux 7 releases do not match the required engine platform for this OLVM installation path.

Q: 2. Which two CPU technologies must be supported for the Engine host processor? (Choose 2)
* A. Intel VT-x
- B. Intel SGX
* C. AMD-V
- D. ARM TrustZone
> The engine host processor must support hardware virtualization through Intel VT-x or AMD-V. These extensions provide the CPU virtualization capability needed by KVM-based virtualization; SGX and TrustZone are unrelated security technologies in this context.

Q: 3. What type of CPU architecture is required for the Engine host?
- A. 32-bit x86
* B. 64-bit x86
- C. ARM64
- D. PowerPC
> The OLVM Engine host requires a 64-bit x86 architecture. The installation stack and supported Oracle Linux platform for this release are not based on 32-bit x86, ARM64, or PowerPC.

Q: 4. Which six repositories must be enabled on the Oracle Linux system for OLVM Engine? (Choose 6)
* A. BaseOS Latest
* B. AppStream
* C. KVM AppStream
* D. oVirt 44
* E. oVirt 44 Extras
* F. Gluster AppStream
> The OLVM Engine needs the base Oracle Linux operating system repositories plus the virtualization and related application streams used by oVirt, KVM, and Gluster components. Missing one of these channels can leave required packages or dependencies unavailable during installation.

Q: 5. Which additional repository channel is required specifically for configuring VDSM on the host?
- A. UEKR6
* B. UEKR7
- C. KVM Utils
- D. Gluster 9
> UEKR7 is required for the host-side virtualization stack referenced in this setup. VDSM and the KVM host components rely on the supported Oracle Linux kernel channel rather than older UEK releases.

Q: 6. What are the MINIMUM hardware requirements for a SMALL deployment Engine? (Choose 3)
* A. 2 core CPUs
* B. 4 GB RAM
* C. 25 GB local writable disk
- D. 8 GB RAM
- E. 50 GB local writable disk
> A small deployment can start with 2 CPU cores, 4 GB RAM, and 25 GB of writable local disk for the engine. These are minimum sizing values; production or heavier environments should use the recommended values instead.

Q: 7. What are the RECOMMENDED hardware requirements for a SMALL deployment? (Choose 3)
- A. 2 cores
* B. 4 cores
* C. 16 GB or greater RAM
- D. 32 GB RAM
* E. 50 GB or greater local writable disk
> For a small deployment, the recommended sizing gives the engine more headroom: 4 cores, at least 16 GB RAM, and at least 50 GB writable local disk. The recommended values reduce the risk of management services becoming constrained as hosts and VMs are added.

Q: 8. How many KVM hosts can a SMALL deployment support?
* A. 1 to 5 hosts
- B. 5 to 10 hosts
- C. 10 to 20 hosts
- D. 20 to 50 hosts
> A small deployment is intended for a limited OLVM environment, supporting roughly 1 to 5 KVM hosts. Larger host counts move the environment into medium or large sizing guidance.

Q: 9. How many virtual machines can a SMALL deployment support?
- A. Up to 20 VMs
* B. Up to 50 VMs
- C. Up to 100 VMs
- D. Up to 200 VMs
> The small deployment profile supports up to about 50 virtual machines. This sizing keeps the engine management workload aligned with the small CPU, memory, and disk recommendations.

Q: 10. What are the recommended hardware requirements for a MEDIUM deployment? (Choose 3)
- A. 4 core CPUs
* B. 8 core CPUs
* C. 32 GB or greater RAM
- D. 64 GB RAM
* E. 100 GB or greater local writable disk
> A medium deployment needs more resources for the larger inventory and management workload: 8 CPU cores, at least 32 GB RAM, and at least 100 GB writable local disk. These values provide capacity for more hosts, VMs, and historical data.

Q: 11. How many KVM hosts can a MEDIUM deployment support?
- A. 1 to 5 hosts
* B. 5 to 50 hosts
- C. 50 to 100 hosts
- D. 100 to 200 hosts
> The medium profile is sized for environments with about 5 to 50 KVM hosts. Below that range the small profile may be sufficient; above it, the large profile is more appropriate.

Q: 12. How many virtual machines can a MEDIUM deployment support?
- A. 50 to 100 VMs
* B. 50 to 500 VMs
- C. 500 to 1000 VMs
- D. 1000 to 2000 VMs
> A medium deployment is intended for roughly 50 to 500 VMs. The VM count is part of the sizing model because each VM increases management, event, monitoring, and database workload.

Q: 13. What are the recommended hardware requirements for a LARGE deployment? (Choose 3)
- A. 8 cores
* B. 16 cores or greater
* C. 64 GB or greater RAM
- D. 128 GB RAM
* E. 200 GB or greater writable disk space
> Large deployments require substantially more engine capacity: at least 16 cores, 64 GB RAM, and 200 GB writable disk. The higher sizing supports the larger number of hosts, VMs, events, and monitoring records.

Q: 14. How many KVM hosts can a LARGE deployment support?
- A. 10 to 50 hosts
* B. 50 to 200 hosts
- C. 200 to 500 hosts
- D. 500 to 1000 hosts
> A large deployment supports roughly 50 to 200 KVM hosts. This scale requires the larger engine sizing so management operations and data collection remain responsive.

Q: 15. How many virtual machines can a LARGE deployment support?
- A. 500 to 1000 VMs
* B. 500 to 2000 VMs
- C. 2000 to 5000 VMs
- D. 5000 to 10000 VMs
> The large deployment profile supports roughly 500 to 2000 VMs. This is a planning guideline that aligns VM scale with the recommended engine CPU, memory, and disk resources.

Q: 16. What is the default port number for SSH access to the Manager?
- A. 21
* B. 22
- C. 23
- D. 3389
> SSH uses TCP port 22 by default. In OLVM installation and administration, SSH is commonly needed for host access, initial setup, and troubleshooting.

Q: 17. Which two ports are used for web interface and REST API access? (Choose 2)
* A. 80 (TCP)
- B. 8080 (TCP)
* C. 443 (TCP)
- D. 8443 (TCP)
> The OLVM web interface and REST API use standard web ports: HTTP on 80 and HTTPS on 443. Ports 8080 and 8443 are common application-server ports, but they are not the expected public access ports in this question.

Q: 18. What is the default port number for PostgreSQL database communication?
- A. 3306
* B. 5432
- C. 5433
- D. 27017
> PostgreSQL uses TCP port 5432 by default. OLVM uses PostgreSQL for engine data, so this port matters when a remote database or database communication path is involved.

Q: 19. Which two ports are used for VDSM communication between the Engine and hosts? (Choose 2)
- A. 54320
* B. 54321
* C. 54322
- D. 54323
> VDSM communication uses ports 54321 and 54322 between the engine and hosts. These ports allow the engine to coordinate host-side virtualization operations through VDSM.

Q: 20. What is the port range used for SPICE protocol remote desktop connections?
- A. 3389 to 3400
- B. 5800 to 5900
* C. 5900 to 6923
- D. 8000 to 9000
> SPICE console connections use the 5900 to 6923 port range. This range supports remote graphical console access to virtual machines through the virtualization environment.

Q: 21. What port is used for iSCSI storage domain access?
- A. 111
- B. 2049
* C. 3260
- D. 32803
> iSCSI uses TCP port 3260 by default. When OLVM uses iSCSI storage domains, hosts must be able to reach the iSCSI target on this port.

Q: 22. Which port is used by the portmapper service for NFS?
* A. 111 (TCP and UDP)
- B. 2049 (TCP and UDP)
- C. 32803 (TCP)
- D. 3260 (TCP)
> The portmapper/rpcbind service uses port 111 for TCP and UDP. NFS environments often rely on RPC-related services, so firewall planning must account for portmapper along with NFS-specific ports.

Q: 23. Which two appstream modules must be enabled before installing the OLVM Engine? (Choose 2)
- A. php:7.4
* B. pki-deps
* C. postgresql:13
- D. nodejs:14
> The `pki-deps` and `postgresql:13` modules must be enabled because engine setup depends on certificate infrastructure and the supported PostgreSQL module stream. Enabling the right module streams prevents incompatible dependency versions.

Q: 24. What does PKI stand for in the context of OLVM?
- A. Package Key Infrastructure
* B. Public Key Infrastructure
- C. Private Key Interface
- D. Portal Key Integration
> PKI means Public Key Infrastructure. In OLVM, PKI is used for certificates and trust relationships, including secure browser access and encrypted component communication.

Q: 25. Which virt module should be DISABLED before installing OLVM Engine?
- A. virt:rhel
* B. virt:ol
- C. virt:kvm
- D. virt:8.5
> The `virt:ol` module should be disabled so it does not conflict with the virtualization module stream required by OLVM. Module stream selection matters because DNF can otherwise resolve packages from an incompatible stream.

Q: 26. Which virt module version should be ENABLED for OLVM Engine installation?
- A. virt:rhel
- B. virt:kvm_utils1
* C. virt:kvm_utils2
- D. virt:ol8
> The `virt:kvm_utils2` module is the virtualization module stream expected for this OLVM Engine installation path. Enabling it ensures the correct KVM utility packages and dependencies are available.

Q: 27. Which repository should be enabled for the latest base operating system packages?
- A. ol7_baseos_latest
* B. ol8_baseos_latest
- C. ol8_appstream
- D. ol8_UEKR7
> `ol8_baseos_latest` provides the latest base operating system packages for Oracle Linux 8. It is the correct base OS repository, while AppStream and UEK repositories serve different dependency areas.

Q: 28. What command is used to install the Oracle oVirt release package for Enterprise Linux 8?
- A. dnf install ovirt-release-el8
- B. dnf install oracle-ovirt-el8
* C. dnf install oracle-ovirt-release-el8
- D. dnf install ovirt-engine-release
> The `oracle-ovirt-release-el8` package configures the Oracle oVirt repositories for Enterprise Linux 8. Installing this release package is the normal way to make the OLVM/oVirt package channels available.

Q: 29. What command is used to install the OLVM Engine package?
- A. yum install olvm-engine
* B. dnf install ovirt-engine
- C. dnf install olvm-manager
- D. yum install oracle-engine
> The OLVM Engine package is installed with `dnf install ovirt-engine`. The package name follows the upstream oVirt naming used by the OLVM engine component.

Q: 30. What command is used to configure the OLVM Engine after package installation?
- A. ovirt-setup
* B. engine-setup
- C. olvm-configure
- D. manager-setup
> `engine-setup` is the configuration command that initializes and configures the OLVM Engine after package installation. Installing the RPMs alone does not create the working engine configuration.

Q: 31. What type of interface does the engine-setup command provide?
- A. Graphical user interface
- B. Web-based interface
* C. Interactive command-line with questions
- D. Silent installation with config file
> `engine-setup` is an interactive command-line installer that asks configuration questions. It is not a graphical or browser-based installer in this workflow.

Q: 32. What does the engine-setup command display after all questions are answered?
- A. Error log
* B. Summary of entered values
- C. Installation progress bar
- D. Database schema
> After collecting answers, `engine-setup` displays a summary so the administrator can review the selected values before applying configuration. This checkpoint helps catch hostname, database, or service choices before setup proceeds.

Q: 33. What information is displayed after the engine-setup configuration is complete?
- A. System resource usage
* B. Details about how to log in to the Administration Portal
- C. List of available VMs
- D. Network topology diagram
> When setup completes, it prints login information for the Administration Portal, including the URL and relevant access details. At that point the engine is ready for browser-based administration.

Q: 34. Which five configuration groupings are part of the engine-setup process? (Choose 5)
* A. Database configuration
- B. Hardware configuration
* C. Network configuration
* D. Administration user setup
* E. Certificates and security
* F. Service configurations
> The setup process covers database, network, administrative user, certificate/security, and service configuration. Hardware sizing is a prerequisite planning activity, not a configuration grouping inside `engine-setup`.

Q: 35. What can the database configuration in engine-setup allow you to configure? (Choose 2)
* A. A local database instance
* B. A remote database
- C. MySQL database
- D. Oracle Database
> `engine-setup` can configure the engine to use a local PostgreSQL database or connect to a remote PostgreSQL database. MySQL and Oracle Database are not the supported database choices for the OLVM engine in this workflow.

Q: 36. What is the default administrative username created during engine-setup?
- A. root
- B. administrator
* C. admin
- D. ovirt
> The default administrative username is `admin`. This account is created during setup so administrators can log in to the Administration Portal initially.

Q: 37. What is the name of the domain/profile for the default admin user?
- A. local
* B. internal
- C. default
- D. admin
> The default admin account belongs to the `internal` domain/profile. OLVM can integrate with external identity providers later, but the built-in setup account uses the internal domain.

Q: 38. If you enable automatic firewall configuration during engine-setup, what happens?
- A. All ports are opened
- B. The firewall is disabled
* C. Necessary ports for OLVM are automatically allowed
- D. Only SSH port is opened
> When automatic firewall configuration is enabled, setup opens the ports required by OLVM services. It does not indiscriminately open all ports or disable the firewall.

Q: 39. Where do you configure an alternate hostname for the OLVM Engine?
- A. /etc/hosts
- B. /etc/ovirt-engine/engine.conf
* C. /etc/ovirt-engine/engine.conf.d/custom-sso-setup.conf
- D. /var/lib/ovirt-engine/hostname.conf
> Alternate SSO hostname configuration is placed in `/etc/ovirt-engine/engine.conf.d/custom-sso-setup.conf`. The `.conf.d` location lets administrators add override configuration without editing the main engine configuration directly.

Q: 40. What command is used to restart the OLVM Engine service after configuring an alternate hostname?
- A. service ovirt-engine restart
* B. systemctl restart ovirt-engine
- C. engine-restart
- D. systemctl reload ovirt-engine
> After changing engine hostname-related configuration, the engine service must be restarted with `systemctl restart ovirt-engine` for the change to take effect. Reloading or using older service syntax is not the expected command here.

Q: 41. Which three benefits does configuring an alternate hostname provide? (Choose 3)
* A. Enhanced accessibility with multiple entry points
- B. Increased security
* C. Load balancing options
* D. Increased flexibility for network segmentation
- E. Faster VM performance
> Alternate hostnames can improve accessibility, enable load-balancing designs, and support network segmentation or multiple entry points. They do not inherently make VMs faster, and security still depends on the full certificate and access-control design.

Q: 42. Which three portals are available in the OLVM Engine web interface? (Choose 3)
* A. Administration Portal
- B. Developer Portal
* C. VM Portal
* D. Monitoring Portal (Grafana)
- E. Storage Portal
> The OLVM web interface exposes the Administration Portal, VM Portal, and Monitoring Portal. The Monitoring Portal is Grafana-based; developer and storage portals are not standard OLVM portal choices in this question.

Q: 43. What is the primary purpose of the Administration Portal?
- A. End users managing their VMs only
* B. Comprehensive management of all OLVM components by administrators
- C. Monitoring only
- D. Backup management
> The Administration Portal is the main administrator interface for managing OLVM infrastructure components. It is broader than end-user VM access and includes hosts, clusters, storage, networks, permissions, and VMs.

Q: 44. What is the Monitoring Portal in OLVM?
- A. A custom Oracle monitoring tool
* B. Grafana for monitoring and analytics
- C. A syslog server
- D. An SNMP trap receiver
> The Monitoring Portal uses Grafana for monitoring and analytics. Grafana visualizes OLVM metrics and trends; it is not a syslog server, SNMP receiver, or Oracle-only custom monitoring tool.

Q: 45. After installing the Engine CA certificate in the browser, what does it allow you to do?
- A. Access VMs directly
* B. Securely access the OLVM Administration Portal without certificate warnings
- C. Increase browser performance
- D. Enable two-factor authentication
> Installing the Engine CA certificate lets the browser trust the engine's certificate chain, reducing or eliminating certificate warnings when accessing the Administration Portal securely. It does not provide VM access by itself or enable two-factor authentication.

Q: 46. What command is used to enable a repository using dnf config-manager?
- A. dnf config-manager enable <repo-name>
* B. dnf config-manager --set-enabled <repo-name>
- C. dnf enable-repo <repo-name>
- D. dnf repository enable <repo-name>
> `dnf config-manager --set-enabled <repo-name>` enables a repository using DNF configuration tools. The exact option matters because DNF does not use the made-up commands shown in the distractors.

Q: 47. What command cleans up the dnf repository cache?
- A. dnf clear
- B. dnf clean
* C. dnf clean all
- D. dnf cache-clean
> `dnf clean all` clears cached repository metadata and packages. This is useful after repository changes so DNF refreshes metadata from the enabled repositories.

Q: 48. After installing the ovirt-engine package, what is the FIRST command you run to begin configuration?
- A. ovirt-config
* B. engine-setup
- C. systemctl start ovirt-engine
- D. engine-configure
> After installing `ovirt-engine`, the first configuration command is `engine-setup`. Starting the service before setup would not complete the required database, certificate, network, and portal configuration.

Q: 49. What file extension does the Engine CA certificate have when downloaded?
- A. .crt
- B. .pem
* C. .cer
- D. .p12
> The quiz source notes that the transcript did not explicitly state this, but the demo indicates the downloaded Engine CA certificate is likely a `.cer` file. The key point is recognizing the certificate file used by the browser trust workflow.

Q: 50. When installing the Engine CA certificate in a browser, which certificate store location is recommended?
- A. Current User
* B. Local Machine
- C. Trusted Root
- D. Personal
> The certificate should be installed for the Local Machine store in this workflow so the browser and system trust the Engine CA at the machine level. The answer is not about the certificate's logical trust category alone; it is the store location selected during installation.

```

## Acknowledgements

- **Source** - OLVM_Practice_Tests_Combined.pdf
- **Generated From** - Oracle Linux Virtualization Manager Exam 1Z0-1170 practice test collection
