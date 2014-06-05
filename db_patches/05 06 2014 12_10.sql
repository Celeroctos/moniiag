-- Column: greeting_status

-- ALTER TABLE mis.doctor_shedule_by_day DROP COLUMN greeting_status;

ALTER TABLE mis.doctor_shedule_by_day ADD COLUMN greeting_status integer;
ALTER TABLE mis.doctor_shedule_by_day ALTER COLUMN greeting_status SET DEFAULT 0;
COMMENT ON COLUMN mis.doctor_shedule_by_day.greeting_status IS 'Статус приёма (да/нет)';

UPDATE mis.doctor_shedule_by_day SET greeting_status=0
