-- Table: mis.comments_oms

-- DROP TABLE mis.comments_oms;

CREATE TABLE mis.comments_oms
(
  id integer NOT NULL DEFAULT nextval('mis.medcard_comments_id_seq'::regclass), -- Первичка
  comment text, -- Сам текст комментария
  id_oms integer, -- Ссылка на ОМС
  create_date timestamp without time zone, -- Дата и время, когда комментарий был сделан
  employer_id integer -- ИД работника, который сделал данный коммент
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.comments_oms
  OWNER TO postgres;
COMMENT ON TABLE mis.comments_oms
  IS 'Комментарии к пациентам, которые видит врач';
COMMENT ON COLUMN mis.comments_oms.id IS 'Первичка';
COMMENT ON COLUMN mis.comments_oms.comment IS 'Сам текст комментария';
COMMENT ON COLUMN mis.comments_oms.id_oms IS 'Ссылка на ОМС';
COMMENT ON COLUMN mis.comments_oms.create_date IS 'Дата и время, когда комментарий был сделан';
COMMENT ON COLUMN mis.comments_oms.employer_id IS 'ИД работника, который сделал данный коммент';
