--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = mis, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: medcard_elements; Type: TABLE; Schema: mis; Owner: moniiag; Tablespace: 
--

CREATE TABLE medcard_elements (
    id integer NOT NULL,
    type integer,
    categorie_id integer,
    label character varying(150),
    guide_id integer,
    allow_add integer DEFAULT 0,
    label_after character varying(200),
    size integer,
    is_wrapped integer,
    path character varying(150),
    "position" integer,
    config text,
    default_value character varying(300),
    label_display character varying(150),
    is_required integer,
    not_printing_values text,
    hide_label_before integer
);


ALTER TABLE mis.medcard_elements OWNER TO moniiag;

--
-- Name: TABLE medcard_elements; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON TABLE medcard_elements IS 'Контролы шаблонов медкарт';


--
-- Name: COLUMN medcard_elements.type; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements.type IS 'Тип контрола';


--
-- Name: COLUMN medcard_elements.categorie_id; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements.categorie_id IS 'Тип категории';


--
-- Name: COLUMN medcard_elements.label; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements.label IS 'Метка для контрола до поля';


--
-- Name: COLUMN medcard_elements.guide_id; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements.guide_id IS 'Справочник';


--
-- Name: COLUMN medcard_elements.allow_add; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements.allow_add IS 'Можно ли добавлять новые значения или нет (комбо)';


--
-- Name: COLUMN medcard_elements.label_after; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements.label_after IS 'Метка после поля';


--
-- Name: COLUMN medcard_elements.size; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements.size IS 'Размер поля';


--
-- Name: COLUMN medcard_elements.is_wrapped; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements.is_wrapped IS 'Перенос строки (да/нет)';


--
-- Name: COLUMN medcard_elements.path; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements.path IS '(Путь math. path)';


--
-- Name: COLUMN medcard_elements."position"; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements."position" IS 'Позиция элемента в категории (приоритет)';


--
-- Name: COLUMN medcard_elements.config; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements.config IS 'Конфигурация элемента. Используется, например, в таблицах';


--
-- Name: COLUMN medcard_elements.default_value; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements.default_value IS 'Значение по умолчанию (используется для выпадающих списков)';


--
-- Name: COLUMN medcard_elements.label_display; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements.label_display IS 'Метка для администратора (отображение)';


--
-- Name: COLUMN medcard_elements.not_printing_values; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements.not_printing_values IS 'Значения справочников элементов, при выборе которых элемент не выводится на печать';


--
-- Name: COLUMN medcard_elements.hide_label_before; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements.hide_label_before IS 'Скрывать ли метку до на печати';


--
-- Name: medcard_elements_dependences; Type: TABLE; Schema: mis; Owner: moniiag; Tablespace: 
--

CREATE TABLE medcard_elements_dependences (
    id integer NOT NULL,
    element_id integer,
    value_id integer,
    dep_element_id integer,
    action integer
);


ALTER TABLE mis.medcard_elements_dependences OWNER TO moniiag;

--
-- Name: TABLE medcard_elements_dependences; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON TABLE medcard_elements_dependences IS 'Зависимости показов элементов медкарты от значений';


--
-- Name: COLUMN medcard_elements_dependences.element_id; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements_dependences.element_id IS 'ID элемента';


--
-- Name: COLUMN medcard_elements_dependences.value_id; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements_dependences.value_id IS 'ID значения элемента';


--
-- Name: COLUMN medcard_elements_dependences.dep_element_id; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements_dependences.dep_element_id IS 'ID зависимого элемента';


--
-- Name: COLUMN medcard_elements_dependences.action; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_elements_dependences.action IS 'Действие (0 - показать, 1 - скрыть)';


--
-- Name: medcard_elements_dependences_id_seq; Type: SEQUENCE; Schema: mis; Owner: moniiag
--

CREATE SEQUENCE medcard_elements_dependences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE mis.medcard_elements_dependences_id_seq OWNER TO moniiag;

--
-- Name: medcard_elements_dependences_id_seq; Type: SEQUENCE OWNED BY; Schema: mis; Owner: moniiag
--

ALTER SEQUENCE medcard_elements_dependences_id_seq OWNED BY medcard_elements_dependences.id;


--
-- Name: medcard_elements_id_seq; Type: SEQUENCE; Schema: mis; Owner: moniiag
--

CREATE SEQUENCE medcard_elements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE mis.medcard_elements_id_seq OWNER TO moniiag;

--
-- Name: medcard_elements_id_seq; Type: SEQUENCE OWNED BY; Schema: mis; Owner: moniiag
--

ALTER SEQUENCE medcard_elements_id_seq OWNED BY medcard_elements.id;


--
-- Name: medcard_guide_values; Type: TABLE; Schema: mis; Owner: moniiag; Tablespace: 
--

CREATE TABLE medcard_guide_values (
    id integer NOT NULL,
    guide_id integer,
    value text,
    greeting_id integer,
    element_path character varying(50)
);


ALTER TABLE mis.medcard_guide_values OWNER TO moniiag;

--
-- Name: TABLE medcard_guide_values; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON TABLE medcard_guide_values IS 'Значения для справочников медкарт';


--
-- Name: COLUMN medcard_guide_values.guide_id; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_guide_values.guide_id IS 'ID медсправочника';


--
-- Name: COLUMN medcard_guide_values.value; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_guide_values.value IS 'Значение';


--
-- Name: COLUMN medcard_guide_values.greeting_id; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_guide_values.greeting_id IS 'ID приёма (для частного значения справочника)';


--
-- Name: COLUMN medcard_guide_values.element_path; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_guide_values.element_path IS 'Путь элемента (для частного значения справочника)';


--
-- Name: medcard_guide_values_id_seq; Type: SEQUENCE; Schema: mis; Owner: moniiag
--

CREATE SEQUENCE medcard_guide_values_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE mis.medcard_guide_values_id_seq OWNER TO moniiag;

--
-- Name: medcard_guide_values_id_seq; Type: SEQUENCE OWNED BY; Schema: mis; Owner: moniiag
--

ALTER SEQUENCE medcard_guide_values_id_seq OWNED BY medcard_guide_values.id;


--
-- Name: medcard_guides; Type: TABLE; Schema: mis; Owner: moniiag; Tablespace: 
--

CREATE TABLE medcard_guides (
    id integer NOT NULL,
    name name
);


ALTER TABLE mis.medcard_guides OWNER TO moniiag;

--
-- Name: TABLE medcard_guides; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON TABLE medcard_guides IS 'Справочники медкарты';


--
-- Name: COLUMN medcard_guides.name; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_guides.name IS 'Название';


--
-- Name: medcard_guides_id_seq; Type: SEQUENCE; Schema: mis; Owner: moniiag
--

CREATE SEQUENCE medcard_guides_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE mis.medcard_guides_id_seq OWNER TO moniiag;

--
-- Name: medcard_guides_id_seq; Type: SEQUENCE OWNED BY; Schema: mis; Owner: moniiag
--

ALTER SEQUENCE medcard_guides_id_seq OWNED BY medcard_guides.id;


--
-- Name: medcard_templates; Type: TABLE; Schema: mis; Owner: moniiag; Tablespace: 
--

CREATE TABLE medcard_templates (
    id integer NOT NULL,
    name character varying(150),
    page_id integer,
    categorie_ids text,
    primary_diagnosis integer DEFAULT 0,
    index integer
);


ALTER TABLE mis.medcard_templates OWNER TO moniiag;

--
-- Name: TABLE medcard_templates; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON TABLE medcard_templates IS 'Шаблоны медкарт';


--
-- Name: COLUMN medcard_templates.name; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_templates.name IS 'Название';


--
-- Name: COLUMN medcard_templates.page_id; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_templates.page_id IS 'ID страницы, где используется шаблон';


--
-- Name: COLUMN medcard_templates.categorie_ids; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_templates.categorie_ids IS 'IDS категорий в шаблоне';


--
-- Name: COLUMN medcard_templates.primary_diagnosis; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_templates.primary_diagnosis IS 'Обязательность заполнения основного диагноза';


--
-- Name: COLUMN medcard_templates.index; Type: COMMENT; Schema: mis; Owner: moniiag
--

COMMENT ON COLUMN medcard_templates.index IS 'Порядковый номер отображения';


--
-- Name: medcard_templates_id_seq; Type: SEQUENCE; Schema: mis; Owner: moniiag
--

CREATE SEQUENCE medcard_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE mis.medcard_templates_id_seq OWNER TO moniiag;

--
-- Name: medcard_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: mis; Owner: moniiag
--

ALTER SEQUENCE medcard_templates_id_seq OWNED BY medcard_templates.id;


--
-- Name: id; Type: DEFAULT; Schema: mis; Owner: moniiag
--

ALTER TABLE ONLY medcard_elements ALTER COLUMN id SET DEFAULT nextval('medcard_elements_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: mis; Owner: moniiag
--

ALTER TABLE ONLY medcard_elements_dependences ALTER COLUMN id SET DEFAULT nextval('medcard_elements_dependences_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: mis; Owner: moniiag
--

ALTER TABLE ONLY medcard_guide_values ALTER COLUMN id SET DEFAULT nextval('medcard_guide_values_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: mis; Owner: moniiag
--

ALTER TABLE ONLY medcard_guides ALTER COLUMN id SET DEFAULT nextval('medcard_guides_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: mis; Owner: moniiag
--

ALTER TABLE ONLY medcard_templates ALTER COLUMN id SET DEFAULT nextval('medcard_templates_id_seq'::regclass);


--
-- Data for Name: medcard_elements; Type: TABLE DATA; Schema: mis; Owner: moniiag
--

COPY medcard_elements (id, type, categorie_id, label, guide_id, allow_add, label_after, size, is_wrapped, path, "position", config, default_value, label_display, is_required, not_printing_values, hide_label_before) FROM stdin;
1836	6	131	1. Первый день</br>последней менструации	\N	0		20	0	2.500.1	1	{"maxValue":"","minValue":""}	\N		0	\N	\N
1842	0	131	Срок беременности</br>на данный момент	\N	0		11	0	2.500.7	7	[]			0	\N	\N
1838	0	131	Предполагаемая</br>дата родов	\N	0		10	1	2.500.3	3	[]			0	\N	\N
1839	6	131	2. Раннее УЗИ           Дата УЗИ	\N	0		20	0	2.500.4	4	{"maxValue":"","minValue":""}	\N		0	\N	\N
1837	0	131	Срок беременности</br>на данный момент	\N	0		11	0	2.500.2	2	[]			0	\N	\N
1847	0	131	Срок беременности</br>на данный момент	\N	0		11	0	2.500.13	13	[]			0	\N	\N
1844	6	131	3. Перенос эмбриона             Дата переноса	\N	0		20	0	2.500.9	9	{"maxValue":"","minValue":""}	\N		0	\N	\N
1840	5	131	Срок беременности</br>по УЗИ	\N	0		3	0	2.500.5	5	{"maxValue":"40","minValue":"0","step":""}	\N		0	\N	\N
1848	0	131	Предполагаемая</br> дата родов	\N	0		10	1	2.500.14	14	[]			0	\N	\N
1849	6	131	4. Шевеление                   Дата шевеления	\N	0		20	0	2.500.15	15	{"maxValue":"","minValue":""}	\N		0	\N	\N
1851	0	131	Срок беременности</br>на данный момент	\N	0		11	0	2.500.17	17	[]			0	\N	\N
1850	2	131	Первые роды:	438	0		14	0	2.500.16	16	[]	\N		0	\N	\N
1852	0	131	Предполагаемая</br>дата родов	\N	0		10	1	2.500.19	19	[]			0	\N	\N
1853	6	131	5. Зачатие                Дата зачатия	\N	0		20	0	2.500.20	20	{"maxValue":"","minValue":""}	\N		0	\N	\N
1841	5	131	нед.	\N	0	дней          	5	0	2.500.6	6	{"maxValue":"6","minValue":"0","step":""}	\N		0	\N	\N
1854	0	131	Срок беременности</br>на данный момент	\N	0		11	0	2.500.21	21	[]			0	\N	\N
1855	0	131	Предполагаемая</br>дата родов	\N	0		10	1	2.500.22	22	[]			0	\N	\N
1856	6	131	6. Овуляция                 Дата овуляции	\N	0		20	0	2.500.23	23	{"maxValue":"","minValue":""}	\N		0	\N	\N
1857	0	131	Срок беременности</br>на данный момент	\N	0		11	0	2.500.24	24	[]			0	\N	\N
1858	0	131	Предполагаемая</br>дата родов	\N	0		10	1	2.500.25	25	[]			0	\N	\N
1859	6	131	7. Инсеминация             Дата инсеминациии 	\N	0		20	0	2.500.26	26	{"maxValue":"","minValue":""}	\N		0	\N	\N
1860	0	131	Срок беременности</br>на данный момент	\N	0		11	0	2.500.27	27	[]			0	\N	\N
1861	0	131	Предполагаемая</br>дата родов	\N	0		10	1	2.500.29	29	[]			0	\N	\N
1862	6	131	8. Первая явка                Дата первой явки	\N	0		20	0	2.500.30	30	{"maxValue":"","minValue":""}	\N		0	\N	\N
1865	0	131	Срок беременности</br>на данный момент	\N	0		11	0	2.500.34	34	[]			0	\N	\N
1866	0	131	Предполагаемая</br>дата родов	\N	0		10	0	2.500.35	35	[]			0	\N	\N
1845	5	131	Возраст эмбриона	\N	0		5	0	2.500.10	10	{"maxValue":"40","minValue":"0","step":""}	\N		0	\N	\N
1846	5	131	нед.	\N	0	дней          	5	0	2.500.11	11	{"maxValue":"6","minValue":"0","step":""}	\N		0	\N	\N
1863	5	131	Срок беременности</br>по первой явке	\N	0		5	0	2.500.31	31	{"maxValue":"40","minValue":"0","step":""}	\N		0	\N	\N
1864	5	131	нед.	\N	0	дней	5	0	2.500.33	33	{"maxValue":"6","minValue":"0","step":""}	\N		0	\N	\N
\.


--
-- Data for Name: medcard_elements_dependences; Type: TABLE DATA; Schema: mis; Owner: moniiag
--

COPY medcard_elements_dependences (id, element_id, value_id, dep_element_id, action) FROM stdin;

\.


--
-- Name: medcard_elements_dependences_id_seq; Type: SEQUENCE SET; Schema: mis; Owner: moniiag
--

SELECT pg_catalog.setval('medcard_elements_dependences_id_seq', 696, true);


--
-- Name: medcard_elements_id_seq; Type: SEQUENCE SET; Schema: mis; Owner: moniiag
--

SELECT pg_catalog.setval('medcard_elements_id_seq', 1866, true);


--
-- Data for Name: medcard_guide_values; Type: TABLE DATA; Schema: mis; Owner: moniiag
--

COPY medcard_guide_values (id, guide_id, value, greeting_id, element_path) FROM stdin;
2330	438	да	\N	\N
2331	438	нет	\N	\N
\.


--
-- Name: medcard_guide_values_id_seq; Type: SEQUENCE SET; Schema: mis; Owner: moniiag
--

SELECT pg_catalog.setval('medcard_guide_values_id_seq', 2331, true);


--
-- Data for Name: medcard_guides; Type: TABLE DATA; Schema: mis; Owner: moniiag
--

COPY medcard_guides (id, name) FROM stdin;
438	Первые роды
\.


--
-- Name: medcard_guides_id_seq; Type: SEQUENCE SET; Schema: mis; Owner: moniiag
--

SELECT pg_catalog.setval('medcard_guides_id_seq', 438, true);


--
-- Data for Name: medcard_templates; Type: TABLE DATA; Schema: mis; Owner: moniiag
--

COPY medcard_templates (id, name, page_id, categorie_ids, primary_diagnosis, index) FROM stdin;
34	Калькулятор беременности	0	[]	0	29
\.


--
-- Name: medcard_templates_id_seq; Type: SEQUENCE SET; Schema: mis; Owner: moniiag
--

SELECT pg_catalog.setval('medcard_templates_id_seq', 34, true);


--
-- Name: medcard_elements_dependences_pkey; Type: CONSTRAINT; Schema: mis; Owner: moniiag; Tablespace: 
--

ALTER TABLE ONLY medcard_elements_dependences
    ADD CONSTRAINT medcard_elements_dependences_pkey PRIMARY KEY (id);


--
-- Name: medcard_elements_pkey; Type: CONSTRAINT; Schema: mis; Owner: moniiag; Tablespace: 
--

ALTER TABLE ONLY medcard_elements
    ADD CONSTRAINT medcard_elements_pkey PRIMARY KEY (id);


--
-- Name: medcard_guide_values_pkey; Type: CONSTRAINT; Schema: mis; Owner: moniiag; Tablespace: 
--

ALTER TABLE ONLY medcard_guide_values
    ADD CONSTRAINT medcard_guide_values_pkey PRIMARY KEY (id);


--
-- Name: medcard_guides_pkey; Type: CONSTRAINT; Schema: mis; Owner: moniiag; Tablespace: 
--

ALTER TABLE ONLY medcard_guides
    ADD CONSTRAINT medcard_guides_pkey PRIMARY KEY (id);


--
-- Name: medcard_templates_pkey; Type: CONSTRAINT; Schema: mis; Owner: moniiag; Tablespace: 
--

ALTER TABLE ONLY medcard_templates
    ADD CONSTRAINT medcard_templates_pkey PRIMARY KEY (id);


--
-- Name: medcard_guide_values_guide_id_fkey; Type: FK CONSTRAINT; Schema: mis; Owner: moniiag
--

ALTER TABLE ONLY medcard_guide_values
    ADD CONSTRAINT medcard_guide_values_guide_id_fkey FOREIGN KEY (guide_id) REFERENCES medcard_guides(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

