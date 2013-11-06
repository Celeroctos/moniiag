<?php
class Employee extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.doctors';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $employee = $connection->createCommand()
                ->select('m.*')
                ->from('mis.doctors m')
                ->where('m.id = :id', array(':id' => $id))
                ->queryRow();

            return $employee;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getRows($enterpriseId, $wardId, $filters = false, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $employees = $connection->createCommand()
            ->select('d.*,
                      m.name as post,
                      de.name as degree,
                      t.name as titul,
                      w.name as ward,
                      c.contact_value as contact
                      ')
            ->from('mis.doctors as d')
            ->join('mis.medpersonal m', 'd.post_id = m.id')
            ->leftJoin('mis.degrees de', 'd.degree_id = de.id')
            ->leftJoin('mis.tituls t', 'd.titul_id = t.id')
            ->leftJoin('mis.contacts c', 'd.contact_code = c.id')
            ->join('mis.wards w', 'd.ward_code = w.id');

        if(isset($_GET['wardid']) && $_GET['wardid'] != -1) {
            $employees->where('d.ward_code=:ward_code', array(':ward_code' => $wardId));
        }
        if(isset($_GET['enterpriseid']) && $_GET['enterpriseid'] != -1) {
            $employees->andWhere('w.enterprise_id=:enterprise_id', array(':enterprise_id' => $enterpriseId));
        }

        if($filters !== false) {
            $this->getSearchConditions($employees, $filters, array(
                'fio' => array(
                    'first_name',
                    'last_name',
                    'middle_name'
                )
            ), array(
                'd' => array('id', 'fio', 'tabel_number', 'date_begin', 'date_end', 'first_name', 'middle_name', 'last_name'),
                'm' => array('post'),
                'de' => array('degree'),
                't' => array('titul'),
                'w' => array('ward'),
                'c' => array('contact')
            ), array(
                'post' => 'name',
                'degree' => 'name',
                'titul' => 'name',
                'ward' => 'name',
                'contact' => 'contact_value'
            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $employees->order($sidx.' '.$sord);
            $employees->limit($limit, $start);
        }

        return $employees->queryAll();
    }

    public function getByWard($id) {
        try {
            $connection = Yii::app()->db;
            $employees = $connection->createCommand()
                ->select('d.*')
                ->from('mis.doctors d')
                ->where('d.ward_code = :id', array(':id' => $id))
                ->queryAll();

            return $employees;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>