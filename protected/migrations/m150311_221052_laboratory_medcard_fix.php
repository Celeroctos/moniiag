<?php

class m150311_221052_laboratory_medcard_fix extends CDbMigration {

	public function safeUp() {
		$sql = <<< SQL
			ALTER TABLE "lis"."direction" ADD "medcard_id" INT REFERENCES "lis"."medcard"("id");
SQL;
		foreach (explode(";", $sql) as $s) {
			$this->execute($s);
		}
	}

	public function safeDown() {
		$sql = <<< SQL
			ALTER TABLE "lis"."direction" DROP "medcard_id";
SQL;
		foreach (explode(";", $sql) as $s) {
			$this->execute($s);
		}
	}
}