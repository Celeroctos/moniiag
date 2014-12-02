<?php
class MedcardElement extends MisActiveRecord {

    private $typesList = array( // Типы контролов
        'Текстовое поле',
        'Текстовая область',
        'Выпадающий список',
        'Выпадающий список с множественным выбором',
        'Редактируемая таблица',
        'Числовое поле',
        'Дата',
        'Двухколоночный список'
    );

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'mis.medcard_elements';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $element = $connection->createCommand()
                ->select('me.*, mc.name as categorie_name')
                ->from('mis.medcard_elements me')
                ->leftJoin('mis.medcard_categories mc', 'me.categorie_id = mc.id')
                ->where('me.id = :id', array(':id' => $id))
                ->queryRow();

            return $element;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

	public function saveElement($element) {
		try {
			return Yii::app()->db->createCommand()
				->insert("mis.medcard_elements", array(
					'type' => $element["type"],
					'categorie_id' => $element["categorie_id"],
					'label' => $element["label"],
					'guide_id' => $element["guide_id"],
					'allow_add' => $element["allow_add"],
					'label_after' => $element["label_after"],
					'size' => $element["size"],
					'is_wrapped' => $element["is_wrapped"],
					'path' => $element["path"],
					'position' => $element["position"],
					'config' => $element["config"],
					'default_value' => $element["default_value"],
					'label_display' => $element["label_display"],
					'is_required' => $element["is_required"],
					'not_printing_values' => $element["not_printing_values"],
					'hide_label_before' => $element["hide_label_before"]
				));
		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'status' => false
			)); die;
		}
		return 0;
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

    // Получить все значения справочника элемента по id элемента
    public function getGuideValuesByElementId($id) {
        try {
            $connection = Yii::app()->db;
            $values = $connection->createCommand()
                ->select('mgv.*')
                ->from('mis.medcard_elements me')
                ->join('mis.medcard_guide_values mgv', 'mgv.guide_id = me.guide_id')
                ->where('me.id = :id', array(':id' => $id))
                ->queryAll();

            return $values;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>