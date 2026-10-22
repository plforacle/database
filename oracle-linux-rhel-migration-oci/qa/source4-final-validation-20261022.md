# Source-4 final guest validation and remaining Console work

Date: October 22, 2026. **Migration and maintenance guest validation passed; Lab 7's OCI Console work remains incomplete.** Lab 8's independent post-maintenance review was accepted at 15:28:13 UTC with a narrowly documented Uptrack wrapper limitation. The final evidence archive was copied locally and hash-verified at 15:30:35 UTC. No cleanup was performed.

This record supplements the [accepted conversion](source4-run-20261022.md) and [maintenance results](source4-maintenance-20261022.md). The target remains source-4, Oracle Linux 9.8 running Oracle RHCK `5.14.0-687.58.1.el9_8.x86_64`. It does not claim OS Management Hub registration, working IAM policy, successful service jobs, or completion of the whole workshop.

## Lab 7 guest preparation

The initial preparation preflight stopped before changes because `ol9_oci_included` was absent. The original result is preserved. The guest's lack of that repository was resolved with the following reviewed preparation:

- A temporary Oracle regional OCI Included repository was used to preview and download `oci-included-release-el9-1.0-2.el9.x86_64`.
- The downloaded package's signature and digests verified against the installed Oracle signing key. Its vendor was Oracle America, and its scriptlet inventory was empty.
- Installation returned zero at 15:17:02 UTC. Package verification passed, the installed signed repository configuration used GPG checking, and the OCI Included repository metadata became available.
- The Oracle Cloud Agent preview proposed only one package. The actual upgrade from `1.63.0-5.el9` to `1.64.0-16.el9` ran from 15:18:26 to 15:19:10 UTC and returned zero.
- Oracle Cloud Agent was enabled and active. Dependencies, Apache, the workload checksum, system state and failed-unit checks passed. Both installed Ksplice updates remained listed, with no new reboot.

The first combined RPM query for the two example job candidates, `tree` and `dos2unix`, returned status `2` because both were absent. An overstrict collection guard stopped that attempt, and its result was preserved. Separate queries then recorded status `1` for each absent package, the expected result. Neither package was installed to hide that observation. The corrected preparation collector recorded `LAB7_SSH_PREPARATION_PASS_CONSOLE_PENDING` at 15:21:04 UTC.

The regional OS Management Hub HTTPS endpoint returned HTTP 404. That response establishes a network/TLS response from the endpoint, not authenticated service access, IAM correctness, registration or plugin eligibility.

The operator reports that the OS Management Hub Agent plugin is not enabled in OCI Console. Policy Advisor identified its missing standard resources. The operator then supplied the proposal: two user groups, one dynamic group and sixteen policy statements for the lab compartment. Those actions were reviewed against the standard setup. The operator subsequently reported that Setup was stuck and explicitly requested stopping at Lab 7. Completion is unconfirmed, and some IAM changes may have been applied. No retry, cancellation, rollback or investigation of that Console operation was attempted. No plugin enablement, profile selection, registration or service jobs are recorded. Required IAM/dynamic-group configuration, plugin eligibility, registration, package job execution, scheduled-job review and removal of the practice job remain unverified. The running Oracle Cloud Agent service and successful guest preparation do not complete these service tasks.

## Lab 8 workload, system and package validation

Two independent snapshots were retained: a pre-registration validation at 15:12:52 UTC and another after the agent update at 15:22:05 UTC. Both returned `PRE_OSMH_MIGRATION_VALIDATION_PASS` without validation errors. The workshop helper passed all 17 checks, covering Oracle identity and release, the running/default RHCK, boot artifacts, runtime libraries, RPM database, dependencies, Apache, workload marker, SELinux, HTTP firewall access and failed units.

Both saved application content and its checksum matched the final RHEL baseline with diff status zero. The checksum remained `11db440c06cd95a31aa4dfe59f97d08389c9235e949f5c0f1f385deab4b04365`. OS identity, kernel and repository comparisons correctly returned diff status `1` for the expected RHEL-to-Oracle changes. Those recorded differences are not failed package or application checks.

The final package inventory retains ten Red Hat-vendor entries: eight protected runtime-kernel packages across two source versions and the two declared Insights exceptions. The RHUI client and third-party monitoring agent remain recorded exceptions; source RHUI repositories remain disabled. The Oracle agent was updated through the signed OCI repository. No retained agent or Insights support claim follows merely from clean dependencies.

## Post-Ksplice independent boot review

The extended independent check at 15:21:09 UTC preserved a `STOP_WITHOUT_REBOOT` result. Its only failed command was the frozen workshop.5 boot-only verifier:

```text
cannot inspect runtime libraries for /sbin/depmod: not a dynamic executable
```

Every other independent boot command returned zero. Oracle kernel and BIOS package ownership/integrity, default selection, GRUB syntax and direct/nested probes, root and boot UUIDs, LVM state, target initramfs dependencies and tooling, critical libraries, services, security state and workload checks passed. No new storage or network argument difference was found.

The diagnostic was traced to the verifier's assumption that `depmod` is an ELF executable. Oracle Uptrack `1.2.80` intentionally replaces that command with a package-owned Bash wrapper. The exact installed chain was inspected:

1. `depmod` and `modprobe` point to their Uptrack `.ksplice` wrappers.
2. Their `.ksplice-orig` links resolve to the Oracle kmod ELF binary.
3. Wrapper payload digests, installed package ownership, Uptrack RPM scriptlets and the Uptrack dracut module establish the intended interposition. The dracut module explicitly installs the original commands into the initramfs.
4. `rpm -V kmod` reported only the two explained command symlink changes. The Uptrack nonconfiguration payload, Bash, Python and critical-library verification passed. Expected Uptrack configuration/timestamp differences were retained in the exploratory record.
5. The original kmod ELF, Bash and Python interpreters resolved their dynamic libraries, and both wrapper version commands succeeded. Direct checks also verified the remaining dracut-install, copy and install ELF dependencies with no unresolved libraries. Supported dracut helper and module-listing checks passed.

The missing-library failure recorded in the earlier rehearsal was not reproduced. Here, `ldd` had been given a script rather than its ELF runtime. This distinction was established from the exact installed payload and execution results, not from dismissing a library warning.

A separate acceptance record at 15:28:13 UTC checked the complete recorded chain, current payload digests, required functional results, every other independent check and the unchanged frozen candidate digest. It bound those inputs by SHA256 and recorded `INDEPENDENT_POST_MAINTENANCE_PASS_AFTER_VERIFIED_UPTRACK_WRAPPER_REVIEW`. The four corresponding local wrapper/runtime/source evidence files independently match the acceptance record's hashes. The original failed verifier result remains preserved.

Exploratory `dracut --version` and unsupported `grubby --version` calls returned `1`. An initial source search also failed because `rg` was absent; subsequent direct Python file inspection and RPM scriptlet evidence supplied the required source proof. These exploratory observations were preserved. Supported module, helper, default-kernel and entry checks supplied the actual acceptance results.

This is a known post-Uptrack limitation of the frozen workshop.5 verifier. The candidate still returns nonzero on this installed wrapper. Its script, launcher and checksum manifest were not changed, and no boot configuration, kernel or live update was repaired or removed. The review performed no reboot. Acceptance applies only to the verified recorded Uptrack chain; unknown wrappers, unexpected payload changes or unresolved libraries still require a stop and review.

## Evidence export and remaining work

The already verified pre-Oracle-reboot archive and the later maintenance evidence remain preserved locally. The final archive `ol-migration-evidence-final-20261022.tar.gz` was copied locally and checked at 15:30:35 UTC: 6,065,474 bytes, SHA256 `645523e46c2e5f9819800f1706818ae2f02b36860dce8b2443d071cfbf378850`, matching the remote hash. Its 331 members include both earlier and final protected system-report archives. Local content review verified the exact frozen candidate and all five hash-bound acceptance inputs, including the original STOP and the final validation result.

The public HTTP check at 15:30:35 UTC returned 200, the workload marker and the unchanged application checksum. The archive and HTTP observations are saved as `local-final-archive-verification.json` and `final-public-http.json` in the private evidence directory.

The final local workshop checker recorded 2,053 checks with zero failures, and scoped `git diff --check` passed. The checker excludes gitignored private transport evidence from deliverable syntax and line-ending checks; historical evidence was preserved. Earlier 150 migration and 43 launcher regression results were not rerun for these documentation/checker changes. The exact frozen candidate was exercised in the recorded live run. The official LiveLabs validator remains unavailable; see [local-results.txt](local-results.txt).

Private source evidence is under ignored `qa/live-evidence/source4-labs7-8-20261022/`, with earlier stages in their own evidence directories. It retains all initial stops, signed repository preparation, agent update, guest preparation, both final validation snapshots, the frozen verifier failure, exact wrapper and runtime provenance, and the separate hash-bound acceptance. Resource identifiers, IPs and private-key paths are excluded from this report.

Work is stopped at Lab 7 by operator request. Resume the pending Console/IAM checkpoints only after a new instruction, and record actual registration and service-job results before claiming that lab complete. Lab 8's current guest checks were performed before those service tasks. Preserve source-4, all failed instances and volumes, backups, logs, the imported-image reference and shared network. No cleanup, resource deletion, commit, push or publication is authorized.
