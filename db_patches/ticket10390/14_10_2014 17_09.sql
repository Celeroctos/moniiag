-- Column: hide_label_before

-- ALTER TABLE mis.medcard_elements DROP COLUMN hide_label_before;

ALTER TABLE mis.medcard_elements ADD COLUMN hide_label_before integer;
COMMENT ON COLUMN mis.medcard_elements.hide_label_before IS '�������� �� ����� �� �� ������';


ALTER TABLE mis.medcard_elements_patient ADD COLUMN hide_label_before integer;
COMMENT ON COLUMN mis.medcard_elements_patient.hide_label_before IS '�������� �� ����� �� �� ������';

UPDATE mis.medcard_elements SET hide_label_before = 0;