# Workshop Split Review: October 26, 2026

## Scope

Split the existing workshop into two entry points within the same repository. This is an authoring update; it does not execute OCI operations or establish successful live testing of the new setup sequence.

- Instructor entry: `workshops/instructor/index.html`, with an instructor introduction and the original two preparation labs.
- Student entry: `workshops/tenancy/index.html`, with a student introduction and twelve labs numbered from 1.
- Source lab folders, images, variables, and helper-file paths remain in place.

## Changes and Traceability

- Student Lab 1 replaces the original Lab 3 entry. It now contains compartment selection or creation, VCN and subnet creation, an internet-route check, SSH/HTTP NSG rules, a security-list review, image import, instance launch, and verification.
- The original image-import and VM-verification commands and screenshots were reused. The network instructions draw from the existing instructor network task and the Oracle documentation linked below.
- Instructor Labs 1 and 2 retain their preparation sequence. Instructor Lab 2 now identifies the student handoff and its new lab/task numbers. Its network task also calls out security-list rules that might allow broader access than the NSG.
- The student introduction, schedule, lab headings, prerequisites, onward references, and validation-helper comment use the new student numbering.
- Student cleanup addresses student resources. Instructor VMs, source images, export objects, buckets, and PARs remain under instructor control.
- The README maps every original student lab number to its new number. Historical QA records remain unchanged.
- Planning estimates are 105 minutes for instructor preparation and 405 minutes for the student workshop, including their separate orientations.

## Validation

- PASS: Repository LiveLabs Markdown validator checked all 16 manifest-referenced local Markdown documents and both pairs of launchers/manifests: zero errors.
- PASS: Instructor manifest contains two contiguous numbered labs; student manifest contains twelve. Every manifest lab title matches its Markdown H1.
- PASS: Manifest tutorial paths, variables, local links, all seven image references, task/step numbering, cross-lab task references, copy tags, and code fences resolve or balance.
- PASS: Schedule totals match lab durations and orientation allocations.
- PASS: Student lab references stay within the new 1-12 sequence; there are no instructor-lab completion prerequisites.
- PASS: 174 Bash snippets passed syntax checks after replacing instructional angle-bracket placeholders with fixture values. Six shell files passed `bash -n`. No snippet was executed against a VM.
- PASS: Bash blocks in the migration and administration labs remain unchanged from the pre-split version. Existing screenshots remain byte-for-byte unchanged. Historical text QA artifacts match after normalizing Git line endings.
- PASS: Student cleanup no longer directs learners to delete preparation VMs or revoke instructor PARs.
- PASS: Learner Markdown contains no em dash characters. `git diff --check` passed.

The skill's generic Python QA wrapper was also run. Both manifests passed its manifest checks. Its other checks assume every root subdirectory is a lab and require the literal `Estimated Time:` label, so it flags pre-existing QA/variables directories, an unused directory, README/test records, and the established `Estimated Lab Time:` labels. These compatibility findings do not replace the repository LiveLabs validator results above.

## Live-Test Follow-up

- Walk through Student Lab 1 from an empty, authorized destination environment. The newly added VCN and security-rule sequence has been checked against documentation, but has not been executed in this editing session.
- Capture real screenshots for VCN creation, the public-subnet route, NSG rules, and the security-list review. Existing import, launch, and post-migration screenshots were retained; no screenshots were fabricated or edited for this split.
- Check the instructor handoff and student permissions with a permitted student test account before class.
- Keep the later UEK, Ksplice, and administration live-test work pending where it was already pending. Structural checks do not establish successful live patching.
- Resume the current migrated-VM test at Student Lab 5, Task 1: Capture post-migration evidence. Rebuilding the VM is not required by this split.
- No OCI resources were changed or cleaned up.

## Sources

- [Oracle VCN networking wizard](https://docs.oracle.com/en-us/iaas/Content/Network/Tasks/quickstartnetworking.htm)
- [Oracle security rules and combined NSG/security-list behavior](https://docs.oracle.com/en-us/iaas/Content/Network/Concepts/securityrules.htm)
- [Oracle security lists and default outbound rules](https://docs.oracle.com/en-us/iaas/Content/Network/Concepts/securitylists.htm)
