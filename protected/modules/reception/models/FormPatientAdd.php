<?php
class FormPatientAdd extends FormMisDefault
{
    public $omsType;
    public $insurance;
    public $policy;
    public $lastName;
    public $firstName;
    public $middleName;
    public $gender;
    public $birthday;
    public $doctype;
    public $serie;
    public $docnumber;
   // public $whoGived;
    //public $documentGivedate;
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
    public $privilege;
    public $privDocname;
    public $privDocnumber;
    public $privDocserie;
    public $privDocGivedate;
    public $profession;
    public $policyGivedate;
    public $policyEnddate;
    public $status;

    public function rules()
    {
		Yii::import('ext.validators.SNILSValidator');
		Yii::import('ext.validators.SerialNumberValidator');
		Yii::import('ext.validators.NameValidator');
		Yii::import('ext.validators.FamilyValidator');
		Yii::import('ext.validators.FathersNameValidator');
        return array(
            array(
                'policy, lastName, firstName, gender, birthday, doctype, serie, docnumber, addressReg, address, contact, omsType, policyGivedate, status, privilege', 'required'
            ),
            array(
                'workPlace, workAddress, post, snils, invalidGroup, middleName, privDocname, privDocnumber, privDocserie, privDocGivedate, profession, policyEnddate, addressRegHidden, addressHidden', 'safe'
            ),
			array(
				'snils', 'SNILSValidator'
				),
			array(
				'serie,docnumber', 'SerialNumberValidator'
			),
			array(
				'lastName', 'FamilyValidator'
			),
		    array(
				'firstName', 'NameValidator'
			),
			array(
				'middleName', 'FathersNameValidator'
			)
        );
    }

    public function attributeLabels()
    {
        return array(
            'omsType' => 'Тип',
            'policy' => 'Номер',
            'insurance' => 'Код страховой компании',
            'lastName' => 'Фамилия',
            'firstName' => 'Имя',
            'middleName' => 'Отчество',
            'gender' => 'Пол',
            'birthday' => 'Дата рождения',
            'doctype' => 'Тип документа',
            'serie' => 'Серия, номер',
            'docnumber' => 'Номер',
           // 'whoGived' => 'Кем выдан',
           // 'documentGivedate' => 'Дата выдачи',
            'addressReg' => 'Адрес регистрации',
            'address' => 'Адрес проживания',
            'workPlace' => 'Место работы',
            'workAddress' => 'Адрес работы',
            'post' => 'Должность',
            'contact' => 'Контактные данные',
            'snils' => 'СНИЛС',
            'invalidGroup' => 'Группа инвалидности',
            'privilege' => 'Льгота',
            'privDocname' => 'Название документа',
            'privDocnumber' => 'Номер',
            'privDocserie' => 'Серия',
            'privDocGivedate' => 'Дата выдачи',
            'profession' => 'Профессия',
            'policyGivedate' => 'Дата выдачи',
            'policyEnddate' => 'Дата окончания действия',
            'status' => 'Статус'
        );
    }
}


?>