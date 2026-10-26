# RHEL to Oracle Linux Migration Workshops

This folder contains separate instructor preparation and student workshops.

- [Instructor workshop](workshops/instructor/index.html): Prepare the reusable RHEL image in two labs, then provide the image URL and OCI access instructions to students.
- [Student workshop](workshops/tenancy/index.html): Begin at Lab 1, create your network and VM, then complete migration, administration, and on-premises OSMH exercises across ten labs.

The existing `workshops/tenancy` entry now opens the student workshop. The instructor workshop has its own manifest and introduction. The first seven student lab folders retain their names. Package maintenance, Apache troubleshooting, security, application-backup, and automation labs are preserved under `archive/retired-student-labs` and are no longer in the student menu. Labs 8 through 10 cover OSMH.

## Entry Files

- Student manifest: `workshops/tenancy/manifest.json`
- Student introduction: `introduction/introduction.md`
- Instructor manifest: `workshops/instructor/manifest.json`
- Instructor introduction: `instructor-introduction/instructor-introduction.md`

Students need an OCI environment and the instructor-provided prepared-image PAR URL. Student Lab 1 contains the full compartment, VCN, access-rule, import, launch, and verification instructions. Students do not need to perform instructor Labs 1 and 2 or have Red Hat credentials.

## OSMH Environment

Labs 1 through 7 use the OCI migration VM as practice infrastructure for the on-premises migration scenario. Labs 8 through 10 use a separate on-premises Oracle Linux client and an instructor-prepared shared management station. They do not register the OCI VM through a non-OCI agent path.

The instructor menu includes an [OSMH setup guide](osmh-instructor-setup/osmh-instructor-setup.md). It covers IAM, support entitlement, the station, mirror synchronization, per-student clients, and the private handoff. The [agent installation reference](osmh-agent-install/osmh-agent-install.md) is shared by station preparation and client registration.

## Lab Number Mapping

The October 25 live-test notes and older QA records retain their original lab numbers. Use this mapping when resuming an existing test. Original Labs 1 and 2 are now the two instructor labs.

| Original lab | Student lab | Title |
| --- | --- | --- |
| 3 | 1 | Build and Verify the Migration Environment |
| 4 | 2 | Linux Command-Line Quickstart |
| 5 | 3 | Assess Readiness and Protect the Source |
| 6 | 4 | Migrate to Oracle Linux |
| 7 | 5 | Validate the Migration |
| 8 | 6 | Explore Oracle Linux Kernel Choices |
| 9 | 7 | Explore Rebootless Patching with Ksplice |
| 10 | Retired | Maintain Oracle Linux Packages |
| 11 | Retired | Configure and Troubleshoot Apache |
| 12 | Retired | Manage Application Security |
| 13 | Retired | Back Up and Restore the Application |
| 14 | Retired | Automate Application Health Checks |
| New | 8 | Prepare OS Management Hub for On-Premises Management |
| New | 9 | Register an On-Premises Oracle Linux Client |
| New | 10 | Patch and Verify Oracle Linux with OS Management Hub |

The live test paused before original Lab 7. Resume at Student Lab 5, Task 1: Capture post-migration evidence, using the existing migrated VM. Renumbering does not require rebuilding it.

## Resource Ownership

Students manage their own destination resources. Instructor preparation VMs, source images, export objects, buckets, and PARs stay under instructor control. On-premises clients, the shared station, profiles, install keys, and all OCI resources are retained for review. No cleanup or unregistration is part of the new OSMH labs; perform it only when requested by the workshop owner.
