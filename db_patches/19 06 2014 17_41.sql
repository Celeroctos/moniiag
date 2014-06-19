-- Table: mis.tasu_fake_greetings

-- DROP TABLE mis.tasu_fake_greetings;

CREATE TABLE mis.tasu_fake_greetings
(
  id serial NOT NULL,
  card_number character varying(20), -- Номер карты
  doctor_id integer, -- ID доктора
  primary_diagnosis_id integer, -- ID первичного диагноза (из МБК-10)
  greeting_date date, -- Дата приёма
  CONSTRAINT tasu_fake_greetings_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.tasu_fake_greetings
  OWNER TO moniiag;
COMMENT ON TABLE mis.tasu_fake_greetings
  IS 'Приёмы для ТАСУ, которые заносятся вручную';
COMMENT ON COLUMN mis.tasu_fake_greetings.card_number IS 'Номер карты';
COMMENT ON COLUMN mis.tasu_fake_greetings.doctor_id IS 'ID доктора';
COMMENT ON COLUMN mis.tasu_fake_greetings.primary_diagnosis_id IS 'ID первичного диагноза (из МБК-10)';
COMMENT ON COLUMN mis.tasu_fake_greetings.greeting_date IS 'Дата приёма';

