   CREATE SEQUENCE mis.doctypes_id_seq
     INCREMENT BY 1
     NO MAXVALUE
     NO MINVALUE
     CACHE 1;

ALTER TABLE mis.doctypes ALTER COLUMN id SET NOT NULL;
ALTER TABLE mis.doctypes ALTER COLUMN id SET DEFAULT nextval('mis.doctypes_id_seq'::regclass);

SELECT setval('mis.doctypes_id_seq', (SELECT MAX(id) FROM mis.doctypes));