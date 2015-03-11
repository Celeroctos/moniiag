<?php

class m150311_221419_laboratory_medcard_number extends CDbMigration {

	public function safeUp() {
		$sql = <<< SQL
			ALTER TABLE "lis"."medcard" ADD "card_number" VARCHAR(50);
SQL;
		foreach (explode(";", $sql) as $s) {
			$this->execute($s);
		}
	}

	public function safeDown() {
		$sql = <<< SQL
			ALTER TABLE "lis"."medcard" DROP "card_number";
SQL;
		foreach (explode(";", $sql) as $s) {
			$this->execute($s);
		}
	}
}