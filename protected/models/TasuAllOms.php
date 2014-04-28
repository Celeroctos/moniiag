<?php
class TasuAllOms extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db3;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ut.ut_PolReg_UsReg';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = $this->getDbConnection();
        $oms = $connection->createCommand()
            ->select('upu.*')
            ->from(TasuAllOms::tableName().' upu')
			->where("REPLACE([upu].[ENP], CHAR(32), '') != ''");

        if($filters !== false) {
            $this->getSearchConditions($oms, $filters, array(
            ), array(
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false ) {
            $oms->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $oms->limit($limit, $start);
        }

        return $oms->queryAll();
    }
	
	public function getNumRows() {
        $connection = $this->getDbConnection();
        $numoms = $connection->createCommand()
            ->select('COUNT(*) as num')
            ->from(TasuAllOms::tableName().' upu')
			->where("REPLACE([upu].[ENP], CHAR(32), '') != ''")
            ->queryRow();
        return $numoms['num'];
    }
}

?>