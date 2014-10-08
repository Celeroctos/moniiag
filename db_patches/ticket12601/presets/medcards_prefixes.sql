--
-- PostgreSQL database dump
--

-- Dumped from database version 9.3.0
-- Dumped by pg_dump version 9.3.0
-- Started on 2014-10-08 11:13:22

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = mis, pg_catalog;

--
-- TOC entry 2382 (class 0 OID 153109)
-- Dependencies: 320
-- Data for Name: medcards_prefixes; Type: TABLE DATA; Schema: mis; Owner: postgres
--

COPY medcards_prefixes (id, value) FROM stdin;
\.


--
-- TOC entry 2387 (class 0 OID 0)
-- Dependencies: 319
-- Name: medcards_prefixes_id_seq; Type: SEQUENCE SET; Schema: mis; Owner: postgres
--

SELECT pg_catalog.setval('medcards_prefixes_id_seq', 2, true);


-- Completed on 2014-10-08 11:13:23

--
-- PostgreSQL database dump complete
--

