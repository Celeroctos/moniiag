-- Column: is_default

-- ALTER TABLE mis.payment_types DROP COLUMN is_default;

ALTER TABLE mis.payment_types ADD COLUMN is_default integer;
COMMENT ON COLUMN mis.payment_types.is_default IS '��������� �������� ���� ������';

UPDATE mis.payment_types SET is_default = 0;
UPDATE mis.payment_types SET is_default = 1 WHERE id = 2;
