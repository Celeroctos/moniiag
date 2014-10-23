-- Column: employee_id

-- ALTER TABLE mis.role_action DROP COLUMN employee_id;

ALTER TABLE mis.role_action ADD COLUMN employee_id integer;
COMMENT ON COLUMN mis.role_action.employee_id IS 'Частное правило: id сотрудника';

-- Column: mode

-- ALTER TABLE mis.role_action DROP COLUMN mode;

ALTER TABLE mis.role_action ADD COLUMN mode integer;
COMMENT ON COLUMN mis.role_action.mode IS 'Частное правило: режим правила. 0 - добавить к роли, 1 - исключить из роли';
