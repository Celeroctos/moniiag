<?php

class FormElementAdd extends CFormModel
{
    public $type;
    public $categorieId;
    public $label;
    public $guideId;
    public $id;
    public $allowAdd;
    public $isRequired;
    public $labelAfter;
    public $size;
    public $position;
    public $isWrapped;
    public $numCols;
    public $numRows;
    public $config;
    public $labelDisplay;
    public $defaultValue;
    public $defaultValueText;
    public $numberFieldMinValue;
    public $numberFieldMaxValue;
    public $numberStep;

    public function rules()
    {
        return array(
            array(
                'type, categorieId, label, position', 'required'
            ),
            array(
                'id, guideId, allowAdd, isRequired, labelAfter, size, isWrapped, numCols, numRows, config, labelDisplay, defaultValue, defaultValueText', 'safe'
            ),
            array(
                'numberFieldMinValue, numberFieldMaxValue, numberStep', 'numerical'
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
            'isRequired' => 'Обязателен для заполнения',
            'labelAfter' => 'Метка после элемента',
            'size' => 'Размер элемента',
            'position' => 'Позиция в категории',
            'isWrapped' => 'Следующий элемент с новой строки',
            'numRows' => 'Количество строк',
            'numCols' => 'Количество столбцов',
            'labelDisplay' => 'Метка для отображения в администрировании',
            'defaultValue' => 'Значение по умолчанию',
            'defaultValueText' => 'Значение по умолчанию',
            'numberFieldMinValue' => 'Минимальное значение',
            'numberFieldMaxValue' => 'Максимальное значение',
            'numberStep' => 'Шаг'
        );
    }
}


?>