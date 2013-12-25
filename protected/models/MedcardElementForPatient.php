<?php
class MedcardElementForPatient extends MisActiveRecord {
    private $typesList = array( // Типы контролов
        'Текстовое поле',
        'Текстовая область',
        'Выпадающий список',
        'Выпадающий список с множественным выбором'
    );

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcard_elements_patient';
    }

    public function getOne($medcardId, $fieldId) {
        try {
            $connection = Yii::app()->db;
            $element = $connection->createCommand()
                ->select('mcp.*')
                ->from('mis.medcard_elements_patient mcp')
                ->where('mcp.medcard_id = :medcard_id AND mcp.element_id = :element_id', array(':medcard_id' => $medcardId, ':element_id' => $fieldId))
                ->queryRow();

            return $element;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getTypesList() {
        return $this->typesList;
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $elements = $connection->createCommand()
            ->select('me.*, mg.name as guide, mc.name as categorie')
            ->from('mis.medcard_elements me')
            ->leftJoin('mis.medcard_guides mg', 'mg.id = me.guide_id')
            ->leftJoin('mis.medcard_categories mc', 'mc.id = me.categorie_id');

        if($filters !== false) {
            $this->getSearchConditions($elements, $filters, array(

            ), array(
                'me' => array('id', 'label'),
                'mg' => array('categorie'),
                'mc' => array('guide')
            ), array(
                'categorie' => 'name',
                'guide' => 'name'
            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $elements->order($sidx.' '.$sord);
            $elements->limit($limit, $start);
        }

        return $elements->queryAll();
    }

    public function getElementsByCategorie($id) {
        try {
            $connection = Yii::app()->db;
            $elements = $connection->createCommand()
                ->select('mc.*')
                ->from('mis.medcard_elements mc')
                ->where('mc.categorie_id = :categorie_id', array(':categorie_id' => $id))
                ->queryAll();

            return $elements;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getMaxHistoryPointId($element, $medcardId) {
        try {
            $connection = Yii::app()->db;
            $elements = $connection->createCommand()
                ->select('MAX(mep.history_id) as history_id_max')
                ->from('mis.medcard_elements_patient mep')
                ->where('mep.element_id = :element_id AND mep.medcard_id = :medcard_id', array(':element_id' => $element['id'], ':medcard_id' => $medcardId));
            return $elements->queryRow();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getHistoryPoints($medcard) {
        try {
            $connection = Yii::app()->db;
            $points = $connection->createCommand()
                ->selectDistinct('SUBSTR(CAST(mep.change_date AS text), 0, CHAR_LENGTH(CAST(mep.change_date AS text)) - 2) AS change_date, mep.medcard_id')
                ->from('mis.medcard_elements_patient mep')
                ->where('mep.medcard_id = :medcard_id', array(':medcard_id' => $medcard['card_number']))
                ->order('change_date DESC');
            return $points->queryAll();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getValuesByDate($date, $medcardId) {
        try {
            $connection = Yii::app()->db;
            $values = $connection->createCommand()
                ->select('mep.*, me.type')
                ->from('mis.medcard_elements_patient mep')
                ->join('mis.medcard_elements me', 'me.id = mep.element_id')
                ->where('mep.medcard_id = :medcard_id AND mep.change_date <= :date', array(':medcard_id' => $medcardId,
                                                                                           ':date' => $date))
                ->andWhere('mep.history_id = (SELECT MAX(mep2.history_id)
                                              FROM mis.medcard_elements_patient mep2
                                              WHERE mep2.element_id = mep.element_id
                                                    AND mep2.medcard_id = :medcard_id
                                                    AND mep2.change_date <= :date)', array(':medcard_id' => $medcardId,
                                                           ':date' => $date))
                ->group('mep.element_id, mep.history_id, mep.medcard_id, me.type');
            return $values->queryAll();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    // Найти все конечные состояния полей, изменённых во время приёма
    public function findAllPerGreeting($greetingId) {
        try {
            $connection = Yii::app()->db;
            $values = $connection->createCommand()
                ->select('mep.*')
                ->from('mis.medcard_elements_patient mep')
                ->where('mep.greeting_id = :greetingId', array(':greetingId' => $greetingId))
                ->andWhere('mep.history_id = (SELECT MAX(mep2.history_id)
                                              FROM mis.medcard_elements_patient mep2
                                              WHERE mep2.element_id = mep.element_id
                                                    AND mep2.greeting_id = :greetingId)', array(':greetingId' => $greetingId))
                ->group('mep.element_id, mep.history_id, mep.medcard_id');
            return $values->queryAll();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>