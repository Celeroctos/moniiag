/* INSERT INTO mis.medcards_history 
	SELECT nextval('mis.medcards_history_seq') as id,  1 as enterprise_id, t.card_number as from, t.card_number as to, t.policy_id as policy_id
	FROM mis.medcards t */
/* UPDATE mis.medcards_history SET rule_id = 15 */
/* UPDATE mis.medcards_history CONCAT('20', CAST(SUBSTRING(t.to, (CHAR_LENGTH(t.to) - 1)) as TEXT)) FROM mis.medcards_history t */
/*UPDATE mis.medcards_history t SET reg_date = (SELECT s.reg_date FROM mis.medcards s WHERE t.to = s.card_number) */