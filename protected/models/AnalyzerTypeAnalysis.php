<?php

/**
 * This is the model class for table "lis.analyzer_type_analysis".
 *
 * The followings are the available columns in table 'lis.analyzer_type_analysis':
 * @property integer $id
 * @property integer $analyser_type_id
 * @property integer $analysis_type_id
 *
 * The followings are the available model relations:
 * @property AnalyzerTypes $analyserType
 * @property AnalysisTypes $analysisType
 */
class AnalyzerTypeAnalysis extends MisActiveRecord
{
    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'lis.analyzer_type_analysis';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('analyser_type_id, analysis_type_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, analyser_type_id, analysis_type_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'analyserType' => array(self::BELONGS_TO, 'AnalyzerTypes', 'analyser_type_id'),
			'analysisType' => array(self::BELONGS_TO, 'AnalysisTypes', 'analysis_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'analyser_type_id' => 'Analyser Type',
			'analysis_type_id' => 'Analysis Type',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('analyser_type_id',$this->analyser_type_id);
		$criteria->compare('analysis_type_id',$this->analysis_type_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AnalyzerTypeAnalysis the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
