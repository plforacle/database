# OSMH Lab Replacement Review: October 26, 2026

## Scope and Authoring Status

Authoring mode: draft adaptation for SME review and live testing. Replaced the active Student Labs 10 through 12 at the user's request after Oracle Linux SME review. The teaching sequence is complete in Markdown, with both manifests and supporting references updated. No OSMH installation, registration, patching, or OCI operation was executed during authoring.

The supplied source is `C:/Users/Perside/Documents/GitHub/osmh-onprem-lab`. It is itself labeled draft and has no live-run screenshots. It was read without modification. The adaptation was checked against the Oracle documentation linked below.

## Curriculum and Environment Decisions

| Student lab | Replacement | Active time |
| --- | --- | ---: |
| 10 | Prepare OS Management Hub for On-Premises Management | 25 minutes |
| 11 | Register an On-Premises Oracle Linux Client | 30 minutes |
| 12 | Patch and Verify Oracle Linux with OS Management Hub | 30 minutes |

The workshop remains on-premises centric. Labs 1 through 9 use the existing OCI migration VM as practice infrastructure. Labs 10 through 12 use a separate, real on-premises Oracle Linux client per student and an instructor-prepared shared management station. The service control plane runs in OCI. This distinction is explicit in the introductions, prerequisites, and host checks.

The new labs do not attempt to register an OCI instance using the non-OCI Management Agent procedure. The OSMH evidence belongs to the on-premises client and does not claim management or patching of the earlier OCI migration VM.

The instructor setup guide handles IAM, support entitlement, agent downloads and install keys, the station, mirror capacity and synchronization, client preparation, and the class handoff. A shared agent-installation reference supports station preparation and Student Lab 11. The station is an intentional component of this exercise; current architecture documentation also describes a stationless non-OCI option outside this chosen path.

The planned student agenda is now 410 minutes. Instructor image preparation plus OSMH setup totals 150 planned active minutes. Provisioning, repository synchronization, and job execution require additional elapsed time. These estimates have not been measured in a live class.

## Source Mapping

| Supplied example content | Adapted location | Main changes |
| --- | --- | --- |
| `prepare/prepare.md` | OSMH Instructor Setup; Student Lab 10 | Move tenancy-wide IAM and entitlement work to the instructor; verify student access and actual host assignments |
| `management-station/management-station.md` | OSMH Instructor Setup; Student Lab 10 | Prepare one shared station before class; students check its health and source readiness |
| `agent-install/agent-install.md` | On-Premises Management Agent reference | Keep station/client proxy settings distinct; use private install-key input; validate sudo rules |
| `register-client/register-client.md` | Student Lab 11 | Use a unique per-student client profile and the assigned on-premises host; verify mirrored content and Apache |
| `patch-and-verify/patch-and-verify.md` | Student Lab 12 | Record target package versions and actual job outcomes; preserve explicit no-update and scheduling-only results |
| `troubleshoot-cleanup/troubleshoot-cleanup.md` | Student Lab 12 troubleshooting and evidence tasks | Retain the diagnostics and evidence concepts; omit unregistration, agent removal, and resource deletion |

The former application-security, application-backup, and health-automation labs and their helper file are preserved under `archive/retired-student-labs`. They are excluded from both active menus. Lab 9's onward reference no longer promises the retired security or backup exercises. Historical live-test records retain their original meanings.

The skill's filename-based source fidelity helper reports expected structural differences: the standalone example's five labs and shared procedure were reorganized into three student labs, instructor preparation, and a shared reference inside the larger migration workshop. The table above records content coverage instead of asserting identical filenames or task counts.

## Verification

- PASS: Both manifests resolve. The student menu has twelve contiguous numbered labs; the instructor menu keeps two numbered preparation labs and adds the OSMH setup guide and shared reference.
- PASS: The repository LiveLabs Markdown validator checked all 18 active Markdown files and both launcher/manifest pairs with zero errors. Its Python indentation check was enabled using the installed Python interpreter.
- PASS: Manifest titles and lab H1 headings match. Task and step sequences, local links, variables, copy tags, code fences, and all seven existing image references validate.
- PASS: Student and instructor schedule totals match their listed durations.
- PASS: 164 Bash blocks passed `bash -n` after substituting fixture values for instructional angle-bracket placeholders. Six shell files, including the archived helper, passed syntax checking. No commands were executed against Linux hosts.
- PASS: Retired lab files match the originals after normalizing Git line endings. Existing screenshots and historical QA records were preserved.
- PASS: New OSMH instructions distinguish the on-premises client from the OCI migration VM, keep station bootstrap separate from client proxying, protect install keys, preserve the migration archives, and retain resources.
- PASS: Explicit update outcomes include applied, no update available, and blocked or failed. A future schedule is not reported as an executed update. No package downgrade is used to manufacture an update.
- PASS: Active learner Markdown contains no em dash characters. `git diff --check` passed.
- PASS: Leftover-term scans found no obsolete example host names or retired lab titles in the new OSMH content.

The skill's generic Python QA helper was also run. Its manifest checks passed. Its root-directory and time-label checks still assume every subdirectory is a numbered lab and require the literal `Estimated Time:` label. They flag archive/QA/variables directories, historical documents, and the established `Estimated Lab Time:` labels. Those compatibility findings are separate from the zero-error repository LiveLabs validator result.

FreeSQL is not applicable because these labs contain no database exercises.

## Live Pilot and Screenshots Still Required

Confirm the support subscription, intended OCI region, real on-premises hosts, source visibility, station/client permissions, network connectivity, and actual mirror storage before class. Pilot with a permitted student account and capture the following real evidence:

| Location | Screenshot or evidence to capture |
| --- | --- |
| Instructor setup | Station configuration and Healthy status; associated managed instance Active |
| Lab 10 | Synchronized BaseOS/AppStream mirrors and assigned station connection details, with secrets hidden |
| Lab 11 | Client profile location and station association; client Active; repository URLs identifying the station; passing application checks |
| Lab 12 | Selected update and target version; successful job and package change, or accurate no-update/failed outcome; maintenance schedule and its real execution state; healthy application |

The five new content folders include image directories. No OSMH screenshots were available from the SME example or an authenticated live environment, so no image references were fabricated. Existing migration screenshots were preserved and were not relabeled as evidence from the on-premises client.

Validate the Management Agent installation and privilege rules, current Console labels, inventory refresh timing, mirror downloads, job outcomes, and application checks during the pilot. A successful static check does not establish successful registration or patching. The user can continue the existing migration live test at Student Lab 5, Task 1, independently of this new section.

All OCI resources, agents, profiles, keys, stations, and clients remain untouched by this editing session. Cleanup is excluded from the new OSMH sequence and requires the workshop owner's later request.

## Oracle References Reviewed

- [Supported environments, subscription requirements, and connectivity](https://docs.oracle.com/en-us/iaas/osmh/doc/getstarted.htm)
- [Management station architecture, health, capacity, and mirrors](https://docs.oracle.com/en-us/iaas/osmh/doc/understand-management-stations.htm)
- [Policy Advisor and Management Agent matching](https://docs.oracle.com/en-us/iaas/osmh/doc/policy-advisor.htm)
- [Software source eligibility for non-OCI systems](https://docs.oracle.com/en-us/iaas/osmh/doc/add-vendor-software-sources.htm)
- [Creating a station](https://docs.oracle.com/en-us/iaas/osmh/doc/create-management-station.htm)
- [Registering a station and its agent](https://docs.oracle.com/en-us/iaas/osmh/doc/register-management-station.htm)
- [Creating a client profile](https://docs.oracle.com/en-us/iaas/osmh/doc/create-profile.htm)
- [Registering a non-OCI client](https://docs.oracle.com/en-us/iaas/osmh/doc/register-non-oci-instance.htm)
- [Distinct OCI-instance registration path](https://docs.oracle.com/en-us/iaas/osmh/doc/register-oci-instance.htm)
- [Synchronizing mirrors](https://docs.oracle.com/en-us/iaas/osmh/doc/sync-mirrors.htm)
- [Installing an individual package update](https://docs.oracle.com/en-us/iaas/osmh/doc/install-updates-instance.htm)
- [Immediate and scheduled update jobs](https://docs.oracle.com/en-us/iaas/osmh/doc/create-scheduled-job-instance.htm)
- [Registration diagnostics](https://docs.oracle.com/en-us/iaas/osmh/doc/troubleshoot-instance-station-registration-failure.htm)
