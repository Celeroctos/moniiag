<?php
class DoctorsTimetable extends MisActiveRecord {


    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function primaryKey()
    {
        return 'id';
        // Для составного первичного ключа следует использовать массив:
        // return array('pk1', 'pk2');
    }

    // Получить докторов для конкретного расписания по его ID
    public function getDoctorsWardsByShedule($idShedule)
    {
        try
        {
            $result = array();
            $connection = Yii::app()->db;
            $doctors = $connection->createCommand()
                ->select('
                    w.name,
                    d.ward_code,
                    d.id,
                    d.first_name,
                    d.last_name,
                    d.middle_name,


                ')
                ->from(DoctorsTimetable::model()->tableName(). ' dtt')
                ->join(Doctor::model()->tableName(). ' d', 'd.id =dtt.id_doctor')
                ->leftJoin(Ward::model()->tableName(). ' w', 'w.id = d.ward_code')
                ->andWhere('dtt.id_timetable=:timetable', array (':timetable'=>$idShedule )  )
                ->order('d.ward_code desc');


            $doctorsList = $doctors->queryAll();

            // А теперь перебираем результат в $doctorsList и расфигачиваем докторов по отделением
            if (count($doctorsList)>0)
            {
                $currentWard = $doctorsList[0]['ward_code'];
                foreach ($doctorsList as $oneDoctor)
                {
                    $wardId = $oneDoctor['ward_code'];
                    $wardName = $oneDoctor['name'];

                    // Если у врача нет отделения, то его id будет -1, а название "Без отделения"
                    if ($wardId == '' || $wardId == NULL)
                    {
                        $wardId = -1;
                        $wardName = 'Без отделения';
                    }

                    // Если в массиве result нет ключа данного отделения - добавляем
                    if (!isset($result[$wardId]))
                    {
                        $result[$wardId] = array();
                        $result[$wardId]['name'] = $wardName;
                    }

                    // Добавляем дохтора в отделение
                    $result[$wardId]['doctors'][$oneDoctor['id']] =
                        $oneDoctor['first_name'].' '.$oneDoctor['middle_name'].' '.$oneDoctor['last_name'];
                }


            }
            return $result;


        }
        catch (Exception $Exc)
        {
            var_dump($Exc);
            exit();
        }



    }

    public function tableName()
    {
        return 'mis.doctors_timetables';
    }

}

?>