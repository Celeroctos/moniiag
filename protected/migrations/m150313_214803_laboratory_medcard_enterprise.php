<?php

class m150313_214803_laboratory_medcard_enterprise extends CDbMigration {

	public function safeUp() {
		$sql = <<< SQL
			ALTER TABLE "lis"."medcard" ADD "enterprise_id" INT REFERENCES "mis"."enterprise_params"("id");
SQL;
		foreach (explode(";", $sql) as $s) {
			$this->execute($s);
		}
	}

	public function safeDown() {
		$sql = <<< SQL
			ALTER TABLE "lis"."medcard" DROP "enterprise_id";
SQL;
		foreach (explode(";", $sql) as $s) {
			$this->execute($s);
		}
	}
}