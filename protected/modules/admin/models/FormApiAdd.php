<?php

class FormApiAdd extends CFormModel {

    public $description;

    public function rules() {
        return array(
            array('description', 'required')
        );
    }

    public function attributeLabels() {
        return array(
            'description' => 'Описание'
        );
    }
}