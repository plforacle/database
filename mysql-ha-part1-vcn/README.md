# MySQL HA Part 1: Prepare the OCI Network

A 35-minute LiveLabs workshop adapted from `MySQL_Part1_VCN.mp4`.

## Open the workshop

From this folder, run:

```text
python -m http.server 8000 --bind 127.0.0.1
```

Open `http://127.0.0.1:8000/workshops/tenancy/index.html` in a browser. The standard LiveLabs renderer loads JavaScript and styles from Oracle's CDN, so internet access is required. Serve the entire folder so relative lab and image paths resolve. Do not open the HTML directly with a file URL.

Start reading without the renderer at [Introduction](introduction/introduction.md).

## Learner sequence

1. Introduction: prerequisites, address plan, scope, and adaptations.
2. Lab 1: create the VCN and public and private regional subnets.
3. Lab 2: configure a scoped MySQL ingress rule on the private subnet.
4. Lab 3: verify the configuration, record identifiers, and optionally clean up.
5. Need Help: collect the details needed to resolve a blocked step.

## Creation steps completed

1. Inventoried the workspace and identified the source recording.
2. Read the LiveLabs workshop author v2 instructions and structure, formatting, screenshot, traceability, FreeSQL, prose, and QA references.
3. Inspected video metadata: approximately 99 seconds, 1512 by 982 pixels, 25 frames per second.
4. Sampled frames every 10 seconds and inspected additional frames at UI transitions.
5. Measured the audio: mean and peak volume were -91 dB, leaving no usable narration to transcribe.
6. Reconstructed the successful UI flow from the visual evidence, excluding a brief standalone VCN form detour.
7. Checked current Oracle documentation for the VCN wizard, managed MySQL ingress settings, and cleanup.
8. Selected how-to-guide mode and a tenancy-based workflow with three hands-on labs.
9. Recorded a managed MySQL follow-on assumption because the video does not identify the eventual database topology.
10. Documented two adaptations: private subnet security list instead of public, and authorized client source CIDR instead of 0.0.0.0/0.
11. Authored objectives, prerequisites, numbered tasks, expected results, troubleshooting, and optional cleanup.
12. Extracted four screenshots and cropped away browser chrome and the meeting overlay without changing UI values.
13. Downloaded the canonical Oracle LiveLabs launch HTML and used the skill's manifest generator, then replaced its generic help lab with the local help page.
14. Added timestamp-to-task traceability, screenshot provenance, assumptions, and open review items in authoring/source-traceability.json.
15. Reviewed prose and validated markdown using the upstream Oracle LiveLabs validator.
16. Checked manifest targets, local links, image paths, image alt text, task sequencing, and the no-em-dash constraint with scripts/validate-workshop.py.
17. Packaged the workshop in a ZIP and recorded verification limits.

## Verification

See authoring/qa-results.json for structural results and authoring/livelabs-validator.txt for the upstream validator output. Rerun the supplemental check with:

```text
python scripts/validate-workshop.py
```

With Bash, Python 3, and standard Unix tools available, rerun the upstream check with:

```text
bash scripts/validate-livelabs-markdown.sh .
```

On Windows, the upstream validator requires a working `python3` command in Bash. If the Windows Store alias intercepts it, define a Bash function `python3() { python "$@"; }` and export it with `export -f python3` before running the validator.

## Review limits

This workshop covers the network preparation shown in Part 1. Database provisioning, HA configuration, connectivity, and failover need additional source material. No OCI resources were created or changed while authoring. Console execution and browser rendering have not been verified in this environment.

Four source screenshots appear in Lab 1. Lab 2 asks the learner to capture the saved rule because the source video does not show the adapted private-subnet result. The original ingress form is retained only under authoring/evidence.

The support alias in the manifest comes from the skill's generator and should be confirmed by the workshop owner before publication. The canonical sample path and skill QA wrapper refer to unavailable macOS paths; this workshop uses the upstream sample HTML and upstream markdown validator directly instead.

FreeSQL is not applicable because the workshop contains network setup rather than runnable SQL. No external accounts or publishing destinations were changed.

## Sources

- User-provided video: MySQL_Part1_VCN.mp4. The original remains in the parent workspace and is excluded from the delivery ZIP.
- [Oracle LiveLabs canonical launch file](https://github.com/oracle-livelabs/common/blob/main/sample-livelabs-templates/sample-workshop/workshops/tenancy/index.html)
- [Oracle LiveLabs markdown validator](https://github.com/oracle-livelabs/common/blob/main/md-validator/.github/scripts/validate-livelabs-markdown.sh)
- Product documentation links appear in each lab's Learn More section and in authoring/source-traceability.json.
