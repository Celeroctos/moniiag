-- Table: mis.insurances_regions

-- DROP TABLE mis.insurances_regions;

CREATE TABLE mis.insurances_regions
(
  id serial NOT NULL, -- Первичка
  insurance_id integer, -- Ссылка на страховую компанию
  region_id integer -- Ссылка на регион
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.insurances_regions
  OWNER TO postgres;
COMMENT ON TABLE mis.insurances_regions
  IS 'Таблица связи страховых компаний и регионов';
COMMENT ON COLUMN mis.insurances_regions.id IS 'Первичка';
COMMENT ON COLUMN mis.insurances_regions.insurance_id IS 'Ссылка на страховую компанию';
COMMENT ON COLUMN mis.insurances_regions.region_id IS 'Ссылка на регион';