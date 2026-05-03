# Lab 1: Build the OCI Network Foundation

## Introduction

In this lab, you will complete the OCI setup that the source tutorial uses for the OLVM demo environment. You will create the compartment, launch the VCN wizard, verify the route tables and security lists, and create the VLAN that the virtual machines will use later in the workshop.

Estimated Time: 25 minutes

### Objectives

In this lab, you will:
* Create a compartment for the OLVM resources.
* Create `OLVM-VCN` with the public and private subnets from the source.
* Add the ingress rules used by the manager UI and noVNC console.
* Create the network security group and VLAN for the virtual machine network.

### Prerequisites (Optional)

This lab assumes you can sign in to your OCI sandbox tenancy and create networking resources.

## Task 1: Create the Compartment

1. Open the OCI navigation menu, go to **Identity & Security**, and open **Compartments**.

2. Create a compartment for the demo environment and use the source naming pattern.

    Use these values:
    * Name: `<lastname>-olvm`
    * Description: `Oracle Linux Virtualization Manager Demo on OCI`

## Task 2: Create the VCN with Internet Connectivity

1. Open the new OLVM compartment, go to **Networking**, and open **Virtual cloud networks**.

2. Start the VCN wizard and choose **Create VCN with Internet Connectivity**.

3. Enter the network values from the source tutorial and create the VCN.

    Use these values:
    * VCN Name: `OLVM-VCN`
    * VCN IPv4 CIDR block: `10.0.0.0/16`
    * Public Subnet IPv4 CIDR block: `10.0.0.0/24`
    * Private Subnet IPv4 CIDR block: `10.0.1.0/24`

4. Review the summary and confirm the VCN is created in the correct compartment.

## Task 3: Verify Routes and Add Security Rules

1. Open `OLVM-VCN` and confirm the wizard created these resources:

    * `Public Subnet-OLVM-VCN` with `10.0.0.0/24`
    * `Private Subnet-OLVM-VCN` with `10.0.1.0/24`
    * `Internet Gateway-OLVM-VCN`
    * `NAT Gateway-OLVM-VCN`
    * `Service Gateway-OLVM-VCN`

2. Open the default route table for `OLVM-VCN` and confirm it contains a single public route rule.

    The source expects:
    * Destination: `0.0.0.0/0`
    * Target Type: `Internet Gateway`
    * Target: `Internet Gateway-OLVM-VCN`

3. Open the private subnet route table and confirm it contains the NAT and Service Gateway rules from the source.

    The source expects:
    * Destination: `0.0.0.0/0`, Target Type: `NAT Gateway`, Target: `NAT Gateway-OLVM-VCN`
    * Destination: `All <your tenancy region> Services in Oracle Services Network`, Target Type: `Service Gateway`, Target: `Service Gateway-OLVM-VCN`

4. Open the default security list and add the ingress rules used by the source tutorial.

    Add these rules:
    * Source CIDR `10.0.0.0/16`, protocol `ICMP`, type `All`, code `3`, description `VCN ICMP PING`
    * Source CIDR `<your public IP with the last octet replaced by 0/24>`, protocol `TCP`, source ports `ALL`, destination ports `ALL`, description `My Home Office IP`
    * Source CIDR `0.0.0.0/0`, protocol `TCP`, destination port `443`, description `SSL for OLVM Manager`
    * Source CIDR `0.0.0.0/0`, protocol `TCP`, destination port `6100`, description `noVNC Console Access`

5. Open a separate browser tab to `https://whatsmyip.org` before you enter the source CIDR for your workstation access rule.

6. Open the private subnet security list and add the trust rules from the source.

    Add these rules:
    * Source CIDR `10.0.0.0/16`, protocol `TCP`, type `All`, description `VCN ICMP PING`
    * Source CIDR `10.0.1.0/24`, protocol `All Protocols`

## Task 4: Create the Network Security Group and VLAN

1. In `OLVM-VCN`, create a network security group named `VLAN_NSG`.

2. Add the ingress and egress rules from the source.

    Use these values:
    * Ingress source CIDR: `10.0.0.0/16`, protocol `All Protocols`
    * Egress destination CIDR: `0.0.0.0/0`, protocol `All Protocols`

3. Open **VLANs** and create the VLAN used by the virtual machine network.

    Use these values:
    * VLAN Name: `VLAN-VMs`
    * IEEE 802.1Q VLAN Tag: `1`
    * VLAN Gateway CIDR: `10.0.10.0/24`
    * Route Table: `default route table for OLVM-VCN`
    * Network Security Group: `VLAN_NSG`

4. If OCI displays the warning about L2 Networking OCVS enablement, stop the build and escalate the issue, as directed by the source tutorial.

5. Confirm the VLAN is created with VLAN tag `1` and gateway CIDR `10.0.10.0/24`.

## Acknowledgements
* **Author** - Codex
* **Contributors** - `seighman` (source tutorial author shown in the PDF)
* **Last Updated By/Date** - Codex, May 2026
