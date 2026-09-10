# Oracle Linux Value Navigator

This repository contains the complete workshop and application source for building Oracle Linux Value Navigator. The workshop guides a learner from an Oracle Cloud Infrastructure LAMP environment to a tested subscription-cost comparison application.

The initial workshop uses only synthetic sample data. It is a subscription-cost comparison, not a quote, licensing determination, or full TCO analysis.

The readable application source of truth is stored under `catalog-database/files/application`. It includes the MySQL schema, private PHP libraries, public controllers and views, a staged deployment script, and automated checks. The generated learner package is `catalog-database/files/ol-value-navigator-application.zip`. Regenerate the ZIP from the readable source after every application change, update the Lab 2 checksum, and test the packaged files before uploading them to Object Storage. Lab 1 is the validated infrastructure baseline. Labs 2 through 6 create, deploy, enable, and test the application.

The standalone team tutorial is stored in `team-user-guide.md`. It documents the current application workflow alongside the built-in Help page.

The five-minute management walkthrough is stored in `management-quick-start.md`.

## Customer-context update for an existing Version 1 deployment

Upload the rebuilt application ZIP to the testing Object Storage location, then follow **Lab 2 Task 1** to download and verify it. Complete **Lab 5 Task 2** to apply the additive database upgrade before deploying stage 5, and **Lab 5 Task 9** to verify saved context and both exports. Do not recreate the database account. Existing comparisons, reviewed lines, and results are retained.

The four optional fields are customer name, objective, comparison scope, and recommended next step. **Edit customer details** changes only these fields, separately from **Revise original inputs**. The update does not change calculation rules, GenAI prompts, OCI infrastructure, or Version 2.

## Usability update for an existing Version 1 deployment

This update adds Home navigation, groups exports and customer editing on Results, and restores form entries after failed saves. Calculation rules and the database schema are unchanged. Upload the rebuilt ZIP, complete **Lab 2 Task 1**, then **Lab 5 Task 2 steps 2 through 5** if the customer-context upgrade is already installed. Verify **Lab 5 Tasks 9 and 10**, then run **Lab 6 Task 1**. Refresh the browser after deployment so it loads the updated stylesheet.

## Production publication checklist

- [x] Complete successful end-to-end testing of Labs 1 through 6.
- [x] Add comprehensive maintainability documentation to the complete readable application source.
- [x] Regenerate the application ZIP and verify its contents, syntax, and unit checks.
- [ ] Deploy the final ZIP to the test compute instance and run the Stage 5 installation verification.
- [ ] Upload the final application ZIP to the LiveLabs production Object Storage location.
- [ ] Replace the testing Object Storage PAR URL in `catalog-database/catalog-database.md` with the LiveLabs production URL.
- [ ] Verify the production object against the SHA-256 checksum documented in Lab 2 before publishing the workshop.
