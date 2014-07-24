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

       /* if($filters !== false) {
            $MAR = new MisActiveRecord();
            $MAR->getSearchConditions($allPatients, $filters, array(
                'fio' => array(
                    'first_name',
                    'last_name',
                    'middle_name'
                )
            ), array(
            ), array(

            ));
        }
*/

        // Ищем в фильтрах поле "fio"
        if ($filters && isset($filters['rules']))
        {
            foreach ($filters['rules'] as $oneFilter)
            {
                if ($oneFilter['field']=='fio')
                {
                    //var_dump($oneFilter['data']);
                    //exit();
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

        //var_dump($allPatients);
        //exit();

        $result = $allPatients->queryAll();
        //var_dump($result);
        //exit();
        return $result;
    }

    public function getNumRowsWritten($filters = false, $sidx = false, $sord = false, $start = false, $limit = false) {
        // Выбираем пациентов и считаем их количество
        return count($this->getPatientFromShedule($filters, $sidx = false, $sord = false, $start = false, $limit = false));

    }

    public function getRowsWritten($filters = false, $sidx = false, $sord = false, $start = false, $limit = false) {
        $allPatients = $this->getPatientFromShedule($filters, $sidx = false, $sord = false, $start = false, $limit = false);

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

        //$directPatients =

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
                    //$oneMediatePatient['phone'];
            }

        }

        // Теперь имеем два массива для опосредованных и обычных пациентов
        //   Перебираем массив всiх пацiэнтов и в завiсимости от их тiпа - добавляем разные поля

        foreach($allPatients as &$onePatientResult)
        {
            //у результирующего набора и добавим перед каждым ИД признак опосредованности 0 - не опосредованный
            //    1 - опосредованный

            //var_dump($onePatientResult);
            //exit();
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


        //return array();
        return $allPatients;
    }
}
?>