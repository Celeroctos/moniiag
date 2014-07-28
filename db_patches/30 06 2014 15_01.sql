ALTER TABLE mis.cancelled_greetings ADD COLUMN policy_id integer;
COMMENT ON COLUMN mis.cancelled_greetings.policy_id IS 'ИД полиса для упрощения запросов при выводе данных из это таблицы';