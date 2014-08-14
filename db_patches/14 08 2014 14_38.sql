-- Column: is_empty

-- ALTER TABLE mis.medcard_records DROP COLUMN is_empty;

ALTER TABLE mis.medcard_records ADD COLUMN is_empty integer;
COMMENT ON COLUMN mis.medcard_records.is_empty IS 'Флаг пустоты шаблона. Если 0 - то в шаблоне нет ни одного заполненного поля, 1 - если хотя бы одно поле проставлено в шаблоне';
