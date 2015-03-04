<?php

class m150304_020617_laboratory_direction_fixes extends CDbMigration {

	public $fixes = [
		[
			"up" => "ALTER TABLE lis.direction ADD department_id INT REFERENCES mis.enterprise_params(id)",
			"down" => "ALTER TABLE lis.direction DROP department_id"
		]
	];

	public function safeUp() {
		foreach ($this->fixes as $fix) {
			$this->getDbConnection()->createCommand($fix["up"])->execute();
		}
	}

	public function safeDown() {
		foreach ($this->fixes as $fix) {
			$this->getDbConnection()->createCommand($fix["down"])->execute();
		}
	}
}