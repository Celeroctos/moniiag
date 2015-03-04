<?php

class m150304_193940_laboratory_entry extends CDbMigration {

	public function safeUp() {

        $this->createTable("lis.direction", [
            "id" => "serial primary key",
            "barcode" => "int",
            "status" => "int",
            "comment" => "text default ''",
            "card_number" => "varchar(50) references mis.medcards(card_number)",
            "sender_id" => "int references mis.doctors(id)",
            "sending_date" => "timestamp default now()",
            "treatment_room_employee_id" => "int references mis.doctors(id)",
            "laboratory_employee_id" => "int references mis.doctors(id)",
            "history" => "text default ''",
            "ward_id" => "int references mis.wards(id)",
            "department_id" => "int references mis.enterprise_params(id)"
        ]);

        $this->createTable("lis.analysis", [
            "id" => "serial primary key",
            "registration_date" => "timestamp default now()",
            "direction_id" => "int references lis.direction(id)",
            "doctor_id" => "int references mis.doctors(id)"
        ]);

        $this->createTable("lis.machine", [
            "id" => "serial primary key",
            "name" => "varchar(50)",
            "serial" => "int",
            "model" => "varchar(20)",
            "software_version" => "varchar(10)",
            "analyzer_type_id" => "int references lis.analyzer_types(id)"
        ]);
	}

	public function safeDown() {

        $this->dropTable("lis.analysis");
        $this->dropTable("lis.machine");
        $this->dropTable("lis.direction");
	}
}