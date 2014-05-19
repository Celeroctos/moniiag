<?php

class FormCommentAdd extends CFormModel
{

    public $id;
    public $patientId;
    public $commentText;

    public function rules()
    {
        return array(
            array(
                'commentText', 'required'
            ),
            array(
                'id, patientId, commentText', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'commentText' => 'Комментарий:',
        );
    }
}


?>