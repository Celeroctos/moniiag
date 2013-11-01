<?php
class Contact extends CActiveRecord {
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