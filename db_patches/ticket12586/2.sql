-- Column: is_default

-- ALTER TABLE mis.medservices DROP COLUMN is_default;

ALTER TABLE mis.medservices ADD COLUMN is_default integer;
COMMENT ON COLUMN mis.medservices.is_default IS 'Значение по умолчанию или нет';

-- Column: service_id

-- ALTER TABLE mis.tasu_fake_greetings DROP COLUMN service_id;

ALTER TABLE mis.tasu_fake_greetings ADD COLUMN service_id integer;
COMMENT ON COLUMN mis.tasu_fake_greetings.service_id IS 'ID услуги для ТАСУ';