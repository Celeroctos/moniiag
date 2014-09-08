ALTER TABLE mis.tasu_greetings_buffer_history ADD COLUMN log_path TEXT;
COMMENT ON COLUMN mis.tasu_greetings_buffer_history.log_path IS 'Путь до лога';