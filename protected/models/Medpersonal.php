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
	public $medcard_templates;
	
	const PAYMENT_TYPE_TRUE_ID=1;
	const PAYMENT_TYPE_FALSE_ID=0;
	const PAYMENT_TYPE_TRUE_NAME='Бюджет';
	const PAYMENT_TYPE_FALSE_NAME='ОМС';
	
	const IS_MEDWORKER_TRUE_ID=1;
	const IS_MEDWORKER_FALSE_ID=0;
	const IS_MEDWORKER_TRUE_NAME='Да';
	const IS_MEDWORKER_FALSE_NAME='Нет';
	
	const IS_FOR_PREGNANTS_TRUE_ID=1;
	const IS_FOR_PREGNANTS_FALSE_ID=0;
	const IS_FOR_PREGNANTS_TRUE_NAME='Да';
	const IS_FOR_PREGNANTS_FALSE_NAME='Нет';
	
	public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	
	public function relations()
	{
		return [
			'medpersonal_type'=>[self::BELONGS_TO, 'Medpersonal_types', 'type'],
			'medpersonal_templates'=>[self::HAS_MANY, 'Medpersonal_templates', 'id_medpersonal'],
		];
	}
	
	public function rules()
	{
		return [
			['name', 'required', 'on'=>'medworkers.update'],
			['name', 'type', 'type'=>'string', 'on'=>'medworkers.update'],
			['id, is_for_pregnants, type, payment_type, is_medworker', 'type', 'type'=>'integer', 'on'=>'medworkers.update'], //[controller].[action]
			['medcard_templates', 'safe', 'on'=>'medworkers.update'], //return array or empty
			
			['name', 'required', 'on'=>'medworkers.create'],
			['name', 'type', 'type'=>'string', 'on'=>'medworkers.create'],
			['id, is_for_pregnants, type, payment_type, is_medworker', 'type', 'type'=>'integer', 'on'=>'medworkers.create'], //[controller].[action]
			['medcard_templates', 'safe', 'on'=>'medworkers.create'], //return array or empty
		];
	}
	
	public function tableName()
	{
		return 'mis.medpersonal';
	}
	
	/**
	 * Используется в activeCheckBoxList()
	 * @return array
	 */
	public static function getMedcard_templatesList()
	{
		return CHtml::listData(MedcardTemplate::model()->findAll(), 'id', 'name');
	}
	
	/**
	 * Используется в activeDropDownList()
	 * @return array
	 */
	public static function getPayment_typeList()
	{
		return CHtml::listData([
					[
						'payment_type'=>self::PAYMENT_TYPE_FALSE_ID,
						'name'=>self::PAYMENT_TYPE_FALSE_NAME,
					],
					[
						'payment_type'=>self::PAYMENT_TYPE_TRUE_ID,
						'name'=>self::PAYMENT_TYPE_TRUE_NAME
					],
				], 'payment_type', 'name');
	}
	
	/**
	 * Используется в activeDropDownList()
	 * @return array
	 */	
	public static function getIs_medworkerList()
	{
		return CHtml::listData([
					[
						'is_medworker'=>self::IS_MEDWORKER_FALSE_ID,
						'name'=>self::IS_MEDWORKER_FALSE_NAME,
					],
					[
						'is_medworker'=>self::IS_MEDWORKER_TRUE_ID,
						'name'=>self::IS_MEDWORKER_TRUE_NAME
					],
				], 'is_medworker', 'name');
	}
	
	/**
	 * Используется в activeDropDownList()
	 * @return array
	 */	
	public static function getIs_for_pregnantsList()
	{
		return CHtml::listData([
					[
						'is_for_pregnants'=>self::IS_FOR_PREGNANTS_FALSE_ID,
						'name'=>self::IS_FOR_PREGNANTS_FALSE_NAME,
					],
					[
						'is_for_pregnants'=>self::IS_FOR_PREGNANTS_TRUE_ID,
						'name'=>self::IS_FOR_PREGNANTS_TRUE_NAME
					],
				], 'is_for_pregnants', 'name');
	}
	
	/**
	 * Используется в CGridView
	 * @return array
	 */	
	public function getPayment_type($payment_type)
	{
		switch($payment_type)
		{
			case self::PAYMENT_TYPE_TRUE_ID:
				return self::PAYMENT_TYPE_TRUE_NAME;
				break;
			case self::PAYMENT_TYPE_FALSE_ID:
				return self::PAYMENT_TYPE_FALSE_NAME;
				break;
			default:
				return 'Не указано';
				break;
		}
	}
	
	/**
	 * Используется в CGridView
	 * @return array
	 */
	public function getIs_medworker($is_medworker)
	{
		switch($is_medworker)
		{
			case self::IS_MEDWORKER_TRUE_ID:
				return self::IS_MEDWORKER_TRUE_NAME;
				break;
			case self::IS_MEDWORKER_FALSE_ID:
				return self::IS_MEDWORKER_FALSE_NAME;
				break;
			default:
				return 'Не указано';
				break;
		}
	}
	
	/**
	 * Используется в CGridView
	 * @return array
	 */
	public function getIs_for_pregnants($is_for_pregnants)
	{
		switch($is_for_pregnants)
		{
			case self::IS_FOR_PREGNANTS_TRUE_ID:
				return self::IS_FOR_PREGNANTS_TRUE_NAME;
				break;
			case self::IS_FOR_PREGNANTS_FALSE_ID:
				return self::IS_FOR_PREGNANTS_FALSE_NAME;
				break;
			default:
				return 'Не указано';
				break;
				
		}
	}
	
	public function attributeLabels()
	{
		return [
			'id'=>'#ID',
			'name'=>'Наименование',
			'payment_type'=>'Тип оплаты',
			'is_medworker'=>'Меддолжность',
			'is_for_pregnants'=>'Прин. беременных',
			'type'=>'Тип персонала',
			'medcard_templates'=>'Шаблоны',
		];
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
					],
					'defaultOrder'=>[
							'id'=>CSort::SORT_DESC,
						],
			],
		]);
	}
}