-- Column: comment

-- ALTER TABLE mis.cancelled_greetings DROP COLUMN comment;

ALTER TABLE mis.cancelled_greetings ADD COLUMN comment text;
COMMENT ON COLUMN mis.cancelled_greetings.comment IS 'Комментарий при записи';
