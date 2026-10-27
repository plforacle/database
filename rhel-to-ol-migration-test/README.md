# RHEL to Oracle Linux Migration Workshops

This folder contains separate instructor preparation and student workshops.

- [Instructor workshop](workshops/instructor/index.html): Prepare the reusable RHEL image in two labs, then provide the image URL and OCI access instructions to students.
- [Student workshop](workshops/tenancy/index.html): Begin at Lab 1, create your network and VM, then complete migration, live patching, kernel choices, and an OSMH quickstart across seven labs on that same VM.

The existing `workshops/tenancy` entry opens the student workshop. The instructor workshop has its own manifest and introduction. The separate migration-validation, package-maintenance, Apache-troubleshooting, security, application-backup, and automation labs are preserved under `archive/retired-student-labs`. The former station-based OSMH labs and supporting guides are preserved under `archive/retired-osmh-onprem`. Lab 4 completes migration, Lab 5 tests Ksplice on RHCK, Lab 6 explores UEK, and Lab 7 introduces OSMH on the same VM. Planned student time is 4 hours 50 minutes, including orientation.

## Entry Files

- Student manifest: `workshops/tenancy/manifest.json`
- Student introduction: `introduction/introduction.md`
- Instructor manifest: `workshops/instructor/manifest.json`
- Instructor introduction: `instructor-introduction/instructor-introduction.md`

Students need an OCI environment and the instructor-provided prepared-image PAR URL. Student Lab 1 contains the full compartment, VCN, access-rule, import, launch, and verification instructions. Students do not need to perform instructor Labs 1 and 2 or have Red Hat credentials.

## OSMH Environment

All seven labs use `ol-migrate-rhel-source` as OCI practice infrastructure for the on-premises migration scenario. The [OSMH quickstart](osmh-oci-quickstart/osmh-oci-quickstart.md) continues on the migrated VM. Students configure the service, register the VM, review inventory, and apply an available package update through the Console.

Students need the documented OSMH, software-source, Compute plugin, and IAM permissions, or tenancy administrator assistance. The live test verified Oracle Cloud Agent installation, but IAM errors prevented completion of service setup. Plugin registration, inventory, and update execution remain unverified on this image. Pilot these before class. Oracle's [OSMH tutorial](https://docs.oracle.com/en/learn/osmh-monitor/index.html) remains additional learning with its own prerequisites.

## Lab Number Mapping

The October 25 live-test notes and older QA records retain their original lab numbers. Use this mapping when resuming an existing test. Original Labs 1 and 2 are now the two instructor labs.

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

The October 27 live test completed kernel switching and the full Ksplice patch test in the earlier lab order. Ksplice applied 135 patches on the retained Oracle RHCK without rebooting during patching, and the application responded before and after. The revised sequence keeps learners on RHCK for Ksplice before introducing UEK. Existing successful test results remain valid. Lab 7 has been restored at the owner's request; historical QA notes retain earlier scope decisions and test results.

## Resource Ownership

Students manage their destination VM, network, OSMH profile, and jobs. Instructor preparation VMs, source images, export objects, buckets, and PARs stay under instructor control. Retain all resources and results for review, including resources from earlier OSMH testing. Documentation changes do not change hosts or cloud resources. Cleanup and unregistration occur only when requested by the workshop owner.
