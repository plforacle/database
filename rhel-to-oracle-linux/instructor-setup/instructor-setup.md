# Instructor Setup: Create the Compartment, VCN, and Compute Instance

## Introduction

Complete this lab yourself in the OCI Console before giving partners access. It creates the RHEL server that partners will later access through SSH. Partners begin with **Lab 1: Connect and Capture the Baseline** after you complete infrastructure setup and the preparation steps in the Instructor Guide.

Start with one pilot server. Complete a migration, Ksplice, management, and recovery rehearsal before preparing the rest of the cohort.

Estimated Time: 30 to 45 minutes for infrastructure creation, excluding guest preparation and the pilot rehearsal.

### Objectives

- Create a workshop compartment.
- Create a VCN and subnet with an approved SSH path.
- Launch a RHEL source Compute instance.
- Connect as the instructor and hand off to guest preparation.

### Prerequisites

- Instructor access to a paid OCI tenancy and the selected region.
- Permission to create compartments, networking, Compute instances, storage, and backups, or an administrator to perform those steps.
- Access to the selected RHEL 9 x86_64 image and sufficient quota and capacity.
- An approved SSH source address range and an SSH key pair, or permission to generate a key pair during launch.

## Task 1: Create the compartment in the OCI Console

1. Sign in to the OCI Console and select the region for the workshop. For the initial rehearsal, use a region where your selected RHEL source image is available.

2. Open the navigation menu and select **Identity & Security**, then **Compartments** under **Identity**.

3. Open the approved parent compartment and select **Create compartment**. Enter:

    - Name: `rhel-to-ol-pilot`
    - Description: `RHEL to Oracle Linux instructor pilot`
    - Parent compartment: the training parent approved by your administrator

4. Select **Create compartment**. Record the compartment name and OCID in your instructor notes. Use this compartment for the pilot VCN and Compute instance.

    For the cohort, use a separate team compartment when granting OS Management Hub access at compartment scope. An administrator must validate isolation between teams before delivery.

## Task 2: Create the VCN before launching Compute

1. Open **Networking**, then **Virtual cloud networks**. Select `rhel-to-ol-pilot` in the compartment filter.

2. Select **Start VCN Wizard**, either directly or under **Actions**, depending on the Console layout. Choose **Create VCN with Internet Connectivity** and start the wizard.

3. Enter the following pilot settings, adapting the CIDRs if they conflict with your organization's network:

    - VCN name: `rhel-to-ol-pilot-vcn`
    - Compartment: `rhel-to-ol-pilot`
    - VCN IPv4 CIDR: `10.0.0.0/16`
    - Public subnet IPv4 CIDR: `10.0.0.0/24`
    - Private subnet IPv4 CIDR: `10.0.1.0/24`

    These are example addresses. Keep IPv6 disabled unless it is part of your tested training configuration.

4. Review and create the network. The wizard configures subnets, gateways, route tables, and initial security lists. Wait for creation to complete, then open the VCN details.

5. Open the public subnet and record its name, security list, and route table. Confirm the route table has an internet route through the VCN's enabled internet gateway.

    This pilot uses a public subnet and public IPv4 address for direct SSH. If your organization requires a private subnet and bastion or VPN, use the approved connection path and provide the corresponding SSH instructions to learners.

## Task 3: Configure and check SSH access

1. Open the security list actually attached to the pilot public subnet. Inspect the existing SSH ingress rule created by the wizard.

2. Set the SSH rule to the approved source IP range. For one instructor with a fixed public egress IP, the source can be that address with `/32`. Set:

    - Source type: **CIDR**
    - Source CIDR: your approved SSH source range
    - IP protocol: **TCP**
    - Source port: all, or leave the source-port field blank where the Console uses blank for all
    - Destination port: `22`
    - Stateful rule: enabled

3. Remove or narrow any broader SSH rule that would otherwise keep access open. Adding a narrow rule alongside a broad rule does not restrict the broad rule. During cohort setup, include the learners' approved egress ranges or the approved bastion path.

4. Confirm outbound routing and security rules allow source repositories, Oracle Linux repositories, Ksplice, and the management services used in the pilot. Retain only the network configuration approved for the training environment.

5. No public HTTP ingress rule is needed for these partner labs. They test the prepared Apache workload from the SSH session using `http://127.0.0.1/`.

## Task 4: Launch the RHEL Compute instance

1. Open **Compute**, then **Instances**. Select the `rhel-to-ol-pilot` compartment and choose **Create instance**.

2. Enter the instance name `rhel-to-ol-pilot-01`. Confirm its compartment and select an availability domain with suitable capacity.

3. In image selection, choose the instructor-selected **RHEL 9 x86_64** source image. Confirm its exact version and name. If using a supplied image OCID, use the Console's image-OCID selection option and verify the resolved image before continuing.

    The instance must start as RHEL for partners to perform the conversion. Record image provenance and review usage charges. Availability and migration compatibility must be checked for the chosen image; the source workshop's recorded image is not a completed pilot for this edition.

4. Select an x86_64-compatible VM shape. The source workshop's example was `VM.Standard.E5.Flex`, 1 OCPU, and 12 GB memory. Use it if the image supports it and capacity is available, or rehearse a compatible alternative. Do not select an Arm shape for this RHCK exercise.

5. In networking, select the existing `rhel-to-ol-pilot-vcn` and its public subnet. Assign a public IPv4 address for the direct-SSH pilot. Confirm that any instance network security groups also permit the approved SSH path.

6. Add the public key for your instructor SSH key pair, or generate a key pair and download the private key before leaving the form. Keep the private key in an approved local location. Use individual learner or team keys when preparing cohort servers.

7. Retain a boot volume at least as large as the image minimum. The source workshop used a 64 GB boot volume. Use more space if required by the image and migration workload. Review any launch options against your organization's policy.

8. Review the name, compartment, RHEL image, shape, memory, network, public address, key, and storage. Select **Create**. Wait until the instance is **Running** and record its public IPv4 address, instance OCID, and boot volume.

## Task 5: Connect and prepare the guest before partner access

1. Obtain the expected host-key fingerprint through an instructor-controlled trusted channel, such as the approved instance console access process. Identify the SSH account for the selected image. The source workshop used `cloud-user` or `opc`; verify the account for your image.

2. From your local terminal, connect using the selected account:

    ```bash
    ssh -i "<private-key-path>" <ssh-user>@<instance-public-ip>
    ```

    Compare the host-key fingerprint before accepting it. If the connection fails, check the public address, route, security rules, username, and corresponding key.

3. In SSH, confirm RHEL, architecture, sudo access, and repository connectivity:

    ```bash
    cat /etc/os-release
    uname -m
    sudo -v
    sudo dnf repolist --enabled
    sudo dnf makecache
    ```

    Require the expected RHEL 9 source release, `x86_64`, working sudo, and working source repositories.

4. Continue to **Instructor Guide**, Task 1, Step 3 to prepare the SSH account and workload. Then stage the workshop files, prepare the recovery point, complete the pilot, and configure management access using the remaining guide tasks.

5. Give partners the server only after preparation and the required checks pass. Their starting point is **Lab 1: Connect and Capture the Baseline**. Their connection sheet contains the assigned hostname or IP, account, key handoff, host-key fingerprint, and support contact; it requires no OCI Console access.

## Learn More

- [Creating a compartment](https://docs.oracle.com/en-us/iaas/Content/Identity/compartments/To_create_a_compartment.htm).
- [VCN wizard](https://docs.oracle.com/en-us/iaas/Content/Network/Tasks/quickstartnetworking.htm).
- [Creating a Compute instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/launchinginstance.htm).

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle.
- **Last Updated By/Date** - Workshop adaptation draft, October 19, 2026.
