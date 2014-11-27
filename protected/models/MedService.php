<?php
class MedService extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medservices';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $service = $connection->createCommand()
                ->select('m.*')
                ->from(MedService::model()->tableName().' m')
                ->where('m.id = :id', array(':id' => $id))
                ->queryRow();

            return $service;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $services = $connection->createCommand()
            ->select('m.*')
            ->from(MedService::model()->tableName().' m');

        if($filters !== false) {
            $this->getSearchConditions($services, $filters, array(

            ), array(
                'm' => array('id', 'name', 'tasu_code', 'tasu_code_without_separator', 'is_default_desc')
            ), array(
				'is_default_desc' => 'is_default'
            ));
        }

        if($sidx !== false && $sord !== false ) {
			if($sidx == 'is_default_desc') {
				$sidx = 'is_default';
			}
            $services->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $services->limit($limit, $start);
        }

        return $services->queryAll();
    }
}

?>