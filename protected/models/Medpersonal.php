<?php
/**
 * AR-модель для работы с медперсоналом. (Дублер класса MedWorker, т.к. 
 * название AR лучше делать, чтобы соответствовало названию таблицы в БД)
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
class Medpersonal extends MisActiveRecord
{
	public $id;
	public $name;
	public $type;
	public $is_for_pregnants;
	public $payment_type;
	public $is_medworker;
	
	public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	
	public function relations()
	{
		return [
			'type'=>[self::BELONGS_TO, 'Medpersonal_templates', 'type'],
		];
	}
	
	public function tableName()
	{
		return 'mis.medpersonal';
	}
}