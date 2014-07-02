CREATE TABLE mis.cancelled_greetings
(
  id serial NOT NULL,
  doctor_id integer, -- Доктор
  medcard_id character varying(50), -- Медкарта
  patient_day date, -- Дата приёма
  patient_time time without time zone, -- Время приёма
  mediate_id integer, -- ID опосредованного пациента (если есть. В противном случае - NULL)
  shedule_id integer, -- ID элемента расписания
  greeting_type integer, -- Тип приёма (первичный-вторичный)
  order_number integer,
  CONSTRAINT cancelled_greetings_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.cancelled_greetings
  OWNER TO moniiag;
COMMENT ON TABLE mis.cancelled_greetings
  IS 'Расписание врачей по дням';
COMMENT ON COLUMN mis.cancelled_greetings.doctor_id IS 'Доктор';
COMMENT ON COLUMN mis.cancelled_greetings.medcard_id IS 'Медкарта';
COMMENT ON COLUMN mis.cancelled_greetings.patient_day IS 'Дата приёма';
COMMENT ON COLUMN mis.cancelled_greetings.patient_time IS 'Время приёма';
COMMENT ON COLUMN mis.cancelled_greetings.mediate_id IS 'ID опосредованного пациента (если есть. В противном случае - NULL)';
COMMENT ON COLUMN mis.cancelled_greetings.shedule_id IS 'ID элемента расписания';
COMMENT ON COLUMN mis.cancelled_greetings.greeting_type IS 'Тип приёма (первичный-вторичный)';

