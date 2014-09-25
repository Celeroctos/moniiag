<?php
class Timetable extends MisActiveRecord {

    public $EntryID = null;

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


    public function afterSave() {
        try{
            // Надо будет разобраться почему без вот этой вот конструкции не возвращается назад ID-шник только что добавленной записи
            $this->EntryID = Yii::app()->db->getLastInsertID('mis.timetable_id_seq');
        }
        catch(Exception $Exc)
        {

        }
        return parent::afterSave();
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false)
    {
        $timeTablesIds = $this->getRowsIds($filters, $sidx, $sord , $start , $limit );

        // А теперь для каждого расписания (столько расписаний, сколько на одной странице в спискоте, т.е. штук 15) выбираем
        //   - date_begin
        //   - date_end
        //   - json
        //   - id (само собой)
        //   -doctors
        //    в массиве doctors имеем распределение докторов по отделениям

        $timeTableObject = new Timetable();
        $timeTableDoctorsObject = new DoctorsTimetable();

        foreach ($timeTablesIds as &$oneTableId)
        {
            // Берём данные по самому расписанию
            $timetableBody = $timeTableObject::model()->findByPk( $oneTableId['id'] );
            //$oneTableId['id'] = $timetableBody['id'];
            $oneTableId['date_begin'] = $timetableBody['date_begin'];
            $oneTableId['date_end'] = $timetableBody['date_end'];
            $oneTableId['json_data'] = $timetableBody['timetable_rules'];
            $oneTableId['wardsWithDoctors'] = $timeTableDoctorsObject->getDoctorsWardsByShedule($oneTableId['id']);


        }

        return $timeTablesIds;
    }


    public function getNumRows($filters, $sidx = false, $sord = false, $start = false, $limit = false)
    {
        // Получим строки с ID и посчитаем их
        return count(  $this->getRowsIds($filters, $sidx, $sord , $start , $limit ) );
    }

    private function getRowsIds($filters, $sidx = false, $sord = false, $start = false, $limit = false)
    {
        try
        {
            $connection = $this->getDbConnection();
            $timetables = $connection->createCommand()
                ->selectDistinct('tt.id, tt.date_end')
                ->from(Timetable::tableName().' tt')
                ->join(DoctorsTimetable::tableName(). ' dtt', 'dtt.id_timetable = tt.id')
                ->join(Doctor::tableName(). ' d', 'd.id =dtt.id_doctor')
                ->leftJoin(Ward::tableName(). ' w', 'w.id = d.ward_code');

                // Смотрим - если заданы врачи - пришпиливаем к запросу врачей
                if ($filters['doctorsIds']!=NULL)
                {
                    $timetables->andWhere('d.id in ('. implode(',',$filters['doctorsIds']) .')');
                }
                else
                {
                    // Иначе - проверяем флаг "без отделения"
                    if ($filters['woWardFlag']==true)
                    {
                        $timetables->andWhere('d.ward_code is NULL');
                    }
                    else
                    {
                       if ($filters['wardsIds']!=NULL)
                       {
                           // Иначе проверяем заданные отделения
                           $timetables->andWhere('d.ward_code in ('. implode(',',$filters['wardsIds']) .')');
                       }
                    }
                }

            if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
                $timetables->order($sidx. ', tt.date_end '.$sord);
                $timetables->limit($limit, $start);
            }



            $timetables = $timetables->queryAll();

           // var_dump($timetables );
           // exit();
            return $timetables;
        }
        catch (Exception $Exc)
        {
            var_dump($Exc);
            exit();
        }
    }

    public function tableName()
    {
        return 'mis.timetable';
    }

}

?>