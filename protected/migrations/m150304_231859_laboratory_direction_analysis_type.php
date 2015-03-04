<?php

class m150304_231859_laboratory_direction_analysis_type extends CDbMigration
{
	public function safeUp() {
        $this->getDbConnection()->createCommand(
            "ALTER TABLE lis.direction ADD analysis_type_id INT REFERENCES lis.analysis_types(id)"
        )->execute();
	}

	public function safeDown() {
        $this->getDbConnection()->createCommand(
            "ALTER TABLE lis.direction DROP analysis_type_id"
        )->execute();
	}
}