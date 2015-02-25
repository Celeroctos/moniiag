<?php

class m150224_213804_medcard_creation_date_default extends CDbMigration {

	public function safeUp() {
		$this->alterColumn("mis.medcards", "date_created", "set default now()");
	}

	public function safeDown() {
		$this->alterColumn("mis.medcards", "date_created", "drop default");
	}
}