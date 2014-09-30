-- Table: mis.timetable

-- DROP TABLE mis.timetable;

CREATE TABLE mis.timetable
(
  id serial NOT NULL, -- Первичка
  date_begin date, -- Дата начала действия графика
  date_end date, -- Дата конца действия графика
  timetable_rules text -- Правила, которые содержит расписание в формате JSON
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.timetable
  OWNER TO postgres;
COMMENT ON TABLE mis.timetable
  IS 'Таблица расписаний (графиков) врачей. Хранит один график. График (расписание) включает в себя один и больше правил. Правило - это составная часть графика, отличающаяся от других правил временем, днём недели или кабинетом. Правила хранятся в поле timetable_rules в данной таблице';
COMMENT ON COLUMN mis.timetable.id IS 'Первичка';
COMMENT ON COLUMN mis.timetable.date_begin IS 'Дата начала действия графика';
COMMENT ON COLUMN mis.timetable.date_end IS 'Дата конца действия графика';
COMMENT ON COLUMN mis.timetable.timetable_rules IS 'Правила, которые содержит расписание в формате JSON';
