# Oracle Linux Value Navigator

This repository contains the complete workshop and application source for building Oracle Linux Value Navigator. The workshop guides a learner from an Oracle Cloud Infrastructure LAMP environment to a tested subscription-cost comparison application.

The initial workshop uses only synthetic sample data. It is a subscription-cost comparison, not a quote, licensing determination, or full TCO analysis.

The readable application source of truth is stored under `catalog-database/files/application`. It includes the MySQL schema, private PHP libraries, public controllers and views, a staged deployment script, and automated checks. The generated learner package is `catalog-database/files/ol-value-navigator-application.zip`. Regenerate the ZIP from the readable source after every application change, update the Lab 2 checksum, and test the packaged files before uploading them to Object Storage. Lab 1 is the validated infrastructure baseline. Labs 2 through 6 create, deploy, enable, and test the application.

## Production publication reminder

- [ ] Complete successful end-to-end testing of Labs 1 through 6.
- [ ] Add concise descriptive comments to the readable application source after lab testing is complete.
- [ ] Regenerate and retest the application ZIP after adding the source comments.
- [ ] Upload the final application ZIP to the LiveLabs production Object Storage location.
- [ ] Replace the testing Object Storage PAR URL in `catalog-database/catalog-database.md` with the LiveLabs production URL.
- [ ] Verify the production object against the SHA-256 checksum documented in Lab 2 before publishing the workshop.
