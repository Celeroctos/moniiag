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
                ->select('d.*, LOWER(w.name) as ward, ep.shortname as enterprise')
                ->from('mis.doctors d')
                ->leftJoin('mis.wards w', 'd.ward_code = w.id')
                ->leftJoin('mis.enterprise_params ep', 'w.enterprise_id = ep.id')
                ->where('d.id = :id', array(':id' => $id))
                ->queryRow();

            return $employee;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    // Получить всех тех, кого можно использовать для привязки к пользователю
    public function getAllWithoutUsers() {
        try {
            $connection = Yii::app()->db;
            $employees = $connection->createCommand()
                 ->select('d.*, LOWER(w.name) as ward, ep.shortname as enterprise')
                 ->from('mis.doctors d')
                 ->join('mis.wards w', 'd.ward_code = w.id')
                 ->join('mis.enterprise_params ep', 'w.enterprise_id = ep.id')
                 ->where('NOT EXISTS(SELECT * FROM mis.users u WHERE u.employee_id = d.id)');

            return $employees->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    // $onlyFree - сотрудники, которые не заняты каким-либо пользователем
    public function getRows($enterpriseId, $wardId, $filters = false, $sidx = false, $sord = false, $start = false, $limit = false, $onlyFree = false) {
        $connection = Yii::app()->db;
        $employees = $connection->createCommand()
            ->select('d.*,
                      m.name as post,
                      de.name as degree,
                      t.name as titul,
                      w.name as ward,
                      w.id as ward_id,
                      w.enterprise_id as enterprise_id,
                      ep.shortname as enterprise
                      ')
            ->from('mis.doctors as d')
            ->leftJoin('mis.medpersonal m', 'd.post_id = m.id')
            ->leftJoin('mis.degrees de', 'd.degree_id = de.id')
            ->leftJoin('mis.tituls t', 'd.titul_id = t.id')
            ->leftJoin('mis.wards w', 'd.ward_code = w.id')
            ->leftJoin('mis.enterprise_params ep', 'w.enterprise_id = ep.id');

        if($wardId != -1) {
            $employees->where('d.ward_code=:ward_code', array(':ward_code' => $wardId));
        }
        if($enterpriseId != -1) {
			if($enterpriseId == -2) {
				$employees->andWhere('w.enterprise_id IS NULL');
			} else {
				$employees->andWhere('w.enterprise_id = :enterprise_id', array(':enterprise_id' => $enterpriseId));
			}
		}
        if($onlyFree) {
            $employees->andWhere('NOT EXISTS(SELECT * FROM mis.users u WHERE u.employee_id = d.id)');
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
                'm' => array('post', 'is_for_pregnants'),
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
        } else {
            $employees->order('d.last_name, d.first_name, d.middle_name desc');
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

    public function getEmployeesPerSpec($id) {
        try {
            $connection = Yii::app()->db;
            $employees = $connection->createCommand()
                ->select('d.*, LOWER(w.name) as ward, ep.shortname as enterprise')
                ->from('mis.doctors d')
                ->join('mis.wards w', 'd.ward_code = w.id')
                ->join('mis.enterprise_params ep', 'w.enterprise_id = ep.id')
                ->where('d.post_id = :id', array(':id' => $id))
                ->queryAll();

            return $employees;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>