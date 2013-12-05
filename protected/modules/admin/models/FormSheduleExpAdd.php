<?php

class FormSheduleExpAdd extends CFormModel
{
    public $cabinet;
    public $timeBegin;
    public $timeEnd;
    public $doctorId;
    public $day;
    public $id;

    public function rules()
    {
        return array(
            array(
                'cabinet, timeBegin, timeEnd, doctorId, day', 'required'
            ),
            array(
                'id', 'safe'
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