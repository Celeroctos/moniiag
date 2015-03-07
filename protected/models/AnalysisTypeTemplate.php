<?php
/**
* AR-модель для работы с analysistype_params
*/
class AnalysisTypeTemplate extends MisActiveRecord 
{
/*    public $analysis_type_id;
    public $analysis_param_id;
    public $is_default;
*/
    public $param_count;
    public $analysis_type;
    public $analysis_param;

    const TRUE_ID=1;
    const FALSE_ID=0;
    const TRUE_NAME='Да';
    const FALSE_NAME='Нет';

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

	public function relations()
	{
		return [
			'analysis_param_id'=>[self::HAS_MANY, 'AnalysisParam', 'id'],
			'analysis_type'=>[self::BELONGS_TO, 'AnalysisType', 'analysis_type_id'],
		];
	}
    
    public function rules()
    {
        return [
            ['analysis_type_id, analysis_param_id', 'required', 'on'=>'analysistypetemplate.update'],
            ['analysis_type_id, analysis_param_id, is_default', 'type', 'type'=>'integer', 'on'=>'analysistypetemplate.update'], //[controller].[action]
            ['is_default', 'safe', 'on'=>'analysistypetemplate.update'],

            ['analysis_type_id, analysis_param_id', 'required', 'on'=>'analysistypetemplate.create'],
            ['analysis_type_id, analysis_param_id, is_default', 'type', 'type'=>'integer', 'on'=>'analysistypetemplate.create'], //[controller].[action]
            ['is_default, analysis_type', 'safe', 'on'=>'analysistypetemplate.create'],
        ];
    }

    public function tableName()
    {
        return 'lis.analysis_type_templates';
    }

    /**
    * Список для выпадающего списка (см. dropDownList Yii)
    * @param string $typeQuery Добавить/обновить
    * @return array
    */
/*    public static function getAnalysisTypeListData($typeQuery)
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
*/
/*    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $analysistypetemplates = $connection->createCommand()
        ->select('att.*, at.name as analysis_type, ap.name as analysis_param')
        ->from('lis.analysis_type_templates att')
        ->join('lis.analysis_types at', 'att.analysis_type_id = at.id')
        ->join('lis.analysis_params ap', 'att.analysis_param_id = ap.id');

        if($filters !== false) {
            $this->getSearchConditions($analysistypetemplates, $filters, array(
                ), array(
                    'att' => array('id', 'analysis_type_id', 'analysis_param_id', 'is_default'),
                    'at' => array('name'),
                    'ap' => array('long_name')
                ), array(
                    'analysis_type' => 'name',
                    'analysis_param' => 'name'
            ));
        }

        if($sidx !== false && $sord !== false) {
            $analysistypetemplates->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $analysistypetemplates->limit($limit, $start);
        }

        return $analysistypetemplates->queryAll();
    }
*/

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
 /*           $analysistypetemplate = $connection->createCommand()
        ->select('att.*, at.name as analysis_type, ap.name as analysis_param')
        ->from('lis.analysis_type_templates att')
        ->join('lis.analysis_types at', 'att.analysis_type_id = at.id')
        ->join('lis.analysis_params ap', 'att.analysis_param_id = ap.id')
            ->where('att.id = :id', array(':id' => $id))
            ->queryRow();
*/
                       $analysistypetemplate = $connection->createCommand()
        ->select('att.*, at.name as analysis_type')
        ->from('lis.analysis_type_templates att')
        ->join('lis.analysis_types at', 'att.analysis_type_id = at.id')
            ->where('att.id = :id', array(':id' => $id))
            ->queryRow();

            return $analysistypetemplate;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function attributeLabels() {
        return [
            'id'=>'#ID',
            'analysis_type'=>'Наименование типа анализа',
            'analysis_param'=>'Наименование параметра анализа',
            'analysis_type_id'=>'Наименование типа анализа',
            'analysis_param_id'=>'Наименование параметра анализа',
            'is_default'=>'Включен по умолчанию?'
        ];
    }

    /**
    * Метод для поиска в CGridView
    */
    public function search()
    {
        $criteria=new CDbCriteria;

	$criteria->with=['analysis_param_id'=>['together'=>true, 'joinType'=>'LEFT JOIN']];
        //        if($this->validate())
        {
/*            $criteria->compare('id', $this->id, false);
            $criteria->compare('analysis_type', $this->analysis_type, true);
            $criteria->compare('analysis_param', $this->analysis_param, true);
            $criteria->compare('is_default', $this->is_default, true);*/
        }
        /*        else
        {
        $criteria->addCondition('id=-1');
        }
        */		
        return  new CActiveDataProvider($this, [
            'pagination'=>['pageSize'=>10],
            'criteria'=>$criteria,
            'sort'=>[
                'attributes'=>[
                    'lis.analysis_params.name' => [
			'asc'=>'analysis_params.name',
			'desc'=>'analysis_params.name DESC',
                    ]
                ],
                'defaultOrder'=>[
                    'analysis_params.name'=>CSort::SORT_ASC,
                ],
            ],
        ]);
    }

 /*   public function types()
    {
        $criteria=new CDbCriteria;
            $criteria->select = 'at.id, at.name as name, count(t.*) as param_count';
            $criteria->group = 'analysis_type_id, at.name';
             $criteria->join = 'LEFT JOIN lis.analysis_types at ON at.id = t.analysis_type_id';
        $qq = new CActiveDataProvider($this, [
            'pagination'=>['pageSize'=>10],
            'criteria'=>$criteria,
        ]);
        return  $qq;
    }*/
    
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
    public function templates($id)
    {
        $criteria=new CDbCriteria   ;
            $criteria->select = 't.id, ap.name, t.is_default';
            $criteria->condition = 't.analysis_type_id=' . $id;
             $criteria->join = 'JOIN lis.analysis_params ap ON t.analysis_param_id = ap.id';
             $criteria->order = 'ap.name';
        $qq = new CActiveDataProvider($this, [
            'pagination'=>['pageSize'=>10],
            'criteria'=>$criteria,
        ]);
        return  $qq;
    }

}