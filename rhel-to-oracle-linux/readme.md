# RHEL to Oracle Linux

## Introduction

Draft partner workshop for an instructor-provided RHEL server. Participants use SSH, sudo, and standard Linux commands to migrate the server, apply Ksplice live updates, and complete an OS Management Hub package job. No cloud account or Console navigation is required from participants.

Estimated Time: 2 hours 30 minutes, provisional pending rehearsal.

### Objectives

- Demonstrate an in-place migration with application and system evidence.
- Demonstrate live kernel patching after the migration reboot.
- Demonstrate centralized package management from the terminal.

## Start Here

- Participant overview: [Introduction](introduction/introduction.md).
- LiveLabs entry point: [Partner workshop](workshops/partner/index.html), served over HTTP.
- Navigation and lab order: [Manifest](workshops/partner/manifest.json).
- Instructor preparation and release gates: [Instructor guide](instructor-guide/instructor-guide.md).
- Instructor LiveLabs entry point: [Instructor workshop](workshops/instructor/index.html), with **Instructor Setup** first, then **Instructor Guide**, followed by the partner labs.
- Command sources and adaptation notes: [Source traceability](qa/source-traceability.json).

The instructor guide is excluded from participant navigation. Publish only participant content to learners; the manifest alone is not an access control.

To preview locally, run this command from the `database` repository folder:

```powershell
python -m http.server 8765 --bind 127.0.0.1
```

Open the [instructor LiveLabs workshop](http://127.0.0.1:8765/rhel-to-oracle-linux/workshops/instructor/index.html). **Instructor Setup** opens first and explains where to create the compartment, VCN, and Compute instance. Use the same address with `partner` instead of `instructor` for the participant menu. The page needs internet access to load the standard LiveLabs assets.

## Draft Status

The source workshop is `../oracle-linux-rhel-migration-oci`. This edition leaves that workshop intact. The source's OCI screenshots do not apply to the terminal workflow and were not copied. Terminal screenshots can be added after the pilot; no results have been fabricated. FreeSQL does not apply to this operating-system workshop.

The exact RHEL release, Oracle Linux target, migration commit, Ksplice patch availability, converted-image agent support, IAM scope, CLI response handling, recovery, and timings require a live pilot. The included migration commit and digest come from the source workshop and are candidate inputs, not evidence of a successful migration.

No cloud resources are created by opening this folder. Instructor scripts must be staged deliberately on disposable training servers.

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle.
- **Last Updated By/Date** - Workshop adaptation draft, October 19, 2026.
