<?php

class FormAvatarEdit extends FormMisDefault
{
    public $avatar;

    public function rules()
    {
        return array(
            array(
                'avatar', 'file', 'maxSize' => 1024 * 50, // В килобайтах
                                  'types' => 'gif, jpg, jpeg, png',
                                 // 'mimeTypes' => 'image/gif, image/jpeg, image/png',

            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'avatar' => 'Аватар',
			'avatarImg' => ''
        );
    }
}


?>