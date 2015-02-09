<?php
/**
 * Класс для работы с payment_types
 */
class Payment extends MisActiveRecord 
{
	public $id;
	public $name;
	public $tasu_string;
	
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

	public function rules()
	{
		return [
			['id', 'type', 'type'=>'integer', 'on'=>'payment.search'],
			['name, tasu_string', 'type', 'type'=>'string', 'on'=>'payment.search'],
			
			['name, tasu_string', 'required', 'on'=>'payments.create'],
			['name, tasu_string', 'type', 'type'=>'string', 'on'=>'payments.create'],
			
			['name, tasu_string', 'required', 'on'=>'payments.update'],
			['name, tasu_string', 'type', 'type'=>'string', 'on'=>'payments.update'],
		];
	}
	
    public function tableName()
    {
        return 'mis.payment_types';
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
			$criteria->compare('name', $this->name);
			$criteria->compare('tasu_string', $this->tasu_string);
		}
		else
		{
			$criteria->addCondition('id=-1');
		}
		return new CActiveDataProvider($this, [
			'pagination'=>['pageSize'=>15],
			'criteria'=>$criteria,
			'sort'=>[
					'defaultOrder'=>[
						'id'=>CSort::SORT_DESC,
					],
			],
		]);
	}	
	
	public function attributeLabels()
	{
		return [
			'id'=>'#ID',
			'name'=>'Имя',
			'tasu_string'=>'Tasu',
		];
	}
	
    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $payment = $connection->createCommand()
                ->select('p.*')
                ->from(Payment::model()->tableName().' p')
                ->where('p.id = :id', array(':id' => $id))
                ->queryRow();

            return $payment;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $payments = $connection->createCommand()
            ->select('p.*')
            ->from('mis.payment_types p');

        if($filters !== false) {
            $this->getSearchConditions($payments, $filters, array(
            ), array(
               'p' => array('id', 'name', 'tasu_string'),
            ));
        }

        if($sidx !== false && $sord !== false) {
            $payments->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $payments->limit($limit, $start);
        }

        return $payments->queryAll();
    } 
}

?>