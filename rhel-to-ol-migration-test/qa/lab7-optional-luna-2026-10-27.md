# Optional Lab 7 and Independent Luna Option

## Owner Direction

The owner confirmed that Lab 7 is optional and suggested students run the independent Luna OSMH quickstart during training. Lab 7 remains in the student menu with its migrated-VM tasks preserved as an alternative.

## Changes

- Marked Lab 7 optional in its title, menu, introductions, and the transition from Lab 6.
- Added the supplied [Luna lab](https://luna.oracle.com/lab/d60f64fb-76b8-4a46-9b90-75ce4ff7e0b5/steps) as an independent option with its own environment, setup, and duration. Learners following Luna do not also need to run the migrated-VM tasks.
- Separated 250 minutes of main workshop activity from optional OSMH time. Preserved the owner's five-hour workshop estimate with time for provisioning, downloads, and reboots. Optional Lab 7 requires extra time; students can start it during training if time allows.
- Made migrated-VM OSMH permissions conditional on choosing that optional path.
- Recorded the owner's report that Labs 1 through 6 were successfully live-tested. Optional Lab 7 testing is planned for Wednesday, October 28, 2026.

## Source and Test Limits

Read the Luna page on October 27, 2026. It covers profiles, OCI instance registration, reports, and update jobs, with its own resource setup and Luna credentials. Also inspected the user-supplied local source at `C:/Users/Perside/main/_01_hands-on-labs/lunalab/osmh-monitor-main/tutorial/index.md` without changing it.

No Luna session was launched. The independent lab has not been live-tested in this session. Earlier migrated-VM results remain unchanged: agent installation passed, but IAM setup errors prevented end-to-end OSMH verification.

Only local documentation changed. No OCI resources were created, modified, or removed.

## Validation

- Repository LiveLabs validator passed all 11 active Markdown files and both launcher/manifest pairs with zero errors.
- Menu titles match lab headings. All active local links resolve.
- Main agenda rows total 250 minutes. Optional OSMH time is listed separately.
- No em dashes were found in active content. `git diff --check` passed.
