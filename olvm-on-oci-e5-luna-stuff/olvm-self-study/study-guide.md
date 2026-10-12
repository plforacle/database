# OLVM Study Guide and Coverage Map

## Introduction

Use this guide to connect your Lab 7 training with the seven self-study modules. The table covers all 38 subtopics you supplied. A question checks reasoning; it does not prove that a learner can perform the corresponding operation.

Estimated Time: 30-45 minutes for planning, followed by the study and practice activities

### Objectives

- Locate relevant clips in the eleven supplied transcripts
- Identify which topics Lab 7 practices and which require further study
- Separate conceptual quiz coverage from practical readiness
- Keep release differences and source gaps visible

## Task 1: Map the Objectives to the Training

Question IDs refer to the new self-study bank. Source locations identify transcript clip starting lines; the source-map JSON additionally records original quiz-bank file and question line. A clip is a review location, not an exact quotation supporting every word of a revised question.

| Subtopic | Transcript review location | Questions | Lab 7 connection | Coverage note |
|---|---|---|---|
| Describe Oracle Virtualization Architecture | `olvm-architecture-overview.txt`: OLVM Overview (line 3) | 20: 1.001, 1.002, 1.003, 1.004, 1.005, 1.006, 1.007, 1.008, 1.009, 1.010, 1.011, 1.012, 1.013, 1.014, 1.015, 1.016, 1.017, 1.018, 1.019, 1.020 | Task 1 | Transcript concepts and practice questions; use checklist for practical readiness |
| Describe Data Warehouse and Databases | `olvm-architecture-overview.txt`: Data Warehouse and Databases (line 158) | 4: 1.026, 1.027, 1.028, 1.029 | Task 8 | Transcript concepts and practice questions; use checklist for practical readiness |
| Describe Hosted-Engine | `olvm-architecture-overview.txt`: Architecture and hosted engine (line 81) | 5: 1.021, 1.022, 1.023, 1.024, 1.025 | Task 1 (architecture only) | Transcript concepts and practice questions; use checklist for practical readiness |
| Describe Administration Interfaces | `olvm-architecture-overview.txt`: Administration Interfaces (line 169) | 17: 1.030, 1.031, 1.032, 1.033, 1.034, 1.035, 1.036, 1.037, 1.038, 1.039, 1.040, 1.041, 1.042, 1.043, 1.044, 1.045, 1.046 | Tasks 1 and 8 | Transcript concepts and practice questions; use checklist for practical readiness |
| Explain Data Centers, Clusters and Hosts | `olvm-core-components.txt`: Data Centers (line 5); `olvm-core-components.txt`: Clusters (line 48); `olvm-core-components.txt`: Hosts (line 110) | 22: 1.047, 1.048, 1.049, 1.050, 1.051, 1.052, 1.053, 1.054, 1.055, 1.056, 1.057, 1.058, 1.059, 1.060, 1.061, 1.062, 1.063, 1.064, 1.065, 1.066, 1.067, 1.068 | Tasks 1 and 2 | Transcript concepts and practice questions; use checklist for practical readiness |
| Explain Storage Pool Manager, Networks and Storage | `olvm-core-components.txt`: Storage Pool Manager (line 140); `olvm-core-components.txt`: Storage (line 209) | 11: 1.069, 1.070, 1.071, 1.072, 1.073, 1.074, 1.075, 1.076, 1.077, 1.078, 1.079 | Tasks 2, 3, 5, and 6 | Transcript concepts and practice questions; use checklist for practical readiness |
| Explain Event Logging and Notifications | `olvm-core-components.txt`: Events and Notifications (line 250) | 6: 1.080, 1.081, 1.082, 1.083, 1.084, 1.085 | Tasks 2 and 5 (events during operations) | Transcript concepts and practice questions; use checklist for practical readiness |
| Explain Engine and KVM Host Requirements | `olvm-install-engine.txt`: Engine Host Prerequisites (line 3); `olvm-kvmhost-install.txt`: Host Prerequisites (line 6); `olvm-kvmhost-install.txt`: Host repositories and packages (line 204) | 35: 2.001, 2.002, 2.003, 2.004, 2.005, 2.006, 2.007, 2.008, 2.009, 2.010, 2.011, 2.012, 2.013, 2.014, 2.015, 2.016, 2.017, 2.018, 2.019, 2.020, 2.021, 2.022, 2.023, 2.024, 2.025, 2.026, 2.027, 2.028, 2.029, 2.030, 2.031, 2.032, 2.033, 2.034, 2.035 | Tasks 1 and 2 | Transcript concepts and practice questions; use checklist for practical readiness |
| Explore Engine and KVM Host Firewall Requirements | `olvm-install-engine.txt`: Engine Firewall Requirements (line 50); `olvm-kvmhost-install.txt`: Host Firewall (line 39) | 12: 2.036, 2.037, 2.038, 2.039, 2.040, 2.041, 2.042, 2.043, 2.044, 2.045, 2.046, 2.047 | Tasks 1 and 2 | Transcript concepts and practice questions; use checklist for practical readiness |
| Perform Engine Setup | `olvm-install-engine.txt`: Engine repositories and packages (line 273); `olvm-install-engine.txt`: Configure Engine (line 326); `olvm-install-engine.txt`: Browser CA trust (line 422) | 17: 2.048, 2.049, 2.050, 2.051, 2.052, 2.053, 2.054, 2.055, 2.056, 2.057, 2.058, 2.059, 2.060, 2.061, 2.062, 2.063, 2.064 | Task 1 | Transcript concepts and practice questions; use checklist for practical readiness |
| Perform Self-Hosted Engine Setup | `olvm-architecture-overview.txt`: Architecture and hosted engine (line 81) | 2: 2.079, 2.080 | No full deployment activity | Partial: planning and HA-agent concepts only; full setup procedure missing |
| Add a KVM Host | `olvm-kvmhost-install.txt`: Register a Host (line 243) | 14: 2.065, 2.066, 2.067, 2.068, 2.069, 2.070, 2.071, 2.072, 2.073, 2.074, 2.075, 2.076, 2.077, 2.078 | Task 2 | Transcript concepts and practice questions; use checklist for practical readiness |
| Describe OLVM Storage | `olvm-storage-domain.txt`: Storage Overview (line 6); `olvm-storage-domain.txt`: Gluster Storage (line 201) | 6: 3.001, 3.002, 3.003, 3.004, 3.005, 3.006 | Tasks 3 and 6 | Transcript concepts and practice questions; use checklist for practical readiness |
| Configure Storage Domains | `olvm-storage-domain.txt`: Storage Domains (line 27); `olvm-storage-domain.txt`: Local Storage (line 94); `olvm-storage-domain.txt`: NFS Storage (line 125) | 41: 3.007, 3.008, 3.009, 3.010, 3.011, 3.012, 3.013, 3.014, 3.015, 3.016, 3.017, 3.018, 3.019, 3.020, 3.021, 3.022, 3.023, 3.024, 3.025, 3.026, 3.027, 3.028, 3.029, 3.030, 3.031, 3.032, 3.033, 3.034, 3.035, 3.036, 3.037, 3.038, 3.039, 3.040, 3.041, 3.042, 3.043, 3.044, 3.045, 3.046, 3.047 | Tasks 3 and 6 | Transcript concepts and practice questions; use checklist for practical readiness |
| Explain OLVM Networking Recommendations | `olvm-network-management.txt`: Network Recommendations Part 1 (line 5) | 9: 3.048, 3.049, 3.050, 3.051, 3.052, 3.053, 3.054, 3.055, 3.056 | Task 3 | Transcript concepts and practice questions; use checklist for practical readiness |
| Configure Bonds, VLANs and Logical Networks | `olvm-network-management.txt`: Network Recommendations Part 2 (line 62); `olvm-network-management.txt`: Custom vNIC HW-ADDR (line 169) | 28: 3.057, 3.058, 3.059, 3.060, 3.061, 3.062, 3.063, 3.064, 3.065, 3.066, 3.067, 3.068, 3.069, 3.070, 3.071, 3.072, 3.073, 3.074, 3.075, 3.076, 3.077, 3.078, 3.079, 3.080, 3.081, 3.082, 3.083, 3.084 | Tasks 3, 4, and 5 | Transcript concepts and practice questions; use checklist for practical readiness |
| Associate Logical Networks with Virtual Functions | `olvm-network-management.txt`: Associate Virtual Functions (line 154) | 8: 3.085, 3.086, 3.087, 3.088, 3.089, 3.090, 3.091, 3.092 | No dedicated SR-IOV activity | Transcript concepts and practice questions; use checklist for practical readiness |
| Configure a VM vNIC with a Specific HW-ADDR | `olvm-network-management.txt`: Custom vNIC HW-ADDR (line 169) | 3: 3.093, 3.094, 3.095 | Task 3 (vNIC fundamentals) | Transcript concepts and practice questions; use checklist for practical readiness |
| Manage Virtual Machines and Their Storage | `olvm-admin-vm.txt`: VM Storage (line 5); `olvm-admin-vm.txt`: Create and Manage VMs (line 70); `olvm-admin-vm.txt`: OVA Export (line 379) | 31: 4.001, 4.002, 4.003, 4.004, 4.005, 4.006, 4.007, 4.008, 4.009, 4.010, 4.011, 4.012, 4.013, 4.014, 4.015, 4.016, 4.017, 4.018, 4.019, 4.020, 4.021, 4.022, 4.023, 4.024, 4.025, 4.026, 4.027, 4.028, 4.029, 4.030, 4.031 | Tasks 3, 4, 5, 6, and 9 | Transcript concepts and practice questions; use checklist for practical readiness |
| Describe Guest Agents and Drivers | `olvm-admin-vm.txt`: Guest Agents and Drivers (line 199) | 5: 4.032, 4.033, 4.034, 4.035, 4.036 | Tasks 3 and 9 | Transcript concepts and practice questions; use checklist for practical readiness |
| Create and Manage VM Templates | `olvm-admin-vm.txt`: Templates (line 163) | 6: 4.045, 4.046, 4.047, 4.048, 4.049, 4.050 | Task 3 | Transcript concepts and practice questions; use checklist for practical readiness |
| Describe VM Administrator Roles | `olvm-admin-vm.txt`: VM Administrator Roles (line 218) | 4: 4.037, 4.038, 4.039, 4.040 | Task 10 | Transcript concepts and practice questions; use checklist for practical readiness |
| Describe VM Portal | `olvm-admin-vm.txt`: VM Portal (line 235); `olvm-architecture-overview.txt`: Administration Interfaces (line 169) | 4: 4.041, 4.042, 4.043, 4.044 | Tasks 1 and 10 | Transcript concepts and practice questions; use checklist for practical readiness |
| Perform Administrator Tasks | `olvm-user-mamangement.txt`: Administrator Tasks (line 6) | 4: 5.001, 5.002, 5.003, 5.004 | Tasks 2, 3, and 10 | Transcript concepts and practice questions; use checklist for practical readiness |
| Manage Users and Roles | `olvm-user-mamangement.txt`: Users and Roles (line 39); `olvm-user-mamangement.txt`: Roles and Privileges (line 366) | 16: 5.005, 5.006, 5.007, 5.008, 5.009, 5.010, 5.011, 5.012, 5.013, 5.014, 5.015, 5.016, 5.017, 5.018, 5.019, 5.020 | Task 10 | Transcript concepts and practice questions; use checklist for practical readiness |
| Manage Groups | `olvm-user-mamangement.txt`: Groups (line 108) | 11: 5.021, 5.022, 5.023, 5.024, 5.025, 5.026, 5.027, 5.028, 5.029, 5.030, 5.031 | Task 10 (permissions foundation) | Transcript concepts and practice questions; use checklist for practical readiness |
| Manage Account Settings | `olvm-user-mamangement.txt`: Account Settings (line 139) | 4: 5.032, 5.033, 5.034, 5.035 | Task 10 | Transcript concepts and practice questions; use checklist for practical readiness |
| Manage Quotas | `olvm-user-mamangement.txt`: Quotas (line 156) | 10: 5.036, 5.037, 5.038, 5.039, 5.040, 5.041, 5.042, 5.043, 5.044, 5.045 | No dedicated quota activity | Transcript concepts and practice questions; use checklist for practical readiness |
| Optimize Clusters, Hosts and VMs | `olvm-optimization-practice.txt`: Optimize Clusters Hosts and VMs (line 6) | 21: 6.001, 6.002, 6.003, 6.004, 6.005, 6.006, 6.007, 6.008, 6.009, 6.010, 6.011, 6.012, 6.013, 6.014, 6.015, 6.016, 6.017, 6.018, 6.019, 6.020, 6.021 | Tasks 2, 5, and 8 (foundations) | Transcript concepts and practice questions; use checklist for practical readiness |
| Configure Highly Available Hosts and VMs | `olvm-optimization-practice.txt`: Highly Available Hosts (line 89); `olvm-optimization-practice.txt`: Highly Available VMs (line 138) | 11: 6.022, 6.023, 6.024, 6.025, 6.026, 6.027, 6.028, 6.029, 6.030, 6.031, 6.032 | Task 5 (planned migration; not full HA setup) | Transcript concepts and practice questions; use checklist for practical readiness |
| Optimize VM Performance | `olvm-optimization-practice.txt`: VM Performance (line 166) | 6: 6.033, 6.034, 6.035, 6.036, 6.037, 6.038 | Task 8 (monitoring foundation) | Transcript concepts and practice questions; use checklist for practical readiness |
| Hot-Plug Devices | `olvm-optimization-practice.txt`: Hot-Plug Devices (line 183) | 9: 6.039, 6.040, 6.041, 6.042, 6.043, 6.044, 6.045, 6.046, 6.047 | No dedicated hot-plug activity | Transcript concepts and practice questions; use checklist for practical readiness |
| Describe Log Files and Event Management | `olvm-manage-events-logs.txt`: Log Files Overview (line 5); `olvm-manage-events-logs.txt`: Event Notification (line 93); `olvm-manage-events-logs.txt`: SNMP Traps (line 126) | 39: 6.048, 6.049, 6.050, 6.051, 6.052, 6.053, 6.054, 6.055, 6.056, 6.057, 6.058, 6.059, 6.060, 6.061, 6.062, 6.063, 6.064, 6.065, 6.066, 6.067, 6.068, 6.069, 6.070, 6.071, 6.072, 6.073, 6.074, 6.075, 6.076, 6.077, 6.078, 6.079, 6.080, 6.081, 6.082, 6.083, 6.084, 6.085, 6.086 | Tasks 2, 5, and 7 (diagnostics) | Transcript concepts and practice questions; use checklist for practical readiness |
| Monitoring and Observability | `olvm-manage-events-logs.txt`: Monitoring and Observability (line 58); `olvm-manage-events-logs.txt`: PostgreSQL diagnostic commands (line 322); `olvm-manage-events-logs.txt`: Grafana and Data Warehouse (line 177) | 14: 6.087, 6.088, 6.089, 6.090, 6.091, 6.092, 6.093, 6.094, 6.095, 6.096, 6.097, 6.098, 6.099, 6.100 | Task 8, optional walkthrough | Transcript concepts and practice questions; use checklist for practical readiness |
| Back Up the Manager | `olvm-srecovery-management.txt`: Backing Up the Manager (line 5) | 13: 7.001, 7.002, 7.003, 7.004, 7.005, 7.006, 7.007, 7.008, 7.009, 7.010, 7.011, 7.012, 7.013 | Task 7 | Transcript concepts and practice questions; use checklist for practical readiness |
| Restore a Manager Backup | `olvm-srecovery-management.txt`: Restoring the Manager (line 65); `olvm-srecovery-management.txt`: Migrating the Data Warehouse (line 118); `olvm-srecovery-management.txt`: Engine Cleanup (line 251) | 17: 7.014, 7.015, 7.016, 7.017, 7.018, 7.019, 7.020, 7.021, 7.022, 7.023, 7.024, 7.025, 7.026, 7.027, 7.028, 7.029, 7.030 | Task 7 (backup foundation; no full restore) | Transcript concepts and practice questions; use checklist for practical readiness |
| Rename the Engine | `olvm-srecovery-management.txt`: Renaming the Engine (line 151) | 5: 7.031, 7.032, 7.033, 7.034, 7.035 | No dedicated rename activity | Transcript concepts and practice questions; use checklist for practical readiness |
| Explain Disaster Recovery Solutions | `olvm-srecovery-management.txt`: Disaster Recovery (line 166) | 10: 7.036, 7.037, 7.038, 7.039, 7.040, 7.041, 7.042, 7.043, 7.044, 7.045 | Tasks 5, 7, and 9 (foundations; no complete DR deployment) | Transcript concepts and practice questions; use checklist for practical readiness |

## Task 2: Check Practical Readiness

Use an appropriate disposable practice environment for configuration and recovery exercises. For each item, be able to explain the purpose, prerequisites, expected result, and where to investigate a failure. Mark an item complete only after demonstrating it or completing an instructor-reviewed exercise.

| Objective area | Readiness check |
|---|---|
| Architecture | Draw engine, database, DWH, host agents, KVM/QEMU, clusters, storage, and network relationships; explain standalone versus hosted engine. |
| Installation | Explain release-aligned prerequisites and firewall paths; perform engine setup and add a host; verify portal access, certificate trust, and host Up status. Complete a separate self-hosted deployment walkthrough because the supplied sources omit the full procedure. |
| Storage and networking | Create and validate a domain; explain NFS permissions, iSCSI discovery and multipathing; map a logical network to hosts; explain bonds and VLAN paths; study VF association and verify a unique custom MAC. |
| VM administration | Create or import a VM, validate its vNIC mapping and service readiness, explain disk allocation, verify agents and drivers, prepare a template, and distinguish VM Portal access from VM administration roles. |
| Users and permissions | Choose the correct identity path; create or select a user; assign an appropriate scoped role; test access; explain group membership, account settings, and quota modes. |
| Optimization and observability | Explain overcommitment, KSM, ballooning, pinning, I/O threads, and huge-page tradeoffs; distinguish fencing and HA restart from planned migration; check hot-plug support; locate relevant logs and correlate guest, host, and historical metrics. |
| Recovery | Create and verify an engine backup; explain VM-disk backup boundaries; complete a restore exercise and validate services; plan engine rename dependencies; compare DR designs, surviving-resource requirements, and recovery testing. |

## Task 3: Review the Lab 7 Connections

| Lab 7 activity | Reinforcement in this bank |
|---|---|
| Task 1: Engine and architecture | Modules 1 and 2: component roles, deployment decisions, setup, and trust |
| Task 2: KVM hosts | Modules 1, 2, and 6: host stack, registration, readiness, and diagnostics |
| Task 3: Network, storage, and test VM | Modules 3 and 4: host mappings, domains, vNICs, and templates |
| Task 4: Application tier | Module 4: OVA integration, network mapping, and service dependencies |
| Task 5: Host live migration | Modules 1, 3, and 6: shared access, destination networks, maintenance, and HA distinctions |
| Task 6: Storage live migration | Module 3: destination readiness, capacity, disk location, and workload validation |
| Task 7: Engine backup | Module 7: scopes, privileged scheduling, artifacts, logs, and restore boundaries |
| Task 8: Optional Grafana | Modules 1 and 6: historical data, dashboards, resource pressure, and data-source access |
| Task 9: Snapshots | Module 4: optional memory capture, guest coordination, preview/commit, and cleanup |
| Task 10: Users and roles | Modules 4 and 5: identity paths, authentication, role scope, and access verification |

## Task 4: Resolve Source Gaps and Version Differences

The recorded course uses OLVM 4.4-era examples on Oracle Linux 8. Lab 7 uses OLVM 4.5 and includes Keycloak and legacy JDBC paths. Repository names, module commands, account configuration, UI controls, backup component scopes, and supported resource limits must be matched to the actual environment. The bank qualifies recorded-course details and replaces many historical-number questions with capacity-planning decisions.

The self-hosted engine objective is only partially covered: architecture, shared storage, and HA agents are available, but a full deployment walkthrough is absent. HA host and VM theory, restore, rename, quotas, SR-IOV, and hot-plug topics appear in the transcripts but are not complete hands-on activities in Lab 7. Arrange practical review for these topics rather than treating quiz completion as a substitute.

The original practice bank contains 578 questions. This new bank selects 500, revises ambiguous or sample-specific content, converts retained multiple-answer questions into single-choice questions, and balances correct-answer positions. It is an adapted training assessment, not 500 newly discovered exam questions. The source-map and question-bank JSON files provide authoring traceability; they also contain the answer key, so students should use the rendered quiz for a first attempt.

[Return to the bank introduction](../workshops/olvm-study/index.html?lab=olvm-self-study)

## Acknowledgements

- **Author** - OLVM training team
- **Sources** - User-supplied transcripts, eleven-section quiz bank, and Lab 7
- **Last Updated** - October 2026
