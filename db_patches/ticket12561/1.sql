-- Function: mis.rename_test_oms_fields()

-- DROP FUNCTION mis.rename_test_oms_fields();

CREATE OR REPLACE FUNCTION mis.rename_test_oms_fields()
  RETURNS trigger AS
$BODY$
BEGIN
	IF (TG_OP = 'UPDATE' AND OLD.first_name != NEW.oms_number) OR TG_OP = 'INSERT' THEN
		UPDATE mis.oms SET
		first_name = NEW.oms_number,
		middle_name = NEW.oms_number,
		last_name = NEW.oms_number
		WHERE id = NEW.id;
	END IF;
	return NEW;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION mis.rename_test_oms_fields()
  OWNER TO postgres;


 -- Trigger: rename_test_oms_fields_trigger on mis.oms

-- DROP TRIGGER rename_test_oms_fields_trigger ON mis.oms;

CREATE TRIGGER rename_test_oms_fields_trigger
  AFTER INSERT OR UPDATE
  ON mis.oms
  FOR EACH ROW
  EXECUTE PROCEDURE mis.rename_test_oms_fields();