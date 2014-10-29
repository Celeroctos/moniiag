<?php
class Doctor extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.doctors';
    }


    public function getAll($forPregnant = false) {
        try {
            $connection = Yii::app()->db;
            $doctors = $connection->createCommand()
                ->select('d.*')
                ->from('mis.doctors d');

            if($forPregnant !== false) {
                $doctors->join('mis.medpersonal m', 'd.post_id = m.id')
                    ->where('m.is_for_pregnants = 1');
            }
            $doctors = $doctors->queryAll();

            foreach($doctors as $key => &$doctor) {
                $doctor['fio'] = $doctor['first_name'].' '.$doctor['middle_name'].' '.$doctor['last_name'];
            }

            return $doctors;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false,
                            $limit = false, $choosedDiagnosis = array(), $greetingDate = false) {
        $connection = Yii::app()->db;
        $doctor = $connection->createCommand()
            ->selectDistinct('d.*, w.name as ward, m.name as post')
            ->from('mis.doctors d')
            ->leftJoin('mis.wards w', 'd.ward_code = w.id')
            ->leftJoin('mis.medpersonal m', 'd.post_id = m.id');

        if(count($choosedDiagnosis) > 0) {
            $doctor->leftJoin('mis.mkb10_distrib md', 'md.employee_id = d.id');
        }

        if($filters !== false) {
            $this->getSearchConditions($doctor, $filters, array(
            ), array(
                'd' => array('id', 'first_name', 'last_name', 'middle_name', 'post_id', 'ward_code')
            ), array(
            ));
        }

        if(count($choosedDiagnosis) > 0) {
            $doctor->andWhere(array('in', 'md.mkb10_id', $choosedDiagnosis));
        }


        // Теперь нужно выяснить сотрудников, которые могут принимать в этот день
        if($greetingDate !== false && $greetingDate !== null) {
            // Теперь мы знаем, каких врачей выбирать, с каким днём
            $doctorsPerDay = DoctorsTimetable::model()->getAllPerDate($greetingDate);
            $doctorIds = array();
            $num = count($doctorsPerDay);
            for($i = 0; $i < $num; $i++) {
                $doctorIds[] = $doctorsPerDay[$i]['employee_id'];
            }
            $doctor->andWhere(array('in', 'd.id', $doctorIds));
        }

        if ($sidx && $sord && $limit) {
            $doctor->order($sidx.' '.$sord);
            $doctor->limit($limit, $start);    
        }

        $doctors = $doctor->queryAll();
       foreach($doctors as $key => &$doctor) {
            $doctor['cabinets'] = array();
            $cabinets = DoctorCabinet::model()->findAll('doctor_id = :doctor_id', array(':doctor_id' => $doctor['id']));
            foreach($cabinets as $cabinet) {
                $cab = Cabinet::model()->findByPk($cabinet->cabinet_id);
                if($cab == null) {
                    continue;
                }
                $doctor['cabinets'][] = array(
                    'id' => $cabinet->cabinet_id,
                    'number' => $cab->cab_number,
                    'description' => $cab->description
                );
            }
        }
        return $doctors;
    }
}

?>