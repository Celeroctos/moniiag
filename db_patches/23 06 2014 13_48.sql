ALTER TABLE mis.cladr_districts ALTER COLUMN code_cladr TYPE character varying(32);
ALTER TABLE mis.cladr_districts ALTER COLUMN code_region TYPE character varying(32);
ALTER TABLE mis.cladr_districts ADD COLUMN fake_cladr integer;
ALTER TABLE mis.cladr_districts ALTER COLUMN fake_cladr SET DEFAULT 0;

ALTER TABLE mis.cladr_regions ALTER COLUMN code_cladr TYPE character varying(32);
ALTER TABLE mis.cladr_regions ADD COLUMN fake_cladr integer;
ALTER TABLE mis.cladr_regions ALTER COLUMN fake_cladr SET DEFAULT 0;

ALTER TABLE mis.cladr_settlements ADD COLUMN fake_cladr integer;
ALTER TABLE mis.cladr_settlements ALTER COLUMN fake_cladr SET DEFAULT 0;

ALTER TABLE mis.cladr_streets ADD COLUMN fake_cladr integer;
ALTER TABLE mis.cladr_streets ALTER COLUMN fake_cladr SET DEFAULT 0;