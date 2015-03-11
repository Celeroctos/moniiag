<?php

class m150311_203540_laboratory_medcards extends CDbMigration {

	public function safeUp() {
		$sql = <<< SQL

			CREATE TABLE "lis"."address" (
			  "id" SERIAL PRIMARY KEY, -- Первичный ключ
			  "street_name" VARCHAR(200) DEFAULT NULL, -- Название улицы
			  "house_number" VARCHAR(10) DEFAULT NULl, -- Номер дома
			  "flat_number" VARCHAR(10) DEFAULT NULL, -- Номер квартиры
			  "post_index" INT DEFAULT NULL, -- Почтовый индекс,
			  "city" VARCHAR(50) DEFAULT NULL -- Город
			);

			CREATE TABLE "lis"."patient" (
			  "id" SERIAL PRIMARY KEY, -- Первичный ключ
			  "surname" VARCHAR(100) NOT NULL, -- Фамилия пациента
			  "name"  VARCHAR(50) NOT NULL, -- Имя пациента
			  "patronymic" VARCHAR(100) DEFAULT NULL, -- Отчество пациента
			  "sex" INT NOT NULL, -- Пол пациента
			  "birthday" DATE NOT NULL, -- Дата рождения
			  "policy_number" VARCHAR(50) DEFAULT NULL, -- Номер ОМС
			  "policy_issue_date" VARCHAR(50) DEFAULT NULL, -- Дата выдачи ОМС
			  "policy_insurance_id" INT REFERENCES "mis"."insurances"("id") DEFAULT NULL, -- СМО, выдавшая ОМС
			  "register_address_id" INT REFERENCES "lis"."address"("id") DEFAULT NULL, -- Адрес регистрации
			  "address_id" INT REFERENCES "lis"."address"("id") DEFAULT NULL, -- Адрес фактического проживания
			  "is_policy_voluntary" INT DEFAULT 0 -- Является ДМС?
			);

			CREATE TABLE "lis"."medcard" (
			  "id" SERIAL PRIMARY KEY, -- Первичный ключ
			  "mis_medcard" VARCHAR(50) REFERENCES "mis"."medcards"("card_number") DEFAULT NULL, -- Медкартка в МИС
			  "sender_id" INT REFERENCES "mis"."doctors"("id") DEFAULT NULL, -- Направитель
			  "patient_id" INT REFERENCES "lis"."patient"("id") DEFAULT NULL -- ОМС/ДМС
			);
SQL;
		foreach (explode(";", $sql) as $s) {
			$this->execute($s);
		}
	}

	public function safeDown() {
		$sql = <<< SQL
			DROP TABLE "lis"."address" CASCADE;
			DROP TABLE "lis"."patient" CASCADE;
			DROP TABLE "lis"."medcard" CASCADE;
SQL;
		foreach (explode(";", $sql) as $s) {
			$this->execute($s);
		}
	}
}