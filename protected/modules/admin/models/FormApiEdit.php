<?php

class FormApiEdit extends CFormModel {

    public $key;
    public $description;

    public function rules() {
        return array(
            array('key', 'required'),
            array('description', 'required')
        );
    }

    public function attributeLabels() {
        return array(
            'key' => 'Ключ',
            'description' => 'Описание'
        );
    }
}