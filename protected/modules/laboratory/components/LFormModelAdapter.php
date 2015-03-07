<?php

class LFormModelAdapter extends LFormModel {

	/**
	 * Constructor.
	 * @param string $scenario name of the scenario that this model is used in.
	 * See {@link CModel::scenario} on how scenario is used by models.
	 * @param array $config - Dynamic form configuration
	 * @see getScenario
	 */
    public function __construct($scenario, $config = []) {
        parent::__construct($scenario); $this->_config = $config;
    }

    /**
     * Override that method to return config. Config should return array associated with
     * model's variables. Every field must contains 3 parameters:
     *  + label - Variable's label, will be displayed in the form
     *  + type - Input type (@see LForm::renderField())
     *  + rules - Basic form's Yii rules, such as 'required' or 'numeric' etc
     * @return Array - Model's config
     */
    public function config() {
        return $this->_config;
    }
}