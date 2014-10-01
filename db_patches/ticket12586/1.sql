INSERT INTO mis.access_actions ("name","group","accessKey") VALUES ('Может добавлять тип оплаты', 1, 'guideAddPaymentType');
INSERT INTO mis.access_actions ("name","group","accessKey") VALUES ('Может редактировать тип оплаты', 1, 'guideEditPaymentType');
INSERT INTO mis.access_actions ("name","group","accessKey") VALUES ('Может удалять разделитель тип оплаты', 1, 'guideDeletePaymentType');

-- Table: mis.payment_types

-- DROP TABLE mis.payment_types;

CREATE TABLE mis.payment_types
(
  id serial NOT NULL,
  name character varying(200), -- Название
  tasu_string character varying(200), -- Строка для ТАСУ
  CONSTRAINT payment_types_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.payment_types
  OWNER TO moniiag;
COMMENT ON TABLE mis.payment_types
  IS 'Типы оплат медуслуг';
COMMENT ON COLUMN mis.payment_types.name IS 'Название';
COMMENT ON COLUMN mis.payment_types.tasu_string IS 'Строка для ТАСУ';