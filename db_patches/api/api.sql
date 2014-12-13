DROP TRIGGER on_delete_api ON mis.api;
DROP FUNCTION mis.delete_api();

DELETE FROM mis.api_rule;
DELETE FROM mis.api;

CREATE TABLE mis.api (
	key character varying(40) NOT NULL,
	description text,
	path character varying(100),
	CONSTRAINT api_pkey PRIMARY KEY (key)
);