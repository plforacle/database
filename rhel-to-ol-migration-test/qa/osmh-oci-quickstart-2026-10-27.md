# OSMH OCI Quickstart Revision

## Direction

The workshop owner reported that the Linux SMEs found the three OSMH labs too complex and disconnected from the migration flow. They requested one quickstart on OCI. The owner clarified that students have Console access and should configure and manage OSMH themselves for their migrated VM.

## Changes

- Replaced former Labs 7 through 9 with Lab 7: OS Management Hub Quickstart on OCI.
- Kept students on `ol-migrate-rhel-source` and its existing Apache workload throughout all seven labs.
- Included student Console setup for Policy Advisor, service-gateway connectivity, vendor software sources, an OCI registration profile, the OSMH plugin, inventory, and one immediate non-kernel update.
- Explained required permissions rather than treating Console sign-in as IAM administration. Kept the platform agent distinct from the retired non-OCI registration procedure.
- Removed the old instructor setup and shared non-OCI agent reference from both active menus. Instructor preparation now grants learner access and pilots the exact image, while students execute the quickstart setup.
- Preserved all five former OSMH folders under `archive/retired-osmh-onprem`. All ten files, including image-directory placeholders, match their pre-move SHA-256 hashes.
- Updated the introductions, architecture, prerequisites, lab transition, README, and archive navigation.
- Student plan: seven labs, 290 minutes including orientation. Instructor image-preparation plan: two labs, 105 minutes including orientation; additional provisioning and pilot time is required.

## Source Traceability

Reviewed October 27, 2026:

- [OCI registration](https://docs.oracle.com/en-us/iaas/osmh/doc/register-oci-instance.htm): Oracle Cloud Agent 1.40+, plugin activation, compatible profile, custom-image OS/architecture selection, and Active registration status.
- [Oracle Cloud Agent](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/manage-plugins.htm): installation, image support limits, plugins, and Compute permissions.
- [Policy Advisor](https://docs.oracle.com/en-us/iaas/osmh/doc/policy-advisor.htm): IAM requirements, compartment scope, group roles, and instance dynamic-group rules.
- [Getting started](https://docs.oracle.com/en-us/iaas/osmh/doc/getstarted.htm): software sources, supported environments, and service-gateway networking.
- [Adding vendor sources](https://docs.oracle.com/en-us/iaas/osmh/doc/add-vendor-software-sources.htm) and [creating profiles](https://docs.oracle.com/en-us/iaas/osmh/doc/create-profile.htm): the student's Console source/profile settings.
- [No matching profile](https://docs.oracle.com/en-us/iaas/osmh/doc/troubleshoot-no-profile-available.htm): manual OS/architecture identification for custom images.
- [Individual updates](https://docs.oracle.com/en-us/iaas/osmh/doc/install-updates-instance.htm), [instance work requests](https://docs.oracle.com/en-us/iaas/osmh/doc/list-jobs-instance.htm), and [job messages](https://docs.oracle.com/en-us/iaas/osmh/doc/view-job-logs-errors.htm): immediate updates and result inspection.

## Live-Test Requirements

The OCI quickstart has not been live-tested. The current VM came from a RHEL-derived custom image. Oracle Cloud Agent documentation guarantees its supported-image path for current platform images and their derivatives, while installation on other image origins is not guaranteed. The OSMH registration guidance supports custom-image OS/architecture selection, but this does not independently prove that the plugin works on this exact image.

First checkpoint: query `rpm -q oracle-cloud-agent` and its service status on the current VM. Then test package availability or installation if needed, plugin availability, registration, source access, inventory, and an actual update job. Resolve a missing or NOT_SUPPORTED plugin with the SMEs or Oracle guidance; do not register this OCI VM through the archived on-premises agent procedure.

Live-test finding: the owner confirmed Oracle Cloud Agent is absent, and `dnf install oracle-cloud-agent` returned No match. OCI repository or RPM preparation remains an unresolved prerequisite. The next diagnostic checks the configured OCI repository and release package. The quickstart is not yet a verified end-to-end procedure on this image.

Follow-up: no OCI repository or release package was present, and IMDS v2 confirmed `us-phoenix-1` in `oraclecloud.com`. Added a conditional OCI repository definition to Task 1, Step 2, based on Oracle's [published OL9 OCI repository example](https://blogs.oracle.com/linux/ocifs-oke-and-object-storage). The learner substitutes the actual region, keeps GPG checks enabled, and queries package availability before installation. Metadata access and installation still await live results. The revised Markdown passes validation, and all nine Bash examples pass syntax checks.

Repository test passed: the owner added the Phoenix OCI-included definition, successfully refreshed its metadata, and found `oracle-cloud-agent.x86_64 1.64.0-16.el9` in that source. Agent installation, service status, plugin compatibility, and OSMH registration are the remaining checkpoints.

Agent installation passed: DNF installed `oracle-cloud-agent-1.64.0-16.el9.x86_64`, its service returned Active, and Apache remained Active with the workload marker. Task 1 is complete. Console permissions, network/service setup, profile, plugin registration, inventory, and update-job execution remain to be live-tested.

Student Console permissions and access to the vendor-source compartment must be confirmed. If IAM permissions are not available, a tenancy administrator must grant the required lab access. A successful login alone is not sufficient.

No available package update is an honest no-update result, not proof that a package was changed. Do not downgrade packages to create a demonstration.

Capture real screenshots of profile creation, plugin activation, Active registration, and a completed job during the live test. The new images directory is empty except for its placeholder; no screenshots have been fabricated.

No OCI resources, hosts, agents, or install keys were modified or cleaned up by the assistant. Earlier lab content and user-edited Ksplice introduction are preserved.

## Validation

- Repository LiveLabs Markdown validator: zero errors across all 11 active Markdown files and both launch/manifest pairs.
- Both manifests parse and all local tutorial paths resolve. Student numbering is 1 through 7; instructor numbering remains 1 and 2. Menu titles match lab headings.
- Student schedule matches the manifest and totals 290 minutes; instructor schedule totals 105 minutes.
- All local links, variable references, and seven existing image references resolve. Active content has no obsolete on-premises OSMH paths or Lab 8/9 references.
- The new lab has five sequential tasks and sequential numbered steps. All six Bash examples pass `bash -n`.
- Ten archived files retain their pre-move hashes. No em dashes were found in active Markdown, and `git diff --check` passed.

These are documentation and syntax checks. Oracle Cloud Agent installation, plugin compatibility, OSMH registration, and update execution on the migrated VM still require the live test described above.
