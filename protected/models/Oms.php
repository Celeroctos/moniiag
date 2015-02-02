<?php
/**
 * Модель AR для работы с полисом
 */
class Oms extends MisActiveRecord 
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.oms';
    }
	
	public function rules()
	{
		return [
			['id', 'type', 'type'=>'integer', 'on'=>'reception.search'],
		];
	}
	
	/**
	 * Метод для поиска в CGridView
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		return new CActiveDataProvider($this, [
			'pagination'=>['pageSize'=>20],
			'criteria'=>$criteria,
		]);
	}
	
    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false, $onlyWithCards=false, $onlyWithoutCards=false, $onlyInGreetings = false,$cancelledGreetings=false, $onlyClosedGreetings = false, $greetingDate = false) {

        $result = array();

        $connection = Yii::app()->db;
        $oms = $connection->createCommand()
            ->selectDistinct('o.id, o.oms_number')
            ->from('mis.oms o')
            ->leftJoin('mis.medcards m', 'o.id = m.policy_id');

        if($onlyInGreetings || $onlyClosedGreetings || $greetingDate) {
            $oms->join(SheduleByDay::model()->tableName().' dsbd', 'm.card_number = dsbd.medcard_id');
        }

        if ($cancelledGreetings)
        {

            $oms->join(CancelledGreeting::model()->tableName().' cg', 'm.card_number = cg.medcard_id');
            $oms->andWhere('cg.deleted = 0 AND cg.patient_day<current_date'  );
        }
		
		if($onlyClosedGreetings) { 
			$oms->andWhere('dsbd.time_end IS NOT NULL');
		}

        if($filters !== false) {
            $this->getSearchConditions($oms, $filters, array(
                'fio' => array(
                    'first_name',
                    'last_name',
                    'middle_name'
                )
            ), array(
                'o' => array('oms_number', 'gender', 'first_name', 'middle_name', 'last_name', 'birthday', 'fio', 'normalized_oms_number', 'e_oms_number', 'k_oms_number', 'a_oms_number', 'b_oms_number', 'c_oms_number'),
                'm' => array('card_number', 'address', 'address_reg', 'snils', 'docnumber', 'serie', 'address_reg_str', 'address_str'),
				'dsbd' => array('doctor_id', 'patient_day')
            ), array(
                'e_oms_number' => 'oms_number',
                'k_oms_number' => 'oms_number',
                'normalized_oms_number' => 'oms_series_number'
            ), array(
                'OR' => array(
                    'e_oms_number',
                    'k_oms_number',
                    'oms_number',
                    'normalized_oms_number'
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

        //var_dump($oms);
        //exit();
        $omsPolices = $oms->queryAll();

        // ������ �������. ������ �� ������ ������ ���� ��������� �� ���������

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


            // ��������� ������, ������� ���� ID-����� ��� � ��� ������� id-�����
            //    1. ���� ����� ����� ��������, ��� ��������� ����� � �������� ���������� ��� ������� ������
            //    2. ���� ������������ ���� ����������� ����, ����������� � ������� ���
            $connection = Yii::app()->db;
            //var_dump($inIds);
           // exit();
            $oms2 = $connection->createCommand()
                ->select('o.*,
                            CASE WHEN COALESCE(o.oms_series,null) is null THEN oms_number
                            ELSE o.oms_series || ' .  "' '"  . ' || o.oms_number
                            END AS oms_number,
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
                ->where('id in ('.$inIds.')',
                    array(
                    ));

            $omsByIds = $oms2->queryAll();

            // ������ ����� oms-� �� $oms2 �������� � ���������
            //   ���������� id-����� � ��������� �������, ������� id-����� � ������������� ���-�
            //    � �������������� ����������
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

    public static function findOmsByNumbers($number1,$number2,$number3,$numberNorm,$id = false, $fioData)
    {
        try
        {
            $connection = Yii::app()->db;
            $oms = $connection->createCommand()
                ->select('o.*')
                ->from('mis.oms o')
                ->where('(oms_number = :oms_number1 OR
                    oms_number = :oms_number2 OR
                    oms_number = :oms_number3 OR
                    oms_series_number = :oms_norm_number)',
                array(
                    ':oms_number1' => $number1,
                    ':oms_number2' => $number2,
                    ':oms_number3' => $number3,
                    ':oms_norm_number' => $numberNorm)
            );
			
			if(isset($fioData['firstName'], $fioData['lastName'], $fioData['birthday'])) {
				$oms->andWhere('first_name = :first_name
					AND middle_name = :middle_name
					AND last_name = :last_name
					AND birthday  = :birthday',
				array(
					':first_name' => $fioData['firstName'],
					':last_name' => $fioData['lastName'],
					':middle_name' => $fioData['middleName'],
					':birthday' => $fioData['birthday']
				));
			}

            // Если ид не равно false - то прихреначиваем ещё одно условие к where
            if ($id!=false)
            {
                $oms->andWhere('id != :policy_id', array(':policy_id'=>$id));
            }

            $result = $oms->queryRow();
/*var_dump($number1); 
var_dump($number2);
var_dump($number3);
var_dump($numberNorm);
var_dump($oms->text);
exit();*/
            return $result;
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getNumRows($filters, $sidx = false, $sord = false, $start = false, $limit = false, $onlyWithCards=false,
                               $onlyWithoutCards=false, $onlyInGreetings = false,$cancelledGreetings = false,
                               $onlyClosedGreetings = false, $greetingDate = false) {

        try
        {
            $connection = Yii::app()->db;
            $oms = $connection->createCommand()
                ->select('COUNT(DISTINCT o.id) as num')
                ->from('mis.oms o')
                ->leftJoin('mis.medcards m', 'o.id = m.policy_id');

            if($onlyInGreetings || $onlyClosedGreetings || $greetingDate) {
                $oms->join(SheduleByDay::model()->tableName().' dsbd', 'm.card_number = dsbd.medcard_id');
            }

            if ($cancelledGreetings)
            {

                $oms->join(CancelledGreeting::model()->tableName().' cg', 'm.card_number = cg.medcard_id');
                $oms->andWhere('cg.deleted = 0 AND cg.patient_day<current_date'  );
            }
			
			if($onlyClosedGreetings) { 
				$oms->andWhere('dsbd.time_end IS NOT NULL');
			}

            if($filters !== false) {
                $this->getSearchConditions($oms, $filters, array(
                    'fio' => array(
                        'first_name',
                        'last_name',
                        'middle_name'
                    )
                ), array(
                    'o' => array('oms_number', 'gender', 'first_name', 'middle_name', 'last_name', 'birthday', 'fio', 'normalized_oms_number' ,'e_oms_number', 'k_oms_number', 'a_oms_number', 'b_oms_number', 'c_oms_number'),
                    'm' => array('card_number', 'address', 'address_reg', 'snils', 'docnumber', 'serie', 'address_reg_str', 'address_str'),
					'dsbd' => array('doctor_id', 'patient_day')
                ), array(
                    'e_oms_number' => 'oms_number',
                    'k_oms_number' => 'oms_number',
                    'normalized_oms_number' => 'oms_series_number'
                ), array(
					'OR' => array(
                        'e_oms_number',
                        'k_oms_number',
                        'oms_number',
                        'normalized_oms_number'
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

          //  var_dump($oms->text);
          // exit();

            $result = $oms->queryRow();

            return $result;
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
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