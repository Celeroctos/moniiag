<?php

class LPatientFormOLD extends CFormModel {

	public $doctype;
	public $serie;
	public $docnumber;
	public $addressReg;
	public $addressRegHidden;
	public $address;
	public $addressHidden;
	public $workPlace;
	public $workAddress;
	public $post;
	public $contact;
	public $snils;
	public $invalidGroup;
	public $policy;
	public $cardNumber;
	public $privilege;
	public $privDocname;
	public $privDocnumber;
	public $privDocserie;
	public $privDocGivedate;
	public $profession;
	public $mediateId; // Опосредованный пациент может быть

	public function rules() {
		Yii::import('ext.validators.SNILSValidator');
		Yii::import('ext.validators.SerialNumberValidator');
		return [
			[ 'doctype, addressReg, address, contact, privilege', 'required' ],
			[ 'workPlace, workAddress, post, snils, invalidGroup, policy, cardNumber, privDocname, privDocnumber, privDocserie, privDocGivedate, profession, mediateId, addressRegHidden, addressHidden', 'safe' ],
			[ 'snils', 'SNILSValidator' ],
			[ 'serie, docnumber', 'SerialNumberValidator' ]
		];
	}

	public function attributeLabels() {
		return [
			'doctype' => 'Тип документа',
			'serie' => 'Серия',
			'docnumber' => 'Номер',
			// 'whoGived' => 'Кем выдан',
			// 'documentGivedate' => 'Дата выдачи',
			'addressReg' => 'Адрес регистрации',
			'address' => 'Адрес проживания',
			'workPlace' => 'Место работы',
			'workAddress' => 'Адрес работы',
			'post' => 'Должность',
			'contact' => 'Телефон',
			'snils' => 'СНИЛС',
			'invalidGroup' => 'Группа инвалидности',
			'privilege' => 'Льгота',
			'privDocname' => 'Название документа',
			'privDocnumber' => 'Номер',
			'privDocserie' => 'Серия, номер',
			'privDocGivedate' => 'Дата выдачи',
			'profession' => 'Профессия'
		];
	}
}