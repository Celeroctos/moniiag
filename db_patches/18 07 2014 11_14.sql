-- Column: oms_series_number

-- ALTER TABLE mis.oms DROP COLUMN oms_series_number;

ALTER TABLE mis.oms ADD COLUMN oms_series_number character varying(60);
COMMENT ON COLUMN mis.oms.oms_series_number IS 'Серия+номер ОМС - дефисы - пробелы';
