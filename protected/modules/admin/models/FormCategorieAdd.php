<?php

class FormCategorieAdd extends CFormModel
{
    public $name;
    public $id;
	public $parentId;
    public $position;
    public $isDynamic;

    public function rules()
    {
        return array(
            array(
                'name, parentId, isDynamic, position', 'required'
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
			'parentId' => 'Категория-родитель',
            'position' => 'Позиция среди сестринских категорий и элементов',
            'isDynamic' => 'Возможность динамического добавления в медкарту'
        );
    }
}


?>