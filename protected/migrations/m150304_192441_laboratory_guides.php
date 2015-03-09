<?php

class m150304_192441_laboratory_guides extends CDbMigration {

	public function safeUp() {

        $this->createTable("lis.analysis_params", [
            "id" => "serial primary key",
            "name" => "varchar(30) not null",
            "long_name" => "varchar(200)",
            "comment" => "text"
        ]);

        $this->createTable("lis.analysis_sample_types", [
            "id" => "serial primary key",
            "type" => "varchar(100) not null",
            "subtype" => "varchar(100)"
        ]);

        $this->createTable("lis.analysis_types", [
            "id" => "serial primary key",
            "name" => "varchar(200) not null",
            "short_name" => "varchar(20)",
            "automatic" => "int not null default 0",
            "manual" => "int not null default 0"
        ]);

        $this->createTable("lis.analyzer_types", [
            "id" => "serial primary key",
            "type" => "varchar(100) not null",
            "name" => "varchar(100)",
            "notes" => "text"
        ]);

        $this->createTable("lis.analysis_type_templates", [
            "id" => "serial primary key",
            "analysis_type_id" => "int references lis.analysis_types(id)",
            "analysis_param_id" => "int references lis.analysis_params(id)",
            "is_default" => "int not null default 0"
        ]);

        $this->createTable("lis.analyzer_type_analysis", [
            "id" => "serial primary key",
            "analyser_type_id" => "int references lis.analyzer_types(id)",
            "analysis_type_id" => "int references lis.analysis_types(id)"
        ]);
	}

	public function safeDown() {

        $this->dropTable("lis.analysis_params");
        $this->dropTable("lis.analysis_sample_types");
        $this->dropTable("lis.analysis_types");
        $this->dropTable("lis.analyzer_types");
        $this->dropTable("lis.analysis_type_templates");
        $this->dropTable("lis.analyzer_type_analysis");
	}
}