<?php
/**
 * Структура БД для модуля платных услуг.
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
class m150215_142147_paid_module extends CDbMigration
{
	public function up()
	{
            $connection=Yii::app()->db;
            $transaction=$connection->beginTransaction();
        
            try 
            {
            $sql=<<<HERE
                    CREATE TABLE IF NOT EXISTS "paid_groups"
                    (
                        "id_paid_group" serial NOT NULL,
                        "name" character varying(255) NOT NULL, --Имя группы
                        "p_id" integer DEFAULT NULL, --Родитель группы, NULL, если нету
                        PRIMARY KEY (id_paid_group)
                    );      
HERE;
            $command=$connection->createCommand($sql);
            $command->execute();
            
            $sql=<<<HERE
					CREATE TABLE IF NOT EXISTS "paid_services"
					(
						"id_paid_service" serial NOT NULL,
						"id_paid_group" integer, --FK (table paid_groups)
						"name" character varying(255) NOT NULL, --Имя услуги
						PRIMARY KEY(id_paid_service)
					);
HERE;
            $command=$connection->createCommand($sql);
			$command->execute();
			
			$sql=<<<HERE
					CREATE TABLE IF NOT EXISTS "paid_services_doctors"
					(
						"id_paid_service_doctor" serial NOT NULL,
						"id_paid_group" integer NOT NULL, --FK (table paid_groups)
						"id_doctor" integer NOT NULL, --FK (table doctors)
						PRIMARY KEY(id_doctor)
					);
HERE;
			$command=$connection->createCommand($sql);
			$command->execute();
			
			$sql=<<<HERE
					CREATE TABLE IF NOT EXISTS "paid_orders"
					(
						"id_paid_order" serial NOT NULL,
						"name" character varying(255),
						PRIMARY KEY(order_id)
					);
HERE;
			$command=$connection->createCommand($sql);
			$command->execute();
			
			$sql=<<<HERE
					CREATE TABLE IF NOT EXISTS "paid_order_details"
					(
						"id_paid_order_detail" serial NOT NULL,
						"id_paid_order" integer NOT NULL, --FK (table paid_orders)
						"id_paid_service" integer NOT NULL, --FK (table paid_services)
					);
HERE;
			
            $transaction->commit();
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