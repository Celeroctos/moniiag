<?php
/**
 * Модель AR для работы с полисом
 */
class Oms extends MisActiveRecord 
{
	public $id;
	public $oms_number;
	public $first_name;
	public $middle_name;
	public $last_name;
	public $gender;
	public $birthday;
	public $type;
	public $givedate;
	public $enddate;
	public $status;
	public $tasu_id;
	public $insurance;
	public $region;
	public $oms_series;
	public $oms_series_number;
	public $lastMedcard; //используется в методе, не ялвяется атрибутом таблицы
	
	/*Атрибуты таблицы Medcards. Used in CGridView*/
	public $card_number;
	public $serie;
	public $docnumber;
	public $address_reg;
	public $address;
	public $snils;

    public $primaryKey = 'id';
	
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	
    public function tableName()
    {
        return 'mis.oms';
    }

    public function primaryKey() {
        return 'id';
    }

    public function beforeSave() {
        parent::beforeSave();
        return true;
    }

    public function afterSave() {
        parent::afterSave();
        return true;
    }

    public function rules()
	{
		return [
			['first_name, oms_number, middle_name, last_name, card_number, serie, docnumber, address_reg, address, snils', 'type', 'type'=>'string', 'on'=>'reception.search'],
			['birthday', 'type', 'type'=>'string', 'on'=>'reception.search'], //TODO type date
		];
	}

	public function relations()
	{
		return [
			'medcards'=>[self::HAS_MANY, 'Medcard', 'policy_id'], //oms
		];
	}

	/**
	 * Извлечение последней медкарты пациента
	 * Используется в CGridView (eval())
	 */
	public function getLastMedcard($policy_id)
	{
		$criteria=new CDbCriteria;
		$criteria->condition='policy_id=:policy_id';
		$criteria->params=[':policy_id'=>$policy_id];
		$criteria->order='card_number DESC';
		$record=Medcard::model()->find($criteria); //берем последнюю медкарту
		return isset($record->card_number) ? $record->card_number : 'Отсутствует';
	}
	
	/**
	 * Метод для поиска в CGridView
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->with=['medcards'=>['together'=>true, 'joinType'=>'LEFT JOIN']];

		if($this->validate() && !empty($this->oms_number) || !empty($this->snils) || !empty($this->card_number) //1.
	   || (!empty($this->last_name) && !empty($this->first_name)) //2.
	   || (!empty($this->serie) && !empty($this->docnumber)) //серия + номер
	   || (!empty($this->last_name) && !empty($this->birthday)) //фамилия + дата
		) //расписываем все возможные сценарии.
		{
			$criteria->compare('oms_number', $this->oms_number, false);
			$criteria->compare('medcards.card_number', $this->card_number, false);
			$criteria->compare('medcards.snils', $this->snils, false);
			$criteria->compare('last_name', $this->last_name, false);
			$criteria->compare('first_name', $this->first_name, false);
			$criteria->compare('middle_name', $this->middle_name, false);
			$criteria->compare('medcards.serie', $this->serie, false);
			$criteria->compare('medcards.docnumber', $this->docnumber, false);
			$criteria->compare('last_name', $this->last_name, false);
			$criteria->compare('birthday', $this->birthday, false);
		}
		else //случай, когда искать ничего не нужно
		{
			$criteria->addCondition('id=-1'); //не сущ. условие, пустая таблица
		}
			
		return new CActiveDataProvider($this, [
			'pagination'=>['pageSize'=>15],
			'criteria'=>$criteria,
			'sort'=>[
					'attributes'=>[
						'oms_number', 
						'last_name', 
						'first_name', 
						'middle_name', 
						'birthday',
						'medcards.card_number'=>[
							'asc'=>'medcards.card_number',
							'desc'=>'medcards.card_number DESC',
						],
						'medcards.serie'=>[
							'asc'=>'medcards.serie',
							'desc'=>'medcards.serie DESC',
						],
						'medcards.docnumber'=>[
							'asc'=>'medcards.docnumber',
							'desc'=>'medcards.docnumber DESC',
						],
						'medcards.address'=>[
							'asc'=>'medcards.address',
							'desc'=>'medcards.address DESC',
						],
						'medcards.address_reg'=>[
							'asc'=>'medcards.address_reg',
							'desc'=>'medcards.address_reg DESC',
						],
						'medcards.snils'=>[
							'asc'=>'medcards.snils',
							'desc'=>'medcards.snils DESC',
						],
					],
					'defaultOrder'=>[
						'id'=>CSort::SORT_DESC,
					],
			],
		]);
	}
	
	/**
	 * Labels for forms
	 */
	public function attributeLabels()
	{
		return [
			'oms_number'=>'№ Полиса',
			'last_name'=>'Фамилия',
			'first_name'=>'Имя',
			'middle_name'=>'Отчество',
			'birthday'=>'День рождения',
			'card_number'=>'№ Медкарты',
			'serie'=>'Серия',
			'docnumber'=>'Номер',
			'address_reg'=>'Адрес регистрации',
			'address'=>'Адрес фактического проживания',
			'snils'=>'СНИЛС',
		];
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