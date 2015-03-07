<?php

class m150304_195327_laboratory_dynamic_guides extends CDbMigration {

	public function safeUp() {

        $this->createTable("lis.guide", [
            "id" => "serial primary key",
            "name" => "varchar(100)"
        ]);

        $this->createTable("lis.guide_column", [
            "id" => "serial primary key",
            "name" => "varchar(100)",
            "type" => "varchar(20)",
            "guide_id" => "int references lis.guide(id) on delete cascade",
            "lis_guide_id" => "int default -1",
            "position" => "int",
            "display_id" => "int default -1",
            "default_value" => "text"
        ]);

        $this->createTable("lis.guide_row", [
            "id" => "serial primary key",
            "guide_id" => "int references lis.guide(id)  on delete cascade",
        ]);

        $this->createTable("lis.guide_value", [
            "id" => "serial primary key",
            "guide_row_id" => "int references lis.guide_row(id) on delete cascade",
            "guide_column_id" => "int references lis.guide_column(id) on delete cascade",
            "value" => "text"
        ]);
	}

	public function safeDown() {

        $this->dropTable("lis.guide");
        $this->dropTable("lis.guide_column");
        $this->dropTable("lis.guide_row");
        $this->dropTable("lis.guide_value");
    }
}