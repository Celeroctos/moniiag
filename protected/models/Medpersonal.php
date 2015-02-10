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
					'defaultOrder'=>[
						'id'=>CSort::SORT_DESC,
					],
			],
		]);
	}
}