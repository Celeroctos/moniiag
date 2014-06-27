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
    public static function getAllForEmployer ($employerId)
    {
        $connection = Yii::app()->db;
        try {
            $sheduleDays= $connection->createCommand()
                ->select("*")
                ->from(SheduleSetted::tableName().' dss')
                ->leftJoin('mis.doctor_shedule_setted_be dssb', 'dss.date_id=dssb.id')
                ->where(

                    'dss.employee_id = :employee_id',
                    array(
                        ':employee_id' => $employerId
                    )

                );
            return $sheduleDays->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
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
				->from(SheduleSetted::tableName().' dss')
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
                ->from(SheduleSetted::tableName().' ss')
                ->where('weekday = :weekday AND NOT EXISTS(SELECT ss2.* FROM '.SheduleSetted::tableName().' ss2 WHERE weekday IS NULL AND day = :date)', array(':weekday' => $weekday, ':date' => $date));
            return $doctors->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    
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
                ->from(SheduleSetted::tableName().' ss')
                ->where('NOT EXISTS(SELECT ss2.* FROM '.SheduleSetted::tableName().' ss2 WHERE weekday IS NULL AND day = :date)', array(':date' => $date));
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