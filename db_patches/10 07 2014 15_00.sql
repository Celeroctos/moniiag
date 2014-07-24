
CREATE TABLE mis.oms_types
(
  id serial NOT NULL, -- Первичка
  tasu_id integer, -- Код в ТАСУ
  name character varying(128) -- Название типа полиса
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.oms_types
  OWNER TO postgres;
COMMENT ON TABLE mis.oms_types
  IS 'Тип ОМС';
COMMENT ON COLUMN mis.oms_types.id IS 'Первичка';
COMMENT ON COLUMN mis.oms_types.tasu_id IS 'Код в ТАСУ';
COMMENT ON COLUMN mis.oms_types.name IS 'Название типа полиса';


INSERT INTO mis.oms_types(tasu_id,"name") VALUES (1,'Тер. полис ОМС');
INSERT INTO mis.oms_types(tasu_id,"name") VALUES (2,'Полис ДМС');
INSERT INTO mis.oms_types(tasu_id,"name") VALUES (3,'Временный');
INSERT INTO mis.oms_types(tasu_id,"name") VALUES (4,'Ходатайство о регистрации');
INSERT INTO mis.oms_types(tasu_id,"name") VALUES (5,'Постоянный');
