<?php
class Contact extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.contacts';
    }

    public function getAll() {
        try {
            $connection = Yii::app()->db;
            $contacts = $connection->createCommand()
                ->select('c.*')
                ->from('mis.contacts c')
                ->queryAll();

            return $contacts;

        } catch(Exception $e) {
            echo $e->getMessage();
        }

    }

    public function getAllWithoutDoctor() {
        try {
            $connection = Yii::app()->db;
            $contacts = $connection->createCommand()
                ->select('c.*')
                ->from('mis.contacts c')
                ->where('NOT EXISTS(SELECT * FROM mis.doctors d WHERE d.contact_code = c.id)')
                ->queryAll();

            return $contacts;

        } catch(Exception $e) {
            echo $e->getMessage();
        }

    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $contacts = $connection->createCommand()
            ->select('c.*, d.first_name, d.middle_name, d.last_name')
            ->from('mis.contacts c')
            ->leftJoin('mis.doctors d', 'd.contact_code = c.id');

        if($filters !== false) {
            $this->getSearchConditions($contacts, $filters, array(
                'fio' => array(
                    'first_name',
                    'last_name',
                    'middle_name',
                )
            ), array(
                'c' => array('id', 'type', 'contact_value'),
                'd' => array('fio', 'first_name', 'last_name', 'middle_name')
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $contacts->order($sidx.' '.$sord);
            $contacts->limit($limit, $start);
        }

        //echo $contacts->text;

        return  $contacts->queryAll();

    }


    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $contact = $connection->createCommand()
                ->select('c.*')
                ->from('mis.contacts c')
                ->where('c.id = :id', array(':id' => $id))
                ->queryRow();

            return $contact;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

}