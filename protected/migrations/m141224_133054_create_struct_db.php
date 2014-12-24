<?php
/**
 * Миграция на создание структуры БД всего проекта.
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */

class m141224_133054_create_struct_db extends CDbMigration
{
	public function up()
	{
		$connection=Yii::app()->db;
		
		$transaction=$connection->beginTransaction();
		$sql="
		CREATE TABLE `actions` (
			`actionId` INT(11) NOT NULL AUTO_INCREMENT,
			`title` VARCHAR(255) NOT NULL,
			`description` TEXT NOT NULL,
			`address` VARCHAR(255) NOT NULL,
			`date` VARCHAR(255) NOT NULL,
			PRIMARY KEY (`actionId`)
		)
		COMMENT='Мероприятия'
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB;
		";
		
		try
		{ //заводим транзакцию
			$command=$connection->createCommand($sql);
			$command->execute();
		}
		catch(Exception $e)
		{
			$transaction->rollback();
		}
	}

	public function down()
	{
	}
}
