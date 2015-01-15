-- Table: mis.doctors_timetables

-- DROP TABLE mis.doctors_timetables;

CREATE TABLE mis.doctors_timetables
(
  id serial NOT NULL, -- Первичка таблицы
  id_doctor integer, -- Ссылка на врача
  id_timetable integer -- Ссылка на расписание
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.doctors_timetables
  OWNER TO postgres;
COMMENT ON TABLE mis.doctors_timetables
  IS 'Таблица связей многие ко многим докторов и расписаний (у доктора как правило несколько расписаний на разные промежутки времени) и у нескольких докторов может быть одно расписание';
COMMENT ON COLUMN mis.doctors_timetables.id IS 'Первичка таблицы';
COMMENT ON COLUMN mis.doctors_timetables.id_doctor IS 'Ссылка на врача';
COMMENT ON COLUMN mis.doctors_timetables.id_timetable IS 'Ссылка на расписание';

