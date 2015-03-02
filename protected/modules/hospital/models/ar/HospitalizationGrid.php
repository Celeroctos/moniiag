<?php
class HospitalizationGrid extends MisActiveRecord {
    public $pageSize = 10;
    public $parentController = null;

    public $fio;
    public $patient_id;
    public $ward_name;
    public $ward_id;
    public $is_pregnant;
    public $direction_id;
    public $pregnant_term;
    public $type;
    public $comission_type_desc;
    public $card_number;
    public $is_showed;
    public $is_showed_display;
    public $is_refused;
    public $id;

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'hospital.hospitalization_grid';
    }

    public function primaryKey() {
        return 'id';
    }

    public function rules() {
        return array(
            array(
                'id, pregnant_term, ward_name, ward_id, fio, card_number, direction_id, is_showed, is_refused', 'safe', 'on' => 'grid.view'
            )
        );
    }

    public function attributeLabels() {
        return array(
            'direction_id' => 'ID',
            'fio' => 'ФИО',
            'ward_name' => 'Отделение',
            'pregnant_term' => 'Срок',
            'comission_type_desc' => 'Тип записи',
            'card_number' => 'Карта',
            'is_showed_display' => 'Статус'
        );
    }

    public function beforeFind() {

    }

    // This changes model after finding
    public function afterFind() {
        // Icon, if patient didn't showed by doctor
        if(!$this->is_showed) {
            if(!$this->is_refused) {
                $this->is_showed_display = '<a href="#" id="qd' . $this->direction_id . '" class="showPatient"><img src="' . Yii::app()->request->baseUrl . '/images/icons/viewmag_7193.png" width="24" height="24" alt="Осмотреть пациента" title="Осмотреть пациента" ></a>';
            } else {
                $this->is_showed_display = 'Отказалась';
            }
        }
    }

    public function getColumnsModel() {
        return array(
            array(
                'type' => 'raw',
                'value' => '%direction_id%',
                'name' => 'direction_id'
            ),
            array(
                'type' => 'raw',
                'value' => '%fio%',
                'name' => 'fio'
            ),
            array(
                'type' => 'raw',
                'value' => '{{%ward_name%|trim}}',
                'name' => 'ward_name',
                'filter' => Ward::model()->getAllForListview()
            ),
            array(
                'type' => 'raw',
                'value' => '%card_number%',
                'name' => 'card_number'
            ),
            array(
                'type' => 'raw',
                'value' => '%comission_type_desc%',
                'name' => 'comission_type_desc',
                'filter' => array('Обычная', 'По записи')
            ),
            array(
                'type' => 'raw',
                'value' => '%is_showed_display%',
                'name' => 'is_showed_display'
            )
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('direction_id', $this->direction_id);
        $criteria->compare('fio', $this->fio, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('ward_id', $this->ward_id);
        $criteria->compare('card_number', $this->card_number);

        $dataProvider = new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => $this->pageSize,
                'route' => 'grid/index'
            ),
            'sort' => array(
                'route' => 'grid/index',
                'attributes' => $this->getSortAttributes($this->getColumnsModel())
            )
        ));

        return $dataProvider;
    }


    private function getSortAttributes($gridModel) {
        $attrs = array();
        foreach($gridModel as $element) {
            $attrs[] = $element['name'];
        }

        return $attrs;
    }

    public function getConnection() {
        return Yii::app()->db;
    }
}
?>