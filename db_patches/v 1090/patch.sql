-- Column: user_id

-- ALTER TABLE mis.doctors DROP COLUMN user_id;

ALTER TABLE mis.doctors ADD COLUMN user_id integer;
COMMENT ON COLUMN mis.doctors.user_id IS 'ID пользователя, к которому привязан сотрудник';

-- Table: mis.tasu_medcards_buffer

-- DROP TABLE mis.tasu_medcards_buffer;

CREATE TABLE mis.tasu_medcards_buffer
(
  id serial NOT NULL,
  medcard character varying(20), -- Номер карты
  import_id integer, -- ID импорта
  status integer DEFAULT 0, -- Статус карты: 0 - не выгружена, 1 - выгружена
  CONSTRAINT tasu_medcards_buffer_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.tasu_medcards_buffer
  OWNER TO moniiag;
COMMENT ON TABLE mis.tasu_medcards_buffer
  IS 'Импортирование медкарт в ТАСУ';
COMMENT ON COLUMN mis.tasu_medcards_buffer.medcard IS 'Номер карты';
COMMENT ON COLUMN mis.tasu_medcards_buffer.import_id IS 'ID импорта';
COMMENT ON COLUMN mis.tasu_medcards_buffer.status IS 'Статус карты: 0 - не выгружена, 1 - выгружена';

-- Table: mis.tasu_medcards_buffer_history

-- DROP TABLE mis.tasu_medcards_buffer_history;

CREATE TABLE mis.tasu_medcards_buffer_history
(
  id serial NOT NULL,
  num_rows integer, -- Кол-во выгруженных строк
  create_date timestamp without time zone, -- Дата создания выгрузки
  status integer, -- Статус выгрузки
  import_id integer, -- ID импорта
  log_path text, -- Путь до лога
  CONSTRAINT tasu_medcards_buffer_history_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.tasu_medcards_buffer_history
  OWNER TO moniiag;
COMMENT ON TABLE mis.tasu_medcards_buffer_history
  IS 'История выгрузок в ТАСУ';
COMMENT ON COLUMN mis.tasu_medcards_buffer_history.num_rows IS 'Кол-во выгруженных строк';
COMMENT ON COLUMN mis.tasu_medcards_buffer_history.create_date IS 'Дата создания выгрузки';
COMMENT ON COLUMN mis.tasu_medcards_buffer_history.status IS 'Статус выгрузки';
COMMENT ON COLUMN mis.tasu_medcards_buffer_history.import_id IS 'ID импорта';
COMMENT ON COLUMN mis.tasu_medcards_buffer_history.log_path IS 'Путь до лога';

-- Column: employee_id

-- ALTER TABLE mis.role_action DROP COLUMN employee_id;

ALTER TABLE mis.role_action ADD COLUMN employee_id integer;

UPDATE mis.role_action SET employee_id = -1;

ALTER TABLE mis.role_action ALTER COLUMN employee_id SET NOT NULL;
COMMENT ON COLUMN mis.role_action.employee_id IS 'Частное правило: id сотрудника';

-- Column: mode

-- ALTER TABLE mis.role_action DROP COLUMN mode;

ALTER TABLE mis.role_action ADD COLUMN mode integer;
COMMENT ON COLUMN mis.role_action.mode IS 'Частное правило: режим правила. 0 - добавить к роли, 1 - исключить из роли';
