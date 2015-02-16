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
						"id_user_create" integer NOT NULL, --Пользователь, создавший заказ
						"id_paid_expense" integer, --Номер счета, при статусе "новое" пустое значение, при статусе "включено в счет" ID счета					
						"status" integer, --Оплачен/не оплачен (1/0)
						PRIMARY KEY(order_id)
					);
HERE;
			$command=$connection->createCommand($sql);
			$command->execute();
			/*Таблица на самом деле является TEMP-хранилищем для создания направлений на её основе, можно чистить.*/
			$sql=<<<HERE
					CREATE TABLE IF NOT EXISTS "paid_order_details"
					(
						"id_paid_order_detail" serial NOT NULL,
						"id_paid_order" integer NOT NULL, --FK (table paid_orders)
						"id_paid_service" integer NOT NULL, --FK (table paid_services)
						PRIMARY KEY(id_paid_order_detail)
					);
HERE;
			$command=$connection->createCommand($sql);
			$command->execute();
			
			$sql=<<<HERE
					CREATE TABLE IF NOT EXISTS "paid_referrals"
					(
						"id_paid_referrals" serial NOT NULL, --Уникальный номер направления
						"id_paid_order" integer NOT NULL, --FK (table paid_orders)
						"id_medcard" integer NOT NULL, --FK (table medcards)
						"date" TIMESTAMPTZ,
						"status" integer, --Сомнительно, возможно удаление (есть в paid_orders)
						PRIMARY KEY(id_paid_referrals)
					);
HERE;
			$command=$connecction->createCommand($sql);
			$command->execute();
			
			$sql=<<<HERE
					CREATE TABLE IF NOT EXISTS "paid_referrals_details"
					(
						"id_paid_referral_detail" serial NOT NULL,
						"id_paid_service" integer NOT NULL,
						"id_paid_referral" integer NOT NULL,
						PRIMARY KEY(id_paid_referral_detail)
					);
HERE;
			$command=$connection->createCommand($sql);
			$command->execute();
			
			$sql=<<<HERE
					CREATE TABLE IF NOT EXISTS "paid_expenses"
					(
						"id_paid_expense" serial NOT NULL,
						"date" TIMESTAMPTZ,
						"id_paid_order" integer NOT NULL, --FK (table paid_orders)
						"status" integer, --Сомнительно, возможно удаление (есть в paid_orders)
						PRIMARY KEY(id_paid_expense)
					);
HERE;
			$command=$connection->createCommand($sql);
			$command->execute();
			
			$sql=<<<HERE
					CREATE TABLE IF NOT EXISTS "paid_payments"
					(
						"id_paid_payment" serial NOT NULL,
						"id_paid_expense" integer NOT NULL, --FK (table paid_expenses)
						
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