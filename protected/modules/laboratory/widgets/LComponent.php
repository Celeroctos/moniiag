<?php

abstract class LComponent extends LForm {

    public $model;

    /**
     * Construct component
     */
    public function __construct() {

        // Construct parent form
        parent::__construct(Yii::app()->getController());

        // First we need construct model
        $modelConfig = $this->model();

        // Create component's adapted model
        $this->model = new LFormModelAdapter(
            $modelConfig
        );

        // Get render configuration
        $viewConfig = $this->view();

        // Save configurations
        $this->_modelConfig = $modelConfig;
        $this->_viewConfig = $viewConfig;

        // Copy view config to component
        foreach ($viewConfig as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Override that method to return model configuration
     * @return array - Model
     */
    public abstract function model();

    /**
     * Override that method to return view configuration
     * @return mixed - View
     */
    public abstract function view();

    /**
     * That method will run your widget and render it
     * @param bool $return - Should widget print or return result
     * @return string - String with widget's data
     * @throws CException
     */
    public function run($return = false) {
        return parent::run($return);
    }

    /**
     * Renders a view.
     * The named view refers to a PHP script (resolved via {@link getViewFile})
     * that is included by this method. If $data is an associative array,
     * it will be extracted as PHP variables and made available to the script.
     * @param string $view - Name of the view to be rendered. See {@link getViewFile} for details
     * about how the view script is resolved.
     * @param array $data - Data to be extracted into PHP variables and made available to the view script
     * @param boolean $return Whether the rendering result should be returned instead of being displayed to end users
     * @return string - The rendering result. Null if the rendering result is not required.
     * @throws CException - If the view does not exist
     * @see getViewFile
     */
    public final function render($view, $data = null, $return = false) {
        return parent::render("LForm", $data, $return);
    }

    /**
     * Get some property or it's default value
     * @param string $key - Property key
     * @param null $default - Default value (if property will be unset)
     * @return mixed - Something from properties array
     */
    public function getProperty($key, $default = null) {
        if (isset($this->_properties[$key])) {
            return $this->_properties[$key];
        }
        return $default;
    }

    private $_properties = [];

    /**
     * @return LFormModelAdapter - Component's model
     */
    public function getModel() {
        return $this->model;
    }

    private $_modelConfig = null;
    private $_viewConfig = null;
} 