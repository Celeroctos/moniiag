<?php

class m150304_014634_laboratory_direction_history extends CDbMigration {

	public function safeUp() {
		$this->getDbConnection()->createCommand(
			"ALTER TABLE lis.direction ADD history TEXT DEFAULT ''"
		)->execute();
	}

	public function safeDown() {
		$this->getDbConnection()->createCommand(
			"ALTER TABLE lis.direction DROP hisstory"
		)->execute();
	}
}