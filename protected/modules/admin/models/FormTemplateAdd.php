<?php

class FormTemplateAdd extends CFormModel
{
    public $id;
    public $categorieIds;
    public $pageId;
    public $name;
    public $index;
    public $primaryDiagnosisFilled;

    public function rules()
    {
        return array(
            array(
                'name, pageId, index, primaryDiagnosisFilled', 'required'
            ),
            array(
                'id, categorieIds', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'pageId' => 'Тип шаблона',
            'categorieIds' => 'Категории',
            'name' => 'Названиие',
            'primaryDiagnosisFilled' => 'Обязательность заполнения основного диагноза',
            'index' => 'Порядковый номер отображения для врача'
        );
    }
}


?>