<?php

class FormTableModel extends CFormModel
{
    public $table;

    public function rules()
    {
        return array(
			array(
				'table', 'safe'
			),
        );
    }

    public function attributeLabels()
    {
        return array(
            'table' => 'Таблица'
        );
    }
}


?>