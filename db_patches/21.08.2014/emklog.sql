-- Table: mis.emklog

-- DROP TABLE mis.emklog;

CREATE TABLE mis.emklog
(
  id serial NOT NULL,
  medcard_id integer, -- Код ЭМК
  user_changed integer, -- Пользователь, который внёс изменения в базу
  startdate date, -- Дата начала действий изменений
  enddate date, -- Дата конца действий изменений
  privelege_code integer, -- Код льготы
  snils character varying(50), -- СНИЛС
  address character varying(200), -- Адрес проживания фактический
  address_reg character varying(200), -- Адрес регистрации
  doctype integer, -- Тип документа
  serie character varying(20), -- Серия
  docnumber character varying(20), -- Номер
  who_gived character varying(200), -- Кем выдан
  gived_date date, -- Дата выдачи
  contact character varying(200), -- Контакты
  invalid_group integer, -- Группа инвалидности
  card_number character varying(50) NOT NULL, -- Номер карты
  enterprise_id integer, -- ID заведения
  policy_id integer, -- Номер полиса
  reg_date date, -- Дата регистрации карты
  work_place character varying(100), -- Место работы
  work_address character varying(100), -- Адрес работы
  post character varying(100), -- Должность на работе
  profession character varying(200), -- Профессия
  motion integer DEFAULT 0, -- Статус движения медкарты
  address_str text, -- Строковое представление адреса проживания для поиска
  address_reg_str text, -- Строковое представление адреса регистрации для поиска
  user_created integer,
  date_created timestamp without time zone,
  CONSTRAINT emklog_pkey PRIMARY KEY (id),
  CONSTRAINT emklog_doctype_fkey FOREIGN KEY (doctype)
      REFERENCES mis.doctypes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT emklog_enterprise_id_fkey FOREIGN KEY (enterprise_id)
      REFERENCES mis.enterprise_params (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT emklog_privelege_code_fkey FOREIGN KEY (privelege_code)
      REFERENCES mis.privileges (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.emklog
  OWNER TO moniiag;
COMMENT ON TABLE mis.emklog
  IS 'Медицинские карты';
COMMENT ON COLUMN mis.emklog.medcard_id IS 'Код ЭМК';
COMMENT ON COLUMN mis.emklog.user_changed IS 'Пользователь, который внёс изменения в базу';
COMMENT ON COLUMN mis.emklog.startdate IS 'Дата начала действий изменений';
COMMENT ON COLUMN mis.emklog.enddate IS 'Дата конца действий изменений';
COMMENT ON COLUMN mis.emklog.privelege_code IS 'Код льготы';
COMMENT ON COLUMN mis.emklog.snils IS 'СНИЛС';
COMMENT ON COLUMN mis.emklog.address IS 'Адрес проживания фактический';
COMMENT ON COLUMN mis.emklog.address_reg IS 'Адрес регистрации';
COMMENT ON COLUMN mis.emklog.doctype IS 'Тип документа';
COMMENT ON COLUMN mis.emklog.serie IS 'Серия';
COMMENT ON COLUMN mis.emklog.docnumber IS 'Номер';
COMMENT ON COLUMN mis.emklog.who_gived IS 'Кем выдан';
COMMENT ON COLUMN mis.emklog.gived_date IS 'Дата выдачи';
COMMENT ON COLUMN mis.emklog.contact IS 'Контакты';
COMMENT ON COLUMN mis.emklog.invalid_group IS 'Группа инвалидности';
COMMENT ON COLUMN mis.emklog.card_number IS 'Номер карты';
COMMENT ON COLUMN mis.emklog.enterprise_id IS 'ID заведения';
COMMENT ON COLUMN mis.emklog.policy_id IS 'Номер полиса';
COMMENT ON COLUMN mis.emklog.reg_date IS 'Дата регистрации карты';
COMMENT ON COLUMN mis.emklog.work_place IS 'Место работы';
COMMENT ON COLUMN mis.emklog.work_address IS 'Адрес работы';
COMMENT ON COLUMN mis.emklog.post IS 'Должность на работе';
COMMENT ON COLUMN mis.emklog.profession IS 'Профессия';
COMMENT ON COLUMN mis.emklog.motion IS 'Статус движения медкарты';
COMMENT ON COLUMN mis.emklog.address_str IS 'Строковое представление адреса проживания для поиска';
COMMENT ON COLUMN mis.emklog.address_reg_str IS 'Строковое представление адреса регистрации для поиска';

