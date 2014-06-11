-- Table: mis.medcard_records

-- DROP TABLE mis.medcard_records;

CREATE TABLE mis.medcard_records
(
  id serial NOT NULL, -- Первичка
  medcard_id character varying, -- Номер медкарты
  greeting_id integer, -- Номер приёма
  record_id integer, -- Номер записи в приёме в карте
  template_name text, -- Имя шаблона
  doctor_id integer, -- Ссылка на врача
  record_date timestamp without time zone, -- Дата сохранения
  template_id integer -- ИД шаблона
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.medcard_records
  OWNER TO postgres;
COMMENT ON TABLE mis.medcard_records
  IS 'Записи в медкарте. Каждая запись соответсвует одному шаблону';
COMMENT ON COLUMN mis.medcard_records.id IS 'Первичка';
COMMENT ON COLUMN mis.medcard_records.medcard_id IS 'Номер медкарты';
COMMENT ON COLUMN mis.medcard_records.greeting_id IS 'Номер приёма';
COMMENT ON COLUMN mis.medcard_records.record_id IS 'Номер записи в приёме в карте';
COMMENT ON COLUMN mis.medcard_records.template_name IS 'Имя шаблона';
COMMENT ON COLUMN mis.medcard_records.doctor_id IS 'Ссылка на врача';
COMMENT ON COLUMN mis.medcard_records.record_date IS 'Дата сохранения';
COMMENT ON COLUMN mis.medcard_records.template_id IS 'ИД шаблона';

