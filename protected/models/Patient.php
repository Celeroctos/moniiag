<?php
class Patient
{
    private function getPatientFromShedule($filters = false, $sidx = false, $sord = false, $start = false, $limit = false)
    {
        // Используем UNION, чтобы скрестить ежа с ужом (опосредованных пациентов и обычных)
        $fromPart =
            '
                (
                    (SELECT first_name,middle_name, last_name, mediate_id AS link_id, 1 as is_mediate, null as card_number
                        FROM mis.doctor_shedule_by_day dsbd
                        JOIN mis.mediate_patients mp ON mp.id = dsbd.mediate_id)
                    UNION
                    (
                        SELECT o.first_name, o.middle_name, o.last_name, mc.policy_id, 0, mc.card_number FROM mis.doctor_shedule_by_day dsbd
                        JOIN mis.medcards mc on mc.card_number = dsbd.medcard_id
                        JOIN mis.oms o on o.id = mc.policy_id
                    )

                ) as subselect
            ';
        $connection = Yii::app()->db;
        $allPatients = $connection->createCommand()
            ->select('subselect.*')
            ->from($fromPart);
        // Ищем в фильтрах поле "fio"
        if ($filters && isset($filters['rules']))
        {
            foreach ($filters['rules'] as $oneFilter)
            {
                if ($oneFilter['field']=='fio')
                {
                    $allPatients = $allPatients->andWhere(
                        "upper(first_name) like '".mb_strtoupper($oneFilter['data'], 'utf-8').
                        "%' OR upper(last_name) like '".
                        mb_strtoupper($oneFilter['data'], 'utf-8').
                        "%' OR upper(middle_name) like '"
                        .mb_strtoupper($oneFilter['data'], 'utf-8')."%'"


                    );

                }
            }
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $allPatients->order($sidx.' '.$sord);
            $allPatients->limit($limit, $start);
        }
        $result = $allPatients->queryAll();
        return $result;
    }

    public function getNumRowsWritten($filters = false, $sidx = false, $sord = false, $start = false, $limit = false) {
        // Выбираем пациентов и считаем их количество
        return count($this->getPatientFromShedule($filters, $sidx = false, $sord = false, $start = false, $limit = false));

    }

    public function getCardNumbersByDate($dateToReport){
        $connection = Yii::app()->db;
        // Надо отфильтровать карты, год которых в номере не равен году в $dateToReport
        $result= $connection->createCommand()
            ->select('mc.*')
            ->from('mis.medcards mc')
            ->where('reg_date = :rd
            AND substring (card_number, length(card_number)-1,2 ) =  substring ( CAST (reg_date AS CHARACTER VARYING), 3,2 )
            ', array(':rd'=>$dateToReport));
        $result = $result->queryAll();
        return $result;
    }

    public function getRegistryWorkForDay($dateToReport,$sidx = false, $sord = false, $start = false, $limit = false)
    {
        $connection = Yii::app()->db;
        // Заберём сначала все карты, которые были зарегистрированы в данный день
        //   Затем у найденных карт нужно найти их предыдущие карты (вторым этапом)

        $result = $this->getCardNumbersByDate($dateToReport);

       // var_dump($result);
       // exit();

        // Вторым этапом нужно вытащить остальные данные по данной медкарте и врач, к которому пациент записан
        // Читаем из результатов запроса номера карт
        $cardNumbers = array();
        foreach ($result as $oneCard)
        {
            array_push($cardNumbers, $oneCard['card_number']);
        }

        //  Имеем массив номеров карт. Теперь нужно вытащить вторым этапом
        // Если массив cardNumbers - выходим
        if (count ($cardNumbers)==0)
            return array();

        $cardNumbersStr = '';

        foreach ($cardNumbers as $oneCardNumber)
        {
            // Если строка-накопитель не пустой - добавляем впереди запятулю
            if (($cardNumbersStr ) != '')
                $cardNumbersStr = $cardNumbersStr.',';

            $cardNumbersStr = ($cardNumbersStr . ( "'".$oneCardNumber."'" ));
        }

        $addingData = $connection->createCommand()
            // Селект внутри селекта - ему так теплее :)
            ->select('
                        mc.*,
                                (
                        SELECT card_number FROM mis.medcards mc1 WHERE
                        mc1.policy_id = mc.policy_id
                        AND
                        (
                                 CAST(SUBSTRING(mc1."card_number", (CHAR_LENGTH(mc1."card_number") - 1)) as INTEGER)
                                 <
                                 CAST(SUBSTRING(mc."card_number", (CHAR_LENGTH(mc."card_number") - 1)) as INTEGER)

                        )
                        ORDER BY CAST(SUBSTRING(mc1."card_number", (CHAR_LENGTH(mc1."card_number") - 1)) as INTEGER)
                         LIMIT 1
                    )
                    old_card_number,
                    (
                        SELECT (d.last_name || \' \' ||  substring (d.first_name,0,2)  || \' \' || substring (d.middle_name,0,2))
                        FROM mis.doctor_shedule_by_day dsbd
                        LEFT JOIN mis.doctors d ON d.id = dsbd.doctor_id
                        WHERE medcard_id = mc.card_number AND patient_day = \''.$dateToReport.'\' order by patient_time
                        LIMIT 1
                    ) fio_doctor,
                   -- mc.card_number,
                    o.last_name || \' \' ||  substring (o.first_name,0,2)  || \' \' || substring (o.middle_name,0,2) as fio,
                    registrator.last_name || \' \' ||  substring (registrator.first_name,0,2)  || \' \' || substring (registrator.middle_name,0,2) as fio_registrator,
                    concat (o.oms_series, \' \',  o.oms_number) as oms
            ')
            ->from('mis.medcards mc')
            ->join ('mis.oms o', 'o.id = mc.policy_id')
            ->leftJoin ('mis.doctors registrator', 'registrator.id = mc.user_created')
            ->where('mc.card_number in (' .  $cardNumbersStr . ")", array());

            if($sidx !== false && $sord !== false )
            {
                $addingData->order($sidx.' '.$sord);
            }

            if ($start !== false && $limit !== false)
            {
                $addingData->limit($limit, $start);
            }

            $result = $addingData -> queryAll();

            foreach ($result as &$oneResult)
            {
                $oneResult ['oms'] = trim($oneResult ['oms']);
            }
        /*
        //var_dump($addingData);
        //exit();
        // Теперь надо сшить результаты двух запросов
        // Перебираем результаты запроса
        $cardsAssociate = array();
        foreach ($addingData as $oneAddingInfo)
        {
            $cardsAssociate[$oneAddingInfo['card_number']]['old_card'] = $oneAddingInfo['old_card'];
            $cardsAssociate[$oneAddingInfo['card_number']]['doctor'] = $oneAddingInfo['fio_doctor'];
            $cardsAssociate[$oneAddingInfo['card_number']]['patient'] = $oneAddingInfo['fio'];
            $cardsAssociate[$oneAddingInfo['card_number']]['registrator'] = $oneAddingInfo['registrator_fio'];
            $cardsAssociate[$oneAddingInfo['card_number']]['oms'] = trim($oneAddingInfo['osm']);
        }

        // Перебираем результат и дописываем туды дополнительные поля
        var_dump($cardsAssociate);
        exit();

        foreach ($result as &$oneCard)
        {
            $oneCard['fio'] = $cardsAssociate[$oneCard['card_number']]['patient'];
            $oneCard['old_card_number'] = $cardsAssociate[$oneCard['card_number']]['old_card'];
            $oneCard['oms'] = $cardsAssociate[$oneCard['card_number']]['oms'];
            $oneCard['fio_registrator'] = $cardsAssociate[$oneCard['card_number']]['registrator'];
            $oneCard['fio_doctor'] = $cardsAssociate[$oneCard['card_number']]['doctor'];
        }*/
        return $result ;
    }

    public function getRowsWritten($filters = false, $sidx = false, $sord = false, $start = false, $limit = false) {
        $allPatients = $this->getPatientFromShedule($filters, $sidx, $sord, $start, $limit);

        // Теперь имея ссылку на пациентов (номер карты или номер опосредованного пациента) и ссылку на таблицу -
        //   Запрашиваем данные из разных таблиц
        $mediateIds = array();
        $directIds = array();
        foreach ($allPatients as $onePatient)
        {
            if ($onePatient['is_mediate']==1)
            {
                array_push($mediateIds,$onePatient['link_id']);
            }
            else
            {
                array_push($directIds,$onePatient['link_id']);
            }
        }

        $connection = Yii::app()->db;
        if (count($mediateIds)!=0)
        {
            $mediatePatients = $connection->createCommand()
                ->select('mp.id, mp.phone')
                ->from(MediatePatient::tableName().' mp');
            $inString = implode(',',$mediateIds);
            $mediatePatients = $mediatePatients->where('id in('.$inString.')'  );

            $mediatePatientsRows = $mediatePatients->queryAll();
            // Создаём специальный массив [ИД опосредованного] = телефон
            $assotiateMediates = array();
            foreach ($mediatePatientsRows as $oneMediatePatient)
            {
                $assotiateMediates [$oneMediatePatient['id']] = $oneMediatePatient['phone'];
            }

        }
        if (count($directIds)!=0)
        {
            $directPatients = $connection->createCommand()
                ->select('
                o.id,
                CASE WHEN COALESCE(o.oms_series,null) is null THEN oms_number
                            ELSE o.oms_series || ' .  "' '"  . ' || o.oms_number
                            END AS oms_number,
                o.birthday
                ')
                ->from(Oms::model()->tableName().' o');
            $inString = implode(',',$directIds);
            $directPatients = $directPatients->where('id in('.$inString.')'  );

            $directPatientsRows = $directPatients->queryAll();
            // Создаём специальный массив [ИД омс] = выбранные поля
            $assotiateDirects = array();
            foreach ($directPatientsRows as $oneDirectPatient)
            {
                $assotiateDirects [$oneDirectPatient['id']] = array(
                    'oms_number' => $oneDirectPatient['oms_number'],
                    'birthday' => $oneDirectPatient['birthday']
                );
            }

        }

        // Теперь имеем два массива для опосредованных и обычных пациентов
        //   Перебираем массив всiх пацiэнтов и в завiсимости от их тiпа - добавляем разные поля

        foreach($allPatients as &$onePatientResult)
        {
            //у результирующего набора и добавим перед каждым ИД признак опосредованности 0 - не опосредованный
            //    1 - опосредованный
            $onePatientResult['id'] = ($onePatientResult['is_mediate'].'_'.$onePatientResult['link_id']);

            if ($onePatientResult['is_mediate']==1)
            {
                // Опосредованный пациент
                $onePatientResult['phone'] = $assotiateMediates[$onePatientResult['link_id']];
            }
            else
            {
                // Обычный пациент
                $onePatientResult['oms_number'] = $assotiateDirects [$onePatientResult['link_id']]['oms_number'];
                $onePatientResult['birthday'] = $assotiateDirects [$onePatientResult['link_id']]['birthday'];
            }
        }

        // Просканируем id у результирующего набора и добавим перед каждым ИД признак опосредованности 0 - не опосредованный
        //    1 - опосредованный
        return $allPatients;
    }
}
?>