<?php
class Enterprise extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.enterprise_params';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $enterprises = $connection->createCommand()
            ->select('ep.*, et.name as enterprise_type')
            ->from('mis.enterprise_params ep')
            ->join('mis.enterprise_types et', 'ep.type = et.id');

        if($filters !== false) {
            $this->getSearchConditions($enterprises, $filters, array(
                'requisits' => array(
                    'bank',
                    'bank_account',
                    'inn',
                    'kpp'
                )
            ), array(
                'ep' => array('id', 'bank', 'bank_account', 'inn', 'kpp', 'fullname', 'shortname'),
                'et' => array('enterprise_type', 'name')
            ), array(
                'enterprise_type' => 'name'
            ));
        }

        //echo $enterprises->text;
        //exit();

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $enterprises->order($sidx.' '.$sord);
            $enterprises->limit($limit, $start);
        }

        return $enterprises->queryAll();
    }


    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $enterprise = $connection->createCommand()
                ->select('ep.*')
                ->from('mis.enterprise_params ep')
                ->where('ep.id = :id', array(':id' => $id))
                ->queryRow();

            return $enterprise;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>