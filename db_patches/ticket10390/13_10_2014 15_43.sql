-- Column: not_printing_values

-- ALTER TABLE mis.medcard_elements DROP COLUMN not_printing_values;

ALTER TABLE mis.medcard_elements ADD COLUMN not_printing_values text;
COMMENT ON COLUMN mis.medcard_elements.not_printing_values IS 'Значения справочников элементов, при выборе которых элемент не выводится на печать';
