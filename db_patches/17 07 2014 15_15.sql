-- Column: oms_series

-- ALTER TABLE mis.oms DROP COLUMN oms_series;

ALTER TABLE mis.oms ADD COLUMN oms_series character varying(20);
COMMENT ON COLUMN mis.oms.oms_series IS 'Серия полиса';
