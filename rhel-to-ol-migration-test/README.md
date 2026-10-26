# RHEL to Oracle Linux Migration Workshops

This folder contains separate instructor preparation and student workshops.

- [Instructor workshop](workshops/instructor/index.html): Prepare the reusable RHEL image in two labs, then provide the image URL and OCI access instructions to students.
- [Student workshop](workshops/tenancy/index.html): Begin at Lab 1, create your network and VM, then complete the migration and administration exercises across twelve labs.

The existing `workshops/tenancy` entry now opens the student workshop. The instructor workshop has its own manifest and introduction. Lab folders retain their names so existing images, helper files, and local links continue to work.

## Entry Files

- Student manifest: `workshops/tenancy/manifest.json`
- Student introduction: `introduction/introduction.md`
- Instructor manifest: `workshops/instructor/manifest.json`
- Instructor introduction: `instructor-introduction/instructor-introduction.md`

Students need an OCI environment and the instructor-provided prepared-image PAR URL. Student Lab 1 contains the full compartment, VCN, access-rule, import, launch, and verification instructions. Students do not need to perform instructor Labs 1 and 2 or have Red Hat credentials.

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
| 10 | 8 | Maintain Oracle Linux Packages |
| 11 | 9 | Configure and Troubleshoot Apache |
| 12 | 10 | Manage Application Security |
| 13 | 11 | Back Up and Restore the Application |
| 14 | 12 | Automate Application Health Checks |

The live test paused before original Lab 7. Resume at Student Lab 5, Task 1: Capture post-migration evidence, using the existing migrated VM. Renumbering does not require rebuilding it.

## Resource Ownership

Students manage their own destination resources. Instructor preparation VMs, source images, export objects, buckets, and PARs stay under instructor control. Cleanup is optional and is never required for migration success.
