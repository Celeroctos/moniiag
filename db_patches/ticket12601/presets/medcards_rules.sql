--
-- PostgreSQL database dump
--

-- Dumped from database version 9.3.0
-- Dumped by pg_dump version 9.3.0
-- Started on 2014-10-08 11:10:36

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = mis, pg_catalog;

--
-- TOC entry 2382 (class 0 OID 153125)
-- Dependencies: 324
-- Data for Name: medcards_rules; Type: TABLE DATA; Schema: mis; Owner: postgres
--

COPY medcards_rules (id, prefix_id, postfix_id, value, parent_id, name, participle_mode_prefix, participle_mode_postfix, prefix_separator_id, postfix_separator_id) FROM stdin;
20	\N	-4	2	19	Стационар. Акушерская клиника 1	\N	0	\N	2
16	\N	-3	0	\N	Регистратура платного отделения	\N	\N	\N	4
17	-2	1	0	\N	Стационар. Отделение эндоскопии	\N	\N	4	5
18	-2	2	0	\N	Стационар. Отделение гинекологии	\N	\N	4	5
19	-2	3	0	\N	Стационар. Приёмное отделение акушерских клиник	\N	\N	4	5
22	-2	-4	2	19	Стационар. Детское отделение. Более одного ребенка (ГГ)	0	0	4	2
23	-3	-4	2	19	Стационар. Детское отделение. Более одного ребенка (ГГГГ)	0	0	4	2
15	\N	-2	0	\N	Регистратура КДО	\N	\N	\N	4
21	\N	4	2	19	Стационар. Детское отделение. Один ребенок.	-1	1	\N	5
\.


--
-- TOC entry 2387 (class 0 OID 0)
-- Dependencies: 323
-- Name: medcards_rules_id_seq; Type: SEQUENCE SET; Schema: mis; Owner: postgres
--

SELECT pg_catalog.setval('medcards_rules_id_seq', 26, true);


-- Completed on 2014-10-08 11:10:37

--
-- PostgreSQL database dump complete
--

