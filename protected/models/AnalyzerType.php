<?php
/**
* AR-модель для работы с analysis_params
*/
class AnalyzerType extends MisActiveRecord 
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        return [
            ['type', 'required', 'on'=>'analyzertypes.update'],
            ['type, name, notes', 'type', 'type'=>'string', 'on'=>'analyzertypes.update'],
            ['id', 'type', 'type'=>'integer', 'on'=>'analyzertypes.update'], //[controller].[action]

            ['type', 'required', 'on'=>'analyzertypes.create'],
            ['type, name, notes', 'type', 'type'=>'string', 'on'=>'analyzertypes.create'],
            ['id', 'type', 'type'=>'integer', 'on'=>'analyzertypes.create'], //[controller].[action]

            ['id, type, name, notes', 'safe', 'on'=>'analyzertypes.search'],
        ];
    }

    public function tableName()
    {
        return 'lis.analyzer_types';
    }

    /**
    * Список для выпадающего списка (см. dropDownList Yii)
    * @param string $typeQuery Добавить/обновить
    * @return array
    */
    public static function getAnalyzerTypeListData($typeQuery)
    {
        $model=new AnalyzerType;
        $criteria=new CDbCriteria;
        $criteria->select='id, type';
        $analyzertypeList=$model->findAll($criteria);

        return CHtml::listData(
            CMap::mergeArray([
                [
                    'id'=>$typeQuery=='insert' ? null : null,
                    'type'=>'',
                ]
                ], $analyzertypeList),
            'id',
            'type'
        );
    }	
/*
    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $analyzertypes = $connection->createCommand()
        ->select('at.*')
        ->from('lis.analysis_params ap');

        if($filters !== false) {
            $this->getSearchConditions($analyzertypes, $filters, array(
                ), array(
                    'ap' => array('analysis_param', 'name')
                ), array(
                    'analysis_param' => 'name'
            ));
        }

        if($sidx !== false && $sord !== false) {
            $analyzertypes->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $analyzertypes->limit($limit, $start);
        }

        return $analyzertypes->queryAll();
    }
*/

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $analyzertype = $connection->createCommand()
            ->select('at.*')
            ->from('lis.analyzer_types at')
            ->where('at.id = :id', array(':id' => $id))
            ->queryRow();

            return $analyzertype;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function attributeLabels() {
        return [
            'id'=>'#ID',
            'type'=>'Тип анализатора',
            'name'=>'Название анализатора',
            'notes'=>'Пометки'
        ];
    }

    /**
    * Метод для поиска в CGridView
    */
    public function search()
    {
        $criteria=new CDbCriteria;

        //        if($this->validate())
        {
            $criteria->compare('id', $this->id, false);
            $criteria->compare('type', $this->type, true);
            $criteria->compare('name', $this->name, true);
            $criteria->compare('notes', $this->notes, true);
        }
        /*        else
        {
        $criteria->addCondition('id=-1');
        }
        */		
        return new CActiveDataProvider($this, [
            'pagination'=>['pageSize'=>10],
            'criteria'=>$criteria,
            'sort'=>[
                'attributes'=>[
                    'id', 
                    'type', 
                    'name', 
                    'notes'
                ],
                'defaultOrder'=>[
                    'type'=>CSort::SORT_ASC,
                ],
            ],
        ]);
    }
}