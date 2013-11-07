<?php

class FormEmployeeAdd extends CFormModel
{
    public $firstName;
    public $middleName;
    public $lastName;
    public $postId;
    public $tabelNumber;
    public $degreeId;
    public $titulId;
    public $dateBegin;
    public $dateEnd;
    public $wardCode;
    public $id;


    public function rules()
    {
        return array(
            array(
                'firstName, middleName, lastName, postId, dateBegin, dateEnd, wardCode', 'required'
            ),
            array(
                'degreeId, titulId, tabelNumber', 'numerical'
            )
        );
    }


    public function attributeLabels()
    {
        return array(
            'firstName'=> 'Имя',
            'middleName' => 'Фамилия',
            'lastName' => 'Отчество',
            'postId' => 'Мед. работник',
            'tabelNumber' => 'Табельный номер',
            'degreeId' => 'Степень',
            'titulId' => 'Звание',
            'dateBegin' => 'Дата начала действия',
            'dateEnd' => 'Дата конца действия',
            'wardCode' => 'Отделение'
        );
    }
}


?>