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
            ->select('m.*')
            ->from('mis.medcards m')
            ->where(array('like', 'm.card_number', '%/'.$code))
            ->order('card_number desc')
            ->limit(1, 0);

        return $medcard->queryAll();
    }

}