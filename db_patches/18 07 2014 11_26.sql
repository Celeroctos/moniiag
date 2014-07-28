-- Index: mis.oms_series_number

-- DROP INDEX mis.oms_series_number;

CREATE INDEX oms_series_number
  ON mis.oms
  USING btree
  (oms_series_number COLLATE pg_catalog."default");
