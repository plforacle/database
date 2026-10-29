# RHEL to Oracle Linux Migration Workshops

This folder contains separate instructor preparation and student workshops.

- [Instructor workshop](workshops/instructor/index.html): Prepare the reusable RHEL image in two labs, then provide the image URL and OCI access instructions to students.
- [Student workshop](workshops/tenancy/index.html): Complete six main labs on the same VM, covering setup, migration, live patching, and kernel choices. Lab 7 is an optional OSMH activity with an independent Luna option.

The existing `workshops/tenancy` entry opens the student workshop. The instructor workshop has its own manifest and introduction. Retired labs and review records are stored separately in the owner's backup, outside this LiveLabs workshop folder. Lab 4 completes migration, Lab 5 tests Ksplice on RHCK, and Lab 6 explores UEK. The main agenda has 250 minutes of activity including orientation; allow about five hours with provisioning, downloads, and reboots. Optional Lab 7 requires extra time.

## Entry Files

- Student manifest: `workshops/tenancy/manifest.json`
- Student introduction: `introduction/introduction.md`
- Instructor manifest: `workshops/instructor/manifest.json`
- Instructor introduction: `instructor-introduction/instructor-introduction.md`

Students need an OCI environment and the instructor-provided prepared-image PAR URL. Student Lab 1 contains the full compartment, VCN, access-rule, import, launch, and verification instructions. Students do not need to perform instructor Labs 1 and 2 or have Red Hat credentials.

## OSMH Environment

Labs 1 through 6 use `ol-migrate-rhel-source` as OCI practice infrastructure for the on-premises migration scenario. Optional [Lab 7](osmh-oci-quickstart/osmh-oci-quickstart.md) links to the independent [Luna OSMH lab](https://luna.oracle.com/lab/d60f64fb-76b8-4a46-9b90-75ce4ff7e0b5/steps), which students can run during training in its own environment. It also retains the migrated-VM tasks as an alternative when the required permissions are available.

For the migrated-VM option, students need the documented OSMH, software-source, Compute plugin, and IAM permissions, or administrator assistance. The owner reported successful live testing of Labs 1 through 6 and completed optional Lab 7 on October 29, 2026, using `ol-migrate-rhel-source` in a Luna tenancy in Frankfurt. The supplied results confirm the OSMH plugin Running, the managed instance Active, all four sources attached, package inventory visible, and a one-package update work request Succeeded at 100 percent. Apache remained active and returned `MIGRATION_WORKLOAD_OK` after the update. The updated package name and before/after versions were not supplied, so the version comparison was not independently verified. These results cover the workshop's Lab 7 tasks in that environment; they do not establish completion of every step in the separate Luna tutorial.

## Lab Number Mapping

Earlier review and live-test notes are kept in the owner's backup and retain their original lab numbers. Use this mapping when resuming an existing test. Original Labs 1 and 2 are now the two instructor labs.

| Original lab | Student lab | Title |
| --- | --- | --- |
| 3 | 1 | Build and Verify the Migration Environment |
| 4 | 2 | Linux Command-Line Quickstart |
| 5 | 3 | Assess Readiness and Protect the Source |
| 6 | 4 | Migrate to Oracle Linux |
| 7 | Retired | Validate the Migration |
| 8 | 6 | Explore Oracle Linux Kernel Choices |
| 9 | 5 | Explore Rebootless Patching with Ksplice |
| 10 | Retired | Maintain Oracle Linux Packages |
| 11 | Retired | Configure and Troubleshoot Apache |
| 12 | Retired | Manage Application Security |
| 13 | Retired | Back Up and Restore the Application |
| 14 | Retired | Automate Application Health Checks |
| New | 7 | OS Management Hub Quickstart on OCI |

The October 27 live test completed kernel switching and the full Ksplice patch test in the earlier lab order. Ksplice applied 135 patches on the retained Oracle RHCK without rebooting during patching, and the application responded before and after. The revised sequence keeps learners on RHCK for Ksplice before introducing UEK. Existing successful test results remain valid. Lab 7 is optional. Historical review notes are retained in the owner's backup.

## Resource Ownership

Students manage their destination VM, network, OSMH profile, and jobs. Instructor preparation VMs, source images, export objects, buckets, and PARs stay under instructor control. Retain all resources and results for review, including resources from earlier OSMH testing. Documentation changes do not change hosts or cloud resources. Cleanup and unregistration occur only when requested by the workshop owner.
