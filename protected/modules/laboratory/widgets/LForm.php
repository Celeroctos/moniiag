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
     * @param String $key - Field name
     * @return string - Result string
     * @throws CException - If field's type hasn't been implemented in renderer
     */
    public function renderField($form, $key) {

        $config = $this->model->config()[$key];

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

        if (isset($config["value"])) {
            $value =  $config["value"];
        } else {
            $value = null;
        }

        if (isset($config["format"])) {
            $format = $config["format"];
            foreach ($data as $i => &$value) {
                $model = clone $value;
                $matches = [];
                preg_match_all("/%\\{([a-zA-Z_]+)\\}/", $format, $matches);
                $value = $format;
                if (count($matches) > 0) {
                    foreach ($matches[1] as $m) {
                        $value = preg_replace("/%\\{([({$m})]+)\\}/", $model[$m], $value);
                    }
                }
            }
        }

        if (isset($config["label"])) {
            $label = $config["label"];
        } else {
            $label = "";
        }

        switch ($type) {
            case "text":
                $result = $form->textField($this->model, $key, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control',
                    'value' => $value
                ]);
                break;
            case "number":
                $result = $form->numberField($this->model, $key, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control',
                    'value' => $value
                ]);
                break;
            case "file":
                $result = $form->fileField($this->model, $key, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control',
                    'value' => $value
                ]);
                break;
            case "hidden":
                $result = $form->hiddenField($this->model, $key, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control',
                    'value' => $value
                ]);
                break;
            case "password":
                $result = $form->passwordField($this->model, $key, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control',
                    'value' => $value
                ]);
                break;
            case "dropdown":
                if (!isset($data[-1])) {
                    $data = [ -1 => "Нет" ] + $data;
                }
                $result = $form->dropDownList($this->model, $key, $data, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control',
                    'value' => $value,
                    'options' => [ $value => [ 'selected' => true ] ]
                ]);
                break;
            case "yesno":
                $result = $form->dropDownList($this->model, $key, [
                    -1 => "Нет",
                    0 => "Да"
                ], [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control',
                    'value' => $value
                ]);
                break;
            case "radio":
                $result = $form->radioButton($this->model, $key, [
                    'value' => $label,
                    'id' => $key,
                    'class' => 'form-control',
                    'value' => $value
                ]);
                break;
            case "textarea":
                $result = $form->textArea($this->model, $key, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control',
                    'value' => $value
                ]);
                break;
            case "date";
                $result = $form->dateField($this->model, $key, [
                    'placeholder' => $label,
                    'id' => $key,
                    'class' => 'form-control',
                    'value' => $value
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