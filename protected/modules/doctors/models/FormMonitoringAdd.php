<?php

class FormMonitoringAdd extends CFormModel
{

    public $id;
    public $monType;
    public $patientId;
    public $frequency;

    public function rules()
    {
        return array(
            // name, email, subject and body are required
            /*array(
                'shortName, fullName, addressFact, addressJur, phone, bank, bankAccount, inn, kpp, ogrn, type', 'required'
            )*/
        );
    }

    public function attributeLabels()
    {
        return array(
            'monType'=> 'Тип мониторинга',
            'frequency' => 'Количество измерений в день'
        );
    }
}


?>