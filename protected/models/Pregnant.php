<?php
class Pregnant extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.pregnants';
    }

    public function getRows($filters) {
        $connection = Yii::app()->db;
        $pregnant = $connection->createCommand()
            ->select('p.*, CONCAT(o.last_name, \' \', o.first_name, \' \', o.middle_name ) as fio')
            ->from('mis.pregnants p')
            ->leftJoin('mis.medcards m', 'p.card_id = m.card_number')
            ->leftJoin('mis.doctors d', 'd.id = p.doctor_id')
            ->leftJoin('mis.users u', 'd.user_id = u.id')
            ->leftJoin('mis.oms o', 'o.id = m.policy_id');

        if($filters !== false) {
            $this->getSearchConditions($pregnant, $filters, array(
            ), array(
                'p' => array('doctor_id'),
                'm' => array(),
                'u' => array('userid')
            ), array(
                'userid' => 'id'
            ));
        }

        return $pregnant->queryAll();
    }
}

?>