# Configure Storage
## Introduction

## Add a Fibre Channel Data Domain

1. Oracle Linux Virtualization Manager uses a centralized storage system for virtual machine disk images, ISO files, and snapshots. In this tutorial, two OCI Block Volumes are attached to the KVM Host and appear as Fibre Channel LUNs.
   
   **What is a storage domain:**
   
   A storage domain is a logical entity in OLVM that represents a storage resource where VM disks, templates, ISOs, and snapshots are stored.
   
   **Types of storage domains:**
   
   1. **Data Domain** (what we're creating)
      - Stores VM disk images
      - Stores templates
      - Stores snapshots
      - Can have multiple data domains per data center
   
   2. **ISO Domain**
      - Stores ISO files (installation media)
      - Optional (can upload ISOs to data domain instead)
      - One per data center
   
   3. **Export Domain**
      - Used for importing/exporting VMs and templates
      - Optional
      - Can be shared between data centers
   
   **Supported storage types:**
   
   - **NFS** - Network File System (most common for smaller deployments)
   - **iSCSI** - Block storage over IP networks
   - **Fibre Channel** - High-speed block storage (what we're using)
   - **Local Storage** - Direct-attached disks (no HA or migration)
   - **GlusterFS** - Distributed file system (scalable storage)
   - **POSIX** - Any POSIX-compliant filesystem
   
   **Why Fibre Channel in this lab:**
   - OCI Block Volumes are attached to the host
   - They appear to the host as Fibre Channel LUNs
   - Provides realistic enterprise storage experience
   - High performance for VM disk I/O
   
   **LUN = Logical Unit Number:**
   - Each Block Volume appears as a separate LUN
   - LUNs are discovered automatically by OLVM
   - Multiple hosts can access the same LUN (shared storage)
   
   **Shared storage requirement:**
   - For VM migration and HA, storage must be accessible by all hosts in cluster
   - Fibre Channel and iSCSI provide shared block storage
   - NFS provides shared file storage
   - Local storage does NOT support migration or HA
   
   **Exam relevance (1Z0-1170):** Storage domain types, configuration, and requirements are heavily tested in the "Storage Management" domain. You must understand when to use each storage type.

1. Go back to the OLVM Manager

   ![](images/olvm-manager-3.png)

1. Using the side navigation menu, go to Storage and click Domains.

   The Storage Domains pane opens.

1. On the Storage Domains pane, click the New Domain button.

   The New Domain dialog box opens.

1. For the Name field, enter a name for the data domain.
   ```
   amd-storage-domain-01
   ```

1. From the Data Center drop-down list, select the Default option in the drop-down list.

1. From the Domain Function drop-down list, select the Data option in the drop-down list.

1. From the Storage Type drop-down list, select Fibre Channel.

1. For the Host to Use drop-down list, select the olkvm01 host.

1. When selecting Fibre Channel as the Storage Type, the New Domain dialog box automatically displays the known targets with unused LUNs.

1. Click Add next to the first LUN ID.

1. Click OK.

    You can click Tasks in the upper right corner of the UI to monitor the various processing steps that this step completes when attaching the FC data domain to the data center.

1. Wait for the Cross Data Center Status to show as Active before continuing the tutorial.