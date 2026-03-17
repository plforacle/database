# Setup Infrastructure

## Introduction

OCI Bootstrap & Cluster Deployment

Deploy olvm, olkvm01, olkvm02 using Ansible from a temporary bootstrap instance.

A temporary bootstrap instance (Oracle Linux Cloud Developer 8) is used as the Ansible controller to deploy the three permanent cluster instances. The bootstrap is terminated after the playbook completes.

Before launching the bootstrap, a VCN must exist in the new compartment. The easiest way is to use the OCI VCN Wizard — it creates everything needed (VCN, public subnet, internet gateway, route tables, security lists) in one step.

### Objectives

### Prerequisites


## Task 1: Create Bootstrap VCN (VCN Wizard)

1. Go to **Networking → Virtual Cloud Networks**.
2. Click **Start VCN Wizard**.
3. Select **Create VCN with Internet Connectivity** → click **Start VCN Wizard**.
4. Fill in:

   | Field               | Value                |
   |---------------------|----------------------|
   | VCN Name            | bootstrap-vcn        |
   | Compartment         | Your new compartment |
   | VCN CIDR Block      | 10.0.0.0/16 (default)|
   | Public Subnet CIDR  | 10.0.0.0/24 (default)|
   | Private Subnet CIDR | 10.0.1.0/24 (default)|

5. Click **Next → Review → Create**.
6. Wait for all resources to show status: **Available**.

      > 💡 **Tip**: The wizard automatically creates the Internet Gateway, Route Table, and Security Lists. No manual networking steps needed.

## Task 2: Launch Bootstrap Instance

1. Go to **Compute → Instances → Create Instance**.
2. Fill in:

   | Field            | Value                                                           |
   |------------------|-----------------------------------------------------------------|
   | Name             | bootstrap                                                       |
   | Compartment      | Your new compartment                                            |
   | Image            | Oracle Linux Cloud Developer 8 (under Oracle Linux Images)      |
   | Shape            | VM.Standard.E4.Flex — 1 OCPU / 16 GB RAM                        |
   | VCN              | bootstrap-vcn                                                   |
   | Subnet           | Public Subnet                                                   |
   | Assign Public IP | Yes                                                             |
   | SSH Keys         | Upload your existing public key                                 |

3. Click **Create** — wait for Status: **Running**.
4. Note the Public IP address — you will SSH to it shortly.

      > ⚠️ **Warning**: The bootstrap instance is TEMPORARY.  You will need to terminate it after the Ansible playbook completes and you have saved the cluster SSH keys.

5. SH into Bootstrap Instance: From PowerShell on your Windows machine:

      ```bash
      <copy>ssh -i C:\Users\<you>\.ssh\<your-key> opc@<bootstrap-public-ip></copy>
      ```

## Task 3: Setup Bootstrap Software 


1. Install prerequisites on bootstrap (Python 3.8 + git)

      ```bash
      <copy>sudo dnf install -y python38 git</copy>
      ```
      ```bash
      <copy>python3.8 --version</copy>
      ```
      ```bash
      <copy>git --version</copy>
      ```



2. Create + activate a Python 3.8 venv (this is the "single Python" part)

      ```bash
      <copy>python3.8 -m venv ~/venv-olvm</copy>
      ```
      ```bash
      <copy>source ~/venv-olvm/bin/activate</copy>
      ```
      ```bash
      <copy>python --version</copy>
      ```      
      ```bash
      <copy>which python</copy>
      ```


3. Install OCI SDK + Ansible + jmespath into the venv

      ```bash
      <copy>python -m pip install --upgrade pip</copy>
      ```
      ```bash
      <copy>pip install oci ansible==6.7.0 jmespath</copy>
      ```
      ```bash
      <copy>ansible --version</copy>
      ```
      ```bash      
      <copy>python -c "import oci; print(oci.__version__)"</copy>
      ```

4. Clone the lab repo and install Ansible collections (with required pins)

      ```bash
      <copy>git clone https://github.com/oracle-devrel/linux-virt-labs.git
      cd ~/linux-virt-labs/olvm</copy>
      ```

      ```bash
      <copy>ansible-galaxy collection install -r requirements.yml --force</copy>
      ```
      ```bash
      <copy>ansible-galaxy collection install community.general:6.6.0 --force</copy>
      ```
      ```bash
      <copy>ansible-galaxy collection install community.crypto:1.9.0 --force</copy>
      ```

      (The pinned versions match the lab's "critical" guidance to avoid Python compatibility issues on remote hosts.)

5. Configure OCI CLI credentials

      ```bash
      <copy>oci setup config</copy>
      ```

      ```bash
      <copy>ls /home/opc/.oci
      cat /home/opc/.oci/oci_api_key_public.pem</copy>
      ```

      ```bash
      <copy>oci iam region list | head</copy>
      ```

## Task 4: Create  OCI Components

1. Generate SSH keypair (used by the automation)

      ```bash
      <copy>ssh-keygen -t rsa -b 2048 -f ~/.ssh/id_rsa -N ""</copy>
      ```

2. Set the compartment OCID (required)

      ```bash
      <copy>export OCI_COMPARTMENT_OCID="ocid1.compartment.oc1..REDACTED"</copy>
      ```
      ```bas
      <copy>echo $OCI_COMPARTMENT_OCID</copy>
      ```

3. Create instances.yml file

      ```bash
      <copy>cat << EOF | tee instances.yml > /dev/null
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
      EOF</copy>
      ```

4. Create `hosts` inventory (force Ansible localhost to use the venv Python)

      ```bash
      <copy>cat << EOF | tee hosts > /dev/null
      localhost ansible_connection=local ansible_python_interpreter=$HOME/venv-olvm/bin/python
      EOF
      cat hosts</copy>
      ```

      (This avoids Python 3.6/3.8 mismatches without changing any oVirt files.)

5. Run the playbook (from the venv)

      ```bash
      <copy>ansible-playbook create_instance.yml -i hosts -e "@instances.yml"</copy>
      ```

      > **⚠️ CRITICAL – Record All IP Addresses**
      >
      > When the playbook pauses, it displays **both public and private IP addresses** for each instance:
      >
      > - **olvm**
      >   - Public IP (used for SSH access and VNC tunneling)
      >   - Private IP (used for internal OLVM communication)
      >
      > - **olkvm01**
      >   - Public IP (SSH access if needed)
      >   - Private IP (VDSM, storage, VM networking)
      >
      > - **olkvm02**
      >   - Public IP (SSH access if needed)
      >   - Private IP (VDSM, storage, VM networking)
      >
      > **Required Actions:**
      > 1. **Record both public and private IPs for all instances**
      > 2. **Leave this terminal open  DO NOT hit `enter` to continue or `ctrl-c` then `a` to abort!!!**
      > **Why this matters:**
      > - OLVM uses **private IPs** for engine ↔ host (VDSM) communication
      > - SSH access may use **public or private IPs**, depending on context
      > - Later steps assume you understand which IP type is being used
      >

## Task 5: Verify OCI component

11. Reboot the OCI compute instances

      ```bash
      <copy>ssh opc@<olvm-public-ip> "sudo reboot"
      ssh opc@<olkvm01-public-ip> "sudo reboot"
      ssh opc@<olkvm02-public-ip> "sudo reboot"</copy>
      ```

12. Save SSH keys to your local machine (run from your local PC PowerShell)

      From PowerShell on Windows, copy the SSH keys before terminating the bootstrap:

      ```powershell
      <copy>scp opc@<bootstrap-ip>:~/.ssh/id_rsa C:\Users\<you>\.ssh\olvm-cluster-id_rsa
      scp opc@<bootstrap-ip>:~/.ssh/id_rsa.pub C:\Users\<you>\.ssh\olvm-cluster-id_rsa.pub</copy>
      ```

13. Open SSH tunnel (PowerShell)**

      ```powershell
      <copy>ssh -L 5901:localhost:5901 -i C:\Users\<you>\.ssh\olvm-cluster-id_rsa opc@<olvm-public-ip></copy>
      ```


