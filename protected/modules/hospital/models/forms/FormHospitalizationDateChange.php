<?php
class FormHospitalizationDateChange extends FormMisDefault
{
    public $id;
    public $hospitalization_date;
    public $grid_id;
    public $write_type;
    public $refuse_comment;

    public function rules() {
        return array(
            array(
                'hospitalization_date', 'date', 'format' => 'dd.mm.yyyy', 'on' => 'view'
            ),
            array(
                'hospitalization_date', 'date', 'format' => 'yyyy-mm-dd', 'on' => 'edit'
            ),
            array(
                'write_type', 'numerical', 'on' => 'view'
            ),
            array(
                'write_type', 'numerical', 'on' => 'edit'
            ),
            array(
                'hospitalization_date, write_type', 'required'
            ),
            array(
                'id, grid_id, refuse_comment', 'safe'
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
            'write_type' => 'Тип записи'
        );
    }
}


?>