<?php

class FormRulesAdd extends CFormModel {

    public $api_key;
    public $controller;
    public $writable;
    public $readable;

    public function rules() {
        return array(
            array('description', 'required'),
            array('controller', 'required'),
            array('writable', 'required'),
            array('readable', 'required')
        );
    }

    public function attributeLabels() {
        return array(
            'description' => 'Описание',
            'controller' => 'Контроллер',
            'writable' => 'Можно писать данные',
            'readable' => 'Можно читать данные',
        );
    }
}