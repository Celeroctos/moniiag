-- Column: type

-- ALTER TABLE mis.medcards_rules DROP COLUMN type;

ALTER TABLE mis.medcards_rules ADD COLUMN type integer;
COMMENT ON COLUMN mis.medcards_rules.type IS 'Тип карты (0 - Амбулаторная, 1 - Стационарная)';

UPDATE mis.medcards_rules SET type = 1;
UPDATE mis.medcards_rules SET type = 0 WHERE id = /* 15 */; -- Регистратура КДО: она единственная имеет пока амбулаторный тип