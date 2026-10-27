# Workshop Ends at Lab 6

## Owner Decision

On October 27, 2026, the workshop owner chose to end the student workshop at Lab 6 and use Oracle's existing OSMH tutorial as an introduction at the end. This replaces the earlier requirement to include a custom OSMH lab in the main workshop.

## Changes

- Removed Lab 7 from the student manifest.
- Preserved its two files unchanged under `archive/retired-student-labs/osmh-oci-quickstart`; file hashes matched after the move.
- Added a separate follow-up link at the end of Lab 6 to [Monitor and Update Oracle Linux with Oracle OS Management Hub](https://docs.oracle.com/en/learn/osmh-monitor/index.html).
- Updated introductions, schedule, README, and archive navigation. Six student labs plus orientation total 250 minutes. The instructor workshop remains two labs plus orientation, totaling 105 minutes.
- Removed required OSMH setup from workshop prerequisites and instructor preparation.
- Preserved historical QA and live-test results, including earlier lab numbers and OSMH setup failures.

The external tutorial has its own environment and permissions prerequisites. Completing it is not required to finish the migration workshop.

## Resource State

Only local documentation changed. No OCI resources, IAM policies, groups, agents, or hosts were changed or removed.

## Validation

- The repository LiveLabs validator passed all 10 active Markdown files and both launcher/manifest pairs with zero errors.
- Student and instructor manifests contain six and two numbered labs respectively.
- Active local links, image paths, and variables resolve. Historical archive links inside preserved labs were not rewritten.
- Active workshop content has no stale Lab 7 continuation or em dash characters.
- `git diff --check` passed.
