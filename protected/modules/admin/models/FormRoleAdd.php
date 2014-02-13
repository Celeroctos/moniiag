<?php

class FormRoleAdd extends CFormModel
{
    public $parentId;
    public $pageId;
    public $name;
    public $id;


    public function rules()
    {
        return array(
            array(
                'name, parentId, pageId', 'required'
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
            'parentId' => 'Родитель',
            'pageId' => 'Стартовая страница после авторизации',
            'pageId' => 'Стартовая страница после авторизации'
        );
    }
}

?>