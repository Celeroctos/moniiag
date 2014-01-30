<?php

class FormMediatePatientAdd extends CFormModel
{
    public $firstName;
    public $lastName;
    public $middleName;
    public $phone;
    public $id;

    public function rules()
    {
        return array(
            array(
                'firstName, lastName, phone', 'required'
            ),
            array(
                'id, middleName', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(

        );
    }
}


?>