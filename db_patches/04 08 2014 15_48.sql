-- Index: mis.reg_date

-- DROP INDEX mis.reg_date;

CREATE INDEX reg_date
  ON mis.medcards
  USING btree
  (reg_date);
