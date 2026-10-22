# MySQL HA Part 1: Prepare the OCI Network

## Introduction

Prepare an Oracle Cloud Infrastructure (OCI) virtual cloud network (VCN) for a later MySQL deployment. Create public and private regional subnets, configure an inbound MySQL rule, and check the resulting network resources.

This workshop adapts the supplied 99-second recording, **MySQL_Part1_VCN.mp4**. The recording demonstrates network preparation only. Completing these labs produces a network foundation; it does not create a database or demonstrate high availability, switchover, or failover.

Estimated Workshop Time: 35 minutes

### Objectives

- Create a VCN with the internet connectivity wizard.
- Identify the public and private subnets.
- Add a scoped MySQL ingress rule to the private subnet security list.
- Check the network configuration and record the resources for a later lab.

### Prerequisites

- An OCI account and access to the Console.
- A training compartment with permission to create, inspect, update, and delete its networking resources. Ask the tenancy administrator for access if an operation is denied.
- Available networking service limits in your selected region.
- Basic familiarity with IPv4 CIDR notation.
- A browser and approximately 35 minutes.

Use your own tenancy. No LiveLabs sandbox is provisioned by this workshop. Follow your account's usage and billing terms for resources you create.

### Workshop plan

| Lab | Outcome | Time |
| --- | --- | --- |
| 1: Create the VCN | VCN, two regional subnets, and wizard-created gateways | 15 minutes |
| 2: Configure MySQL access | Private subnet rule for TCP 3306 from the client subnet | 10 minutes |
| 3: Verify and clean up | Recorded configuration and retained or removed training resources | 10 minutes |

### Network plan

| Resource | Workshop value | Purpose |
| --- | --- | --- |
| VCN name | ForMySQL | Matches the recording |
| VCN IPv4 CIDR | 10.0.0.0/16 | Contains both subnets |
| Public regional subnet | 10.0.0.0/24 | Planned client compute subnet |
| Private regional subnet | 10.0.1.0/24 | Planned managed MySQL DB system subnet |
| Ingress destination | TCP 3306 | MySQL Classic protocol |
| Authorized source | 10.0.0.0/24 | Planned client subnet |

If these addresses overlap networks you will connect later, select unused ranges and substitute them consistently throughout the labs.

### Adaptation from the recording

The video opens the public subnet's default security list and enters source `0.0.0.0/0` for TCP `3306`. This workshop uses the **private subnet's security list** and source **10.0.0.0/24**, following Oracle's managed MySQL networking procedure. These are deliberate adaptations. The client compute instance and DB system are future resources and are not created here.

Screenshots are extracts from the supplied video. Names, compartments, and Console layouts can differ in your tenancy.

## Learn More

- [MySQL networking overview](https://docs.oracle.com/en-us/iaas/mysql-database/doc/overview-networking.html)
- [Create a VCN for MySQL](https://docs.oracle.com/en-us/iaas/mysql-database/doc/creating-virtual-cloud-network.html)

## Acknowledgements

- **Author** - Shay Hameed, Cloud Engineer, Oracle
- **Last Updated By/Date:** Workshop adaptation, October 2026.
