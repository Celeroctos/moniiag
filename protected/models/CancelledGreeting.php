<?php
class CancelledGreeting extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.cancelled_greetings';
    }

    public function getRows($filters,$sidx = false, $sord = false, $start = false, $limit = false, $mediateOnly = false,$afterToday = true)
    {
        try {
            $connection = Yii::app()->db;
            $greetings = $connection->createCommand()
                ->selectDistinct('cg.*,
                                  o.first_name as p_first_name,
                                  o.middle_name as p_middle_name,
                                  o.last_name as p_last_name,
                                  d.first_name as d_first_name,
                                  d.middle_name as d_middle_name,
                                  d.last_name as d_last_name,
                                  m.motion,
                                  m.card_number,
                                  m.contact,
                                  mdp.phone,
                                  mdp.first_name as m_first_name,
                                  mdp.middle_name as m_middle_name,
                                  mdp.last_name as m_last_name,
                                  o.id as oms_id,
                                  mp.name as post')
                ->from('mis.cancelled_greetings cg')
                ->leftJoin('mis.medcards m', 'cg.medcard_id = m.card_number')
                ->leftJoin('mis.oms o', 'm.policy_id = o.id')
                ->join('mis.doctors d', 'd.id = cg.doctor_id')
                ->join('mis.medpersonal mp', 'd.post_id = mp.id')
                ->leftJoin('mis.mediate_patients mdp', 'mdp.id = cg.mediate_id');


            if($filters !== false) {
                $this->getSearchConditions($greetings, $filters, array(
                    'doctor_fio' => array(
                        'd_first_name',
                        'd_last_name',
                        'd_middle_name'
                    ),
                    'patient_fio' => array(
                        'p_first_name',
                        'p_last_name',
                        'p_middle_name',
                        'm_last_name',
                        'm_first_name',
                        'm_middle_name'
                    ),
                    'phone' => array(
                        'contact',
                        'm_phone'
                    )
                ), array(
                    'mp' => array('is_for_pregnants'),
                    'o' => array('p_first_name', 'p_middle_name', 'p_last_name', 'patient_fio', 'patients_ids'),
                    'd' => array('d_first_name', 'd_middle_name', 'd_last_name', 'doctor_fio', 'doctors_ids'),
                    'm' => array('contact'),
                    'mdp' => array('m_first_name', 'm_middle_name', 'm_last_name', 'patient_fio', 'm_phone'),
                    'cg' => array('patient_day', 'medcard_id', 'mediates_ids')
                ), array(
                    'phone' => 'contact',
                    'd_first_name' => 'first_name',
                    'd_last_name' => 'last_name',
                    'd_middle_name' => 'middle_name',
                    'p_first_name' => 'first_name',
                    'p_last_name' => 'last_name',
                    'p_middle_name' => 'middle_name',
                    'm_last_name' => 'last_name',
                    'm_first_name' => 'first_name',
                    'm_middle_name' => 'middle_name',
                    'patients_ids' => 'id',
                    'doctors_ids' => 'id',
                    'mediates_ids' => 'mediate_id',
                    'm_phone' => 'phone'
                ), array(
                    'OR' => array(
                        'mediates_ids',
                        'pateints_ids'
                    )
                ));
            }
            if($mediateOnly) {
                $greetings->andWhere('cg.mediate_id IS NOT NULL');
            }
            $greetings->andWhere('NOT (cg.deleted=1)');

            $greetings->order('cg.patient_time');
            $greetings->group('cg.id, o.first_name, o.last_name, o.middle_name, d.first_name, d.last_name, d.middle_name, m.motion, o.id, mp.name, m.card_number, mdp.phone, mdp.last_name, mdp.middle_name, mdp.first_name');

            if($limit !== false && $start !== false) {
                $greetings->limit($limit, $start);
            }

            //var_dump($greetings->text);
            //exit();
            $result = $greetings->queryAll();
            return $result;
            //  var_dump($result );
            //  exit();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>