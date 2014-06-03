<?php

class FormCommentAdd extends CFormModel
{

    public $commentId;
    public $forPatientId;
    public $commentText;

    public function rules()
    {
        return array(
            array(
                'commentText', 'required'
            ),
            array(
                'commentId, forPatientId, commentText', 'safe'
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