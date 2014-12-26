-- Column: categorie

-- ALTER TABLE mis.doctors DROP COLUMN categorie;

ALTER TABLE mis.doctors ADD COLUMN categorie integer;
COMMENT ON COLUMN mis.doctors.categorie IS '-- Категория врача';

/* В таблице mis.degrees изменить: Кандидат наук -> К.м.н , Доктор наук -> Д.м.н*/