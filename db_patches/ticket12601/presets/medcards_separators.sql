--
-- PostgreSQL database dump
--

-- Dumped from database version 9.3.0
-- Dumped by pg_dump version 9.3.0
-- Started on 2014-10-08 11:14:07

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = mis, pg_catalog;

--
-- TOC entry 2382 (class 0 OID 153169)
-- Dependencies: 335
-- Data for Name: medcards_separators; Type: TABLE DATA; Schema: mis; Owner: moniiag
--

COPY medcards_separators (id, value) FROM stdin;
1	|
2	#
4	/
5	-
\.


--
-- TOC entry 2387 (class 0 OID 0)
-- Dependencies: 334
-- Name: medcards_separators_id_seq; Type: SEQUENCE SET; Schema: mis; Owner: moniiag
--

SELECT pg_catalog.setval('medcards_separators_id_seq', 5, true);


-- Completed on 2014-10-08 11:14:07

--
-- PostgreSQL database dump complete
--

