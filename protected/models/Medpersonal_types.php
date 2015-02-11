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

	/**
	 * Используется в activeDropDownList()
	 * @return array
	 */
	public static function getNameList()
	{
		return CHtml::listData(MedPersonal_types::model()->findAll(), 'id', 'name');
	}
	
	public function attributeLabels()
	{
		return [
			'name'=>'Тип персонала',
		];
	}
	
	public function tableName()
	{
		return 'mis.medpersonal_types';
	}
}