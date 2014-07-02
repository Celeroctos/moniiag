-- Table: mis.rebinded_medcards

-- DROP TABLE mis.rebinded_medcards;

CREATE TABLE mis.rebinded_medcards
(
  id serial NOT NULL, -- Первичка
  card_number character varying(100), -- Номер карты
  old_policy integer, -- Старый номер полиса
  new_policy integer, -- Новый номер полиса
  changing_timestamp timestamp without time zone, -- Дата изменения
  worker_id integer -- ИД работника, выполнившего действие
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.rebinded_medcards
  OWNER TO postgres;
COMMENT ON TABLE mis.rebinded_medcards
  IS 'Перепривязанные медкарты. Используется для того, чтобы знать какая карта когда была привязана к другому полису';
COMMENT ON COLUMN mis.rebinded_medcards.id IS 'Первичка';
COMMENT ON COLUMN mis.rebinded_medcards.card_number IS 'Номер карты';
COMMENT ON COLUMN mis.rebinded_medcards.old_policy IS 'Старый номер полиса';
COMMENT ON COLUMN mis.rebinded_medcards.new_policy IS 'Новый номер полиса';
COMMENT ON COLUMN mis.rebinded_medcards.changing_timestamp IS 'Дата изменения';
COMMENT ON COLUMN mis.rebinded_medcards.worker_id IS 'ИД работника, выполнившего действие';

