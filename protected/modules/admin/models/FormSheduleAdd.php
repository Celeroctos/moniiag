<?php

class FormSheduleAdd extends CFormModel
{
    public $cabinet0;
    public $cabinet1;
    public $cabinet2;
    public $cabinet3;
    public $cabinet4;
    public $cabinet5;
    public $cabinet6;

    public $timeBegin0;
    public $timeBegin1;
    public $timeBegin2;
    public $timeBegin3;
    public $timeBegin4;
    public $timeBegin5;
    public $timeBegin6;

    public $timeEnd0;
    public $timeEnd1;
    public $timeEnd2;
    public $timeEnd3;
    public $timeEnd4;
    public $timeEnd5;
    public $timeEnd6;

    public $dateBegin;
    public $dateEnd;
    public $doctorId;
	public $weekEnds;
	public $sheduleEmployeeId;

    public function rules()
    {
        return array(
         //   array('cabinet0, cabinet1, cabinet2, cabinet3, cabinet4, cabinet5, cabinet6, timeBegin0, timeBegin1, timeBegin2, timeBegin3, timeBegin4, timeBegin5, timeBegin6, timeEnd0, timeEnd1, timeEnd2, timeEnd3, timeEnd4, timeEnd5, timeEnd6, dateBegin, dateEnd, doctorId, weekEnds, sheduleEmployeeId', 'required'),
			array('cabinet0, cabinet1, cabinet2, cabinet3, cabinet4, cabinet5, cabinet6, timeBegin0, timeBegin1, timeBegin2, timeBegin3, timeBegin4, timeBegin5, timeBegin6, timeEnd0, timeEnd1, timeEnd2, timeEnd3, timeEnd4, timeEnd5, timeEnd6, dateBegin, dateEnd, doctorId, weekEnds, sheduleEmployeeId', 'required'),    
		);
    }

    public function attributeLabels()
    {
        return array(
            'dateBegin' => 'Дата начала действия',
            'dateEnd' => 'Дата конца действия'
        );
    }
}


?>