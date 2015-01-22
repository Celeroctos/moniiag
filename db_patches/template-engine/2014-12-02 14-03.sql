DROP TRIGGER IF EXISTS on_medcard_categories_delete ON mis.medcard_categories;

CREATE OR REPLACE FUNCTION mis.medcard_categories_reset_children() RETURNS TRIGGER AS $$
BEGIN
	UPDATE mis.medcard_categories SET parent_id    = -1 WHERE parent_id    = OLD.id;
	UPDATE mis.medcard_elements   SET categorie_id = -1 WHERE categorie_id = OLD.id;
	RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER on_medcard_categories_delete BEFORE DELETE ON mis.medcard_categories FOR EACH ROW EXECUTE PROCEDURE
	mis.medcard_categories_reset_children();
	
UPDATE mis.medcard_elements SET guide_id = -1 WHERE guide_id IS NULL;
UPDATE mis.medcard_elements_patient SET guide_id = -1 WHERE guide_id IS NULL;
