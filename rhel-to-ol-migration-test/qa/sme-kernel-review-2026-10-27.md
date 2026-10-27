# Kernel Choices SME Review

## Scope

Applied the comments labeled Lab 6: Explore Oracle Linux Kernel Choices in the SME document. This lab is now Student Lab 5 following removal of the separate validation lab. POINT means Step.

- Task 1, Step 1 now contains three commands: OS identity, running kernel, and default kernel. Removed report-directory creation, the repeated starting vendor query, saved RHCK path, and boot-ID recording.
- Kept Task 1, Step 2 and Task 2 unchanged.
- Kept Task 3's UEK selection and reboot procedure; removed its reference to the deleted saved RHCK file.
- Kept Task 4, Step 1's OS and running-kernel checks, removing the dependent boot-ID file write.
- Removed Task 4, Step 2, the repeated application and security checks.
- Removed Task 5, including the challenge, fallback procedure, and result-file requirement.
- Updated the objectives, prerequisites, introduction, task headings, and student menu description to match the shorter lab. The existing 30-minute estimate is unchanged pending live testing.

## Sources

- SME feedback: `C:\Users\Perside\main\_01_hands-on-labs\rhel-ol-hol\PLAB_RS_COMMENTS.docx`.
- [Oracle Linux 9.8 installation and boot guidance](https://docs.oracle.com/en/operating-systems/oracle-linux/9/relnotes9.8/ol9-features-Installation.html), reviewed October 27, 2026.
- [Changing the default kernel](https://docs.oracle.com/en/operating-systems/oracle-linux/9/boot/boot-UsinggrubbyToManageKernels_change_the_default_kernel.html), reviewed October 27, 2026.

## Validation

- Repository LiveLabs Markdown validator: zero errors for the kernel lab and student manifest.
- Confirmed four sequential tasks, sequential steps, and exactly three starting commands.
- Confirmed Task 2 is unchanged and no removed kernel-report files are referenced.
- Student manifest parses, local tutorial paths resolve, and `git diff --check` passed.

These are documentation checks. No commands were run on the VM, and no OCI resources, backups, screenshots, or historical test records were changed.
