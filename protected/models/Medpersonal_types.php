<?php
/**
 * AR-модель для работы с типами медперсонала
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
class Medpersonal_types extends MisActiveRecord
{
	public $id;
	public $name;
	
	public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	
	public function relations()
	{
		return [
			'medpersonals'=>[self::HAS_MANY, 'Medpersonal', 'type'],
		];
	}

	public function attributeLabels()
	{
		return [
			'name'=>'Наименование',
		];
	}
	
	public function tableName()
	{
		return 'mis.medpersonal_types';
	}
}