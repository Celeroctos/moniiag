--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: lis; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA lis;


ALTER SCHEMA lis OWNER TO postgres;

SET search_path = lis, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: analysis; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE analysis (
    id integer NOT NULL,
    registration_date timestamp without time zone DEFAULT now(),
    doctor_id integer
);


ALTER TABLE lis.analysis OWNER TO postgres;

--
-- Name: COLUMN analysis.id; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN analysis.id IS 'Первичка';


--
-- Name: COLUMN analysis.registration_date; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN analysis.registration_date IS 'Время регистрации заказа в системе';


--
-- Name: COLUMN analysis.doctor_id; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN analysis.doctor_id IS 'Идентификатор врача в ЛИС';


--
-- Name: analysis_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE analysis_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.analysis_id_seq OWNER TO postgres;

--
-- Name: analysis_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE analysis_id_seq OWNED BY analysis.id;


--
-- Name: blood; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE blood (
    id integer NOT NULL,
    analysis_id integer,
    machine_id integer,
    patient_id integer,
    order_number integer,
    creation_time timestamp without time zone,
    order_request_person_name character varying(26),
    ward_name character varying(13),
    order_request_department character varying(13),
    blood_sample_date timestamp without time zone,
    order_entry_date timestamp without time zone,
    patient_number integer,
    measurement_parameter_group_number integer,
    normal_range_table_number integer,
    charge_person character varying(26),
    laboratory_number integer,
    order_comment character varying(128)
);


ALTER TABLE lis.blood OWNER TO postgres;

--
-- Name: blood_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE blood_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.blood_id_seq OWNER TO postgres;

--
-- Name: blood_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE blood_id_seq OWNED BY blood.id;


--
-- Name: department; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE department (
    id integer NOT NULL,
    name character varying(20),
    enterprise_id integer DEFAULT 0
);


ALTER TABLE lis.department OWNER TO postgres;

--
-- Name: COLUMN department.id; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN department.id IS 'Первичка';


--
-- Name: COLUMN department.name; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN department.name IS 'Название департамента';


--
-- Name: COLUMN department.enterprise_id; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN department.enterprise_id IS 'Ссылка на департамент в МИС';


--
-- Name: department_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE department_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.department_id_seq OWNER TO postgres;

--
-- Name: department_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE department_id_seq OWNED BY department.id;


--
-- Name: direction; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE direction (
    id integer NOT NULL,
    barcode integer,
    status integer,
    comment text,
    patient_id integer,
    sender_id integer,
    department_id integer,
    seding_date date,
    treatment_room_employee_id integer,
    laboratory_employee_id integer
);


ALTER TABLE lis.direction OWNER TO postgres;

--
-- Name: direction_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE direction_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.direction_id_seq OWNER TO postgres;

--
-- Name: direction_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE direction_id_seq OWNED BY direction.id;


--
-- Name: doctor; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE doctor (
    id integer NOT NULL,
    surname character varying(50),
    name character varying(50),
    patronymic character varying(50),
    doctor_id integer DEFAULT 0
);


ALTER TABLE lis.doctor OWNER TO postgres;

--
-- Name: COLUMN doctor.id; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN doctor.id IS 'Первичка';


--
-- Name: COLUMN doctor.surname; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN doctor.surname IS 'Фамилия';


--
-- Name: COLUMN doctor.name; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN doctor.name IS 'Имя';


--
-- Name: COLUMN doctor.patronymic; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN doctor.patronymic IS 'Отчество';


--
-- Name: COLUMN doctor.doctor_id; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN doctor.doctor_id IS 'Ссылка на врача в МИС';


--
-- Name: doctor_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE doctor_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.doctor_id_seq OWNER TO postgres;

--
-- Name: doctor_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE doctor_id_seq OWNED BY doctor.id;


--
-- Name: guide; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE guide (
    id integer NOT NULL,
    name character varying(100)
);


ALTER TABLE lis.guide OWNER TO postgres;

--
-- Name: guide_column; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE guide_column (
    id integer NOT NULL,
    name character varying(100),
    type character varying(20),
    guide_id integer,
    lis_guide_id integer DEFAULT (-1),
    "position" integer DEFAULT 0 NOT NULL,
    display_id integer DEFAULT (-1) NOT NULL,
    default_value text DEFAULT ''::text
);


ALTER TABLE lis.guide_column OWNER TO postgres;

--
-- Name: guide_column_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE guide_column_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.guide_column_id_seq OWNER TO postgres;

--
-- Name: guide_column_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE guide_column_id_seq OWNED BY guide_column.id;


--
-- Name: guide_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE guide_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.guide_id_seq OWNER TO postgres;

--
-- Name: guide_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE guide_id_seq OWNED BY guide.id;


--
-- Name: guide_row; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE guide_row (
    id integer NOT NULL,
    guide_id integer
);


ALTER TABLE lis.guide_row OWNER TO postgres;

--
-- Name: guide_row_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE guide_row_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.guide_row_id_seq OWNER TO postgres;

--
-- Name: guide_row_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE guide_row_id_seq OWNED BY guide_row.id;


--
-- Name: guide_value; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE guide_value (
    id integer NOT NULL,
    guide_row_id integer,
    guide_column_id integer,
    value text
);


ALTER TABLE lis.guide_value OWNER TO postgres;

--
-- Name: guide_value_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE guide_value_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.guide_value_id_seq OWNER TO postgres;

--
-- Name: guide_value_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE guide_value_id_seq OWNED BY guide_value.id;


--
-- Name: machine; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE machine (
    id integer NOT NULL,
    name character varying(50),
    serial integer,
    model character varying(10),
    software_version character varying(8)
);


ALTER TABLE lis.machine OWNER TO postgres;

--
-- Name: COLUMN machine.id; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN machine.id IS 'Первичка';


--
-- Name: COLUMN machine.name; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN machine.name IS 'Название машины';


--
-- Name: COLUMN machine.serial; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN machine.serial IS 'Серийный код';


--
-- Name: COLUMN machine.model; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN machine.model IS 'Модель';


--
-- Name: COLUMN machine.software_version; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN machine.software_version IS 'Версия ПО';


--
-- Name: machine_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE machine_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.machine_id_seq OWNER TO postgres;

--
-- Name: machine_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE machine_id_seq OWNED BY machine.id;


--
-- Name: medcard; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE medcard (
    id integer NOT NULL,
    surname character varying(100),
    name character varying(100),
    patronymic character varying(100) DEFAULT NULL::character varying,
    sex integer,
    birthday date,
    mis_patient_id integer DEFAULT (-1),
    charge_person integer DEFAULT (-1),
    number integer,
    policy character varying(50) DEFAULT NULL::character varying,
    policy_region character varying(50) DEFAULT NULL::character varying,
    policy_register_date date,
    passport_series character varying(10) DEFAULT NULL::character varying,
    passport_number character varying(20) DEFAULT NULL::character varying,
    snils character varying(20) DEFAULT NULL::character varying,
    phone character varying(20),
    register_address character varying(200),
    address character varying(200)
);


ALTER TABLE lis.medcard OWNER TO postgres;

--
-- Name: COLUMN medcard.id; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.id IS 'Первчика';


--
-- Name: COLUMN medcard.surname; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.surname IS 'Фамилия';


--
-- Name: COLUMN medcard.name; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.name IS 'Имя';


--
-- Name: COLUMN medcard.patronymic; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.patronymic IS 'Отчество';


--
-- Name: COLUMN medcard.sex; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.sex IS 'Пол';


--
-- Name: COLUMN medcard.birthday; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.birthday IS 'Дата рождения';


--
-- Name: COLUMN medcard.mis_patient_id; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.mis_patient_id IS 'ID пациента в МИС';


--
-- Name: COLUMN medcard.charge_person; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.charge_person IS 'Направитель пациента';


--
-- Name: COLUMN medcard.number; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.number IS 'Номер ЛКП';


--
-- Name: COLUMN medcard.policy; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.policy IS 'Номер полиса ОМС';


--
-- Name: COLUMN medcard.policy_region; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.policy_region IS 'Регион выдачи полиса ОМС';


--
-- Name: COLUMN medcard.policy_register_date; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.policy_register_date IS 'Дата выдачи ОМС';


--
-- Name: COLUMN medcard.passport_series; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.passport_series IS 'Серия и номер паспорта';


--
-- Name: COLUMN medcard.passport_number; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.passport_number IS 'Номер СНИЛС';


--
-- Name: COLUMN medcard.snils; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.snils IS 'Номер СНИЛС';


--
-- Name: COLUMN medcard.phone; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.phone IS 'Контактный телефон';


--
-- Name: COLUMN medcard.register_address; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.register_address IS 'Адрес регистрации';


--
-- Name: COLUMN medcard.address; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN medcard.address IS 'Адрес фактического проживания';


--
-- Name: medcard_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE medcard_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.medcard_id_seq OWNER TO postgres;

--
-- Name: medcard_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE medcard_id_seq OWNED BY medcard.id;


--
-- Name: passport; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE passport (
    id integer NOT NULL,
    series integer,
    number integer,
    subdivision_name text,
    issue_date date,
    subdivision_code integer
);


ALTER TABLE lis.passport OWNER TO postgres;

--
-- Name: passport_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE passport_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.passport_id_seq OWNER TO postgres;

--
-- Name: passport_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE passport_id_seq OWNED BY passport.id;


--
-- Name: patient; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE patient (
    id integer NOT NULL,
    surname character varying(50),
    name character varying(50),
    patronymic character varying(50),
    sex integer,
    birthday date,
    age integer
);


ALTER TABLE lis.patient OWNER TO postgres;

--
-- Name: COLUMN patient.id; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN patient.id IS 'Первичка';


--
-- Name: COLUMN patient.surname; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN patient.surname IS 'Фамилия';


--
-- Name: COLUMN patient.name; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN patient.name IS 'Имя';


--
-- Name: COLUMN patient.patronymic; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN patient.patronymic IS 'Отчество';


--
-- Name: COLUMN patient.sex; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN patient.sex IS 'Пол пациента';


--
-- Name: COLUMN patient.birthday; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN patient.birthday IS 'Дата рождения';


--
-- Name: COLUMN patient.age; Type: COMMENT; Schema: lis; Owner: postgres
--

COMMENT ON COLUMN patient.age IS 'Возраст';


--
-- Name: patient_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE patient_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.patient_id_seq OWNER TO postgres;

--
-- Name: patient_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE patient_id_seq OWNED BY patient.id;


--
-- Name: phone; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE phone (
    id integer NOT NULL,
    number character varying(50),
    type integer
);


ALTER TABLE lis.phone OWNER TO postgres;

--
-- Name: phone_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE phone_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.phone_id_seq OWNER TO postgres;

--
-- Name: phone_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE phone_id_seq OWNED BY phone.id;


--
-- Name: region; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE region (
    id integer NOT NULL,
    name character varying(100)
);


ALTER TABLE lis.region OWNER TO postgres;

--
-- Name: region_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE region_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.region_id_seq OWNER TO postgres;

--
-- Name: region_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE region_id_seq OWNED BY region.id;


--
-- Name: ward; Type: TABLE; Schema: lis; Owner: postgres; Tablespace: 
--

CREATE TABLE ward (
    id integer NOT NULL,
    name character varying(20),
    department_id integer,
    ward_id integer DEFAULT 0
);


ALTER TABLE lis.ward OWNER TO postgres;

--
-- Name: ward_id_seq; Type: SEQUENCE; Schema: lis; Owner: postgres
--

CREATE SEQUENCE ward_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE lis.ward_id_seq OWNER TO postgres;

--
-- Name: ward_id_seq; Type: SEQUENCE OWNED BY; Schema: lis; Owner: postgres
--

ALTER SEQUENCE ward_id_seq OWNED BY ward.id;


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY analysis ALTER COLUMN id SET DEFAULT nextval('analysis_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY blood ALTER COLUMN id SET DEFAULT nextval('blood_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY department ALTER COLUMN id SET DEFAULT nextval('department_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY direction ALTER COLUMN id SET DEFAULT nextval('direction_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY doctor ALTER COLUMN id SET DEFAULT nextval('doctor_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY guide ALTER COLUMN id SET DEFAULT nextval('guide_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY guide_column ALTER COLUMN id SET DEFAULT nextval('guide_column_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY guide_row ALTER COLUMN id SET DEFAULT nextval('guide_row_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY guide_value ALTER COLUMN id SET DEFAULT nextval('guide_value_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY machine ALTER COLUMN id SET DEFAULT nextval('machine_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY medcard ALTER COLUMN id SET DEFAULT nextval('medcard_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY passport ALTER COLUMN id SET DEFAULT nextval('passport_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY patient ALTER COLUMN id SET DEFAULT nextval('patient_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY phone ALTER COLUMN id SET DEFAULT nextval('phone_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY region ALTER COLUMN id SET DEFAULT nextval('region_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY ward ALTER COLUMN id SET DEFAULT nextval('ward_id_seq'::regclass);


--
-- Data for Name: analysis; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY analysis (id, registration_date, doctor_id) FROM stdin;
\.


--
-- Name: analysis_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('analysis_id_seq', 1, false);


--
-- Data for Name: blood; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY blood (id, analysis_id, machine_id, patient_id, order_number, creation_time, order_request_person_name, ward_name, order_request_department, blood_sample_date, order_entry_date, patient_number, measurement_parameter_group_number, normal_range_table_number, charge_person, laboratory_number, order_comment) FROM stdin;
\.


--
-- Name: blood_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('blood_id_seq', 1, false);


--
-- Data for Name: department; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY department (id, name, enterprise_id) FROM stdin;
\.


--
-- Name: department_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('department_id_seq', 1, false);


--
-- Data for Name: direction; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY direction (id, barcode, status, comment, patient_id, sender_id, department_id, seding_date, treatment_room_employee_id, laboratory_employee_id) FROM stdin;
\.


--
-- Name: direction_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('direction_id_seq', 1, false);


--
-- Data for Name: doctor; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY doctor (id, surname, name, patronymic, doctor_id) FROM stdin;
\.


--
-- Name: doctor_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('doctor_id_seq', 1, false);


--
-- Data for Name: guide; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY guide (id, name) FROM stdin;
45	Параметры анализов
46	Категории пациентов
47	Типы и подтипы образцов
49	Сотрудники лаборатории
50	Типы анализаторов
44	Типы анализов
48	МУ – направителя
\.


--
-- Data for Name: guide_column; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY guide_column (id, name, type, guide_id, lis_guide_id, "position", display_id, default_value) FROM stdin;
43	Курение	smoke	46	-1	1	-1	0
44	Беременность	pregnant	46	-1	2	-1	0
45	Срок беременности	trimester	46	-1	3	-1	-1
46	Менструальный цикл	menstruation	46	-1	4	-1	-1
47	Расовая принадлежность	race	46	-1	5	-1	-1
161	Тип образца	text	47	-1	1	-1	
162	Подтип образца	text	47	-1	2	-1	
136	Фамилия	text	49	-1	1	-1	
137	Имя	text	49	-1	2	-1	
138	Отчество	text	49	-1	3	-1	
139	Должность	text	49	-1	4	-1	
140	Роль	text	49	-1	5	-1	
129	Название типа анализатора	text	50	-1	1	-1	
130	Название анализатора	text	50	-1	2	-1	
131	Пометки	textarea	50	-1	3	-1	
50	Наименование МУ	text	48	-1	1	-1	
51	Notum	yesno	48	-1	2	-1	0
163	Краткое наименование анализа	dropdown	45	44	1	168	-1
164	Наименование параметра	text	45	-1	2	-1	
165	По умолчанию	yesno	45	-1	3	-1	0
168	Краткое наименование анализа	text	44	-1	1	-1	
169	Наименование анализа	text	44	-1	2	-1	
171	Автоматическая методика	yesno	44	-1	3	-1	0
170	Ручная методика	yesno	44	-1	4	-1	0
\.


--
-- Name: guide_column_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('guide_column_id_seq', 214, true);


--
-- Name: guide_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('guide_id_seq', 65, true);


--
-- Data for Name: guide_row; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY guide_row (id, guide_id) FROM stdin;
33	50
15	44
16	44
17	44
\.


--
-- Name: guide_row_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('guide_row_id_seq', 39, true);


--
-- Data for Name: guide_value; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY guide_value (id, guide_row_id, guide_column_id, value) FROM stdin;
56	15	170	0
57	16	168	Анализ 2
58	16	169	Анализ 2 Длинно
59	16	171	0
60	16	170	1
61	17	168	Анализ 3
62	17	169	Анализ 3 Длинно
63	17	171	1
64	17	170	0
113	33	129	
114	33	130	
115	33	131	
53	15	168	Анализ 1
54	15	169	Анализ 1 Длинно
55	15	171	1
\.


--
-- Name: guide_value_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('guide_value_id_seq', 125, true);


--
-- Data for Name: machine; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY machine (id, name, serial, model, software_version) FROM stdin;
\.


--
-- Name: machine_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('machine_id_seq', 1, false);


--
-- Data for Name: medcard; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY medcard (id, surname, name, patronymic, sex, birthday, mis_patient_id, charge_person, number, policy, policy_region, policy_register_date, passport_series, passport_number, snils, phone, register_address, address) FROM stdin;
\.


--
-- Name: medcard_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('medcard_id_seq', 1, false);


--
-- Data for Name: passport; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY passport (id, series, number, subdivision_name, issue_date, subdivision_code) FROM stdin;
\.


--
-- Name: passport_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('passport_id_seq', 1, false);


--
-- Data for Name: patient; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY patient (id, surname, name, patronymic, sex, birthday, age) FROM stdin;
\.


--
-- Name: patient_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('patient_id_seq', 1, false);


--
-- Data for Name: phone; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY phone (id, number, type) FROM stdin;
\.


--
-- Name: phone_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('phone_id_seq', 1, false);


--
-- Data for Name: region; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY region (id, name) FROM stdin;
\.


--
-- Name: region_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('region_id_seq', 1, false);


--
-- Data for Name: ward; Type: TABLE DATA; Schema: lis; Owner: postgres
--

COPY ward (id, name, department_id, ward_id) FROM stdin;
\.


--
-- Name: ward_id_seq; Type: SEQUENCE SET; Schema: lis; Owner: postgres
--

SELECT pg_catalog.setval('ward_id_seq', 1, false);


--
-- Name: analysis_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY analysis
    ADD CONSTRAINT analysis_pkey PRIMARY KEY (id);


--
-- Name: blood_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY blood
    ADD CONSTRAINT blood_pkey PRIMARY KEY (id);


--
-- Name: department_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY department
    ADD CONSTRAINT department_pkey PRIMARY KEY (id);


--
-- Name: direction_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY direction
    ADD CONSTRAINT direction_pkey PRIMARY KEY (id);


--
-- Name: doctor_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY doctor
    ADD CONSTRAINT doctor_pkey PRIMARY KEY (id);


--
-- Name: guide_column_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY guide_column
    ADD CONSTRAINT guide_column_pkey PRIMARY KEY (id);


--
-- Name: guide_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY guide
    ADD CONSTRAINT guide_pkey PRIMARY KEY (id);


--
-- Name: guide_row_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY guide_row
    ADD CONSTRAINT guide_row_pkey PRIMARY KEY (id);


--
-- Name: guide_value_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY guide_value
    ADD CONSTRAINT guide_value_pkey PRIMARY KEY (id);


--
-- Name: machine_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY machine
    ADD CONSTRAINT machine_pkey PRIMARY KEY (id);


--
-- Name: medcard_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY medcard
    ADD CONSTRAINT medcard_pkey PRIMARY KEY (id);


--
-- Name: passport_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY passport
    ADD CONSTRAINT passport_pkey PRIMARY KEY (id);


--
-- Name: patient_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY patient
    ADD CONSTRAINT patient_pkey PRIMARY KEY (id);


--
-- Name: phone_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY phone
    ADD CONSTRAINT phone_pkey PRIMARY KEY (id);


--
-- Name: region_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY region
    ADD CONSTRAINT region_pkey PRIMARY KEY (id);


--
-- Name: ward_pkey; Type: CONSTRAINT; Schema: lis; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ward
    ADD CONSTRAINT ward_pkey PRIMARY KEY (id);


--
-- Name: analysis_doctor_id_fkey; Type: FK CONSTRAINT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY analysis
    ADD CONSTRAINT analysis_doctor_id_fkey FOREIGN KEY (doctor_id) REFERENCES doctor(id);


--
-- Name: blood_analysis_id_fkey; Type: FK CONSTRAINT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY blood
    ADD CONSTRAINT blood_analysis_id_fkey FOREIGN KEY (analysis_id) REFERENCES analysis(id);


--
-- Name: blood_machine_id_fkey; Type: FK CONSTRAINT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY blood
    ADD CONSTRAINT blood_machine_id_fkey FOREIGN KEY (machine_id) REFERENCES machine(id);


--
-- Name: blood_patient_id_fkey; Type: FK CONSTRAINT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY blood
    ADD CONSTRAINT blood_patient_id_fkey FOREIGN KEY (patient_id) REFERENCES patient(id);


--
-- Name: direction_department_id_fkey; Type: FK CONSTRAINT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY direction
    ADD CONSTRAINT direction_department_id_fkey FOREIGN KEY (department_id) REFERENCES department(id);


--
-- Name: direction_laboratory_employee_id_fkey; Type: FK CONSTRAINT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY direction
    ADD CONSTRAINT direction_laboratory_employee_id_fkey FOREIGN KEY (laboratory_employee_id) REFERENCES doctor(id);


--
-- Name: direction_patient_id_fkey; Type: FK CONSTRAINT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY direction
    ADD CONSTRAINT direction_patient_id_fkey FOREIGN KEY (patient_id) REFERENCES patient(id);


--
-- Name: direction_sender_id_fkey; Type: FK CONSTRAINT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY direction
    ADD CONSTRAINT direction_sender_id_fkey FOREIGN KEY (sender_id) REFERENCES doctor(id);


--
-- Name: direction_treatment_room_employee_id_fkey; Type: FK CONSTRAINT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY direction
    ADD CONSTRAINT direction_treatment_room_employee_id_fkey FOREIGN KEY (treatment_room_employee_id) REFERENCES doctor(id);


--
-- Name: guide_column_guide_id_fkey; Type: FK CONSTRAINT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY guide_column
    ADD CONSTRAINT guide_column_guide_id_fkey FOREIGN KEY (guide_id) REFERENCES guide(id) ON DELETE CASCADE;


--
-- Name: guide_row_guide_id_fkey; Type: FK CONSTRAINT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY guide_row
    ADD CONSTRAINT guide_row_guide_id_fkey FOREIGN KEY (guide_id) REFERENCES guide(id) ON DELETE CASCADE;


--
-- Name: guide_value_guide_column_id_fkey; Type: FK CONSTRAINT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY guide_value
    ADD CONSTRAINT guide_value_guide_column_id_fkey FOREIGN KEY (guide_column_id) REFERENCES guide_column(id) ON DELETE CASCADE;


--
-- Name: guide_value_guide_row_id_fkey; Type: FK CONSTRAINT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY guide_value
    ADD CONSTRAINT guide_value_guide_row_id_fkey FOREIGN KEY (guide_row_id) REFERENCES guide_row(id) ON DELETE CASCADE;


--
-- Name: ward_department_id_fkey; Type: FK CONSTRAINT; Schema: lis; Owner: postgres
--

ALTER TABLE ONLY ward
    ADD CONSTRAINT ward_department_id_fkey FOREIGN KEY (department_id) REFERENCES department(id);


--
-- PostgreSQL database dump complete
--

