# Verify the Network and Manage Training Resources

## Introduction

Check the network configuration and record the resources needed by a later MySQL lab. Retain the network for that lab or remove it when the exercise ends.

Estimated Time: 10 minutes

### Objectives

- Check subnet, gateway, route, and security settings.
- Record resource identifiers for future use.
- Remove only the resources created for this exercise when finished.

### Prerequisites

Complete Labs 1 and 2.

## Task 1: Check the network resources

1. Open **ForMySQL** and confirm that its state is **Available**. Inspect each subnet and confirm the CIDRs and access types recorded in Lab 1.

2. Open **Gateways** or the corresponding resource sections. Check that the wizard created an internet gateway, a NAT gateway, and a service gateway.

3. Inspect the route table associated with each subnet. Compare its routes with this checklist. Service destination labels vary by region.

    | Subnet | Destination | Expected target |
    | --- | --- | --- |
    | Public | 0.0.0.0/0 | Internet gateway |
    | Private | 0.0.0.0/0 | NAT gateway |
    | Private | Regional Oracle Services Network destination | Service gateway |

    These checks supplement the video with the documented wizard behavior. Review routes in the Console; no route edits are required when the wizard completes as expected.

4. Reopen the security list associated with the private subnet and check the TCP **3306** rule from **10.0.0.0/24**. Review the other rules before treating the subnet as ready for a later deployment.

## Task 2: Record the handoff

1. Copy the following identifiers from the resource detail pages into your private lab notes.

    | Item | Your value |
    | --- | --- |
    | Region | Record selected region |
    | Compartment name and OCID | Record training compartment |
    | VCN name and OCID | Record ForMySQL VCN |
    | Public subnet OCID and CIDR | Record client subnet |
    | Private subnet OCID and CIDR | Record planned DB subnet |
    | Private subnet security list OCID | Record list containing TCP 3306 rule |

2. Check your results against this completion list: two available regional subnets, the three wizard gateways, the expected route targets, and the saved scoped MySQL rule.

3. Retain the VCN if you will use it in a follow-on deployment lab. A later lab must create the DB system, client access, and any HA configuration before database connectivity and failover can be tested.

## Task 3: Clean up when the exercise ends

1. Perform this task only when you no longer need the training network. Confirm that the selected VCN name and OCID match the resources you created in Lab 1 and that no other exercise uses them.

2. Open the VCN's **Actions** menu and select **Delete**. Select the option to search for associated resources and run **Scan**.

3. Review the scan. If a database, compute instance, or another workload occupies a subnet, stop and handle that dependency through its own service. Do not delete resources owned by another exercise.

4. For this exercise's unused network, select **Delete All** to remove its associated networking resources. Wait for completion, then check that the VCN no longer appears in the compartment. Keep the compartment itself.

## Task 4: Resolve common blockers

1. If the VCN does not appear, check the selected region and compartment before creating another VCN.

2. If creation fails, inspect the reported resource and confirm that your account has permissions and available limits. A partially completed wizard can leave resources behind; inspect them before retrying.

3. If the ingress rule appears in the wrong list, open the private subnet's security list association and save the intended rule there. Remove only an accidental rule you created during this exercise.

4. If cleanup reports a dependency, use the linked resource details to identify the blocking workload. Leave shared resources in place and ask the resource owner how to proceed.

## Learn More

- [Virtual networking wizard behavior](https://docs.oracle.com/en-us/iaas/Content/Network/Tasks/quickstartnetworking.htm)
- [Delete a VCN and associated resources](https://docs.oracle.com/en-us/iaas/Content/Network/Tasks/delete_vcn.htm)

## Acknowledgements

- **Author** - Shay Hameed, Cloud Engineer, Oracle
- **Last Updated By/Date:** Workshop adaptation, October 2026.
