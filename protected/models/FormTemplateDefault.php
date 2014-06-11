<?php

class FormTemplateDefault extends FormMisDefault
{
    public $name;
    public $id;
    public $medcardId;
    public $greetingId;
    public $templateName;
    public $templateId;

    public $attributeLabels = array();
    public $safeFields = array('templateName,medcardId, greetingId', 'safe');

    // Грязный хак для динамической модели
    public function __set($name,$value) {
        $this->$name = $value;
    }

    public function rules()
    {
        return array(
            $this->safeFields
        );
    }

    public function attributeLabels()  {
        return $this->attributeLabels;
    }

    public function setAttributeLabels($name, $label) {
        $this->attributeLabels[$name] = $label;
    }

    public function setSafeRule($name) {
        $this->safeFields[0] .= $name.', ';
    }
}


?>