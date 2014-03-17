<?php

class FormElementAdd extends CFormModel
{
    public $type;
    public $categorieId;
    public $label;
    public $guideId;
    public $id;
    public $allowAdd;
    public $labelAfter;
    public $size;
    public $position;
    public $isWrapped;
    public $numCols;
    public $numRows;
    public $config;

    public function rules()
    {
        return array(
            array(
                'type, categorieId, label, position', 'required'
            ),
            array(
                'id, guideId, allowAdd, labelAfter, size, isWrapped, numCols, numRows, config', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'type' => 'Тип элемента',
            'categorieId' => 'Категория',
            'label' => 'Метка рядом с элементом',
            'guideId' => 'Справочник',
            'allowAdd' => 'Можно добавлять новые значения врачу?',
            'labelAfter' => 'Метка после элемента',
            'size' => 'Размер элемента',
            'position' => 'Позиция в категории',
            'isWrapped' => 'Перенос строки',
            'numRows' => 'Количество строк',
            'numCols' => 'Количество столбцов'
        );
    }
}


?>