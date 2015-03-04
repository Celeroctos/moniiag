<?php

class m150304_015135_laboratory_direction_ward extends CDbMigration {

	public function safeUp() {
		$this->getDbConnection()->createCommand(
			"ALTER TABLE lis.direction ADD ward_id INT REFERENCES mis.wards(id)"
		)->execute();
	}

	public function safeDown() {
		$this->getDbConnection()->createCommand(
			"ALTER TABLE lis.direction DROP ward_id"
		)->execute();
	}
}