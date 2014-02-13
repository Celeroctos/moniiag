<?php

class FormStartpageAdd extends CFormModel
{
    public $id;
    public $name;
    public $url;
    public $priority;


    public function rules()
    {
        return array(
            array(
                'name, url, priority', 'required'
            ),
            array(
                'id', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'name'=> 'Название',
            'url' => 'Адрес',
            'priority' => 'Приоритет'
        );
    }
}

?>