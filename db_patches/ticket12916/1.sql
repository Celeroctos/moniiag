-- Column: is_default

-- ALTER TABLE mis.payment_types DROP COLUMN is_default;

ALTER TABLE mis.payment_types ADD COLUMN is_default integer;
COMMENT ON COLUMN mis.payment_types.is_default IS '��������� �������� ���� ������';

UPDATE mis.payment_types SET is_default = 0;
UPDATE mis.payment_types SET is_default = 1 WHERE id = 2;

-- Column: is_default

-- ALTER TABLE mis.medservices DROP COLUMN is_default;

ALTER TABLE mis.medservices ADD COLUMN is_default integer;
COMMENT ON COLUMN mis.medservices.is_default IS '�������� �� ��������� ��� ���';

UPDATE mis.medservices SET is_default = 0;
-- Column: service_id

-- ALTER TABLE mis.tasu_fake_greetings DROP COLUMN service_id;

ALTER TABLE mis.tasu_fake_greetings ADD COLUMN service_id integer;
COMMENT ON COLUMN mis.tasu_fake_greetings.service_id IS 'ID ������ ��� ����';
