<?php

class m150225_211341_analysis_medcard_number extends CDbMigration {

    /**
     * Override that method to upgrade database
     * @return string - Sql query
     */
    public function safeUp() {
        $this->getDbConnection()->createCommand(
            "ALTER TABLE lis.analysis ADD medcard_number VARCHAR(50) REFERENCES mis.medcards(card_number)"
        )->execute();
    }

    /**
     * Override that method to downgrade database
     * @return string - Sql query
     */
    public function safeDown() {
        $this->getDbConnection()->createCommand(
            "ALTER TABLE lis.analysis DROP medcard_number"
        )->execute();
    }
}