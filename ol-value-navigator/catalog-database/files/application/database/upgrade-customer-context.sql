-- Add only missing presentation-context columns. Safe to rerun after a partial
-- upgrade or against a fresh schema. No comparison data or results are changed.
USE ol_value_navigator;

SET @context_sql = IF(
  EXISTS(SELECT 1 FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'comparison' AND column_name = 'customer_name'),
  'SELECT ''customer_name already present'' AS upgrade_status',
  'ALTER TABLE comparison ADD COLUMN customer_name VARCHAR(120) NOT NULL DEFAULT ''''');
PREPARE context_upgrade FROM @context_sql;
EXECUTE context_upgrade;
DEALLOCATE PREPARE context_upgrade;

SET @context_sql = IF(
  EXISTS(SELECT 1 FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'comparison' AND column_name = 'customer_objective'),
  'SELECT ''customer_objective already present'' AS upgrade_status',
  'ALTER TABLE comparison ADD COLUMN customer_objective VARCHAR(300) NOT NULL DEFAULT ''''');
PREPARE context_upgrade FROM @context_sql;
EXECUTE context_upgrade;
DEALLOCATE PREPARE context_upgrade;

SET @context_sql = IF(
  EXISTS(SELECT 1 FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'comparison' AND column_name = 'comparison_scope'),
  'SELECT ''comparison_scope already present'' AS upgrade_status',
  'ALTER TABLE comparison ADD COLUMN comparison_scope VARCHAR(300) NOT NULL DEFAULT ''''');
PREPARE context_upgrade FROM @context_sql;
EXECUTE context_upgrade;
DEALLOCATE PREPARE context_upgrade;

SET @context_sql = IF(
  EXISTS(SELECT 1 FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'comparison' AND column_name = 'recommended_next_step'),
  'SELECT ''recommended_next_step already present'' AS upgrade_status',
  'ALTER TABLE comparison ADD COLUMN recommended_next_step VARCHAR(300) NOT NULL DEFAULT ''''');
PREPARE context_upgrade FROM @context_sql;
EXECUTE context_upgrade;
DEALLOCATE PREPARE context_upgrade;

SELECT column_name, character_maximum_length
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = 'comparison'
  AND column_name IN ('customer_name', 'customer_objective', 'comparison_scope', 'recommended_next_step')
ORDER BY column_name;

