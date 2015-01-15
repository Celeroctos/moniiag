-- Column: employee_id

-- ALTER TABLE mis.role_action DROP COLUMN employee_id;

ALTER TABLE mis.role_action ADD COLUMN employee_id integer;
COMMENT ON COLUMN mis.role_action.employee_id IS 'Частное правило: id сотрудника';

-- Column: mode

-- ALTER TABLE mis.role_action DROP COLUMN mode;

ALTER TABLE mis.role_action ADD COLUMN mode integer;
COMMENT ON COLUMN mis.role_action.mode IS 'Частное правило: режим правила. 0 - добавить к роли, 1 - исключить из роли';

UPDATE mis.role_action SET employee_id = -1; -- Сброс на дефолтного сотрудника

-- Constraint: mis."role-action_pkey"

-- ALTER TABLE mis.role_action DROP CONSTRAINT "role-action_pkey";

ALTER TABLE mis.role_action
  ADD CONSTRAINT "role-action_pkey" PRIMARY KEY(role_id, action_id, employee_id);
  
-- Column: user_id

-- ALTER TABLE mis.doctors DROP COLUMN user_id;

ALTER TABLE mis.doctors ADD COLUMN user_id integer;
COMMENT ON COLUMN mis.doctors.user_id IS 'ID пользователя, к которому привязан сотрудник';
