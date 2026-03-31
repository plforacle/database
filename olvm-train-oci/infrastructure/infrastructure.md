# Setup OLVM Infrastructure on OCI (Bootstrap + Ansible)

## Introduction

This lab walks you through provisioning an Oracle Linux Virtualization Manager (OLVM) environment on Oracle Cloud Infrastructure (OCI) using a temporary **bootstrap** instance as the Ansible controller. You will create a VCN, launch the bootstrap host, install prerequisites, configure OCI credentials, run the provisioning playbook, and verify access.

Estimated Lab Time: 30–45 minutes

### Objectives

In this lab, you will:
- Create a VCN for the bootstrap deployment
- Launch a temporary bootstrap compute instance
- Install required software (Python, Git, Ansible, OCI SDK) on the bootstrap host
- Configure OCI credentials and generate SSH keys used by automation
- Run an Ansible playbook to provision `olvm`, `olkvm01`, and `olkvm02`
- Verify deployed instances and copy required SSH keys before terminating the bootstrap host

### Prerequisites

This lab assumes you have:
- An OCI tenancy with sufficient quotas for the lab instances
- A compartment for lab resources
- Access to the OCI Console
- Your SSH public key for initial access to the bootstrap instance

> **Important:** Use a dev/test environment and clean up resources to avoid unnecessary costs.

> **CRITICAL REMINDER:** When the playbook completes and prompts you to confirm removal of artifacts, press **Ctrl+C** then **a** to abort. Do **not** type `y` — doing so will delete all provisioned resources.




## Task 1: Create Bootstrap VCN (VCN Wizard)

1. In the OCI Console, navigate to **Networking → Virtual Cloud Networks**.
2. Click **Start VCN Wizard**.
3. Select **Create VCN with Internet Connectivity**.
4. Enter values similar to the following (adjust for your environment):

    | Field | Value |
    |---|---|
    | VCN Name | `bootstrap-vcn` |
    | Compartment | *your compartment* |
    | VCN CIDR Block | `10.0.0.0/16` |
    | Public Subnet CIDR | `10.0.0.0/24` |
    | Private Subnet CIDR | `10.0.1.0/24` |

5. Click **Next → Review → Create**.
6. Wait until all resources show **Available**.

## Task 2: Launch Bootstrap Instance

1. In the OCI Console, navigate to **Compute → Instances → Create Instance**.
2. Use values similar to the following (adjust for your environment):

    | Field | Value |
    |---|---|
    | Name | `bootstrap` |
    | Compartment | *your compartment* |
    | Image | Oracle Linux Cloud Developer 8 |
    | Shape | VM.Standard.E4.Flex — 1 OCPU / 16 GB |
    | VCN | `bootstrap-vcn` |
    | Subnet | Public Subnet |
    | Assign Public IP | Yes |
    | SSH Keys | Upload your public key |

3. Click **Create** and wait for the instance state to become **Running**.
4. Record the instance **Public IP**.

5. From Windows PowerShell, connect to the bootstrap instance:

    ```powershell
    <copy>ssh -i C:\Users\<you>\.ssh\<your-key> opc@<bootstrap-public-ip></copy>
    ```

> **Warning:** The bootstrap instance is temporary. Do not terminate it until after the playbook completes and you have copied the required SSH keys to your local machine.

## Task 3: Set Up Bootstrap Software

1. Install prerequisites (Python 3.8 + Git)

    ```bash
    <copy>sudo dnf install -y python38 git
    python3.8 --version
    git --version</copy>
    ```

2. Create and activate a Python virtual environment

    ```bash
    <copy>python3.8 -m venv ~/venv-olvm
    source ~/venv-olvm/bin/activate
    python --version
    which python</copy>
    ```

3. Install OCI SDK + Ansible + jmespath into the virtual environment

    ```bash
    <copy>python -m pip install --upgrade pip
    pip install oci ansible==6.7.0 jmespath
    ansible --version
    python -c "import oci; print(oci.__version__)"</copy>
    ```

4. Clone the lab repository

    ```bash
    <copy>git clone https://github.com/oracle-devrel/linux-virt-labs.git
    cd ~/linux-virt-labs/olvm</copy>
    ```

5.  Install Ansible collections

    ```bash
    <copy>ansible-galaxy collection install -r requirements.yml --force
    ansible-galaxy collection install community.general:6.6.0 --force
    ansible-galaxy collection install community.crypto:1.9.0 --force</copy>
    ```

## Task 4: Configure OCI CLI credentials

1. Run the OCI configuration workflow:

    ```bash
    <copy>oci setup config</copy>
    ```

2. Validate that credentials and keys were created:

    ```bash
    <copy>ls /home/opc/.oci
    cat /home/opc/.oci/oci_api_key_public.pem</copy>
    ```

3. Optional quick check:

    ```bash
    <copy>oci iam region list | head</copy>
    ```

## Task 5: Create OCI Components and Run the Playbook

1. Generate an SSH keypair (used by the automation)

    ```bash
    <copy>ssh-keygen -t rsa -b 2048 -f ~/.ssh/id_rsa -N ""</copy>
    ```

2. Set the compartment OCID (required)

    ```bash
    <copy>export OCI_COMPARTMENT_OCID="ocid1.compartment.oc1..REDACTED"</copy>
    ```

3. Display the compartment variable

    ```bash
    <copy>echo "$OCI_COMPARTMENT_OCID"</copy>
    ```

4. Create `instances.yml`

    ```bash
    <copy>cd ~/linux-virt-labs/olvm

    cat > instances.yml <<'EOF'
    compute_instances:
      1:
        instance_name: "olvm"
        type: "engine"
        instance_ocpus: 2
        instance_memory: 32
      2:
        instance_name: "olkvm01"
        type: "kvm"
        instance_ocpus: 8
        instance_memory: 64
      3:
        instance_name: "olkvm02"
        type: "kvm"
        instance_ocpus: 8
        instance_memory: 64
    use_vnc_on_engine: true
    EOF

    cat instances.yml</copy>
    ```

5. Create `hosts` inventory (force Ansible localhost to use the venv Python)

    ```bash
    <copy>cat << EOF | tee hosts > /dev/null
    localhost ansible_connection=local ansible_connection=local ansible_python_interpreter=/usr/bin/python3.6
    EOF

    cat hosts</copy>
    ```

6. Run the playbook (from the virtual environment)

    ```bash
    <copy>ansible-playbook create_instance.yml -i hosts -e "@instances.yml"</copy>
    ```
    > **CRITICAL:**    
    > **Required Actions:**
    > - **Leave this terminal open  DO NOT hit `enter` to continue ...this action will delete all of the server components.**
    > - **Please hit `ctrl-c` then `a` to abort!!! ...this action will preserve all of the server components.**

    > Record **both public and private IPs** for:
    > - `olvm` (engine)
    > - `olkvm01`
    > - `olkvm02`
    >

## Task 6: Verify and Access Deployed Instances


1. Reboot the OCI instances.

    ```bash
    <copy>ssh opc@<olvm-public-ip> "sudo reboot"
    ssh opc@<olkvm01-public-ip> "sudo reboot"
    ssh opc@<olkvm02-public-ip> "sudo reboot"</copy>
    ```


2. Copy the cluster SSH keys to your Windows machine (before terminating bootstrap)

    ```powershell
    <copy>scp opc@<bootstrap-ip>:~/.ssh/id_rsa C:\Users\<you>\.ssh\olvm-cluster-id_rsa
    scp opc@<bootstrap-ip>:~/.ssh/id_rsa.pub C:\Users\<you>\.ssh\olvm-cluster-id_rsa.pub</copy>
    ```
    > **Note:** Open a **new PowerShell window** on your local Windows machine for this step. Do not run these commands from the bootstrap SSH session.

3. Open an SSH tunnel for VNC (PowerShell)

    ```powershell
    <copy>ssh -L 5914:localhost:5901 -i C:\Users\<you>\.ssh\olvm-cluster-id_rsa oracle@<olvm-public-ip></copy>
    ```
    Example: ssh -L 5914:localhost:5901 -i  ~/.ssh/olvm-cluster-id_rsa oracle@132.145.xx.xx


    > **Note:** Open a **new PowerShell window** on your local Windows machine for this step. Do not run this command from the bootstrap SSH session.


### ✅ Setup OLVM Infrastructure Checkpoint

At this point, you should have:

- ✓ Bootstrap instance running and accessible via SSH
- ✓ Python virtual environment with Ansible and OCI SDK installed
- ✓ OCI CLI credentials configured and validated
- ✓ Three instances deployed: olvm, olkvm01, and olkvm02
- ✓ Public and private IPs recorded for all instances
- ✓ SSH keys copied to your local Windows machine


## Learn More

- Oracle Linux Virtualization Manager install lab (official): https://docs.oracle.com/en/learn/olvm-install/index.html

## Acknowledgements

- **Author** - Shawn Kelley, John Priest  
- **Contributors** - Perside Foster
- **Last Updated By/Date** - Perside Foster , April 1, 2026
