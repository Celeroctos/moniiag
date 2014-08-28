-- Table: mis.tasu_fake_greetings_secondary_diag

-- DROP TABLE mis.tasu_fake_greetings_secondary_diag;

CREATE TABLE mis.tasu_fake_greetings_secondary_diag
(
  id serial NOT NULL,
  buffer_id integer, -- ID буфера выгрузки
  diagnosis_id integer,
  CONSTRAINT tasu_fake_greetings_secondary_diag_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.tasu_fake_greetings_secondary_diag
  OWNER TO postgres;
COMMENT ON COLUMN mis.tasu_fake_greetings_secondary_diag.buffer_id IS 'ID буфера выгрузки';

