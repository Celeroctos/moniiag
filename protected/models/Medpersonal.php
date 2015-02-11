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
			'medpersonal_type'=>[self::BELONGS_TO, 'Medpersonal_types', 'type'],
		];
	}
	
	public function tableName()
	{
		return 'mis.medpersonal';
	}
	
	public function getPayment_type($payment_type)
	{
		switch($payment_type)
		{
			case "1":
				return 'Бюджет';
				break;
			case "0":
				return 'ОМС';
				break;
			default:
				return 'Не указано';
				break;
		}
	}
	
	public function getIs_medworker($is_medworker)
	{
		switch($is_medworker)
		{
			case "1":
				return 'да';
				break;
			case "":
				return 'нет';
				break;
			default:
				return 'Не указано';
				break;
		}
	}

	public function attributeLabels()
	{
		return [
			'name'=>'Тип персонала',
			'payment_type'=>'Тип оплаты',
			'is_medworker'=>'Меддолжность',
			'is_for_pregnants'=>'Прин. беременных',
		];
	}
	
	public function getIs_for_pregnants($is_for_pregnants)
	{
		switch($is_for_pregnants)
		{
			case "1":
				return 'Да';
				break;
			case "0":
				return 'Нет';
				break;
			default:
				return 'Не указано';
				break;
				
		}
	}
	
	/**
	 * Метод для поиска в CGridView
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		
		if($this->validate())
		{
			$criteria->compare('id', $this->id, false);
			$criteria->compare('name', $this->name, true);
		}
		else
		{
			$criteria->addCondition('id=-1');
		}
		return new CActiveDataProvider($this, [
			'pagination'=>['pageSize'=>10],
			'criteria'=>$criteria,
			'sort'=>[
					'attributes'=>[
						'id', 
						'name', 
						'payment_type', 
						'is_medworker', 
						'is_for_pregnants',
						'medpersonal_type.name'=>[
							'asc'=>'medpersonal_types.name',
							'desc'=>'medpersonal_types.name DESC',
						],
						'defaultOrder'=>[
							'id'=>CSort::SORT_DESC,
						],
					],
			]
		]);
	}
}