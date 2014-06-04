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

    // Старый код. Сделано для бек-апа перед изменениями - вдруг ничего не получится
    /*
    public function getRows($filters, $sidx = false, $sord = false, $start = false,
                            $limit = false, $onlyWithCards=false, $onlyWithoutCards=false, $onlyInGreetings = false) {
        $connection = Yii::app()->db;
        $oms = $connection->createCommand()
            ->selectDistinct('o.*, m.card_number, m.reg_date')
            ->from('mis.oms o')
            ->leftJoin('mis.medcards m', 'o.id = m.policy_id');

        if($onlyInGreetings) {
            $oms->join(SheduleByDay::model()->tableName().' dsbd', 'm.card_number = dsbd.medcard_id');
        }

        if($filters !== false) {
            $this->getSearchConditions($oms, $filters, array(
                'fio' => array(
                    'first_name',
                    'last_name',
                    'middle_name'
                )
            ), array(
                'o' => array('oms_number', 'gender', 'first_name', 'middle_name', 'last_name', 'birthday', 'fio', 'e_oms_number', 'k_oms_number', 'a_oms_number', 'b_oms_number', 'c_oms_number'),
                'm' => array('card_number', 'address', 'address_reg', 'snils', 'docnumber', 'serie', 'address_reg_str', 'address_str')
            ), array(
                'e_oms_number' => 'oms_number',
                'k_oms_number' => 'oms_number'
            ), array(
                'OR' => array(
                    'e_oms_number',
                    'k_oms_number',
                    'oms_number'
                )
            ));
        }

        // WHERE card_number==null
        if ($onlyWithoutCards)
        {
            $oms->andWhere("coalesce(m.card_number,'')=''");
        }
        // WHERE card_number!=null
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
	
	public function getNumRows($filters, $sidx = false, $sord = false, $start = false,
                            $limit = false, $onlyWithCards=false, $onlyWithoutCards=false, $onlyInGreetings = false) {

    	$connection = Yii::app()->db;
        $oms = $connection->createCommand()
            ->select('COUNT(*) as num')
            ->from('mis.oms o')
            ->leftJoin('mis.medcards m', 'o.id = m.policy_id');

        if($onlyInGreetings) {
            $oms->join(SheduleByDay::model()->tableName().' dsbd', 'm.card_number = dsbd.medcard_id');
        }

        if($filters !== false) {
            $this->getSearchConditions($oms, $filters, array(
                'fio' => array(
                    'first_name',
                    'last_name',
                    'middle_name'
                )
            ), array(
                'o' => array('oms_number', 'gender', 'first_name', 'middle_name', 'last_name', 'birthday', 'fio', 'e_oms_number', 'k_oms_number', 'a_oms_number', 'b_oms_number', 'c_oms_number'),
                'm' => array('card_number', 'address', 'address_reg', 'snils', 'docnumber', 'serie', 'address_reg_str', 'address_str')
            ), array(
                'e_oms_number' => 'oms_number',
                'k_oms_number' => 'oms_number'
            ), array(
                'OR' => array(
                    'e_oms_number',
                    'k_oms_number',
                    'oms_number'
                )
            ));
        }

        // WHERE card_number==null
        if ($onlyWithoutCards)
        {
            $oms->andWhere("coalesce(m.card_number,'')=''");
        }
        // WHERE card_number!=null
        if ($onlyWithCards)
        {
            $oms->andWhere("coalesce(m.card_number,'')!=''");
        }

        $result = $oms->queryRow();

        //var_dump($result);
        //exit();
        return $result;
	}
    */

    public function getRows($filters, $sidx = false, $sord = false, $start = false,
                            $limit = false, $onlyWithCards=false, $onlyWithoutCards=false, $onlyInGreetings = false) {

        $result = array();

        $connection = Yii::app()->db;
        $oms = $connection->createCommand()
            ->selectDistinct('o.id, o.oms_number')
            ->from('mis.oms o')
            ->leftJoin('mis.medcards m', 'o.id = m.policy_id');

        if($onlyInGreetings) {
            $oms->join(SheduleByDay::model()->tableName().' dsbd', 'm.card_number = dsbd.medcard_id');
        }

        if($filters !== false) {
            $this->getSearchConditions($oms, $filters, array(
                'fio' => array(
                    'first_name',
                    'last_name',
                    'middle_name'
                )
            ), array(
                'o' => array('oms_number', 'gender', 'first_name', 'middle_name', 'last_name', 'birthday', 'fio', 'e_oms_number', 'k_oms_number', 'a_oms_number', 'b_oms_number', 'c_oms_number'),
                'm' => array('card_number', 'address', 'address_reg', 'snils', 'docnumber', 'serie', 'address_reg_str', 'address_str')
            ), array(
                'e_oms_number' => 'oms_number',
                'k_oms_number' => 'oms_number'
            ), array(
                'OR' => array(
                    'e_oms_number',
                    'k_oms_number',
                    'oms_number'
                )
            ));
        }

        // WHERE card_number==null
        if ($onlyWithoutCards)
        {
            $oms->andWhere("coalesce(m.card_number,'')=''");
        }
        // WHERE card_number!=null
        if ($onlyWithCards)
        {
            $oms->andWhere("coalesce(m.card_number,'')!=''");
        }

        if ($sidx && $sord && $limit)
        {

            $oms->order($sidx.' '.$sord);
            $oms->limit($limit, $start);
        }

        $omsPolices = $oms->queryAll();

        // Полисы выбраны. Теперь по номеру полиса надо довыбрать всё остальное

        $policeIds = array();
        $policeOrders = array();
        $policeOrdersIndex = 0;
        foreach ($omsPolices as $onePoliceId)
        {

            array_push($policeIds,$onePoliceId['id']);

            $policeOrders[$policeOrdersIndex] = $onePoliceId['id'];
            $policeOrdersIndex++;
        }

        if (count($omsPolices)>0)
        {
            $inIds = implode(",",$policeIds);


            // Выполняем запрос, который берёт ID-шники ОМС и для каждого id-шника
            //    1. ищет такой номер медкарты, две последние цифры у которого максималны для данного полиса
            //    2. ищет максимальную дату регистрацию карт, привязанных к данному ОМС
            $connection = Yii::app()->db;
            $oms2 = $connection->createCommand()
                ->select('o.*,
                            (
                                    SELECT
                                    m.card_number
                                    FROM mis.medcards m
                                    WHERE	(
                                            substring(m.card_number, (char_length(m.card_number)-2) , (char_length(m.card_number))  )
                                            =
                                            (
                                                SELECT
                                                MAX
                                                (
                                                    substring(m2.card_number , char_length(m2.card_number)-2 , char_length(m2.card_number)  )
                                                )
                                                FROM mis.medcards m2
                                                WHERE ( m2.policy_id=o.id  )

                                            )
                                        )
                                        AND
                                        (
                                            m.policy_id = o.id

                                        )
                                 ) as card_number,
                                 (
                                    SELECT MAX(m3.reg_date)
                                    FROM mis.medcards m3
                                    WHERE m3.policy_id = o.id
                                 ) as reg_date

                ')

                ->from('mis.oms o')
                ->where('id in (:policy_ids)',
                    array(
                        ':policy_ids'=>$inIds
                    ));

            $omsByIds = $oms2->queryAll();

            // Теперь нужно oms-ы из $oms2 засунуть в результат
            //   Перебираем id-шники и результат запроса, сверяем id-шники и перебрасываем омс-ы
            //    в результирующую переменную
            foreach ($policeIds as $onePoliceId)
            {
                foreach($omsByIds as $oneOms2)
                {
                    //var_dump($onePoliceId);
                    //var_dump($oneOms2['id']);
                    //    exit();
                    if ($onePoliceId==$oneOms2['id'])
                    {
                        array_push($result,$oneOms2);
                    }
                }
            }

        }
        //var_dump($result);
        //exit();
        return $result;
    }

    public function getNumRows($filters, $sidx = false, $sord = false, $start = false,
                               $limit = false, $onlyWithCards=false, $onlyWithoutCards=false, $onlyInGreetings = false) {

        $connection = Yii::app()->db;
        $oms = $connection->createCommand()
            ->select('COUNT(DISTINCT o.id) as num')
            ->from('mis.oms o')
            ->leftJoin('mis.medcards m', 'o.id = m.policy_id');

        if($onlyInGreetings) {
            $oms->join(SheduleByDay::model()->tableName().' dsbd', 'm.card_number = dsbd.medcard_id');
        }

        if($filters !== false) {
            $this->getSearchConditions($oms, $filters, array(
                'fio' => array(
                    'first_name',
                    'last_name',
                    'middle_name'
                )
            ), array(
                'o' => array('oms_number', 'gender', 'first_name', 'middle_name', 'last_name', 'birthday', 'fio', 'e_oms_number', 'k_oms_number', 'a_oms_number', 'b_oms_number', 'c_oms_number'),
                'm' => array('card_number', 'address', 'address_reg', 'snils', 'docnumber', 'serie', 'address_reg_str', 'address_str')
            ), array(
                'e_oms_number' => 'oms_number',
                'k_oms_number' => 'oms_number'
            ), array(
                'OR' => array(
                    'e_oms_number',
                    'k_oms_number',
                    'oms_number'
                )
            ));
        }

        // WHERE card_number==null
        if ($onlyWithoutCards)
        {
            $oms->andWhere("coalesce(m.card_number,'')=''");
        }
        // WHERE card_number!=null
        if ($onlyWithCards)
        {
            $oms->andWhere("coalesce(m.card_number,'')!=''");
        }

        $result = $oms->queryRow();

        //var_dump($result);
        //exit();
        return $result;
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