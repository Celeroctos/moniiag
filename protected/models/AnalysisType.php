<?php
/**
* AR-модель для работы с analysistype_params
*/
class AnalysisType extends MisActiveRecord 
{
    public $id;
    public $name;
    public $short_name;
    public $automatic;
    public $manual;

    const TRUE_ID=true;
    const FALSE_ID=false;
    const TRUE_NAME='Да';
    const FALSE_NAME='Нет';

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        return [
            ['name, short_name', 'required', 'on'=>'analysistypes.update'],
            ['name, short_name', 'type', 'type'=>'string', 'on'=>'analysistypes.update'],
            ['id, automatic, manual', 'type', 'type'=>'integer', 'on'=>'analysistypes.update'], //[controller].[action]
            ['automatic, manual', 'safe', 'on'=>'analysistypes.update'],

            ['name, short_name', 'required', 'on'=>'analysistypes.create'],
            ['name, short_name', 'type', 'type'=>'string', 'on'=>'analysistypes.create'],
            ['id, automatic, manual', 'type', 'type'=>'integer', 'on'=>'analysistypes.create'], //[controller].[action]
            ['automatic, manual', 'safe', 'on'=>'analysistypes.create'],

            ['id, name, short_name, automatic, manual', 'safe', 'on'=>'analysistypes.search'],
        ];
    }

    public function tableName()
    {
        return 'lis.analysis_types';
    }

    /**
    * Список для выпадающего списка (см. dropDownList Yii)
    * @param string $typeQuery Добавить/обновить
    * @return array
    */
    public static function getAnalysisTypeListData($typeQuery)
    {
        $model=new AnalysisType;
        $criteria=new CDbCriteria;
        $criteria->select='id, short_name';
        $analysistypeList=$model->findAll($criteria);

        return CHtml::listData(
            CMap::mergeArray([
                [
                    'id'=>$typeQuery=='insert' ? null : null,
                    'short_name'=>'',
                ]
                ], $analysistypeList),
            'id',
            'short_name'
        );
    }	

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $analysistypes = $connection->createCommand()
        ->select('at.*')
        ->from('lis.analysis_types at');

        if($filters !== false) {
            $this->getSearchConditions($analysistypes, $filters, array(
                ), array(
                    'at' => array('analysis_type', 'name')
                ), array(
                    'analysis_type' => 'name'
            ));
        }

        if($sidx !== false && $sord !== false) {
            $analysistypes->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $analysistypes->limit($limit, $start);
        }

        return $analysistypes->queryAll();
    }


    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $analysistype = $connection->createCommand()
            ->select('at.*')
            ->from('lis.analysis_types at')
            ->where('at.id = :id', array(':id' => $id))
            ->queryRow();

            return $analysistype;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function attributeLabels() {
        return [
            'id'=>'#ID',
            'name'=>'Наименование анализа',
            'short_name'=>'Краткое наименование анализа',
            'automatic'=>'Автоматическая методика',
            'manual'=>'Ручная методика'
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
            $criteria->compare('name', $this->name, true);
            $criteria->compare('short_name', $this->name, true);
            $criteria->compare('automatic', $this->name, true);
            $criteria->compare('manual', $this->name, true);
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
                    'name', 
                    'short_name', 
                    'automatic', 
                    'manual'
                ],
                'defaultOrder'=>[
                    'id'=>CSort::SORT_DESC,
                ],
            ],
        ]);
    }

    /**
    * Используется в activeDropDownList()
    * @return array
    */    
    public static function getAutomaticList()
    {
        return CHtml::listData([
            [
                'automatic'=>self::FALSE_ID,
                'name'=>self::FALSE_NAME,
            ],
            [
                'automatic'=>self::TRUE_ID,
                'name'=>self::TRUE_NAME
            ],
            ], 'automatic', 'name');
    }

    /**
    * Используется в activeDropDownList()
    * @return array
    */    
    public static function getManualList()
    {
        return CHtml::listData([
            [
                'manual'=>self::FALSE_ID,
                'name'=>self::FALSE_NAME,
            ],
            [
                'manual'=>self::TRUE_ID,
                'name'=>self::TRUE_NAME
            ],
            ], 'manual', 'name');
    }

    /**
    * Используется в CGridView
    * @return array
    */
    public function getBool($id)
    {
        switch($id)
        {
            case self::TRUE_ID:
                return self::TRUE_NAME;
                break;
            case self::FALSE_ID:
                return self::FALSE_NAME;
                break;
            default:
                return 'Не указано';
                break;
        }
    }

}