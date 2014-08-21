-- Table: mis.emklog

-- DROP TABLE mis.emklog;

CREATE TABLE mis.emklog
(
  id serial NOT NULL,
  medcard_id integer, -- ��� ���
  user_changed integer, -- ������������, ������� ��� ��������� � ����
  startdate date, -- ���� ������ �������� ���������
  enddate date, -- ���� ����� �������� ���������
  privelege_code integer, -- ��� ������
  snils character varying(50), -- �����
  address character varying(200), -- ����� ���������� �����������
  address_reg character varying(200), -- ����� �����������
  doctype integer, -- ��� ���������
  serie character varying(20), -- �����
  docnumber character varying(20), -- �����
  who_gived character varying(200), -- ��� �����
  gived_date date, -- ���� ������
  contact character varying(200), -- ��������
  invalid_group integer, -- ������ ������������
  card_number character varying(50) NOT NULL, -- ����� �����
  enterprise_id integer, -- ID ���������
  policy_id integer, -- ����� ������
  reg_date date, -- ���� ����������� �����
  work_place character varying(100), -- ����� ������
  work_address character varying(100), -- ����� ������
  post character varying(100), -- ��������� �� ������
  profession character varying(200), -- ���������
  motion integer DEFAULT 0, -- ������ �������� ��������
  address_str text, -- ��������� ������������� ������ ���������� ��� ������
  address_reg_str text, -- ��������� ������������� ������ ����������� ��� ������
  user_created integer,
  date_created timestamp without time zone,
  CONSTRAINT emklog_pkey PRIMARY KEY (id),
  CONSTRAINT emklog_doctype_fkey FOREIGN KEY (doctype)
      REFERENCES mis.doctypes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT emklog_enterprise_id_fkey FOREIGN KEY (enterprise_id)
      REFERENCES mis.enterprise_params (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT emklog_privelege_code_fkey FOREIGN KEY (privelege_code)
      REFERENCES mis.privileges (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mis.emklog
  OWNER TO moniiag;
COMMENT ON TABLE mis.emklog
  IS '����������� �����';
COMMENT ON COLUMN mis.emklog.medcard_id IS '��� ���';
COMMENT ON COLUMN mis.emklog.user_changed IS '������������, ������� ��� ��������� � ����';
COMMENT ON COLUMN mis.emklog.startdate IS '���� ������ �������� ���������';
COMMENT ON COLUMN mis.emklog.enddate IS '���� ����� �������� ���������';
COMMENT ON COLUMN mis.emklog.privelege_code IS '��� ������';
COMMENT ON COLUMN mis.emklog.snils IS '�����';
COMMENT ON COLUMN mis.emklog.address IS '����� ���������� �����������';
COMMENT ON COLUMN mis.emklog.address_reg IS '����� �����������';
COMMENT ON COLUMN mis.emklog.doctype IS '��� ���������';
COMMENT ON COLUMN mis.emklog.serie IS '�����';
COMMENT ON COLUMN mis.emklog.docnumber IS '�����';
COMMENT ON COLUMN mis.emklog.who_gived IS '��� �����';
COMMENT ON COLUMN mis.emklog.gived_date IS '���� ������';
COMMENT ON COLUMN mis.emklog.contact IS '��������';
COMMENT ON COLUMN mis.emklog.invalid_group IS '������ ������������';
COMMENT ON COLUMN mis.emklog.card_number IS '����� �����';
COMMENT ON COLUMN mis.emklog.enterprise_id IS 'ID ���������';
COMMENT ON COLUMN mis.emklog.policy_id IS '����� ������';
COMMENT ON COLUMN mis.emklog.reg_date IS '���� ����������� �����';
COMMENT ON COLUMN mis.emklog.work_place IS '����� ������';
COMMENT ON COLUMN mis.emklog.work_address IS '����� ������';
COMMENT ON COLUMN mis.emklog.post IS '��������� �� ������';
COMMENT ON COLUMN mis.emklog.profession IS '���������';
COMMENT ON COLUMN mis.emklog.motion IS '������ �������� ��������';
COMMENT ON COLUMN mis.emklog.address_str IS '��������� ������������� ������ ���������� ��� ������';
COMMENT ON COLUMN mis.emklog.address_reg_str IS '��������� ������������� ������ ����������� ��� ������';

