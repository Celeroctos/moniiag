<?php
class Doctype extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.doctypes';
    }

/*
    public function primaryKey()
    {
        return 'id';
        // Для составного первичного ключа следует использовать массив:
        // return array('pk1', 'pk2');
    }
*/
    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $doctypes = $connection->createCommand()
            ->select('dt.*')
            ->from('mis.doctypes dt');

        if($filters !== false) {
            $this->getSearchConditions($doctypes, $filters, array(
            ), array(
                'dt' => array('id', 'name')
            ), array(

            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $doctypes->order($sidx.' '.$sord);
            $doctypes->limit($limit, $start);
        }

        return $doctypes->queryAll();

    }

    public static function getForSelect ()
    {
        $docTypeObject = new Doctype();
        $documentTypes = $docTypeObject->getRows(false);
        $result = array();
        foreach($documentTypes as $oneType)
        {
            $result[$oneType['id']] = $oneType['name'];
        }
        return $result;

    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $doctype = $connection->createCommand()
                ->select('dt.*')
                ->from('mis.doctypes dt')
                ->where('dt.id = :id', array(':id' => $id))
                ->queryRow();

            return $doctype;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    /*public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $insurance = $connection->createCommand()
                ->select('ins.*')
                ->from('mis.insurances ins')
                ->where('ins.id = :id', array(':id' => $id))
                ->queryRow();

            return $insurance ;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }*/
}

?>