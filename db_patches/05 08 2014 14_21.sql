-- Table: mis.logs

-- DROP TABLE mis.logs;

CREATE TABLE mis.logs
(
  id serial NOT NULL,
  user_id integer, -- ID пользователя
  url text, -- Текст запроса
  changedate date, -- Дата действия
  changetime time without time zone, -- Время действия
  CONSTRAINT logs_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.logs
  OWNER TO moniiag;
COMMENT ON TABLE mis.logs
  IS 'Логи системы';
COMMENT ON COLUMN mis.logs.user_id IS 'ID пользователя';
COMMENT ON COLUMN mis.logs.url IS 'Текст запроса';
COMMENT ON COLUMN mis.logs.changedate IS 'Дата действия';
COMMENT ON COLUMN mis.logs.changetime IS 'Время действия';