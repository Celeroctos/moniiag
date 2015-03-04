<?php

class m150304_200256_laboratory_migration_fix extends CDbMigration {

	public function safeUp() {
        $this->getDbConnection()->createCommand(
            "ALTER TABLE lis.analysis ADD medcard_number VARCHAR(50) REFERENCES mis.medcards(card_number)"
        )->execute();
	}

	public function safeDown() {
        $this->getDbConnection()->createCommand(
            "ALTER TABLE lis.analysis DROP medcard_number"
        )->execute();
	}
}