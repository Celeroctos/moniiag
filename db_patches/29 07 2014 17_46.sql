/* INSERT INTO mis.oms_statuses (tasu_id, name) VALUES(5, 'Неизвестно');
INSERT INTO mis.oms_types (tasu_id, name) VALUES(6, 'Неизвестно'); */

/* Удаление дубликатов. SELECT (который для безопасности) меняем на DELETE для выполнения удаления */
/* SELECT * FROM mis.oms t1 WHERE EXISTS(SELECT * FROM mis.oms t2 WHERE t1.id != t2.id AND (
	CASE WHEN t1.oms_series IS NULL THEN
		t1.oms_number = t2.oms_number 
	ELSE
		t1.oms_series = t2.oms_series AND t1.oms_number = t2.oms_number
	END)) AND NOT EXISTS(SELECT * FROM mis.medcards m WHERE m.policy_id = t1.id)
*/

/* Эта часть удаляем всех, у кого нет карт (всех ОМСников без карт) */
/* DELETE FROM mis.oms t1 WHERE NOT EXISTS(SELECT *
	FROM mis.medcards t2
	WHERE t1.id = t2.policy_id); */

/* Эта часть строит */
/* UPDATE mis.oms
   SET oms_series_number = (
	CASE WHEN oms_series IS NULL THEN
		REPLACE(REPLACE(oms_number, '-', ''), ' ','')
	ELSE
		CONCAT(REPLACE(REPLACE(oms_series, '-', ''), ' ',''), REPLACE(REPLACE(oms_number, '-', ''), ' ',''))
	END); */