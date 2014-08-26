-- Index: mis.greeting_id

-- DROP INDEX mis.greeting_id;

CREATE INDEX greeting_id
  ON mis.medcard_elements_patient
  USING btree
  (greeting_id);
