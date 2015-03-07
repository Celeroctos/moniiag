<?php
/**
* AR-модель для работы с analysis_params
*/
class AnalysisSampleType extends MisActiveRecord 
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        return [
            ['type', 'required', 'on'=>'analysissampletypes.update'],
            ['type, subtype', 'type', 'type'=>'string', 'on'=>'analysissampletypes.update'],
            ['id', 'type', 'type'=>'integer', 'on'=>'analysissampletypes.update'], //[controller].[action]

            ['type', 'required', 'on'=>'analysissampletypes.create'],
            ['type, subtype', 'type', 'type'=>'string', 'on'=>'analysissampletypes.create'],
            ['id', 'type', 'type'=>'integer', 'on'=>'analysissampletypes.create'], //[controller].[action]

            ['id, type, subtype', 'safe', 'on'=>'analysissampletypes.search'],
        ];
    }

    public function tableName()
    {
        return 'lis.analysis_sample_types';
    }

    /**
    * Список для выпадающего списка (см. dropDownList Yii)
    * @param string $typeQuery Добавить/обновить
    * @return array
    */
    public static function getAnalysisSampleTypeListData($typeQuery)
    {
        $model=new AnalysisSampleType;
        $criteria=new CDbCriteria;
        $criteria->select='id, type';
        $analysissampletypeList=$model->findAll($criteria);

        return CHtml::listData(
            CMap::mergeArray([
                [
                    'id'=>$typeQuery=='insert' ? null : null,
                    'type'=>'',
                ]
                ], $analysissampletypeList),
            'id',
            'type'
        );
    }	
/*
    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $analysissampletypes = $connection->createCommand()
        ->select('at.*')
        ->from('lis.analysis_params ap');

        if($filters !== false) {
            $this->getSearchConditions($analysissampletypes, $filters, array(
                ), array(
                    'ap' => array('analysis_param', 'name')
                ), array(
                    'analysis_param' => 'name'
            ));
        }

        if($sidx !== false && $sord !== false) {
            $analysissampletypes->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $analysissampletypes->limit($limit, $start);
        }

        return $analysissampletypes->queryAll();
    }
*/

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $analysissampletype = $connection->createCommand()
            ->select('ast.*')
            ->from('lis.analysis_sample_types ast')
            ->where('ast.id = :id', array(':id' => $id))
            ->queryRow();

            return $analysissampletype;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function attributeLabels() {
        return [
            'id'=>'#ID',
            'type'=>'Тип образца',
            'subtype'=>'Подтип образца',
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
            $criteria->compare('subtype', $this->subtype, true);
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
                    'subtype'
                ],
                'defaultOrder'=>[
                    'id'=>CSort::SORT_DESC,
                ],
            ],
        ]);
    }

}