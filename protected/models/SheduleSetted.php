<?php
class SheduleSetted extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.doctor_shedule_setted';
    }

    public function getOne($id) {
        try {


        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    // Получить все смены для сотрудника
    public static function getAllForEmployer ($employerId,$dateBegin=false,$dateEnd=false)
    {
        $connection = Yii::app()->db;
        try {
           /* $sheduleDays= $connection->createCommand()
                ->select("*")
                ->from(SheduleSetted::model()->tableName().' dss')
                ->leftJoin('mis.doctor_shedule_setted_be dssb', 'dss.date_id=dssb.id')
                ->where(

                    'dss.employee_id = :employee_id',
                    array(
                        ':employee_id' => $employerId
                    )

                );
            return $sheduleDays->queryAll();
           */



        } catch(Exception $e) {
            echo $e->getMessage();
            exit();
        }
    }

    public function getRows() {

    }

    public function getByEnterprise($id) {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

	public static function getMode($doctorId, $weekDay,$formatDate)
	{
		$connection = Yii::app()->db;
		try {
			$sheduleDays= $connection->createCommand()
				->select("*")
				->from(SheduleSetted::model()->tableName().' dss')
				->join('mis.doctor_shedule_setted_be dssb', 'dss.date_id=dssb.id')
				->where(
		
					'dss.employee_id = :employee_id
					AND (weekday = :weekday OR day = :day)
					AND (:day >= dssb.date_begin) AND (:day <= dssb.date_end)
					
					',
					array(
						':employee_id' => $doctorId,
						':weekday' => $weekDay,
						':day' => $formatDate
						)		
			
		);
			return $sheduleDays->queryAll();
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

    // Получить всех id врачей, которые могут принмать по этой дате
    public function getAllPerDate($date) {
        $weekday = date('w', strtotime($date));
        $connection = Yii::app()->db;
        try {
            $doctors = $connection->createCommand()
                ->selectDistinct('ss.employee_id')
                ->from(SheduleSetted::model()->tableName().' ss')
                ->where('weekday = :weekday AND NOT EXISTS(SELECT ss2.* FROM '.SheduleSetted::model()->tableName().' ss2 WHERE weekday IS NULL AND day = :date)', array(':weekday' => $weekday, ':date' => $date));
            return $doctors->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    // Это старый, ненужный код. Потом выбросить я думаю
    /*
    // Получаем рабочий ли день $dayDate для врача $doctorId
    public static function getDoctorRegimeByDay($dayDate, $doctorId)
    {

            $shedule = SheduleSetted::getAllForEmployer($doctorId);
            $result = array();

            // Количество дней в месяце


            // Здесь составляем карту расписания на каждый день: разбираем на общее расписание и исключения
            $usual = array();
            $usualData = array();
            $exps = array();
            $expsData = array();

            //var_dump($shedule);
            //exit();
            foreach($shedule as $key => $element) {
                // Обычное расписание
                if($element['type'] == 0) {
                    array_push($usual, $element['weekday']);
                    array_push($usualData, $element);
                }
                // Исключения
                if($element['type'] == 1) {
                    array_push($exps, $element['day']);
                    array_push($expsData, $element);
                }
            }
            // Теперь вынем стабильное расписание выходных
            $restDays = SheduleRest::model()->findAll();
            $restDaysArr = array();
            foreach($restDays as $restDay) {
                $restDaysArr[] = $restDay->day;
            }
            $restDaysLonely = SheduleRestDay::model()->findAll('doctor_id = :doctor',
                array(
                    //   ':date' => $paramDate,
                    ':doctor' => $doctorId
                ));
            // var_dump($restDaysLonely);

            $restDaysArrLonely = array();
            foreach($restDaysLonely as $dayLonely) {
                // var_dump( substr($dayLonely->date,0,10));
                // exit();
                // $parts = explode('-', $dayLonely->date);
                $restDaysArrLonely[] = substr($dayLonely->date,0,10);
            }
            $weekday = date('w', strtotime($dayDate));
            $expsIndex = array_search($dayDate, $exps);
            $usualIndex = SheduleSetted::getIndexWorkingDay($usualData,  $weekday,$dayDate);
            if(($usualIndex !== false && array_search($weekday, $restDaysArr) === false && array_search($dayDate, $restDaysArrLonely) === false) || $expsIndex !== false) {
                // День существует, врач работает
                $result['worked'] = true;
                $result['restDay'] = false;

                // Начало и конец смены
                if($expsIndex !== false) {
                    $result['beginTime'] = $expsData[$expsIndex]['time_begin'];
                    $result['endTime'] = $expsData[$expsIndex]['time_end'];
                }

                if($usualIndex !== false) {
                    $result['beginTime'] = $usualData[$usualIndex]['time_begin'];
                    $result['endTime'] = $usualData[$usualIndex]['time_end'];
                }

            } else {
                    // Если это выходной, его тоже нужно помечать
                    // состыкуем дату


                    //if(array_search($weekday, $restDaysArr) !== false || array_search($i, $restDaysArrLonely) !== false) {
                    //var_dump($formatDate);
                    //var_dump($restDaysArrLonely);
                    if(array_search($weekday, $restDaysArr) !== false || array_search($dayDate, $restDaysArrLonely) !== false) {
                        $result['restDay'] = true;
                    } else {
                        $result['restDay'] = false;
                    }
                    $result['worked'] = false;

            }
                //$resultArr[(string)$i - 1]['day'] = $i;

            return  $result;
    }*/

    // Функция, которая прочёсывает массив рабочих дней в каждую из смен для одного врача и определяет
    //      индекс строки с расписанием для конекретной даты, конкретного дня недели для конкретного врача
    public static function getIndexWorkingDay($workingDaysArray, $weekDay,$workingDate)
    {
        $result = false;

        // Пробегаемся по массиву
        for ($i=0;$i<count($workingDaysArray);$i++)
        {

            // 1. Если рабочий день из атрибута не равен рабочему дню из поданых параметров - то следующая
            //    итерация
            if ($workingDaysArray[$i]['weekday']!=$weekDay )
            {
                continue;
            }

            // 2. Если weekday таки равен - надо проверить на попадение даты в интервал beginDate и endDate
            $currentDate = strtotime($workingDate);
            $beginDate = strtotime($workingDaysArray[$i]['date_begin']);
            $endDate = strtotime($workingDaysArray[$i]['date_end']);


            // Если текущая дата попадает в промежуток между begin и date - то мы нашли искомый индекс
            if ( ($currentDate >= $beginDate) && ($currentDate <= $endDate) )
            {
                $result = $i;
                // Теоретически тут конечно можно поставить break - ибо мы нашли индекс.
                //   Но острой необходимости в этом нет
            }
        }

        return $result;
    }

    // Получить всех id врачей, которые могут принмать по этой дате + на неделю вперёд
    public function getAllPerDates($date) {
        $connection = Yii::app()->db;
        try {
            $doctors = $connection->createCommand()
                ->selectDistinct('ss.employee_id')
                ->from(SheduleSetted::model()->tableName().' ss')
                ->where('NOT EXISTS(SELECT ss2.* FROM '.SheduleSetted::model()->tableName().' ss2 WHERE weekday IS NULL AND day = :date)', array(':date' => $date));
            return $doctors->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getCabinetPerWeekday($weekday, $doctorId) {
        $connection = Yii::app()->db;
        try {
            $cabinet = $connection->createCommand()
                ->selectDistinct('c.*')
                ->from('mis.cabinets c')
                ->join('mis.doctor_shedule_setted dss', 'dss.cabinet_id = c.id')
                ->where('dss.weekday = :weekday AND dss.day IS NULL AND dss.employee_id = :doctor_id', array(':doctor_id' => $doctorId, ':weekday' => $weekday));
            return $cabinet->queryRow();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

}

?>