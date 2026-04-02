# Perform Live Migration

## Introduction

### Overview

Live migration allows you to move a running virtual machine from one KVM host to another without downtime. This is a key capability for maintenance operations, load balancing, and high availability scenarios.

### Objectives

In this lab, you will:
- Restart the OLVM engine to ensure cache consistency before migration
- Verify that migration prerequisites are met (hosts Up, shared storage, matching networks)
- Perform a live migration of the ol9-mysql VM from olkvm01 to olkvm02
- Verify the VM, database, and multi-tier application remain fully operational after migration

### Prerequisites

- Both hosts must be in the same cluster
- Both hosts must have access to the shared storage domain
- The destination host must have sufficient resources (CPU, memory)
- The logical network used by the VM must be available on both hosts



## Task 1: Restart the engine
1. **Lab-Specific Prerequisite:** After the extensive infrastructure changes made during the previous labs, the engine's internal cache may be out of sync. Restart the engine before attempting migration:

    ```bash
    <copy>sudo systemctl restart ovirt-engine</copy>
    ```
    Wait 2–3 minutes for the engine to fully restart, then refresh the Administration Portal in Firefox before proceeding. This does not affect running VMs.

## Task 2: Verify Migration Prerequisites

1. Go to **Compute** → **Hosts**. Verify both hosts show status **Up**.

2. Go to **Compute** → **Virtual Machines**. Verify ol9-mysql is running on olkvm01 (check the Host column).


## Task 3: Perform Live Migration

1. In the Virtual Machines pane, select **ol9-mysql**.

2. Click **Migrate** in the toolbar.

3. In the Migrate dialog, select **olkvm02** from the Destination Host dropdown.

4. Click **Migrate** to start the migration.

5. Monitor the progress — the status will change to **Migrating From** with a progress indicator.

    **What happens during live migration:**
    ```
    ┌─────────────────┐                    ┌─────────────────┐
    │    olkvm01      │                    │    olkvm02      │
    │   (Source)      │                    │  (Destination)  │
    │                 │                    │                 │
    │  ┌───────────┐  │  1. Memory copy    │  ┌───────────┐  │
    │  │ ol9-mysql │  │ =================> │  │ ol9-mysql │  │
    │  │  (VM)     │  │  2. Dirty pages    │  │  (copy)   │  │
    │  └───────────┘  │ =================> │  └───────────┘  │
    │                 │  3. Final sync     │                 │
    │                 │ =================> │                 │
    │                 │  4. Switch active  │                 │
    └─────────────────┘                    └─────────────────┘
            │                                      │
            └──────────── Shared Storage ──────────┘
                        (amd-storage-domain-01)
    ```

    1. **Pre-copy phase**: VM memory is copied to destination while VM continues running
    2. **Iterative copy**: Changed (dirty) memory pages are re-copied
    3. **Stop-and-copy**: Brief pause to transfer final state
    4. **Activation**: VM resumes on destination host

6. Wait for migration to complete (typically 30–60 seconds).



## Task 4: Verify Migration Success

1. In the Virtual Machines pane, verify ol9-mysql now shows **Host: olkvm02** and **Status: Up**.

2. Click the **ol9-mysql** VM name → **Events** tab. You should see:
    - "VM ol9-mysql started migration on Host olkvm01"
    - "VM ol9-mysql was migrated to Host olkvm02"

3. From the olvm engine terminal, test connectivity to the migrated VM:
    ```bash
    <copy>ping -c 3 10.0.10.100</copy>
    ```

4. Verify the database is still accessible:
    ```bash
    <copy>ssh opc@10.0.10.100 "mysql -u empapp -pWelcome#123 employee_db -e 'SELECT COUNT(*) FROM employees;'"</copy>
    ```
   The output should show **8**, confirming the database is operational after migration.

5. Confirm the web application still connects to the migrated database:
    ```bash
    <copy>curl -s http://10.0.10.101:8080/employee-app/employees | grep -c "<tr>"</copy>
    ```
    The output should show **9** (1 header + 8 employee rows), confirming end-to-end connectivity.



### ✅ Perform Live Migration Checkpoint

At this point, you should have:
- ✓ ol9-mysql successfully migrated from olkvm01 to olkvm02
- ✓ VM accessible at the same IP address on the new host
- ✓ Database operational after migration
- ✓ Web application connectivity confirmed end-to-end



## Quick Reference — Essential OLVM Commands

### Installation & Engine Setup
```bash
# Enable OLVM repositories
<copy>sudo dnf install -y oracle-ovirt-release-45-el8</copy>

# Install engine
sudo dnf install -y ovirt-engine

# Configure engine (will prompt for admin password)
sudo engine-setup --accept-defaults

# Access portal
# https://<fqdn>/ovirt-engine
```

### Host & Cluster Management
```bash
# Get engine SSH public key (for adding hosts)
ssh-keygen -y -f /etc/pki/ovirt-engine/keys/engine_id_rsa

# Verify VDSM service on host
sudo systemctl status vdsm
```

### VM Access and Connectivity
```bash
# Connect to VMs directly from olvm engine
ssh opc@10.0.10.100  # MySQL VM
ssh opc@10.0.10.101  # Webapp VM

# Test connectivity
ping 10.0.10.1
ping 8.8.8.8

# Check VM network configuration
ip addr show eth0
```

### Portal Navigation
- **Create logical network:** Portal → Network → Networks → New
- **Import template:** Portal → Compute → Templates → Import
- **Import OVA VM:** Portal → Compute → Virtual Machines → Import
- **Migrate VM:** Portal → Compute → Virtual Machines → Select VM → Migrate

### Troubleshooting
```bash
# Check logs
sudo tail -50 /var/log/ovirt-engine/engine.log
sudo tail -50 /var/log/vdsm/vdsm.log

# Check service status
sudo systemctl status vdsm
sudo systemctl status ovirt-engine

# Restart engine (if cache issues)
sudo systemctl restart ovirt-engine

# Check connectivity
ip addr show eth0
sudo ss -tlnp | grep 8080
```



## Conclusion

Congratulations on completing the OLVM Foundations training! You have successfully:

- ✓ Deployed a complete OLVM virtualization platform from scratch
- ✓ Configured two KVM hosts in a cluster
- ✓ Set up networking and shared storage for virtual machines
- ✓ Imported templates and created virtual machines
- ✓ Deployed a multi-tier application using pre-built OVA files
- ✓ Performed live VM migration between hosts with zero downtime

You now have hands-on experience with the core OLVM administration tasks: engine installation, host clustering, networking, storage, VM management, and live migration. This foundation prepares you to confidently discuss and demonstrate Oracle Virtualization with customers and partners.



*This LiveLabs workshop is for learning and evaluation purposes. For production deployments, consult Oracle's official documentation and best practices guides.*


## Learn More

- Oracle Linux Virtualization Manager install lab (official): https://docs.oracle.com/en/learn/olvm-install/index.html 
- Oracle Luna Labs: https://luna.oracle.com/ 




## Acknowledgements

- **Author** - SShawn Kelley, John Priest  
- **Contributors** - Perside Foster
- **Last Updated By/Date** - Perside Foster , April 1, 2026
