<?php

class FormCategorieAdd extends CFormModel
{
    public $name;
    public $id;
	public $parentId;

    public function rules()
    {
        return array(
            array(
                'name, parentId', 'required'
            ),
			array(
				'parentId', 'numerical'
			),
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Название категории',
			'parentId' => 'Категория-родитель'
        );
    }
}


?>