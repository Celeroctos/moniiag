<?php

/*
 * TODO: "Not Implemented Fields"

    "Reset",
    "Submit",
    "Button",
    "CheckBox",
    "Image",
    "Color",
    "DateTime",
    "DateTime-Local",
    "Email",
    "Range",
    "Search",
    "Tel",
    "Time",
    "Url",
    "Month",
    "Week"
 */

class LForm extends LWidget {

    public $id = null;
    public $url = null;

    /**
     * @var LFormModel - Form's model
     */
    public $model = null;

    /**
     * Override that method to return just rendered component
     * @throws CException
     * @return string - Just rendered component or nothing
     */
    public function run() {
        if (is_array($this->model)) {
            $config = [];
            foreach ($this->model as $i => $model) {
                if ($this->test($model)) {
                    $config += $model->config();
                }
            }
            $this->model = new LFormModelAdapter($config);
        } else {
            $this->test($this->model);
        }
        foreach ($this->model->view() as $key => $value) {
            $this->$key = $value;
        }
        $this->render(__CLASS__, [
            "model" => $this->model,
            "class" => __CLASS__
        ]);
    }

    /**
     * Test model for LFormModel inheritance and not null
     * @param Mixed $model - Model which must extends LFormModel
     * @return bool - True if everything ok
     * @throws CException
     */
    private function test($model) {
        if (!$model || !($model instanceof LFormModel)) {
            throw new CException("Unresolved model field or form model isn't instance of LFormModel ".(int)$model);
        }
        return true;
    }

    /**
     * Format every data field with specific format, it will get data format field's
     * from model
     * @param String $format - String with data format, for example ${id} or ${surname}
     * @param Array $data - Array with data to format
     */
    private function format($format, array& $data) {
        foreach ($data as $i => &$value) {
            $model = clone $value;
            $matches = [];
            preg_match_all("/%\\{([a-zA-Z_]+)\\}/", $format, $matches);
            $value = $format;
            if (!count($matches)) {
                continue;
            }
            foreach ($matches[1] as $m) {
                $value = preg_replace("/%\\{([({$m})]+)\\}/", $model[$m], $value);
            }
        }
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
            $type = $config["type"];
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
            $this->format($config["format"], $data);
        }

        if (isset($config["label"])) {
            $label = $config["label"];
        } else {
            $label = "";
        }

        if (isset($config["options"])) {
            $options = $config["options"];
        } else {
            $options = [];
        }

        if (isset($config["update"])) {
            $options["data-update"] = $config["update"];
        }

        $result = LFieldCollection::getCollection()->find($type)->renderEx(
            $form, $this->model, $key, $label, $value, $data, $options
        );

        return $result;
    }

    /**
     * Check model's type via it's configuration
     * @param string $key - Name of native key to check
     * @param string $type - Type to test
     * @return bool - True if type if equal else false
     */
    public function checkType($key, $type) {
        $config = $this->model->config()[$key];
        if (!isset($config["type"])) {
            $config["type"] = "text";
        }
        return strtolower($config["type"]) == strtolower($type);
    }

    /**
     * Check if field has hidden property
     * @param string $key - Name of native key to check
     * @return bool - True if field must be hidden
     */
    public function isHidden($key) {
        $config = $this->model->config()[$key];
        if (!isset($config["hidden"])) {
            return false;
        }
        return $config["hidden"];
    }
} 