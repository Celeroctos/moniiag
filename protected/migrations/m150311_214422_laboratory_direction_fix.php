<?php

class m150311_214422_laboratory_direction_fix extends CDbMigration {

	public function safeUp() {
		$sql = <<< SQL
			ALTER TABLE "lis"."direction" DROP "card_number";
			ALTER TABLE "lis"."direction" ADD "patient_id" INT REFERENCES "lis"."patient"("id");
SQL;
		foreach (explode(";", $sql) as $s) {
			$this->execute($s);
		}
	}

	public function safeDown() {
		$sql = <<< SQL
			ALTER TABLE "lis"."direction" DROP "patient_id";
			ALTER TABLE "lis"."direction" ADD "card_number" VARCHAR(50) REFERENCES "mis"."medcards"("card_number");
SQL;
		foreach (explode(";", $sql) as $s) {
			$this->execute($s);
		}
	}
}