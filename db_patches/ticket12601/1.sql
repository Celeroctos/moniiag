INSERT INTO mis.access_actions ("name","group","accessKey") VALUES ('Может добавлять правило генерации медкарты', 1, 'guideAddMedcardRule');
INSERT INTO mis.access_actions ("name","group","accessKey") VALUES ('Может редактировать правило генерации медкарты', 1, 'guideEditMedcardRule');
INSERT INTO mis.access_actions ("name","group","accessKey") VALUES ('Может удалять правило редактирования медкарты', 1, 'guideDeleteMedcardRule');
INSERT INTO mis.access_actions ("name","group","accessKey") VALUES ('Может добавлять префикс для медкарты', 1, 'guideAddMedcardPrefix');
INSERT INTO mis.access_actions ("name","group","accessKey") VALUES ('Может редактировать префикс для медкарты', 1, 'guideEditMedcardPrefix');
INSERT INTO mis.access_actions ("name","group","accessKey") VALUES ('Может удалять префикс для медкарты', 1, 'guideDeleteMedcardPrefix');
INSERT INTO mis.access_actions ("name","group","accessKey") VALUES ('Может добавлять постфикс для медкарты', 1, 'guideAddMedcardPostfix');
INSERT INTO mis.access_actions ("name","group","accessKey") VALUES ('Может редактировать постфикс для медкарты', 1, 'guideEditMedcardPostfix');
INSERT INTO mis.access_actions ("name","group","accessKey") VALUES ('Может удалять постфикс для медкарты', 1, 'guideDeleteMedcardPostfix');


-- Table: mis.medcards_history

-- DROP TABLE mis.medcards_history;

CREATE TABLE mis.medcards_history
(
  id serial NOT NULL,
  enterprise_id integer, -- ID заведения, у кого обозначена такая карта
  "from" character varying(50), -- Номер "до"
  "to" character varying(50), -- Номер "после"
  policy_id integer, -- ID ОМСа, к которому привязана карта to
  rule_id integer, -- ID правила, по которому сгенерирован номер
  reg_date date, -- Дата произведения действия
  CONSTRAINT medcards_history_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.medcards_history
  OWNER TO moniiag;
COMMENT ON TABLE mis.medcards_history
  IS 'История генерации-перерегистрации карт в привязке к ОМС';
COMMENT ON COLUMN mis.medcards_history.enterprise_id IS 'ID заведения, у кого обозначена такая карта';
COMMENT ON COLUMN mis.medcards_history."from" IS 'Номер "до"';
COMMENT ON COLUMN mis.medcards_history."to" IS 'Номер "после"';
COMMENT ON COLUMN mis.medcards_history.policy_id IS 'ID ОМСа, к которому привязана карта to';
COMMENT ON COLUMN mis.medcards_history.rule_id IS 'ID правила, по которому сгенерирован номер';
COMMENT ON COLUMN mis.medcards_history.reg_date IS 'Дата произведения действия';


-- Table: mis.medcards_postfixes

-- DROP TABLE mis.medcards_postfixes;

CREATE TABLE mis.medcards_postfixes
(
  id serial NOT NULL,
  value character varying(50),
  CONSTRAINT medcards_postfixes_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.medcards_postfixes
  OWNER TO moniiag;
  
-- Table: mis.medcards_prefixes

-- DROP TABLE mis.medcards_prefixes;

CREATE TABLE mis.medcards_prefixes
(
  id serial NOT NULL,
  value character varying(50),
  CONSTRAINT medcards_prefixes_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.medcards_prefixes
  OWNER TO moniiag;
COMMENT ON TABLE mis.medcards_prefixes
  IS 'Префиксы в номерах медкарт';

  -- Table: mis.medcards_rules

-- DROP TABLE mis.medcards_rules;

CREATE TABLE mis.medcards_rules
(
  id serial NOT NULL,
  prefix_id integer, -- ID префикса
  postfix_id integer, -- ID постфикса
  value integer, -- Правило формирования номера
  parent_id integer, -- Унаследован от правила
  name character varying(250),
  participle_mode_prefix integer, -- Режим работы с предыдущим префиксом. 0 - замена, 1 - добавление к старым новых
  participle_mode_postfix integer, -- Режим работы с предыдущим постфиксом. 0 - замена, 1 - добавление к старым новых
  prefix_separator_id integer, -- ID разделителя префикса
  postfix_separator_id integer, -- ID разделителя постфикса
  last_number character varying(250) -- Последний номер для такого правила
  CONSTRAINT medcards_rules_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.medcards_rules
  OWNER TO moniiag;
COMMENT ON TABLE mis.medcards_rules
  IS 'Правила формирования медкарт';
COMMENT ON COLUMN mis.medcards_rules.prefix_id IS 'ID префикса';
COMMENT ON COLUMN mis.medcards_rules.postfix_id IS 'ID постфикса';
COMMENT ON COLUMN mis.medcards_rules.value IS 'Правило формирования номера';
COMMENT ON COLUMN mis.medcards_rules.parent_id IS 'Унаследован от правила';
COMMENT ON COLUMN mis.medcards_rules.participle_mode_prefix IS 'Режим работы с предыдущим префиксом. 0 - замена, 1 - добавление к старым новых';
COMMENT ON COLUMN mis.medcards_rules.participle_mode_postfix IS 'Режим работы с предыдущим постфиксом. 0 - замена, 1 - добавление к старым новых';
COMMENT ON COLUMN mis.medcards_rules.prefix_separator_id IS 'ID разделителя префикса';
COMMENT ON COLUMN mis.medcards_rules.postfix_separator_id IS 'ID разделителя постфикса';

-- Table: mis.medcards_separators

-- DROP TABLE mis.medcards_separators;

CREATE TABLE mis.medcards_separators
(
  id serial NOT NULL,
  value character varying(50), -- Сам разделитель
  CONSTRAINT medcards_separators_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.medcards_separators
  OWNER TO moniiag;
COMMENT ON TABLE mis.medcards_separators
  IS 'Разделители номеров медицинских карт';
COMMENT ON COLUMN mis.medcards_separators.value IS 'Сам разделитель';

ALTER TABLE mis.wards ADD COLUMN rule_id INTEGER;

UPDATE mis.wards SET rule_id = 15;
UPDATE mis.medcards_rules SET last_number = '' WHERE id = 15;