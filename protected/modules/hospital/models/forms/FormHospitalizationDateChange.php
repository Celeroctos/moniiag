<?php
class FormHospitalizationDateChange extends FormMisDefault
{
    public $id;
    public $hospitalization_date;
    public $grid_id;

    public function rules() {
        return array(
            array(
                'hospitalization_date', 'date', 'format' => 'dd.mm.yyyy', 'on' => 'view'
            ),
            array(
                'hospitalization_date', 'date', 'format' => 'yyyy-mm-dd', 'on' => 'edit'
            ),
            array(
                'hospitalization_date', 'required'
            ),
            array(
                'id, grid_id', 'safe'
            )
        );
    }

    public function beforeValidate() {
        if($this->hospitalization_date) {
            $this->hospitalization_date = implode('-', array_reverse(explode('.', $this->hospitalization_date)));
        }

        return true;
    }

    public function attributeLabels() {
        return array(
            'hospitalization_date' => 'Дата госпитализации',
        );
    }
}


?>