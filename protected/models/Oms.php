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

    public function getRows($filters, $sidx = false, $sord = false, $start = false,
                            $limit = false, $onlyWithCards=false, $onlyWithoutCards=false) {
        $connection = Yii::app()->db;
        $oms = $connection->createCommand()
            ->select('o.*, m.card_number, m.reg_date')
            ->from('mis.oms o')
            ->leftJoin('mis.medcards m', 'o.id = m.policy_id');

        if($filters !== false) {
            $this->getSearchConditions($oms, $filters, array(
                'fio' => array(
                    'first_name',
                    'last_name',
                    'middle_name'
                )
            ), array(
                'o' => array('oms_number', 'gender', 'first_name', 'middle_name', 'last_name', 'birthday', 'fio'),
                'm' => array('card_number', 'address', 'address_reg', 'snils', 'docnumber', 'serie')
            ), array(

            ));
        }

        // Если только без карт - ставим условие WHERE card_number==null
        if ($onlyWithoutCards)
        {
            $oms->andWhere("coalesce(m.card_number,'')=''");
        }
        // Если только без карт - ставим условие WHERE card_number!=null
        if ($onlyWithCards)
        {
            $oms->andWhere("coalesce(m.card_number,'')!=''");
        }
        
        if ($sidx && $sord && $limit)
        {

            $oms->order($sidx.' '.$sord);
            $oms->limit($limit, $start);    
        }
        
        return $oms->queryAll();
    }

    public function getDistinctRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $oms = $connection->createCommand()
            ->select('o.*')
            ->from('mis.oms o');

        if($filters !== false) {
            $this->getSearchConditions($oms, $filters, array(
                'fio' => array(
                    'first_name',
                    'last_name',
                    'middle_name'
                )
            ), array(
                'o' => array('oms_number', 'gender', 'first_name', 'middle_name', 'last_name', 'birthday', 'fio')
            ), array(

            ));
        }

        return $oms->queryAll();
    }
}

?>