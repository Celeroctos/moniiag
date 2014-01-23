<?php

class FormRestDaysEdit extends FormMisDefault
{
    public $restDays;
    public $id;

    public function rules()
    {
        return array(
            array(
                'restDays', 'required'
            ),
        );
    }


    public function attributeLabels()
    {
        return array(
            'restDays' => 'Выберите выходные дни недели',
        );
    }
}


?>