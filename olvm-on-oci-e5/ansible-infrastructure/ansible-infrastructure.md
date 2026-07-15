# Create Your OLVM on OCI Environment

## Introduction

In this lab, you create the OCI environment that you will use throughout the workshop. You will build the network, create three servers, connect the servers to the required networks, add storage, and test your environment before installing OLVM.

Take this lab one task at a time. Each task builds one part of the environment, and you will check your work before moving on.

Estimated Time: 90-120 minutes.

### Objectives

In this lab, you will:

- Create a compartment for the workshop resources
- Create the network that connects your OLVM servers
- Create the OLVM manager and two KVM servers
- Add the private and VM networks to the servers
- Add shared storage for virtual machines
- Verify that you are ready for Lab 2

### Prerequisites

- OCI permissions to create networking, compute, VLAN, and block volume resources
- VLAN or Layer 2 network virtualization enabled in the selected region
- Capacity for one `2 OCPU, 32 GB` instance, two `8 OCPU, 64 GB` instances, and two `1 TB` block volumes
- A local computer with Windows PowerShell, macOS Terminal, or a Linux terminal

> **Important:** Use one compartment for all resources in this lab. Do not continue to Lab 2 until you complete the checkpoint in Task 9.

## Task 1: Create Your Workshop Compartment

In this task, you create one compartment to keep all workshop resources together.

1. In the OCI Console, open the navigation menu, select **Identity & Security**, then select **Compartments**.

2. In the compartment list, select the parent compartment where you are allowed to create workshop resources. For many users, this is the tenancy root compartment.

3. Click **Create Compartment** and enter these values.

    | Field | Value |
    |---|---|
    | Name | `olvm-workshop-<your-initials>` |
    | Description | `OLVM on OCI workshop resources` |

4. Click **Create Compartment**. Wait until its lifecycle state is **Active**, then select it from the compartment picker at the top of the OCI Console.

    If you do not see **Create Compartment** or cannot select the tenancy root compartment, ask your tenancy administrator to create a workshop compartment for you and grant you permission to manage resources inside it. Use that assigned compartment for every remaining task.

## Task 2: Create Your Network

In this task, you create the OCI network used by the OLVM manager, KVM hosts, and virtual machines.

1. In the OCI Console, open the navigation menu, select **Networking**, and then select **Virtual cloud networks**. Select your workshop compartment and click **Start VCN Wizard**.

2. Select **Create VCN with Internet Connectivity**, then click **Start VCN Wizard**. Enter these values and leave the other wizard values at their defaults.

    | Field | Value |
    |---|---|
    | VCN name | `OLV-VCN` |
    | VCN CIDR block | `10.0.0.0/16` |
    | Public subnet CIDR block | `10.0.0.0/24` |
    | Private subnet CIDR block | `10.0.1.0/24` |

3. Click **Next**, review the values, and click **Create**. Wait for the wizard to finish, then click **View VCN**.

4. At the top of the `OLV-VCN` page, click the **Subnets** tab. Confirm that the VCN contains one public subnet with `10.0.0.0/24` and one private subnet with `10.0.1.0/24`.

    The wizard normally names these `Public Subnet-OLV-VCN` and `Private Subnet-OLV-VCN`. If your names differ, use the subnet CIDR blocks to identify the public and private subnets in later tasks.

## Task 3: Connect and Protect Your Network

In this task, you verify the resources created by the VCN wizard, add the required security rules, and create the VM VLAN.

1. At the top of the `OLV-VCN` page, click **Gateways**. Confirm that both the Internet Gateway and Service Gateway show **Available**. The VCN wizard created these gateways.

2. Click **Routing**, then open **Default Route Table for OLV-VCN**. Confirm that it contains this public route:

    | Destination | Target type |
    |---|---|
    | `0.0.0.0/0` | Internet Gateway |

3. Return to **Routing** and open **Route Table for Private Subnet-OLV-VCN**. Confirm that it contains the Oracle Services Network route through the Service Gateway. Do not add an Internet Gateway route to the private route table.

4. Click **Security**, then open **Default Security List for OLV-VCN**. This list belongs to the public subnet. Keep its existing egress rules and add these two ingress rules. Replace `<your-public-ip>/32` with your current public IP address.

    | Source CIDR | IP protocol | Destination port | Description |
    |---|---|---|---|
    | `<your-public-ip>/32` | TCP | `22` | `SSH from my computer` |
    | `<your-public-ip>/32` | TCP | `443` | `OLVM portal from my computer` |

    If your public IP changes, update these two rules. Do not open SSH port `22` to `0.0.0.0/0`.

5. Return to **Security Lists** and open **security list for private subnet-OLV-VCN**. This list belongs to the private management subnet. Keep its existing egress rules and add this ingress rule:

    | Source CIDR | IP protocol | Description |
    |---|---|---|
    | `10.0.0.0/16` | All Protocols | `Allow internal VCN traffic` |

    This rule allows the three hosts to ping and communicate across their private management VNICs.

6. On the **Security** page, scroll to **Network Security Groups** and click **Create Network Security Group**. Enter `L2 Network` for the name, leave tags empty, remove the optional blank rule with its **X**, and click **Create**.

7. Open `L2 Network`, click **Add Security Rules**, and add these rules:

    | Direction | Source or destination | IP protocol | Description |
    |---|---|---|---|
    | Ingress | Source CIDR `10.0.0.0/16` | All Protocols | `Allow VCN traffic` |
    | Egress | Destination CIDR `0.0.0.0/0` | All Protocols | `Allow outbound traffic` |

8. Click **VLANs**, then click **Create VLAN**. Complete the form as follows.

    | Field | Value |
    |---|---|
    | VLAN name | `VLAN-VMs` |
    | VLAN type | **Regional** |
    | IEEE 802.1Q VLAN tag | `1` |
    | VLAN gateway CIDR | `10.0.10.0/24` |
    | Route table | `Default Route Table for OLV-VCN` |
    | Network security group | `L2 Network` |

    Enter exactly `1` for the VLAN tag. Leave resource tags empty, then click **Create VLAN**. If **VLANs** is unavailable, stop because your tenancy or region requires VLAN enablement.

    **Checkpoint:** The VLAN must show **Available** before you continue.

## Task 4: Create Your Three Servers

In this task, you create the OLVM manager and the two servers that will run virtual machines.

1. Create an SSH key pair on your local computer. OCI adds the **public** key to your servers so that you can sign in securely. Keep the private key on your computer and never upload or share it.

    In Windows PowerShell, run:

    ```powershell
    <copy>New-Item -ItemType Directory -Force -Path "$HOME\.ssh" | Out-Null
    ssh-keygen -t ed25519 -f "$HOME\.ssh\olvm-workshop" -C "olvm-workshop"
    Get-Content "$HOME\.ssh\olvm-workshop.pub"</copy>
    ```

    In macOS Terminal or a Linux terminal, run:

    ```bash
    <copy>mkdir -p ~/.ssh
    ssh-keygen -t ed25519 -f ~/.ssh/olvm-workshop -C "olvm-workshop"
    cat ~/.ssh/olvm-workshop.pub</copy>
    ```

    When `ssh-keygen` asks for a passphrase, you can enter a passphrase or press Enter twice to leave it empty for this temporary workshop key. Copy the one-line output that begins with `ssh-ed25519`. This is your public key.

2. Open the navigation menu, select **Compute**, then **Instances**, and click **Create instance**. Select your workshop compartment, use Oracle Linux 8, click **Change shape**, select `VM.Standard.E5.Flex`, and enter the values below. Under networking, select `OLV-VCN`, the public subnet, and **Assign a public IPv4 address**. Under **Add SSH keys**, choose **Paste public keys** and paste the public key you copied in the previous step.

    | Field | Value |
    |---|---|
    | Name | `olvm` |
    | Shape | `VM.Standard.E5.Flex` |
    | OCPUs | `2` |
    | Memory | `32 GB` |
    | Primary VNIC | `Public Subnet-OLV-VCN` |
    | Public IP | Yes |

3. Click **Create instance** again and create the first KVM host. Use the same image, public subnet, and SSH public key.

    | Field | Value |
    |---|---|
    | Name | `olkvm01` |
    | Shape | `VM.Standard.E5.Flex` |
    | OCPUs | `8` |
    | Memory | `64 GB` |
    | Primary VNIC | `Public Subnet-OLV-VCN` |
    | Public IP | Yes |

4. Click **Create instance** again and create `olkvm02` with the same settings as `olkvm01`.

5. Wait for all three instances to show **Running**. Record the public IP address of each instance.

6. From your local terminal, verify SSH access to each instance as `opc` before you continue. Replace `<public-ip>` with each server's recorded public IP address.

    ```bash
    <copy>ssh -i ~/.ssh/olvm-workshop opc@<public-ip> hostname</copy>
    ```

## Task 5: Add the Private Management Network

In this task, you give all three servers a private network connection so they can communicate with each other.

1. In **Compute -> Instances**, click `olvm`. At the top of the instance page, click the **Networking** tab. In the **Attached VNICs** section, click **Create VNIC**. Select **Normal setup: subnet**, select `Private Subnet-OLV-VCN`, keep **Automatically assign private IPv4 address** selected, and do not assign a public IP. Under **DNS**, keep **Assign a private DNS record** selected and enter the hostname shown below.

    | VNIC name | DNS hostname | Network | Public IP |
    |---|---|---|---|
    | `vdsm` | `vdsm` | `Private Subnet-OLV-VCN` | No |

    Before you click **Create VNIC**, record the fully qualified domain name displayed below the **Hostname** field. You use this name later when configuring OLVM.

2. Repeat the preceding navigation for `olkvm01` and `olkvm02`. Create one private-subnet VNIC on each host. Keep **Assign a private DNS record** selected and enter the matching DNS hostname below.

    | Instance | VNIC name | DNS hostname | Network | Public IP |
    |---|---|---|---|---|
    | `olkvm01` | `vdsm01` | `vdsm01` | `Private Subnet-OLV-VCN` | No |
    | `olkvm02` | `vdsm02` | `vdsm02` | `Private Subnet-OLV-VCN` | No |

    Record the full DNS name displayed below each **Hostname** field. Do not assume it will match an example shown elsewhere in the workshop.

3. Connect to each instance as `opc` and configure its new private VNIC. Run these commands before you add the VLAN VNICs in Task 6.

    ```bash
    <copy>sudo oci-network-config configure
    ip -br addr</copy>
    ```

    In the output, find the interface with a `10.0.1.x/24` address. Record both the interface name and address for each host. For example:

    ```text
    enp1s0  UP  10.0.1.78/24
    ```

4. On each host, create a persistent NetworkManager connection for the private VNIC. Replace `<private-interface>` and `<private-ip>` with the values you recorded on that host. Run this command only once on each host.

    ```bash
    <copy>sudo nmcli connection add \
      type ethernet \
      ifname <private-interface> \
      con-name private-management \
      ipv4.method manual \
      ipv4.addresses <private-ip>/24 \
      ipv4.never-default yes \
      connection.autoconnect yes

    sudo nmcli connection up private-management</copy>
    ```

    For example, if the private interface is `enp1s0` and its address is `10.0.1.78`, use `enp1s0` for `<private-interface>` and `10.0.1.78` for `<private-ip>`.

5. Confirm that NetworkManager now manages the private interface and that the private address is present.

    ```bash
    <copy>nmcli connection show --active
    ip -br addr</copy>
    ```

    **Expected result:** `private-management` is active, and the private interface shows its `10.0.1.x/24` address.

6. Reboot each host to confirm that the private configuration persists. Your SSH connection will close during the reboot.

    ```bash
    <copy>sudo reboot</copy>
    ```

    Wait 2-5 minutes, reconnect through the host's public IP, and verify the private address again:

    ```bash
    <copy>nmcli connection show --active
    ip -br addr</copy>
    ```

    Do not continue unless `private-management` is active and the `10.0.1.x/24` address remains after the reboot.

7. From each host, confirm that the other private management addresses respond to ping.

    ```bash
    <copy>ping -I <private-interface> -c 3 <other-host-private-ip></copy>
    ```

    Do not continue until all three hosts can reach one another across `10.0.1.0/24`.

## Task 6: Add the Virtual Machine Network

In this task, you add the separate VLAN network used by virtual machines. Complete Task 5 first.

1. In **Compute -> Instances**, open `olkvm01`. At the top of the instance page, click the **Networking** tab. In the **Attached VNICs** section, click **Create VNIC**. In the advanced networking choices, select the `VLAN-VMs` VLAN, do not assign a public IP, and enter the values below.

    | VNIC name | Network | Public IP |
    |---|---|---|
    | `l2-vm-network` | `VLAN-VMs` | No |

2. Repeat the preceding step for `olkvm02`.

3. On both KVM hosts, confirm that the new VLAN VNIC appears as a separate interface.

    ```bash
    <copy>ip -br link
    nmcli device status</copy>
    ```

    The new VLAN interface does not need an IP address at this stage. OLVM configures it in a later lab. Do not run `oci-network-config configure` after adding the VLAN VNIC.

4. Confirm that the persistent private-management connection and its `10.0.1.x/24` address are still present on both KVM hosts.

    ```bash
    <copy>nmcli connection show --active
    ip -br addr</copy>
    ```

    If `private-management` is not active, restore it with:

    ```bash
    <copy>sudo nmcli connection up private-management</copy>
    ```

    Verify the private address and repeat the private-network ping test from Task 5 before you continue.

## Task 7: Add Shared Storage

In this task, you create storage that both KVM hosts will use later for virtual machines.

1. Open the navigation menu, select **Storage**, then **Block Storage**, and click **Block Volumes**. Select your workshop compartment and click **Create Block Volume**. Create the two volumes below in the same availability domain as the KVM hosts.

    | Name | Size |
    |---|---|
    | `amd-storage-domain-01` | `1 TB` |
    | `amd-storage-domain-02` | `1 TB` |

2. Open each block volume, click **Attached Instances**, then click **Attach to instance**. Attach both volumes to `olkvm01` and then to `olkvm02` using the settings below.

    | Field | Value |
    |---|---|
    | Attachment type | Paravirtualized |
    | Access | Read/write |
    | Shareable | Yes |

3. Do not format or mount these volumes. OLVM configures them as storage domains in a later lab.

## Task 8: Make It Easy to Connect to Your Servers

In this task, you create the workshop user and configure SSH access for the later labs.

1. On each instance, create the `oracle` workshop user and authorize the same SSH key used for `opc`.

    ```bash
    <copy>sudo id oracle >/dev/null 2>&1 || sudo useradd -m -G wheel oracle
    sudo mkdir -p /home/oracle/.ssh
    sudo cp /home/opc/.ssh/authorized_keys /home/oracle/.ssh/authorized_keys
    sudo chown -R oracle:oracle /home/oracle/.ssh
    sudo chmod 700 /home/oracle/.ssh
    sudo chmod 600 /home/oracle/.ssh/authorized_keys
    echo 'oracle ALL=(ALL:ALL) NOPASSWD: ALL' | sudo tee /etc/sudoers.d/oracle
    sudo chmod 440 /etc/sudoers.d/oracle</copy>
    ```

2. On `olvm`, add the KVM host private management addresses to `/etc/hosts`. Replace the placeholders with the addresses recorded in Task 5.

    ```bash
    <copy>sudo tee -a /etc/hosts <<'EOF'
    <olkvm01-private-ip> olkvm01
    <olkvm02-private-ip> olkvm02
    EOF</copy>
    ```

3. On `olvm`, switch to the `oracle` user and create an SSH key.

    ```bash
    <copy>sudo su - oracle
    ssh-keygen -t rsa -b 2048 -f ~/.ssh/id_rsa -N ""
    cat ~/.ssh/id_rsa.pub</copy>
    ```

4. Copy the complete line displayed by `cat ~/.ssh/id_rsa.pub`. It begins with `ssh-rsa`.

5. From your local computer, open an SSH session to `olkvm01` as `opc` using its public IP. Run the following command, paste the copied `ssh-rsa` line, and then press **Ctrl+D**.

    ```bash
    <copy>sudo tee -a /home/oracle/.ssh/authorized_keys</copy>
    ```

    Set the correct ownership and permissions:

    ```bash
    <copy>sudo chown oracle:oracle /home/oracle/.ssh/authorized_keys
    sudo chmod 600 /home/oracle/.ssh/authorized_keys</copy>
    ```

6. Repeat the preceding step on `olkvm02`.

7. Return to the `oracle` shell on `olvm` and verify passwordless SSH to both KVM hosts.

    ```bash
    <copy>ssh olkvm01 hostname -f
    ssh olkvm02 hostname -f</copy>
    ```

## Task 9: Check Your Environment

In this task, you confirm that everything needed for the OLVM installation is ready.

1. From your local machine, verify that you can connect to the OLVM manager as `oracle`.

    ```bash
    <copy>ssh -i ~/.ssh/olvm-workshop oracle@<olvm-public-ip> hostname -f</copy>
    ```

2. Confirm that `olvm`, `olkvm01`, and `olkvm02` are running, the private management network works, both KVM hosts have a separate VLAN interface, and both shared block volumes are attached to both KVM hosts.

3. Complete this worksheet. You need these values in later labs.

    | Value | `olvm` | `olkvm01` | `olkvm02` |
    |---|---|---|---|
    | Public IP |  |  |  |
    | Primary private IP (`10.0.0.x`) |  |  |  |
    | Management IP (`10.0.1.x`) |  |  |  |
    | Management FQDN |  |  |  |
    | Primary interface |  |  |  |
    | Management interface |  |  |  |

    Record the local SSH private-key path separately. The default path used in this lab is `~/.ssh/olvm-workshop`.

4. Confirm all of the following before continuing:

    - `private-management` remains active after reboot on all three hosts.
    - All three management IP addresses respond to ping from the other hosts.
    - `olkvm01` and `olkvm02` each have a separate VLAN interface without an IP address.
    - Both shared volumes are attached read/write and shareable to both KVM hosts.
    - From `olvm`, the `oracle` user can SSH to `olkvm01` and `olkvm02` without a password.

You may now **proceed to the next lab**.

## Acknowledgements

- **Author** - Shawn Kelley, Mark Atkinson, John Priest, Perside Foster
- **Contributor** - Marvin Kim
- **Last Updated By/Date** - Perside Foster, July 15, 2026
