<?php

class FormApiRuleAdd extends CFormModel {

    public $api_key;
	public $controller;
	public $writable;
	public $readable;

    public function rules() {
        return array(
			array('api_key', 'required'),
			array('controller', 'required'),
			array('writable', 'required'),
			array('readable', 'required')
        );
    }

    public function attributeLabels() {
        return array(
            'api_key' => 'Ключ API',
			'controller' => 'Контроллер',
			'writable' => 'Изменять',
			'readable' => 'Читать'
        );
    }
}