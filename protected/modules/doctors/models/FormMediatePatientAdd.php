<?php

class FormMediatePatientAdd extends CFormModel
{
    public $firstName;
    public $lastName;
    public $middleName;
    public $phone;
    public $comment;
    public $id;

    public function rules()
    {
        return array(
            array(
                'firstName, lastName, phone', 'required'
            ),
            array(
                'id, middleName, comment', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'phone' => 'Контактный телефон',
            'firstName' => 'Имя',
            'lastName' => 'Фамилия',
            'middleName' => 'Отчество',
            'comment' => 'Комментарий'
        );
    }
}


?>