<?php
class MedcardRecord extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcard_records';
    }

    public static function getMaxRecIdOnGreeting($template, $greeting)
    {
        try
        {
            $connection = Yii::app()->db;
            $queryToRun = $connection->createCommand()
                ->select('
                        MAX (record_id)
                        ')
                ->from('mis.medcard_records mr')
                ->where('greeting_id=:greeting AND template_id=:template',
                    array(
                        ':greeting' => $greeting,
                        ':template' => $template
                    )
                );
                $result = $queryToRun->queryScalar();
            return $result;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function getHistoryMedcardByCardId($medcard)
    {
       // var_dump('!');
       // exit();
        // Достанем номер ОМС по медкарте
        $medcardObject = Medcard::model()->find('card_number = :number', array( ':number' => $medcard ) );
        $omsNumber = $medcardObject['policy_id'];
        //var_dump($omsNumber);
        //exit();

        $connection = Yii::app()->db;
        $points = $connection->createCommand()
            ->select('
					SUBSTR(CAST(mr.record_date AS text), 0, CHAR_LENGTH(CAST(mr.record_date AS text)) - 2) AS date_change,
					mr.greeting_id,
					mr.record_id as id_record,
					mr.medcard_id,
					mr.template_name,
					mr.template_id,
					d.first_name,
					d.last_name,
					d.middle_name
					')
            ->from('mis.medcard_records mr')
            ->join('mis.medcards as mc', 'mr.medcard_id = mc.card_number')
            ->join('mis.doctor_shedule_by_day dsbd', 'mr.greeting_id=dsbd.id')
            ->join('mis.doctors d', 'dsbd.doctor_id=d.id')
            //->where('mep.medcard_id = :medcard_id and mep.is_record=1


            //->where('mc.policy_id=:oms ',
                ->where('mc.policy_id=:oms AND ( NOT(mr.is_empty=1)  OR  mr.is_empty IS NULL)',
                array(':oms' => $omsNumber))
            ->order('greeting_id, template_id, id_record desc')

        ;

        $pointsFromBase = $points->queryAll();
        $result = array();
         // var_dump($pointsFromBase);
         // exit();
        $currentGreeting = false;
        $currentTemplate = false;
        if (count($pointsFromBase )>0)
        {
            $currentGreeting = $pointsFromBase[0]['greeting_id'];
            $currentTemplate  = $pointsFromBase[0]['template_id'];
            array_push($result,$pointsFromBase[0]);
        }

        // Дальше проверяем в цикле - если id элемента поменялся
        //    о сравнению с предыдущими строками - то нужно запихать текущую строку в результат
        foreach ($pointsFromBase as $oneRecord)
        {
            if ($oneRecord['greeting_id']!=$currentGreeting || $oneRecord['template_id']!= $currentTemplate )
            {
                $currentGreeting = $oneRecord['greeting_id'];
                $currentTemplate  = $oneRecord['template_id'];
                array_push($result,$oneRecord);
            }
        }

        // Пересортируем массив по дате

        //  var_dump($result);
        //  exit();

        //var_dump($result);
        //exit();
        usort($result, function($record1, $record2) {
            if($record1['date_change'] > $record2['date_change']) {
                return -1;
            } elseif($record1['date_change'] < $record2['date_change']) {
                return 1;
            } else {
                return 0;
            }
        });


        return $result;
    }
}

?>