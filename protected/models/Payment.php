<?php
class Payment extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.payment_types';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $payment = $connection->createCommand()
                ->select('p.*')
                ->from(Payment::model()->tableName().' p')
                ->where('p.id = :id', array(':id' => $id))
                ->queryRow();

            return $payment;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $payments = $connection->createCommand()
            ->select('p.*')
            ->from('mis.payment_types p');

        if($filters !== false) {
            $this->getSearchConditions($payments, $filters, array(
            ), array(
               'p' => array('id', 'name', 'tasu_string'),
            ));
        }

        if($sidx !== false && $sord !== false) {
            $payments->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $payments->limit($limit, $start);
        }

        return $payments->queryAll();
    } 
}

?>