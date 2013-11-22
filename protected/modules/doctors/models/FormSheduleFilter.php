<?php

class FormSheduleFilter extends FormMisDefault
{
    public $date;

    public function rules()
    {
        return array(
            array(
                'date', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'date' => ''
        );
    }
}


?>