<?php

class FormRoleAdd extends CFormModel
{
    public $parentId;
    public $name;
    public $id;


    public function rules()
    {
        return array(
            array(
                'name, parentId', 'required'
            ),
            array(
                'id', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Название',
            'parentId' => 'Родитель'
        );
    }
}

?>