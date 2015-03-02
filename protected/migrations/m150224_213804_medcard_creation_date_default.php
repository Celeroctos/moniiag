<?php

class m150224_213804_medcard_creation_date_default extends CDbMigration {

	public function safeUp() {
        $this->getDbConnection()->createCommand("ALTER TABLE mis.medcards ALTER COLUMN date_created SET DEFAULT now()")->execute();
	}

	public function safeDown() {
        $this->getDbConnection()->createCommand("ALTER TABLE mis.medcards ALTER COLUMN date_created DROP DEFAULT")->execute();
	}
}