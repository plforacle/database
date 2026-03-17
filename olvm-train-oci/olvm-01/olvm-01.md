# Setup OLVM Infrastructure on OCI (Bootstrap + Ansible)

## Overview

Deploy `olvm`, `olkvm01`, `olkvm02` using Ansible from a temporary **bootstrap** instance. The bootstrap is used as the Ansible controller and should be terminated after the playbook completes and you have copied the cluster SSH keys to your local machine [1].

Before launching the bootstrap, create a VCN in your target compartment. The simplest approach is the OCI **VCN Wizard**, which creates the VCN, subnets, internet gateway, route tables, and security lists in one step [1].

---

## Objectives

- Create a bootstrap VCN and launch a temporary bootstrap instance [1].
- Install prerequisites (Python 3.8, Git, Ansible, OCI SDK) on the bootstrap [1].
- Configure OCI credentials and generate SSH keys [1].
- Define instance configurations and run the Ansible playbook to provision OLVM components [1].
- Verify and access deployed instances; copy keys; open SSH tunnel [1].

---

## Prerequisites

- OCI tenancy with sufficient quotas (lab guidance often assumes ~18 OCPUs and ~160 GB memory for the 3 main instances) [1]
- A compartment for lab resources
- Access to OCI Console
- Your SSH public key for initial access to the bootstrap

> Important: Use a dev/test environment and clean up resources to avoid unnecessary costs [1].

---

## Task 1 — Create Bootstrap VCN (VCN Wizard)

1. OCI Console → **Networking → Virtual Cloud Networks**
2. Click **Start VCN Wizard**
3. Select **Create VCN with Internet Connectivity**
4. Fill in (example values from lab) [1]:

| Field | Value |
|---|---|
| VCN Name | `bootstrap-vcn` |
| Compartment | your compartment |
| VCN CIDR Block | `10.0.0.0/16` |
| Public Subnet CIDR | `10.0.0.0/24` |
| Private Subnet CIDR | `10.0.1.0/24` |

5. **Next → Review → Create**
6. Wait until all resources show **Available** [1].

---

## Task 2 — Launch Bootstrap Instance

1. OCI Console → **Compute → Instances → Create Instance**
2. Use (lab example) [1]:

| Field | Value |
|---|---|
| Name | `bootstrap` |
| Compartment | your compartment |
| Image | Oracle Linux Cloud Developer 8 |
| Shape | VM.Standard.E4.Flex — 1 OCPU / 16 GB |
| VCN | `bootstrap-vcn` |
| Subnet | Public Subnet |
| Assign Public IP | Yes |
| SSH Keys | upload your public key |

3. Click **Create** and wait for **Running**
4. Note the **Public IP** [1]

From Windows PowerShell:

```powershell
<copy>ssh -i C:\Users\<you>\.ssh\<your-key> opc@<bootstrap-public-ip></copy>
```

> Warning: The bootstrap instance is temporary. Terminate it after the playbook finishes and after you save the cluster SSH keys locally [1].

---

## Task 3 — Setup Bootstrap Software

### 3.1 Install prerequisites (Python 3.8 + Git) [1]

```bash
<copy>sudo dnf install -y python38 git
python3.8 --version
git --version</copy>
```

### 3.2 Create + activate a Python 3.8 virtual environment [1]

```bash
<copy>python3.8 -m venv ~/venv-olvm
source ~/venv-olvm/bin/activate
python --version
which python</copy>
```

### 3.3 Install OCI SDK + Ansible + jmespath into the venv [1]

```bash
<copy>python -m pip install --upgrade pip
pip install oci ansible==6.7.0 jmespath
ansible --version
python -c "import oci; print(oci.__version__)"</copy>
```

### 3.4 Clone the lab repo and install Ansible collections [1]

```bash
<copy>git clone https://github.com/oracle-devrel/linux-virt-labs.git
cd ~/linux-virt-labs/olvm</copy>
```
```bash
<copy>ansible-galaxy collection install -r requirements.yml --force
ansible-galaxy collection install community.general:6.6.0 --force
ansible-galaxy collection install community.crypto:1.9.0 --force</copy>
```

### 3.5 Configure OCI CLI credentials [1]

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

---

## Task 4 — Create OCI Components and Run the Playbook

### 4.1 Generate SSH keypair (used by the automation) [1]

```bash
<copy>ssh-keygen -t rsa -b 2048 -f ~/.ssh/id_rsa -N ""</copy>
```

### 4.2 Set the compartment OCID (required) [1]

```bash
<copy>export OCI_COMPARTMENT_OCID="ocid1.compartment.oc1..REDACTED"</copy>
```

```bash
<copy>echo "$OCI_COMPARTMENT_OCID"</copy>
```

### 4.3 Create `instances.yml` (copy/paste-safe)

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

### 4.4 Create `hosts` inventory (force Ansible localhost to use the venv Python) [1]

```bash
<copy>cat > hosts <<'EOF'
localhost ansible_connection=local ansible_python_interpreter=/home/opc/venv-olvm/bin/python
EOF

cat hosts</copy>
```

### 4.5 Run the playbook (from the venv) [1]

```bash
<copy>ansible-playbook create_instance.yml -i hosts -e "@instances.yml"</copy>
```

#### CRITICAL — Record All IP Addresses [1]
When the playbook pauses, record **both public and private IPs** for:
- `olvm` (engine)
- `olkvm01`
- `olkvm02`

Leave the terminal open at the pause; don’t continue/abort unless the lab instructs you [1].

---

## Task 5 — Verify and Access

### 5.1 Reboot the OCI instances [1]

```bash
ssh opc@<olvm-public-ip> "sudo reboot"
ssh opc@<olkvm01-public-ip> "sudo reboot"
ssh opc@<olkvm02-public-ip> "sudo reboot"
```

### 5.2 Copy the cluster SSH keys to your Windows machine (before terminating bootstrap) [1]

```powershell
scp opc@<bootstrap-ip>:~/.ssh/id_rsa C:\Users\<you>\.ssh\olvm-cluster-id_rsa
scp opc@<bootstrap-ip>:~/.ssh/id_rsa.pub C:\Users\<you>\.ssh\olvm-cluster-id_rsa.pub
```

### 5.3 Open SSH tunnel for VNC (PowerShell) [1]

```powershell
ssh -L 5901:localhost:5901 -i C:\Users\<you>\.ssh\olvm-cluster-id_rsa opc@<olvm-public-ip>
```

---

## Cleanup

- Terminate the bootstrap instance after you confirm everything is deployed and you’ve copied the SSH keys [1].
- Clean up lab resources when finished to control costs.
```

---

## Part B — The two “files” as standalone (for quick validation)

### `instances.yml` (correct structure)
```yaml
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
```

### `hosts` (inventory)
```ini
localhost ansible_connection=local ansible_python_interpreter=/home/opc/venv-olvm/bin/python
```

