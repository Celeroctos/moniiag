<?php
/**
 * AR-модель для работы с enterprise_params
 */
class Enterprise extends MisActiveRecord 
{
	public $id;
	public $shortname;
	
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	
	/**
	 * Список для выпадающего списка (см. dropDownList Yii)
	 * @param string $typeQuery Добавить/обновить
	 * @return array
	 */
	public static function getEnterpriseListData($typeQuery)
	{
		$model=new Enterprise;
		$criteria=new CDbCriteria;
		$criteria->select='id, shortname';
		$enterpriseList=$model->findAll($criteria);
		
		return CHtml::listData(
			CMap::mergeArray([
				[
					'id'=>$typeQuery=='insert' ? null : null,
					'shortname'=>'',
				]
			], $enterpriseList),
			'id',
			'shortname'
		);
	}	
	
	public function relations()
	{
		return [
			'wards'=>[self::HAS_MANY, 'Ward', 'enterprise_id'],
		];
	}
	
    public function tableName()
    {
        return 'mis.enterprise_params';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $enterprises = $connection->createCommand()
            ->select('ep.*, et.name as enterprise_type')
            ->from('mis.enterprise_params ep')
            ->join('mis.enterprise_types et', 'ep.type = et.id');

        if($filters !== false) {
            $this->getSearchConditions($enterprises, $filters, array(
                'requisits' => array(
                    'bank',
                    'bank_account',
                    'inn',
                    'kpp'
                )
            ), array(
                'ep' => array('id', 'bank', 'bank_account', 'inn', 'kpp', 'fullname', 'shortname'),
                'et' => array('enterprise_type', 'name')
            ), array(
                'enterprise_type' => 'name'
            ));
        }

        if($sidx !== false && $sord !== false) {
            $enterprises->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $enterprises->limit($limit, $start);
        }

        return $enterprises->queryAll();
    }


    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $enterprise = $connection->createCommand()
                ->select('ep.*')
                ->from('mis.enterprise_params ep')
                ->where('ep.id = :id', array(':id' => $id))
                ->queryRow();

            return $enterprise;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}