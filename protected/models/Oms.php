<?php
class Oms extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.oms';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $oms = $connection->createCommand()
            ->select('o.*, m.card_number, m.reg_date')
            ->from('mis.oms o')
            ->leftJoin('mis.medcards m', 'o.id = m.policy_id');

        if($filters !== false) {
            $this->getSearchConditions($oms, $filters, array(
            ), array(
                'o' => array('oms_number', 'gender', 'first_name', 'middle_name', 'last_name', 'birthday'),
                'm' => array('card_number', 'address', 'address_reg', 'snils', 'docnumber', 'serie')
            ), array(
            ));
        }

        return $oms->queryAll();
    }
}

?>