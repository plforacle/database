# VM Live Migration
## Introduction

### Overview
Live migration allows you to move a running virtual machine from one KVM host to another without downtime. This is a key capability for maintenance operations, load balancing, and high availability scenarios.

**Prerequisites for VM Migration:**
- Both hosts must be in the same cluster
- Both hosts must have access to the shared storage domain
- The destination host must have sufficient resources (CPU, memory)
- The logical network used by the VM must be available on both hosts

**Lab-Specific Prerequisite:** After the extensive infrastructure changes made during this lab (network creation, storage domain attachment, VM provisioning), the engine's internal cache may be out of sync with the current host state. Restart the engine before attempting migration:
```bash
   sudo systemctl restart ovirt-engine
```

   Wait 2–3 minutes for the engine to fully restart, then refresh the Administration Portal in Firefox before proceeding.

   **Why this is needed:** The oVirt engine caches host capabilities, network topology, and storage connectivity in memory. Over a long session with many configuration changes, this cache can become stale — causing a "Could not fetch data needed for VM migrate operation" error. Restarting the engine forces a full re-query of all hosts via VDSM, rebuilding an accurate view of the cluster. This does not affect running VMs, as VDSM on each host manages them independently.

**Learning Objectives (Exam: 1Z0-1170 Alignment):**
- Perform live VM migration between hosts in a cluster (Exam Topic: Administrating Virtual Machines).
- Understand migration prerequisites and verify successful migration (Exam Topic: Host & Cluster Management).

## Verify Migration Prerequisites

1. Switch to the browser within the VNC session.

1. Log in to the Administration Portal.

1. Using the side navigation menu, go to Compute and click Hosts.

1. Verify both hosts show status **Up**:
   - olkvm01: Up
   - olkvm02: Up

1. Using the side navigation menu, go to Compute and click Virtual Machines.

1. Verify the ol9-mysql VM is running on olkvm01:
   - Check the **Host** column shows `olkvm01`
   - Check the **Status** column shows `Up` (running)

## Perform Live Migration

1. In the Virtual Machines pane, select the **ol9-mysql** virtual machine by clicking on it.

1. Click the **Migrate** button in the toolbar.

   The Migrate Virtual Machine(s) dialog box opens.

1. In the Migrate Virtual Machine(s) dialog:
   - **Select Destination Host**: Choose `Select destination host automatically` or select `olkvm02` manually from the dropdown
   
   **Automatic vs Manual Selection:**
   - **Automatic**: OLVM selects the best host based on available resources and scheduling policies
   - **Manual**: You specify the exact destination host
   
   For this lab, select **olkvm02** manually to ensure the VM moves to the second host.

1. Click **Migrate** to start the migration.

1. Monitor the migration progress:
   - The VM status will change to **Migrating From**
   - A progress indicator shows the migration percentage
   - You can also click on the VM name and view the **Events** tab for detailed progress

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

1. Wait for the migration to complete (typically 30-60 seconds for this VM).

## Verify Migration Success

1. In the Virtual Machines pane, verify the ol9-mysql VM now shows:
   - **Host**: `olkvm02`
   - **Status**: `Up`

1. Click on the **ol9-mysql** VM name to open the details view.

1. Click the **Events** tab to see the migration history:
   - "VM ol9-mysql started migration on Host olkvm01"
   - "VM ol9-mysql was migrated to Host olkvm02"

1. Verify the VM is still accessible and functioning:

   From the **olvm engine** terminal (via VNC), test connectivity to the migrated VM:
```bash
   ping -c 3 10.0.10.100
```
   
   **Note:** You are pinging from the olvm engine, not from inside the VM. This confirms the VM is reachable at the same IP address even though it now runs on a different physical host (olkvm02).

1. Verify the database is still accessible:
```bash
   ssh opc@10.0.10.100 "mysql -u empapp -pWelcome#123 employee_db -e 'SELECT COUNT(*) FROM employees;'"
```
   
   The output should show `8`, confirming the database is operational after migration.

## Verify from Web Application

1. From the **olvm engine** terminal, confirm the web application still connects to the migrated database:
```bash
   curl -s http://10.0.10.101:8080/employee-app/employees | grep -c "<tr>"
```
   
   The output should show `9` (1 header + 8 employee rows), confirming end-to-end connectivity.

---

**Exam relevance (1Z0-1170):** Live migration is a critical OLVM capability tested in the "Administrating Virtual Machines" domain. You should understand:
- Migration prerequisites (shared storage, network, host compatibility)
- How to initiate migration via the Administration Portal
- How to verify successful migration
- Use cases: host maintenance, load balancing, failure recovery

**Additional Migration Options:**

| Migration Type | Description | Use Case |
|----------------|-------------|----------|
| **Live Migration** | VM stays running during move | Production workloads, zero downtime |
| **Offline Migration** | VM is stopped, then moved | Large VMs, guaranteed consistency |
| **Automatic Migration** | OLVM moves VMs based on policies | Load balancing, host maintenance mode |

**Tip:** Before performing maintenance on a host, use **Compute → Hosts → [select host] → Management → Maintenance**. OLVM will automatically migrate all VMs to other hosts in the cluster.