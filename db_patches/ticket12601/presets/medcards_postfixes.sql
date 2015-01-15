--
-- PostgreSQL database dump
--

-- Dumped from database version 9.3.0
-- Dumped by pg_dump version 9.3.0
-- Started on 2014-10-08 11:13:43

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = mis, pg_catalog;

--
-- TOC entry 2382 (class 0 OID 153117)
-- Dependencies: 322
-- Data for Name: medcards_postfixes; Type: TABLE DATA; Schema: mis; Owner: postgres
--

COPY medcards_postfixes (id, value) FROM stdin;
1	Э
2	Г
3	Р
4	Н
\.


--
-- TOC entry 2387 (class 0 OID 0)
-- Dependencies: 321
-- Name: medcards_postfixes_id_seq; Type: SEQUENCE SET; Schema: mis; Owner: postgres
--

SELECT pg_catalog.setval('medcards_postfixes_id_seq', 4, true);


-- Completed on 2014-10-08 11:13:44

--
-- PostgreSQL database dump complete
--

