# Instructor Guide: RHEL to Oracle Linux

## Introduction

Prepare and operate the infrastructure for an SSH-only partner workshop. Participants receive an assigned RHEL server, connection instructions, a prepared application, and local workshop tools. They do not create cloud resources, configure IAM, register the server, or use a cloud Console.

**Start with Instructor Setup in the LiveLabs menu.** You create the compartment, VCN, security rules, and RHEL Compute instance there, in your OCI Console. Return to this guide after the source server is running and you can connect with SSH. Partners start at Lab 1 after you finish preparing their servers.

Estimated Time: reserve a half day for the first complete pilot and recovery test. Measure preparation time for subsequent cohorts.

### Objectives

- Prepare an isolated, reproducible source server for each learner or team.
- Validate the exact migration, Ksplice, management, and recovery sequence.
- Keep cloud administration in the instructor workflow.
- Collect evidence, restore failed servers, and remove training resources.

## Delivery Status

This is an authoring draft, not a completed pilot. The source workshop documented source setup but did not establish a complete conversion and management rehearsal. Do not advertise this edition as validated until the release checklist below passes. Use disposable instructor-owned training infrastructure for every pilot.

The skill's canonical sample and validator paths are machine-specific and unavailable here. The new structure follows its documented contract and reuses the existing workshop's LiveLabs loader. Local structural checks and helper tests do not replace runtime, renderer, or official validator checks.

## Task 1: Prepare the assigned source servers

1. Complete **Instructor Setup: Create the Compartment, VCN, and Compute Instance** in the LiveLabs menu for your first pilot server. Prepare one RHEL 9 x86_64 VM per learner or team after the pilot passes. Rehearse the selected fixed minor release and compatible Oracle Linux 9 target before fixing the lab version.

2. Record the infrastructure and connection details created during Instructor Setup. Keep compartment, shape, storage, networking, repository access, image licensing, and billing as instructor work. Allow only the learner's approved SSH path; a public HTTP endpoint is unnecessary because the labs use loopback HTTP.

3. Provide an individual SSH account or team account, its credential handoff process, sudo access, and explicit permission to reboot. Record the hostname, host-key fingerprint, and expected recovery window for the connection sheet. Do not publish private keys in the workshop repository or share one team's key with another.

4. Configure the source repositories, current source kernel, adequate free space, and required tools. Perform any source update and reboot before establishing the teaching baseline. Verify the running source kernel is the newest installed kernel. Avoid introducing new source package versions between rehearsal and delivery without repeating the affected checks.

5. Copy [prepare-workload.sh](files/prepare-workload.sh) to the disposable source and review it before execution:

    ```bash
    sudo bash prepare-workload.sh --training-source
    ```

    The script refuses an existing application page and installs only the training dependencies. Confirm tmux, curl, RPM, DNF, grubby, firewalld, SELinux, Python 3, and Apache work. The marker `MIGRATION_WORKLOAD_OK` must be returned by `curl http://127.0.0.1/`. Keep the demo package, normally `tree`, absent for the managed-install exercise.

## Task 2: Stage the reviewed workshop artifacts

1. On the source, create the root-owned staging locations:

    ```bash
    sudo install -d -m 0755 /opt/rhel-to-oracle-linux /etc/rhel-to-oracle-linux
    ```

2. Copy [workshop.env.example](files/workshop.env.example) to a local `workshop.env` and replace the source and target version placeholders with the fixed releases selected for the pilot. The commit and checksum are candidate values inherited from the original workshop. Review and validate the exact revision before cohort delivery. Keep the commit and digest together when changing revisions.

3. From the directory containing your edited settings, download and verify the migration artifact:

    ```bash
    . ./workshop.env
    curl --fail --location --output migrate-to-oracle-linux.sh \
      "https://raw.githubusercontent.com/oracle/migrate-to-ol/${MIGRATION_COMMIT}/migrate-to-oracle-linux.sh"
    printf '%s  migrate-to-oracle-linux.sh\n' "$MIGRATION_SHA256" | sha256sum --check
    ```

    Stop on a mismatch. Do not calculate a new expected digest from an unexplained download and treat that as verification. Inspect the script, help, supported versions, report paths, and RHUI behavior. The repository describes its scripts as community based and not officially supported by Oracle. See the [repository](https://github.com/oracle/migrate-to-ol).

4. For this OCI-hosted edition, download the documented Ksplice installer to the instructor staging directory:

    ```bash
    curl --fail --location --output install-uptrack https://www.ksplice.com/uptrack/install-uptrack-oc
    ```

    Review and pilot the installer, then freeze those reviewed bytes for the cohort. Create `install-uptrack.sha256` with `sha256sum install-uptrack > install-uptrack.sha256`. That digest records the instructor-reviewed artifact, not an independently published vendor signature. For a different hosting environment, arrange the appropriate entitlement and installer before adapting this step. [Ksplice installation guidance](https://docs.oracle.com/en-us/iaas/oracle-linux/oci/install-ksplice.htm)

5. With the helper files and reviewed artifacts in your current staging directory, install them:

    ```bash
    sudo install -m 0644 workshop.env /etc/rhel-to-oracle-linux/workshop.env
    sudo install -m 0755 capture-state.sh validate-system.sh /opt/rhel-to-oracle-linux/
    sudo install -m 0755 migrate-to-oracle-linux.sh /opt/rhel-to-oracle-linux/
    sudo install -m 0644 install-uptrack install-uptrack.sha256 /opt/rhel-to-oracle-linux/
    sudo install -m 0755 workshop-management.py /usr/local/bin/workshop-management
    ```

    The supplied [capture tool](files/capture-state.sh), [validation tool](files/validate-system.sh), and [management helper](files/workshop-management.py) use LF line endings. Check those line endings after transfer. Store no credentials in either settings file. Version settings and digests are readable by learners; ownership protects them from accidental changes, not from a learner with sudo.

6. Install a pinned, approved OCI CLI in a dedicated location, such as `/opt/workshop-cli/bin/oci`, following the [CLI installation documentation](https://docs.oracle.com/en-us/iaas/Content/API/SDKDocs/cliinstall.htm). Record Python and CLI versions in the pilot report. Test the CLI and virtual environment again after conversion; replacing Python or dependencies can affect a preinstalled CLI.

## Task 3: Rehearse migration, live patching, and recovery

1. Create a recoverable pre-conversion backup after staging the application and workshop artifacts. Record the VM, source image, repository model, versions, backup reference, creation time, commit, digest, and source kernel. Restore it on a disposable recovery server and verify boot, SSH, repositories, and the application. Define who performs recovery and the troubleshooting time limit.

2. Complete participant Labs 1 through 3 using the actual learner account. Test both a normal session and SSH disconnection followed by tmux reattachment while the process is still running. Do not bypass readiness errors, force unsupported source/target combinations, or remove RHUI packages manually to get past a failure. Confirm the staged script produces the documented logs and reports.

3. Rehearse Lab 4 on the kernel actually selected by the conversion. Oracle Linux 9 RHCK is maintained with Ksplice, but patch availability depends on the exact running kernel. Recently released kernels can lack applicable patches and some commands can report that support is not yet available. [Maintained kernels](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-MaintainedKernels.html)

4. Require a real applicable patch for a hands-on live-patching claim. Record the base kernel RPM, installer digest, entitlement check, available patch identifiers, applied patches, boot IDs, and application results. Do not apply all patches automatically before learners arrive. Do not run a general DNF upgrade and reboot between conversion and Ksplice. If the exact conversion output has no usable patches, revise and repeat the tested sequence or provide a clearly separate demonstration server. Do not silently downgrade or disable readiness checks.

5. Repeat the recovery procedure after a simulated inaccessible-server scenario. Restore or replace the learner server through the instructor's infrastructure workflow, verify the connection identity, issue updated connection details when needed, and resume from a known checkpoint. Never instruct learners to suppress SSH host-key checks.

## Task 4: Prepare delegated management access

1. Prepare authentication for service calls made from the converted VM. The helper uses instance principal authentication and supplies the region and assigned managed-instance ID from a root-owned file. No personal user API key is needed. [Instance principal setup](https://docs.oracle.com/en-us/iaas/Content/Identity/Tasks/callingservicesfrominstances.htm)

2. Have an administrator define and test the dynamic-group matching rule and permissions. The agent's registration/service permissions and the helper's control-plane permissions serve different purposes; successful registration does not prove that a learner can query inventory or submit a package job. The required control-plane operations are GetManagedInstance, ListManagedInstanceInstalledPackages, ListManagedInstanceAvailablePackages, InstallPackagesOnManagedInstance, GetWorkRequest, and ListWorkRequestErrors. Map these using the [OS Management Hub policy reference](https://docs.oracle.com/en-us/iaas/osmh/doc/policies-reference.htm).

3. Prefer one training compartment and one managed target per team when compartment-level grants are used. Do not use tenancy-wide management grants. Test authorized operations on the team's target and denial against another team's target. A root-owned helper file, package allowlist, or matching rule for the caller alone does not restrict what resource the principal can manage; IAM must enforce target access.

4. Treat the learner account as holding the instance's service privileges. An SSH user inherits the instance principal's grants, and sudo allows changing the wrapper. Instance principals also have an inherent ability to inspect compartment metadata. If the assignment forbids any visibility into the hosting tenancy, use a dedicated training tenancy or a separately authorized service broker design. This edition removes cloud administration from the learner curriculum; it does not conceal the hosting platform.

5. Prepare the registration profile, appropriate vendor software sources, network route, and Oracle Cloud Agent installation path for the converted guest. Verify the minimum documented agent version, currently 1.40, against [registration requirements](https://docs.oracle.com/en-us/iaas/osmh/doc/register-oci-instance.htm). Confirm agent package availability on this converted image rather than assuming an Oracle Linux platform-image configuration.

## Task 5: Register after conversion and complete the managed-job pilot

1. After the migration and direct Ksplice exercise, save the pre-registration repository files and record the application state. Enable the OS Management Hub Agent plugin for the converted VM through the instructor Console or tested automation, choose a profile matching Oracle Linux 9 x86_64, and wait for registration and initial inventory. Do not pre-register the RHEL source as an Oracle Linux guest.

2. Copy [management.example.json](files/management.example.json) to `management.json`. Set the actual region, managed-instance ID returned by the service, absolute CLI path, and one approved absent package. Keep `allowed_packages` and `DEMO_PACKAGE` aligned. Install the configuration:

    ```bash
    sudo install -m 0644 management.json /etc/rhel-to-oracle-linux/management.json
    ```

3. As the learner, run `workshop-management status`. The API's managed status should be `NORMAL`, with a recent check-in. This is the API field `status`, not a `lifecycle-state` field. The Console can use a different status label. Validate the actual JSON from the pinned CLI before cohort delivery. See the [ManagedInstance model](https://docs.oracle.com/en-us/iaas/tools/python/latest/api/os_management_hub/models/oci.os_management_hub.models.ManagedInstance.html).

4. Complete participant Lab 5. Confirm a real asynchronous request is submitted, its tracking identifier is saved, the final service status is `SUCCEEDED`, the package is installed, and the application remains healthy. The helper expects the CLI submission response to contain `opc-work-request-id`; capture the real response and confirm that contract during the pilot. [Install-packages command](https://docs.oracle.com/en-us/iaas/tools/oci-cli/latest/oci_cli_docs/cmdref/os-management-hub/managed-instance/install-packages.html)

5. Test error handling without running harmful transactions: missing configuration, denied permission, unavailable package, stale inventory, a request still in progress, and an ambiguous submission. The helper leaves a pending marker on a submission error to discourage duplicate jobs. Reconcile service history before clearing it. Preserve prior evidence before resetting any local tracking file. The helper is an exercise aid; it is not a durable transaction manager or a security boundary.

6. Limit this lab to package installation. The direct Uptrack exercise does not configure OS Management Hub Ksplice operations, which have separate client and source requirements. Do not select Ksplice update categories or install a conflicting client as part of this package exercise. [OS Management Hub Ksplice configuration](https://docs.oracle.com/en-us/iaas/osmh/doc/linux-package-management.htm#using-ksplice-for-oracle-linux)

## Task 6: Release, facilitate, and clean up

1. Before releasing the workshop, complete this checklist and record evidence in `qa/pilot-results.json` or your approved test record:

    - Exact source image, fixed source/target releases, script commit, checksum, and package versions recorded.
    - Learner SSH, host-key verification, sudo, tmux, disconnect/reconnect, and reboot tested.
    - Recovery point available and restored successfully.
    - Dry run and conversion pass using the source repository model.
    - Oracle RHCK boots; repositories, package database, security state, and application checks pass.
    - A real Ksplice patch applies; boot identity is unchanged and the application responds.
    - Post-conversion CLI, agent installation, registration, and recent inventory work.
    - Managed package request succeeds; another team's target is inaccessible.
    - Final evidence capture, privileged log export, and archive collection work.
    - Participant renderer preview, LiveLabs validation, terminal screenshots where useful, and measured timings reviewed.
    - Source billing, license terms, and cleanup responsibilities confirmed independently of the guest OS identity.

2. If OS Management Hub cannot pass its gate, designate Lab 5 as instructor-demonstrated or remove it from the delivered manifest. Do not mark it successful based only on a local `dnf install`. If Ksplice has no applicable patch, identify Lab 4 as preview-only. Update the learner introduction and duration to reflect the delivered scope.

3. Provide a connection sheet with the assigned hostname, account, key handoff, host-key fingerprint, workshop URL, recovery contact, and reboot waiting window. Give learners the participant manifest, not this instructor guide. During the workshop, confirm each learner's backup before conversion and perform registration at the Lab 5 handoff.

4. Collect evidence before destroying resources. Export migration logs and reports with appropriate ownership so learners can archive them. If a snapshot capture fails, preserve its directory, diagnose the failure, and move the incomplete directory to a distinct archive name before rerunning the same label. The capture tool refuses overwrites to avoid mixing results.

5. Cancel any outstanding training jobs, unregister the converted instance while it remains reachable, remove workshop-only profiles and IAM grants, then terminate or reset the assigned VM and remove workshop-only storage, backups, and networking. Preserve shared resources. Use the [unregistration procedure](https://docs.oracle.com/en-us/iaas/osmh/doc/unregister-instance.htm), and verify that no managed records or future jobs remain.

6. Recheck the exact kernel, available live patches, CLI contracts, IAM isolation, and artifacts before each cohort. A successful pilot applies to the recorded configuration, not every RHEL image or future patch state.

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle.
- **Last Updated By/Date** - Workshop adaptation draft, October 19, 2026.
