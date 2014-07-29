INSERT INTO mis.oms_statuses (tasu_id, name) VALUES(5, 'Неизвестно');
INSERT INTO mis.oms_types (tasu_id, name) VALUES(6, 'Неизвестно');

DELETE FROM mis.oms t1 WHERE NOT EXISTS(SELECT *
	FROM mis.medcards t2
	WHERE t1.id = t2.policy_id);

UPDATE mis.oms
   SET oms_series_number = (
	CASE WHEN oms_series IS NULL THEN
		REPLACE(REPLACE(oms_number, '-', ''), ' ','')
	ELSE
		CONCAT(REPLACE(REPLACE(oms_series, '-', ''), ' ',''), REPLACE(REPLACE(oms_number, '-', ''), ' ',''))
	END);

