# Appendices
## Introduction
## Appendix 1: Exam Mapping and Next Steps

### How This Lab Maps to Exam: 1Z0-1170 Objectives

**Exam Coverage Summary:**

This lab covers approximately **30-40%** of the exam objectives.

### Lab Part to Exam Topic Mapping

| Lab Part | Exam Domain | Coverage | Key Skills Practiced |
|----------|-------------|----------|----------------------|
| Part 1: Infrastructure | Installation & Configuration | ⭐⭐⭐⭐⭐ | Repository setup, engine installation, portal access |
| Part 2: Hosts | Host & Cluster Management | ⭐⭐⭐⭐⭐ | SSH keys, VDSM, host status verification |
| Part 3: Networking, Storage, and VM  | Networking, Storage, VM Administration | ⭐⭐⭐⭐⭐ | Logical networks, storage domains, VM creation, external access |
| Part 4: Application Tier | Real-world validation (not exam-tested) | ⭐⭐☆☆☆ | Application deployment, troubleshooting |

### Fully Covered Topics
- ✓ Installation & Configuration
- ✓ Host & Cluster Management
- ✓ Networking & Storage
- ✓ VM Administration

### Partially Covered Topics
- ◐ Monitoring & Troubleshooting

### Topics Requiring Additional Study

To achieve complete exam readiness, supplement this lab with study on:
- User and role management in OLVM
- Backup and disaster recovery procedures
- Advanced high availability (HA) configurations
- Self-hosted engine deployment
- Performance optimization and tuning

> **Exam Tip:** The exam tests both conceptual knowledge and practical skills. This lab gives you hands-on experience, complement it with Oracle's official documentation and training materials for theoretical understanding.

---

## Appendix 2: Quick Reference - Essential OLVM Commands

### Overview

This section provides a condensed reference of the most important commands and procedures used throughout the lab. Use this as a quick lookup guide during the lab or as a study aid for exam preparation.

### Installation & Engine Setup

```bash
# Enable OLVM repositories
sudo dnf install -y oracle-ovirt-release-45-el8

# Install engine
sudo dnf install -y ovirt-engine

# Configure engine (will prompt for admin password)
sudo engine-setup --accept-defaults

# Access portal
https://<fqdn>/ovirt-engine
```

### Host & Cluster Management

```bash
# Get engine SSH public key (for adding hosts)
ssh-keygen -y -f /etc/pki/ovirt-engine/keys/engine_id_rsa

# Verify VDSM service on host
sudo systemctl status vdsm

# Disable SELinux (if needed for troubleshooting)
sudo setenforce 0
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

### Networking & VM Operations

- **Create logical network:** Portal > Network > Networks > New
- **Import template:** Portal > Compute > Templates > Import
- **Test connectivity:** `ping 10.0.10.1` or `ping 8.8.8.8`


### Troubleshooting

```bash
# Check logs
sudo tail -50 /var/log/ovirt-engine/engine.log
sudo tail -50 /var/log/vdsm/vdsm.log

# Check service status
sudo systemctl status vdsm
sudo systemctl status ovirt-engine

# Check connectivity
ip addr show eth0
sudo ss -tlnp | grep 8080
```

---

## Next Steps

### For Exam Preparation

| Goal | Recommended Actions |
|------|---------------------|
| **Exam Preparation** | Review exam mapping above, practice with official Oracle training materials |
| **Deeper OLVM Skills** | Explore snapshots, cloning, HA configurations, backup/recovery |
| **Automation** | Learn Ansible for OLVM, explore CI/CD integration |

### Explore Additional Features

- Create VM snapshots before making changes
- Clone VMs to create additional web servers
- Practice VM migration between hosts
- Monitor resource usage (CPU, memory, disk)
- Set up high availability configurations
- Configure backup and recovery procedures

### Learn More

- Explore Oracle Linux Virtualization Manager documentation
- Study Java EE / Jakarta EE application development
- Learn about database optimization and indexing
- Understand load balancing and scaling strategies
- Practice backup and recovery procedures
- Study container technologies and migration paths
- Explore automation with Ansible
- Learn about CI/CD integration

---

## Conclusion

Congratulations on completing this comprehensive OLVM lab! You have successfully:

- ✓ Deployed a complete OLVM virtualization platform
- ✓ Configured KVM hosts in a cluster
- ✓ Set up networking and storage for virtual machines
- ✓ Created and managed virtual machines
- ✓ (Optional) Deployed a multi-tier application

### Key Achievements

- Gained hands-on experience with 30-40% of Exam: 1Z0-1170 objectives
- Built practical skills in OLVM installation, configuration, and administration
- Demonstrated real-world virtualization platform capabilities

### Remember

**The Luna lab environment expires after your session.** Make sure to:
- Note down key learnings and configurations
- Take screenshots of important setups
- Review the exam mapping section for areas requiring additional study

**Thank you for completing this lab, and good luck with your Oracle Linux Virtualization Manager certification journey!**

---

*This lab is for learning, testing, and evaluation purposes. For production deployments, consult Oracle's official documentation and best practices guides.*