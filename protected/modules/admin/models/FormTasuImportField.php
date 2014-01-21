<?php

class FormTasuImportField extends CFormModel
{
    public $dbField;
    public $tasuField;

    public function rules()
    {
        return array(
            array(
                'dbField, tasuField', 'required'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'dbField' => 'Поле базы',
            'tasuField' => 'Поле импортируемого файла'
        );
    }
}


?>