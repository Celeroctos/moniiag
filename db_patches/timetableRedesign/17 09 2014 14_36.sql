-- Table: mis.timetable_facts

-- DROP TABLE mis.timetable_facts;

CREATE TABLE mis.timetable_facts
(
  id serial NOT NULL, -- Первичка
  is_range integer, -- Флаг о том, что нужно указать промежуток, а не день
  name character varying(150)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.timetable_facts
  OWNER TO postgres;
COMMENT ON TABLE mis.timetable_facts
  IS 'Таблица обстоятельств для расписания';
COMMENT ON COLUMN mis.timetable_facts.id IS 'Первичка';
COMMENT ON COLUMN mis.timetable_facts.is_range IS 'Флаг о том, что нужно указать промежуток, а не день';