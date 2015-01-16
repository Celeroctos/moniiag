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
	
	 public function getByUserId($id) {
        try {
            $connection = Yii::app()->db;
            $employee = $connection->createCommand()
                ->select('d.*, LOWER(w.name) as ward, ep.shortname as enterprise')
                ->from('mis.doctors d')
                ->leftJoin('mis.wards w', 'd.ward_code = w.id')
                ->leftJoin('mis.enterprise_params ep', 'w.enterprise_id = ep.id')
                ->where('d.user_id = :user_id', array(':user_id' => $id))
                ->queryAll();

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
                 ->where('d.user_id IS NULL')
                 ->order('last_name asc');

            return $employees->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    // $onlyFree - сотрудники, которые не заняты каким-либо пользователем
    public function getRows($enterpriseId, $wardId, $filters = false, $sidx = false, $sord = false, $start = false, $limit = false, $onlyFree = false) {

        $connection = Yii::app()->db;
        $employees = $connection->createCommand()
            ->selectDistinct('d.*,
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
            $employees->andWhere('d.user_id IS NULL');
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

        if($sidx !== false && $sord !== false) {
            $employees->order($sidx.' '.$sord);
        } else {
            $employees->order('d.last_name, d.first_name, d.middle_name desc');
        }

        if($start !== false && $limit !== false) {
            $employees->limit($limit, $start);
        }

        return $employees->queryAll();
    }

    public function getByWard($wardId, $medworkerId) {
        try {
            $connection = Yii::app()->db;
            $employees = $connection->createCommand()
                ->select('d.*, m.name as post, w.name as ward')
                ->from('mis.doctors d')
				->leftJoin('mis.wards w', 'w.id = d.ward_code')
				->leftJoin('mis.medpersonal m', 'd.post_id = m.id');
			if($wardId != -1) {
				if(!is_array($wardId)) {
					$employees->andWhere('d.ward_code = :wardId', array(':wardId' => $wardId));
				} else {
					$employees->andWhere(array('in', 'd.ward_code', $wardId));
				}
			}
			if($medworkerId != -1) {
				if(!is_array($medworkerId)) {
					$employees->andWhere('m.id = :medworkerId', array(':medworkerId' => $medworkerId));
				} else {
					$employees->andWhere(array('in', 'm.id', $medworkerId));
				}
			}
            $employees->order('d.last_name', 'asc');
			
            return $employees->queryAll();;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
	
	public function getByWardAndMedworker($wardId, $medworkerId) {
		return self::getByWard($wardId, $medworkerId); 
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
                ->order('d.last_name asc')
                ->queryAll();

            return $employees;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>