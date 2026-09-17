-- Run as the V3 DB administrator only. No password belongs in this file.
-- All three grants were verified by Perside with SQL and PHP on September 17, 2026.
-- Existing V3 rehearsal already has these grants. New installs apply this once.
GRANT EXECUTE ON FUNCTION sys.ML_GENERATE
TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT EXECUTE ON PROCEDURE sys.ML_CLUSTER_CHECK
TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT EXECUTE ON FUNCTION sys.ML_GENAI_VARIABLE
TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
