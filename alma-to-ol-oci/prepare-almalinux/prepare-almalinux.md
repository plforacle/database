# Lab 1: Create the OCI Environment and AlmaLinux Instance

## Introduction

Use the OCI Console to create a compartment, a virtual cloud network (VCN), and a new AlmaLinux 9.8 instance. This dedicated VM will host the test application and undergo the migration. You select the official AlmaLinux partner image and verify its version after the first boot.

Estimated Lab Time: 45 minutes, plus instance provisioning

### Objectives

- Create a compartment for workshop resources.
- Create a VCN with internet connectivity and restrict inbound access.
- Launch an AlmaLinux x86_64 VM on E5.Flex with 1 OCPU and 12 GB RAM.
- Connect with SSH and verify AlmaLinux 9.8 before continuing.
- Record resources for later recovery and cleanup.

### Prerequisites

- An OCI tenancy and permission to create compartments, network resources, Compute instances, and boot-volume backups.
- Available E5.Flex capacity in your selected region and availability domain.
- An official AlmaLinux 9.8 x86_64 partner image build available in your region, or an administrator-provided compatible AlmaLinux 9.8 custom image.
- An SSH client and your workstation's public IPv4 address.

## Task 1: Create the workshop compartment

1. Sign in to the OCI Console and select the region where you will run the workshop.

2. Open **Identity & Security**, then **Compartments**. Select the parent compartment where you have permission to create workshop resources, then select **Create compartment**.

3. Enter these values:

    | Field | Value |
    | --- | --- |
    | Name | `alma-to-ol-lab` |
    | Description | `AlmaLinux to Oracle Linux migration workshop` |
    | Parent compartment | Your authorized parent compartment |

4. Select **Create compartment**. Record its OCID and select this compartment in the later network and Compute screens. Creating a compartment does not grant new permissions; resolve any authorization error with your tenancy administrator.

## Task 2: Create the VCN

1. Open **Networking**, then **Virtual cloud networks**. Select `alma-to-ol-lab` in the compartment selector.

2. Select **Start VCN Wizard**, or **Actions**, then **Start VCN Wizard**. Choose **Create VCN with Internet Connectivity**.

3. Enter the following workshop values. If your organization reserves these ranges, use approved non-overlapping ranges instead:

    | Field | Value |
    | --- | --- |
    | VCN name | `alma-to-ol-vcn` |
    | Compartment | `alma-to-ol-lab` |
    | VCN IPv4 CIDR | `10.20.0.0/16` |
    | Public subnet IPv4 CIDR | `10.20.0.0/24` |
    | Private subnet IPv4 CIDR | `10.20.1.0/24` |
    | DNS hostnames | Enabled |

4. Review the wizard's resource list and create the VCN. It creates public and private subnets, gateways, route tables, and security lists. Open the resulting VCN and record their names and OCIDs.

5. Open the public subnet's route table. Verify that `0.0.0.0/0` targets the VCN's internet gateway. Use this public subnet for the lab VM.

## Task 3: Restrict workshop network access

1. Open the public subnet's security list and inspect its ingress rules. If the wizard created SSH access from `0.0.0.0/0`, edit that rule to use your workstation's public IPv4 address followed by `/32`.

2. Configure these stateful ingress rules. Leave source ports as All:

    | Source CIDR | Protocol | Destination port | Purpose |
    | --- | --- | --- | --- |
    | `<workstation-public-ip>/32` | TCP | 22 | SSH |
    | `<workstation-public-ip>/32` | TCP | 80 | Workshop Apache page |

    Security-list and network-security-group rules are additive. Check for another broad rule that would still expose ports 22 or 80. The guest firewall rule for HTTP is added in Lab 2.

3. Verify that the subnet's egress rules permit repository HTTPS and name resolution. The wizard's default egress rule permits outbound traffic; if your organization requires narrower rules, use its approved HTTPS and DNS policy.

## Task 4: Select AlmaLinux and the E5.Flex shape

1. Open **Compute**, then **Instances**. Select `alma-to-ol-lab`, then select **Create instance**. Name the VM `alma-to-ol-source` and select an availability domain with E5.Flex capacity.

2. Under **Image and shape**, select **Change image**. Choose **Marketplace**, then **Partner images**, or **Partner images** directly if your Console displays that option. Search for **AlmaLinux OS 9**.

3. Use the official x86_64 image linked from the [AlmaLinux OCI image guide](https://wiki.almalinux.org/cloud/OCI.html). Expand the available image builds and select a **9.8** build. Confirm the publisher, architecture, listing instructions, and terms before selecting the image.

    The latest AlmaLinux OS 9 image can change over time. Do not assume that the latest build is 9.8. If 9.8 is unavailable, use an administrator-provided compatible 9.8 image through **My images**, then **Custom images**. A different release needs a revised and tested workshop target.

4. Select **Change shape**, choose **Virtual machine** and the **AMD** series, then configure:

    | Setting | Value |
    | --- | --- |
    | Shape | `VM.Standard.E5.Flex` |
    | OCPUs | `1` |
    | Memory | `12 GB` |
    | Reported network bandwidth | `1 Gbps` for the selected configuration |

5. In **Security**, leave **Shielded instance** and **Confidential computing** disabled. Review the actual switches; capability labels in the shape picker do not mean the features are enabled.

6. In **Networking**, select `alma-to-ol-vcn` and its public subnet. Enable assignment of an ephemeral public IPv4 address.

7. Add your SSH public key, or generate a key pair and download the private key before launching. Record the SSH username documented by the selected image. Keep the private key on your workstation.

8. Keep the image's default boot-volume size, or choose a larger size if your lab requires it. Use the image's default compatible boot and launch options. Leave initialization scripts empty.

9. Review the compartment, AlmaLinux 9.8 x86_64 image, shape, memory, security switches, network, and key. Select **Create** and wait for Running. Record the instance's public address.

## Task 5: Connect and verify the source

1. From your local terminal, connect using the username documented by the image listing:

    ```bash
    <copy>
    ssh -i "<private-key-path>" <ssh-user>@<public-ip>
    </copy>
    ```

    For an image that documents `opc`, substitute `opc` for `<ssh-user>`. Use the same account throughout the migration. If SSH fails, check the image username, key permissions, public IP, route, and port 22 rule before proceeding.

2. Verify the source OS, architecture, and sudo access:

    ```bash
    <copy>
    . /etc/os-release
    printf 'ID=%s VERSION_ID=%s ARCH=%s USER=%s\n' "$ID" "$VERSION_ID" "$(uname -m)" "$(id -un)"
    sudo -v
    </copy>
    ```

    Continue only when the results show `ID=almalinux`, `VERSION_ID=9.8`, and `ARCH=x86_64`. If the selected image booted a different release, correct the image selection before Lab 2.

3. Verify storage and repository connectivity:

    ```bash
    <copy>
    df -h / /boot
    getent hosts raw.githubusercontent.com yum.oracle.com www.ksplice.com
    curl --fail --location --head https://yum.oracle.com/
    sudo dnf repolist --enabled
    </copy>
    ```

## Task 6: Record the resource ledger

1. Record the compartment, VCN, public and private subnets, gateways, route tables, security lists, instance, and boot-volume OCIDs. Mark these new resources as workshop-owned.

2. Record the source image listing or image OCID, build version, availability domain, SSH account, public address, and shape settings. The recovery rehearsal will use the same compartment and lab network.

3. Confirm the checkpoint: the new compartment and VCN exist; the VM boots AlmaLinux 9.8 x86_64; SSH and sudo work; E5.Flex has 1 OCPU and 12 GB RAM; both optional security features are disabled; repositories respond.

## Learn More

- [Creating a compartment](https://docs.oracle.com/en-us/iaas/Content/Identity/compartments/To_create_a_compartment.htm)
- [VCN wizard](https://docs.oracle.com/en-us/iaas/Content/Network/Tasks/quickstartnetworking.htm)
- [Official AlmaLinux OCI images](https://wiki.almalinux.org/cloud/OCI.html)
- [Creating an instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/launchinginstance.htm)
- [Connecting to a Linux instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/connect-to-linux-instance.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Perside Foster with Codex assistance, October 2026
