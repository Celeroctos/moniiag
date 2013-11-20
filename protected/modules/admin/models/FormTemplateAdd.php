<?php

class FormTemplateAdd extends CFormModel
{
    public $id;
    public $categorieIds;
    public $pageId;
    public $name;

    public function rules()
    {
        return array(
            array(
                'name, pageId', 'required'
            ),
            array(
                'id, categorieIds', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'pageId' => 'Страница',
            'categorieIds' => 'Категории',
            'name' => 'Названиие'
        );
    }
}


?>