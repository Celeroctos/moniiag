<?php

class LForm extends LWidget {

    public $title = null;
    public $id = null;
    public $fields = null;
    public $url = null;

    /**
     * @var LFormModel - Form's model
     */
    public $model = null;

    public function run($return = false) {

        if (!$this->model || !($this->model instanceof LFormModel)) {
            throw new CException("Unresolved model field or form model isn't instance of LFormModel");
        }

        return $this->render(__CLASS__, [
            "model" => $this->model,
            "class" => __CLASS__
        ], $return);
    }

    /**
     * That function will render all form elements based on it's type
     * @param CActiveForm $form - Form widget
     * @param LFormModel $model - Form's model
     * @param String $key - Field name
     * @return string - Result string
     * @throws CException - If field's type hasn't been implemented in renderer
     */
    public static function renderField($form, $model, $key) {

        $config = $model->config()[$key];

        if (isset($config["type"])) {
            $type = strtolower($config["type"]);
        } else {
            $type = "text";
        }

        if (isset($config["data"])) {
            $data =  $config["data"];
        } else {
            $data = [];
        }

        if (isset($config["label"])) {
            $label = $config["label"];
        } else {
            $label = "";
        }

        switch ($type) {
            case "text":
                $result = $form->textField($model, $key, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control'
                ]);
                break;
            case "number":
                $result = $form->numberField($model, $key, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control'
                ]);
                break;
            case "file":
                $result = $form->fileField($model, $key, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control'
                ]);
                break;
            case "hidden":
                $result = $form->hiddenField($model, $key, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control'
                ]);
                break;
            case "password":
                $result = $form->passwordField($model, $key, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control'
                ]);
                break;
            case "dropdown":
                $result = $form->dropDownList($model, $key, $data, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control'
                ]);
                break;
            case "yesno":
                $result = $form->dropDownList($model, $key, [
                    -1 => "Нет",
                    0 => "Да"
                ], [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control'
                ]);
                break;
            case "radio":
                $result = $form->radioButton($model, $key, [
                    'value' => $label,
                    'id' => $key,
                    'class' => 'form-control'
                ]);
                break;
            case "textarea":
                $result = $form->textArea($model, $key, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control'
                ]);
                break;
            case "date";
                $result = $form->dateField($model, $key, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control'
                ]);
                break;
            case "reset":
            case "submit":
            case "button":
            case "checkbox":
            case "image":
            case "color":
            case "datetime";
            case "datetime-local";
            case "email";
            case "range";
            case "search";
            case "tel";
            case "time";
            case "url";
            case "month";
            case "week";
            default:
                throw new CException("LForm component render not implemented ({$type})");
        }

        return $result;
    }
} 