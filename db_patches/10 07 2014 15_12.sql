CREATE TABLE mis.oms_statuses
(
  id serial NOT NULL, -- Первичка
  tasu_id integer, -- Код в ТАСУ
  name character varying(128) -- Название статуса
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.oms_statuses
  OWNER TO postgres;
COMMENT ON TABLE mis.oms_statuses
  IS 'Статусы полисов';
COMMENT ON COLUMN mis.oms_statuses.id IS 'Первичка';
COMMENT ON COLUMN mis.oms_statuses.tasu_id IS 'Код в ТАСУ';
COMMENT ON COLUMN mis.oms_statuses.name IS 'Название статуса';




INSERT INTO mis.oms_statuses(tasu_id,"name") VALUES (1,'Активен');
INSERT INTO mis.oms_statuses(tasu_id,"name") VALUES (2,'Дубль');
INSERT INTO mis.oms_statuses(tasu_id,"name") VALUES (3,'Погашен');
INSERT INTO mis.oms_statuses(tasu_id,"name") VALUES (4,'Приостановлен');