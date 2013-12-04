<?php
class Medcard extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcards';
    }

    public function getAll() {


    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false, $enterpriseId = false, $wardId = false, $employeeId = false) {


    }

    public function getOne($id) {


    }

    public function getLastMedcardPerYear($code) {
        $connection = Yii::app()->db;
        $medcard = $connection->createCommand()
            ->select('m.*, CAST(SUBSTRING("m"."card_number", 0, (CHAR_LENGTH("m"."card_number") - 2)) as INTEGER) as "fx"') // Выделение части ключа: нужно отсутствие суррогатного ключа
            ->from('mis.medcards m')
            ->where(array('like', 'm.card_number', '%/'.$code))
            ->order('fx desc')
            ->limit(1, 0);

        return $medcard->queryAll();
    }

}