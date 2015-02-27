<?php

class m150227_121638_laboratory_guides extends CDbMigration
{
	public function up()
	{
$connection=Yii::app()->db;

$sql_create_schema=<<<HERE
CREATE SCHEMA lis;
HERE;


$sql_alter_schema=<<<HERE
ALTER SCHEMA lis OWNER TO postgres;
HERE;

$sql_search_path=<<<HERE
SET search_path = lis, pg_catalog;
HERE;

$sql_default_tablespace=<<<HERE
SET default_tablespace = '';
HERE;

$sql_default_with_oids=<<<HERE
SET default_with_oids = false;
HERE;

$sql_create_analysis_param_id_seq=<<<HERE
CREATE SEQUENCE lis.analysis_param_id_seq
  INCREMENT 1
  MINVALUE 1
  NO MAXVALUE
  START 1
  CACHE 1;
HERE;
$sql_owner_analysis_param_id_seq=<<<HERE
ALTER TABLE lis.analysis_param_id_seq OWNER TO moniiag;
HERE;

$sql_create_analysis_sample_type_id_seq=<<<HERE
CREATE SEQUENCE lis.analysis_sample_type_id_seq
  INCREMENT 1
  MINVALUE 1
  NO MAXVALUE
  START 1
  CACHE 1;
HERE;

$sql_owner_analysis_sample_type_id_seq=<<<HERE
ALTER TABLE lis.analysis_sample_type_id_seq OWNER TO moniiag;
HERE;

$sql_create_analysis_type_id_seq=<<<HERE
CREATE SEQUENCE lis.analysis_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
HERE;
$sql_owner_analysis_type_id_seq=<<<HERE
ALTER TABLE lis.analysis_type_id_seq OWNER TO moniiag;
HERE;

$sql_create_analysis_type_template_id_seq=<<<HERE
CREATE SEQUENCE lis.analysis_type_template_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
HERE;

$sql_owner_analysis_type_template_id_seq=<<<HERE
ALTER TABLE lis.analysis_type_template_id_seq OWNER TO moniiag;
HERE;

$sql_create_analyzer_type_id_seq=<<<HERE
CREATE SEQUENCE lis.analyzer_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
HERE;
$sql_owner_analyzer_type_id_seq=<<<HERE
ALTER TABLE lis.analyzer_type_id_seq OWNER TO moniiag;
HERE;

$sql_create_analyzer_type_analysis_id_seq=<<<HERE
CREATE SEQUENCE lis.analyzer_type_analysis_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
HERE;
$sql_owner_analyzer_type_analysis_id_seq=<<<HERE
ALTER TABLE lis.analyzer_type_analysis_id_seq OWNER TO moniiag;
HERE;

$sql_create_analysis_params=<<<HERE
CREATE TABLE lis.analysis_params
(
  id integer NOT NULL DEFAULT nextval('lis.analysis_param_id_seq'::regclass), -- Первичный ключ
  name character varying(30) NOT NULL, -- Краткое наименование параметра анализа
  long_name character varying(200), -- Полное наименование параметра анализа
  comment text, -- Примечания
  CONSTRAINT analysis_param_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
HERE;

$sql_owner_analysis_params=<<<HERE
ALTER TABLE lis.analysis_params OWNER TO moniiag;
HERE;

$sql_owned_analysis_param_id_seq=<<<HERE
ALTER SEQUENCE lis.analysis_param_id_seq OWNED BY lis.analysis_params.id;
HERE;

$sql_comment_analysis_params=<<<HERE
COMMENT ON TABLE lis.analysis_params IS 'Список параметров анализов';
HERE;

$sql_comment_analysis_params_id=<<<HERE
COMMENT ON COLUMN lis.analysis_params.id IS 'Первичный ключ';
HERE;

$sql_comment_analysis_params_name=<<<HERE
COMMENT ON COLUMN lis.analysis_params.name IS 'Краткое наименование параметра анализа';
HERE;

$sql_comment_analysis_params_long_name=<<<HERE
COMMENT ON COLUMN lis.analysis_params.long_name IS 'Полное наименование параметра анализа';
HERE;

$sql_comment_analysis_params_comment=<<<HERE
COMMENT ON COLUMN lis.analysis_params.comment IS 'Примечания';
HERE;

$sql_create_analysis_samples_types=<<<HERE
CREATE TABLE lis.analysis_samples_types
(
  id integer NOT NULL DEFAULT nextval('lis.analysis_sample_type_id_seq'::regclass), -- Первичный ключ
  type character varying(100), -- Тип образца
  subtype character varying(100), -- Подтип образца
  CONSTRAINT analysis_sample_type_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
HERE;

$sql_owner_analysis_samples_types=<<<HERE
ALTER TABLE lis.analysis_samples_types OWNER TO moniiag;
HERE;

$sql_owned_analysis_sample_type_id_seq=<<<HERE
ALTER SEQUENCE lis.analysis_sample_type_id_seq OWNED BY lis.analysis_samples_types.id;
HERE;

$sql_comment_analysis_samples_types=<<<HERE
COMMENT ON TABLE lis.analysis_samples_types IS 'Типы и подтипы образцов для анализа';
HERE;

$sql_comment_analysis_samples_types_id=<<<HERE
COMMENT ON COLUMN lis.analysis_samples_types.id IS 'Первичный ключ';
HERE;

$sql_comment_analysis_samples_types_type=<<<HERE
COMMENT ON COLUMN lis.analysis_samples_types.type IS 'Тип образца';
HERE;

$sql_comment_analysis_samples_types_subtype=<<<HERE
COMMENT ON COLUMN lis.analysis_samples_types.subtype IS 'Подтип образца';
HERE;

$sql_create_analysis_types=<<<HERE
CREATE TABLE lis.analysis_types
(
  id integer NOT NULL DEFAULT nextval('lis.analysis_type_id_seq'::regclass), -- Первичный ключ
  name character varying(200), -- Наименование анализа
  short_name character varying(20), -- Краткое наименование анализа
  automatic boolean NOT NULL DEFAULT false, -- Автоматическая методика
  manual boolean NOT NULL DEFAULT false, -- Ручная методика
  CONSTRAINT analysis_type_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
HERE;

$sql_owner_analysis_types=<<<HERE
ALTER TABLE lis.analysis_types OWNER TO moniiag;
HERE;

$sql_owned_analysis_type_id_seq=<<<HERE
ALTER SEQUENCE lis.analysis_type_id_seq OWNED BY lis.analysis_types.id;
HERE;

$sql_comment_analysis_types=<<<HERE
COMMENT ON TABLE lis.analysis_types IS 'Списко типов анализов';
HERE;

$sql_comment_analysis_types_id=<<<HERE
COMMENT ON COLUMN lis.analysis_types.id IS 'Первичный ключ';
HERE;

$sql_comment_analysis_types_name=<<<HERE
COMMENT ON COLUMN lis.analysis_types.name IS 'Наименование анализа';
HERE;

$sql_comment_analysis_types_short_name=<<<HERE
COMMENT ON COLUMN lis.analysis_types.short_name IS 'Краткое наименование анализа';
HERE;

$sql_comment_analysis_types_automatic=<<<HERE
COMMENT ON COLUMN lis.analysis_types.automatic IS 'Автоматическая методика';
HERE;

$sql_comment_analysis_types_manual=<<<HERE
COMMENT ON COLUMN lis.analysis_types.manual IS 'Ручная методика';
HERE;


$sql_create_analysis_type_templates=<<<HERE
CREATE TABLE lis.analysis_type_templates
(
  id integer NOT NULL DEFAULT nextval('lis.analysis_type_template_id_seq'::regclass),
  analysis_type_id integer NOT NULL, -- ID типа анализа
  analysis_param_id integer NOT NULL, -- ID параметра анализа
  is_default boolean NOT NULL DEFAULT false, -- Включен по умолчанию?
  CONSTRAINT analysis_type_templates_pk PRIMARY KEY (analysis_type_id, analysis_param_id),
  CONSTRAINT template_analysis_params_fk FOREIGN KEY (analysis_param_id)
      REFERENCES lis.analysis_params (id) MATCH FULL
      ON UPDATE NO ACTION ON DELETE RESTRICT,
  CONSTRAINT analysis_type_templates_uk UNIQUE (analysis_type_id, analysis_param_id)
)
WITH (
  OIDS=FALSE
);
HERE;

$sql_owner_analysis_type_templates=<<<HERE
ALTER TABLE lis.analysis_type_templates OWNER TO moniiag;
HERE;

$sql_owned_analysis_type_template_id_seq=<<<HERE
ALTER SEQUENCE lis.analysis_type_template_id_seq OWNED BY lis.analysis_type_templates.id;
HERE;

$sql_comment_analysis_type_templates=<<<HERE
COMMENT ON TABLE lis.analysis_type_templates IS 'Шаблон типа анализа (список параметров типа)';
HERE;

$sql_comment_analysis_type_templates_analysis_type_id=<<<HERE
COMMENT ON COLUMN lis.analysis_type_templates.analysis_type_id IS 'ID типа анализа';
HERE;

$sql_analysis_type_templates_analysis_param_id=<<<HERE
COMMENT ON COLUMN lis.analysis_type_templates.analysis_param_id IS 'ID параметра анализа';
HERE;

$sql_comment_analysis_type_templates_is_default=<<<HERE
COMMENT ON COLUMN lis.analysis_type_templates.is_default IS 'Включен по умолчанию?';
HERE;


$sql_create_analyzer_type_analysis=<<<HERE
CREATE TABLE lis.analyzer_type_analysis
(
  id integer NOT NULL DEFAULT nextval('lis.analyzer_type_analysis_id_seq'::regclass),
  analyser_type_id integer NOT NULL, -- ID типа анализатора
  analysis_type_id integer NOT NULL, -- ID типа анализа
  CONSTRAINT analyzer_type_analysis_pk PRIMARY KEY (analyzer_type_analysis.id),
  CONSTRAINT analyzer_type_analysis_type_fk FOREIGN KEY (analysis_type_id)
      REFERENCES lis.analysis_types (id) MATCH FULL
      ON UPDATE NO ACTION ON DELETE RESTRICT,
  CONSTRAINT analyzer_type_fk FOREIGN KEY (analyser_type_id)
      REFERENCES lis.analyzer_types (id) MATCH FULL
      ON UPDATE NO ACTION ON DELETE RESTRICT,
	  CONSTRAINT analyzer_type_analysis_uk UNIQUE (analyser_type_id, analysis_type_id)
)
WITH (
  OIDS=FALSE
);
HERE;

$sql_owner_analyzer_type_analysis=<<<HERE
ALTER TABLE lis.analyzer_type_analysis OWNER TO moniiag;
HERE;
$sql_owned_analyzer_type_analysis_id_seq=<<<HERE
ALTER SEQUENCE lis.analyzer_type_analysis_id_seq OWNED BY lis.analyzer_type_analysis.id;
HERE;

$sql_comment_analyzer_type_analysis=<<<HERE
COMMENT ON TABLE lis.analyzer_type_analysis IS 'Список типов анализов доступных на анализаторе определенного типа';
HERE;

$sql_comment_analyzer_type_analysis_id=<<<HERE
COMMENT ON COLUMN lis.analyzer_type_analysis.id IS 'Первичный ключ';
HERE;

$sql_comment_analyzer_type_analysis_analyser_type_id=<<<HERE
COMMENT ON COLUMN lis.analyzer_type_analysis.analyser_type_id IS 'ID типа анализатора';
HERE;

$sql_comment_analyzer_type_analysis_analysis_type_id=<<<HERE
COMMENT ON COLUMN lis.analyzer_type_analysis.analysis_type_id IS 'ID типа анализа';
HERE;

$sql_create_analyzer_types=<<<HERE
CREATE TABLE lis.analyzer_types
(
  id integer NOT NULL DEFAULT nextval('lis.analyzer_type_id_seq'::regclass), -- Первичный ключ
  type character varying(100), -- Тип анализатора
  name character varying(100), -- Название анализатора
  notes text, -- Пометки
  CONSTRAINT analyzer_type_id_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
HERE;

$sql_owner_analyzer_types=<<<HERE
ALTER TABLE lis.analyzer_types OWNER TO moniiag;
HERE;
$sql_owned_analyzer_type_id_seq=<<<HERE
ALTER SEQUENCE lis.analyzer_type_id_seq OWNED BY analyzer_types.id;
HERE;

$sql_comment_analyzer_types=<<<HERE
COMMENT ON TABLE lis.analyzer_types IS 'Список типов анализаторов';
HERE;

$sql_comment_analyzer_types_id=<<<HERE
COMMENT ON COLUMN lis.analyzer_types.id IS 'Первичный ключ';
HERE;

$sql_comment_analyzer_types_type=<<<HERE
COMMENT ON COLUMN lis.analyzer_types.type IS 'Тип анализатора';
HERE;

$sql_comment_analyzer_types_name=<<<HERE
COMMENT ON COLUMN lis.analyzer_types.name IS 'Название анализатора';
HERE;

$sql_comment_analyzer_types_notes=<<<HERE
COMMENT ON COLUMN lis.analyzer_types.notes IS 'Пометки';
HERE;



$sql_create_fki_analyzer_type_analysis_type_fk=<<<HERE
CREATE INDEX lis.fki_analyzer_type_analysis_type_fk ON lis.analyzer_type_analysis USING btree (analysis_type_id);
HERE;

$sql_create_fki_template_analysis_params_fk=<<<HERE
CREATE INDEX lis.fki_template_analysis_params_fk ON lis.analysis_type_templates USING btree (analysis_param_id);
HERE;

$sql_create_fki_template_analysis_type_fk=<<<HERE
CREATE INDEX lis.fki_template_analysis_type_fk ON lis.analysis_type_templates USING btree (analysis_type_id);
HERE;

$command=$connection->createCommand($sql_create_schema);
$command->execute();
unset($command);
			
$command=$connection->createCommand($sql_search_path);
$command->execute();
unset($command);

$command=$connection->createCommand($sql_default_tablespace);
$command->execute();
unset($command);

$command=$connection->createCommand($sql_default_with_oids);
$command->execute();
unset($command);

$command=$connection->createCommand($sql_create_analysis_param_id_seq);
$command->execute();
unset($command);


$command=$connection->createCommand($sql_owner_analysis_param_id_seq);
$command->execute();
unset($command);

$command=$connection->createCommand($sql_create_analysis_sample_type_id_seq);
$command->execute();
unset($command);

$command=$connection->createCommand($sql_owner_analysis_sample_type_id_seq);
$command->execute();
unset($command);

$command=$connection->createCommand($sql_create_analysis_type_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owner_analysis_type_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_create_analysis_type_template_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owner_analysis_type_template_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_create_analyzer_type_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owner_analyzer_type_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_create_analyzer_type_analysis_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owner_analyzer_type_analysis_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_create_analysis_params);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owner_analysis_params);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owned_analysis_param_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_params);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_params_id);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_params_name);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_params_long_name);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_params_comment);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_create_analysis_samples_types);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owner_analysis_samples_types);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owned_analysis_sample_type_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_samples_types);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_samples_types_id);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_samples_types_type);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_samples_types_subtype);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_create_analysis_types);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owner_analysis_types);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owned_analysis_type_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_types);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_types_id);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_types_name);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_types_short_name);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_types_automatic);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_types_manual);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_create_analysis_type_templates);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owner_analysis_type_templates);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owned_analysis_type_template_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_type_templates);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_type_templates_analysis_type_id);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_analysis_type_templates_analysis_param_id);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analysis_type_templates_is_default);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_create_analyzer_type_analysis);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owner_analyzer_type_analysis);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owned_analyzer_type_analysis_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analyzer_type_analysis);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analyzer_type_analysis_id);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analyzer_type_analysis_analyser_type_id);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analyzer_type_analysis_analysis_type_id);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_create_analyzer_types);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owner_analyzer_types);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_owned_analyzer_type_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analyzer_types);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analyzer_types_id);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analyzer_types_type);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analyzer_types_name);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_comment_analyzer_types_notes);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_create_fki_analyzer_type_analysis_type_fk);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_create_fki_template_analysis_params_fk);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_create_fki_template_analysis_type_fk);
$command->execute();
unset($command);

 		}

	public function down()
	{
$sql_drop_analysis_params=<<<HERE
DROP TABLE lis.analysis_params CASCADE;
HERE
$sql_drop_analysis_samples_types=<<<HERE
DROP TABLE lis.analysis_samples_types CASCADE;
HERE
$sql_drop_analysis_type_templates=<<<HERE
DROP TABLE lis.analysis_type_templates CASCADE;
HERE
$sql_drop_analysis_types=<<<HERE
DROP TABLE lis.analysis_types CASCADE;
HERE
$sql_drop_analyzer_type_analysis=<<<HERE
DROP TABLE lis.analyzer_type_analysis CASCADE;
HERE
$sql_drop_analyzer_types=<<<HERE
DROP TABLE lis.analyzer_types CASCADE;
HERE
$sql_drop_analysis_param_id_seq=<<<HERE
DROP SEQUENCE lis.analysis_param_id_seq;
HERE
$sql_drop_analysis_sample_type_id_seq=<<<HERE
DROP SEQUENCE lis.analysis_sample_type_id_seq;
HERE
$sql_drop_analysis_type_id_seq=<<<HERE
DROP SEQUENCE lis.analysis_type_id_seq;
HERE
$sql_drop_analysis_type_template_id_seq=<<<HERE
DROP SEQUENCE lis.analysis_type_template_id_seq;
HERE
$sql_drop_analyzer_type_id_seq=<<<HERE
DROP SEQUENCE lis.analyzer_type_id_seq;
HERE
$sql_drop_analysis_type_templates_id_seq=<<<HERE
DROP SEQUENCE lis.analysis_type_templates_id_seq;
HERE

$command=$connection->createCommand($sql_drop_analysis_params);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_drop_analysis_samples_types);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_drop_analysis_type_templates);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_drop_analysis_types);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_drop_analyzer_type_analysis);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_drop_analyzer_types);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_drop_analysis_param_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_drop_analysis_sample_type_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_drop_analysis_type_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_drop_analysis_type_template_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_drop_analyzer_type_id_seq);
$command->execute();
unset($command);
$command=$connection->createCommand($sql_drop_analysis_type_templates_id_seq);
$command->execute();
unset($command);

		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}