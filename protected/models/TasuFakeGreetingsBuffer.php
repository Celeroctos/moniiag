<?php
class TasuFakeGreetingsBuffer extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.tasu_fake_greetings';
    }

    public function primaryKey() {
        return 'id';
    }

    public function beforeSave() {
        parent::beforeSave();
        return true;
    }

    public function afterSave() {
        parent::afterSave();
        $this->id = Yii::app()->db->getLastInsertID('mis.tasu_fake_greetings_id_seq');
        return true;
    }

    public function getNumRows($doctorId, $dateBegin = false, $dateEnd = false) {
        $connection = Yii::app()->db;
        $tfg = $connection->createCommand()
            ->select('COUNT(tfg.*) as num_greetings')
            ->from(self::model()->tableName().' tfg')
			->where('tfg.doctor_id = :doctor_id', array(':doctor_id' => $doctorId));
		if($dateBegin !== false) {
			$tfg->andWhere('tfg.greeting_date >= :patient_day_from', array(':patient_day_from' => $dateBegin));
		}
		if($dateEnd !== false) {
			$tfg->andWhere('tfg.greeting_date <= :patient_day_to', array(':patient_day_to' => $dateEnd));
		}
		return $tfg->queryRow();
	}
}

?>